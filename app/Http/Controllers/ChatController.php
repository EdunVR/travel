<?php

namespace App\Http\Controllers;

use App\Traits\HasOutletFilter;

use App\Events\MessageSent;
use App\Models\Message;
use App\Models\User;
use App\Notifications\NewChatMessage;
use App\Services\ChatbotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class ChatController extends Controller
{
    use \App\Traits\HasOutletFilter;

    /**
     * Display the chat panel view
     */
    public function panel(): View
    {
        return view('admin.chat.panel');
    }

    /**
     * Get messages for the authenticated user
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getMessages(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'mode' => 'required|in:superadmin,chatbot',
            'user_id' => 'nullable|integer|exists:users,id|min:1',
            'page' => 'nullable|integer|min:1|max:1000',
        ], [
            'mode.required' => 'Mode chat harus dipilih.',
            'mode.in' => 'Mode chat tidak valid. Pilih superadmin atau chatbot.',
            'user_id.exists' => 'User tidak ditemukan.',
            'user_id.min' => 'User ID tidak valid.',
            'page.min' => 'Halaman minimal 1.',
            'page.max' => 'Halaman maksimal 1000.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validasi gagal',
                'messages' => $validator->errors(),
                'details' => $validator->errors()->first()
            ], 422);
        }

        $userId = auth()->id();
        $isSuperadmin = $this->isSuperadmin();
        $mode = $request->input('mode');
        $requestedUserId = $request->input('user_id');

        // Permission check: regular users can only see their own messages
        if (!$isSuperadmin && $requestedUserId && $requestedUserId != $userId) {
            return response()->json([
                'error' => 'Unauthorized access'
            ], 403);
        }

        // Build optimized query with eager loading
        $query = Message::with(['sender:id,name,email,avatar', 'receiver:id,name,email,avatar'])
            ->byMode($mode)
            ->orderBy('created_at', 'desc');

        if ($isSuperadmin && $requestedUserId) {
            // Superadmin viewing specific user's conversation
            $query->where(function ($q) use ($requestedUserId) {
                $q->where('sender_id', $requestedUserId)
                  ->orWhere('receiver_id', $requestedUserId);
            });
        } elseif (!$isSuperadmin) {
            // Regular user viewing their own messages
            $query->forUser($userId);
        }
        // If superadmin without user_id, show all messages (for chatbot monitoring)

        // Use cursor pagination for better performance on large datasets
        // Fall back to regular pagination if page parameter is provided
        $messages = $query->paginate(50);

        return response()->json([
            'messages' => $messages->items(),
            'pagination' => [
                'current_page' => $messages->currentPage(),
                'last_page' => $messages->lastPage(),
                'per_page' => $messages->perPage(),
                'total' => $messages->total(),
            ]
        ]);
    }

    /**
     * Send a new message
     * 
     * @param Request $request
     * @param ChatbotService $chatbotService
     * @return JsonResponse
     */
    public function sendMessage(Request $request, ChatbotService $chatbotService): JsonResponse
    {
        // Check rate limit manually to provide custom error message
        $key = 'chat_message_' . auth()->id();
        $maxAttempts = 10; // 10 messages per minute
        $decayMinutes = 1;
        
        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn($key);
            
            return response()->json([
                'error' => 'Terlalu banyak pesan dikirim. Silakan tunggu ' . ceil($seconds / 60) . ' menit.',
                'retry_after' => $seconds
            ], 429);
        }
        
        $validator = Validator::make($request->all(), [
            'content' => [
                'required',
                'string',
                'min:1',
                'max:1000',
                function ($attribute, $value, $fail) {
                    // Ensure content is not just whitespace
                    if (trim($value) === '') {
                        $fail('Pesan tidak boleh kosong.');
                    }
                },
            ],
            'mode' => 'required|in:superadmin,chatbot',
            'receiver_id' => 'nullable|integer|exists:users,id',
        ], [
            'content.required' => 'Pesan tidak boleh kosong.',
            'content.min' => 'Pesan minimal 1 karakter.',
            'content.max' => 'Pesan maksimal 1000 karakter.',
            'mode.required' => 'Mode chat harus dipilih.',
            'mode.in' => 'Mode chat tidak valid.',
            'receiver_id.exists' => 'Penerima tidak ditemukan.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validasi gagal',
                'messages' => $validator->errors(),
                'details' => $validator->errors()->first()
            ], 422);
        }
        
        // Hit the rate limiter
        \Illuminate\Support\Facades\RateLimiter::hit($key, $decayMinutes * 60);

        $userId = auth()->id();
        // Sanitize content to prevent XSS - strip tags and trim
        $content = strip_tags(trim($request->input('content')));
        $mode = $request->input('mode');
        $receiverId = $request->input('receiver_id');

        // Determine receiver based on mode
        if ($mode === 'superadmin' && !$receiverId) {
            // Regular user sending to superadmin - find first superadmin
            $superadmin = User::whereHas('role', function ($q) {
                $q->where('name', 'super_admin');
            })->first();

            if (!$superadmin) {
                return response()->json([
                    'error' => 'No superadmin available'
                ], 404);
            }

            $receiverId = $superadmin->id;
        }

        // Create user message
        $message = Message::create([
            'sender_id' => $userId,
            'receiver_id' => $receiverId,
            'mode' => $mode,
            'content' => $content,
            'outlet_id' => session('outlet_id'),
        ]);

        // Load relationships
        $message->load(['sender', 'receiver']);

        // Broadcast event if there's a receiver
        if ($receiverId) {
            broadcast(new MessageSent($message, $receiverId))->toOthers();
        }

        // Send notification to all superadmins when a regular user sends a message
        if ($mode === 'superadmin' && !$this->isSuperadmin()) {
            $superadmins = User::whereHas('role', function ($q) {
                $q->where('name', 'super_admin');
            })->get();

            Notification::send($superadmins, new NewChatMessage($message));
            
            // Clear user list cache for all superadmins
            foreach ($superadmins as $superadmin) {
                Cache::forget("user_list_superadmin_{$superadmin->id}");
            }
        }
        
        // Clear unread count cache for receiver
        if ($receiverId) {
            Cache::forget("unread_count_{$receiverId}");
        }

        // Handle chatbot response if in chatbot mode
        $chatbotMessage = null;
        if ($mode === 'chatbot') {
            try {
                // Get chatbot response
                $chatbotResponse = $chatbotService->processMessage($content, $userId);

                // Create chatbot message
                $chatbotMessage = Message::create([
                    'sender_id' => null, // Chatbot has no user ID
                    'receiver_id' => $userId,
                    'mode' => 'chatbot',
                    'content' => $chatbotResponse,
                    'outlet_id' => session('outlet_id'),
                ]);

                // Load relationships
                $chatbotMessage->load(['sender', 'receiver']);

                // Broadcast chatbot response to user
                broadcast(new MessageSent($chatbotMessage, $userId))->toOthers();
            } catch (\Exception $e) {
                // Log error but don't fail the request
                \Log::error('Failed to generate chatbot response', [
                    'user_id' => $userId,
                    'message' => $content,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return response()->json([
            'message' => $message,
            'chatbot_message' => $chatbotMessage,
            'success' => true
        ], 201);
    }

    /**
     * Get list of users with messages (superadmin only)
     * Optimized with caching and efficient queries
     * 
     * @return JsonResponse
     */
    public function getUserList(): JsonResponse
    {
        if (!$this->isSuperadmin()) {
            return response()->json([
                'error' => 'Unauthorized access'
            ], 403);
        }

        $superadminId = auth()->id();
        $cacheKey = "user_list_superadmin_{$superadminId}";

        // Cache for 5 minutes to reduce database load
        $users = Cache::remember($cacheKey, 300, function () use ($superadminId) {
            // Optimized query using subqueries to get last message and unread count
            $users = User::select([
                'users.id',
                'users.name',
                'users.email',
                'users.avatar',
                DB::raw('(SELECT content FROM messages WHERE sender_id = users.id OR receiver_id = users.id ORDER BY created_at DESC LIMIT 1) as last_message_content'),
                DB::raw('(SELECT created_at FROM messages WHERE sender_id = users.id OR receiver_id = users.id ORDER BY created_at DESC LIMIT 1) as last_message_at'),
                DB::raw("(SELECT COUNT(*) FROM messages WHERE sender_id = users.id AND receiver_id = {$superadminId} AND is_read = 0) as unread_count")
            ])
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('messages')
                    ->whereRaw('messages.sender_id = users.id OR messages.receiver_id = users.id');
            })
            ->where('users.id', '!=', $superadminId)
            ->orderByDesc('last_message_at')
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'avatar' => $user->avatar ?? $user->profile_photo_url,
                    'last_message' => $user->last_message_content ? [
                        'content' => htmlspecialchars($user->last_message_content, ENT_QUOTES, 'UTF-8'),
                        'created_at' => $user->last_message_at,
                    ] : null,
                    'unread_count' => (int) $user->unread_count,
                    'last_message_at' => $user->last_message_at,
                ];
            });

            return $users;
        });

        return response()->json([
            'users' => $users
        ]);
    }

    /**
     * Mark messages as read
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function markAsRead(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'message_ids' => 'required|array|min:1|max:100',
            'message_ids.*' => 'integer|exists:messages,id|min:1',
        ], [
            'message_ids.required' => 'ID pesan harus disertakan.',
            'message_ids.array' => 'ID pesan harus berupa array.',
            'message_ids.min' => 'Minimal 1 pesan harus dipilih.',
            'message_ids.max' => 'Maksimal 100 pesan dapat ditandai sekaligus.',
            'message_ids.*.integer' => 'ID pesan harus berupa angka.',
            'message_ids.*.exists' => 'Pesan tidak ditemukan.',
            'message_ids.*.min' => 'ID pesan tidak valid.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validasi gagal',
                'messages' => $validator->errors(),
                'details' => $validator->errors()->first()
            ], 422);
        }

        $userId = auth()->id();
        $messageIds = $request->input('message_ids');

        // Update only messages where user is the receiver
        $updated = Message::whereIn('id', $messageIds)
            ->where('receiver_id', $userId)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        // Clear unread count cache
        Cache::forget("unread_count_{$userId}");

        return response()->json([
            'success' => true,
            'updated_count' => $updated
        ]);
    }

    /**
     * Get unread message count
     * 
     * @return JsonResponse
     */
    public function getUnreadCount(): JsonResponse
    {
        $userId = auth()->id();
        $isSuperadmin = $this->isSuperadmin();

        $cacheKey = "unread_count_{$userId}";

        $unreadCount = Cache::remember($cacheKey, 30, function () use ($userId, $isSuperadmin) {
            if ($isSuperadmin) {
                // Superadmin sees aggregate count across all users
                return Message::where('receiver_id', $userId)
                    ->unread()
                    ->count();
            } else {
                // Regular user sees their own unread count
                return Message::where('receiver_id', $userId)
                    ->unread()
                    ->count();
            }
        });

        return response()->json([
            'unread_count' => $unreadCount
        ]);
    }

    /**
     * Check if current user is superadmin
     * 
     * @return bool
     */
    private function isSuperadmin(): bool
    {
        return auth()->user()->role_id == 1;
    }
}
