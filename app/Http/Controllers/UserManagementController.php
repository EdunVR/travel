<?php

namespace App\Http\Controllers;

use App\Traits\HasOutletFilter;

use App\Models\User;
use App\Models\Role;
use App\Models\Outlet;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserManagementController extends Controller
{
    use \App\Traits\HasOutletFilter;

    public function index()
    {
        $users = User::with(['role', 'outlets'])->get();
        $roles = Role::all();
        $outlets = Outlet::where('is_active', true)->get();
        
        return view('admin.user-management.users.index', compact('users', 'roles', 'outlets'));
    }

    public function getData(Request $request): JsonResponse
    {
        try {
            $query = User::with(['role', 'outlets']);

            if ($request->role_id) {
                $query->where('role_id', $request->role_id);
            }

            if ($request->status && $request->status !== 'all') {
                $query->where('is_active', $request->status === 'active');
            }

            if ($request->search) {
                $query->where(function($q) use ($request) {
                    $q->where('name', 'like', "%{$request->search}%")
                      ->orWhere('email', 'like', "%{$request->search}%");
                });
            }

            $users = $query->orderBy('created_at', 'desc')->get()->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'role_name' => $user->role->display_name ?? '-',
                    'role_id' => $user->role_id,
                    'outlets_count' => $user->outlets->count(),
                    'outlets' => $user->outlets->pluck('nama_outlet')->toArray(),
                    'is_active' => $user->is_active,
                    'last_login_at' => $user->last_login_at ? $user->last_login_at->format('Y-m-d H:i') : null,
                    'created_at' => $user->created_at->format('Y-m-d H:i')
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $users
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:8',
                'phone' => 'nullable|string|max:20',
                'role_id' => 'required|exists:roles,id',
                'outlet_ids' => 'nullable|array',
                'outlet_ids.*' => 'exists:outlets,id_outlet',
                'signature' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'role_id' => $request->role_id,
                'is_active' => true,
                'email_verified_at' => now()
            ];

            // Handle signature upload
            if ($request->hasFile('signature')) {
                $file = $request->file('signature');
                $filename = 'signature_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                
                // Create directory if it doesn't exist
                $signatureDir = public_path('img/signatures');
                if (!file_exists($signatureDir)) {
                    mkdir($signatureDir, 0755, true);
                }
                
                $file->move($signatureDir, $filename);
                $userData['signature_path'] = 'img/signatures/' . $filename;
            }

            $user = User::create($userData);

            // Assign outlets
            if ($request->has('outlet_ids') && is_array($request->outlet_ids)) {
                $user->outlets()->attach($request->outlet_ids);
            }

            UserActivityLog::log('create', "Created user: {$user->name}", 'sistem.users', ['user_id' => $user->id]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'User berhasil ditambahkan',
                'data' => $user
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan user: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $user = User::with(['role', 'outlets'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'role_id' => $user->role_id,
                    'outlet_ids' => $user->outlets->pluck('id_outlet')->toArray(),
                    'is_active' => $user->is_active,
                    'signature_url' => $user->signature_url
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan'
            ], 404);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $user = User::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $id,
                'password' => 'nullable|min:8',
                'phone' => 'nullable|string|max:20',
                'role_id' => 'required|exists:roles,id',
                'outlet_ids' => 'nullable|array',
                'outlet_ids.*' => 'exists:outlets,id_outlet',
                'is_active' => 'boolean',
                'signature' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $updateData = [
                'name'      => $request->name,
                'email'     => $request->email,
                'phone'     => $request->phone,
                'role_id'   => $request->role_id,
                // Gunakan boolean() agar false dikirim saat checkbox tidak tercentang
                'is_active' => $request->boolean('is_active'),
            ];

            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            // Handle signature upload
            if ($request->hasFile('signature')) {
                // Delete old signature if exists
                if ($user->signature_path && file_exists(public_path($user->signature_path))) {
                    unlink(public_path($user->signature_path));
                }

                $file = $request->file('signature');
                $filename = 'signature_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                
                // Create directory if it doesn't exist
                $signatureDir = public_path('img/signatures');
                if (!file_exists($signatureDir)) {
                    mkdir($signatureDir, 0755, true);
                }
                
                $file->move($signatureDir, $filename);
                $updateData['signature_path'] = 'img/signatures/' . $filename;
            }

            $user->update($updateData);

            // Sync outlets — selalu sync, meski kosong (uncheck semua = hapus semua)
            $user->outlets()->sync($request->input('outlet_ids', []));

            UserActivityLog::log('update', "Updated user: {$user->name}", 'sistem.users', ['user_id' => $user->id]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'User berhasil diperbarui',
                'data' => $user
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui user: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $user = User::findOrFail($id);

            // Prevent deleting super admin
            if ($user->hasRole('super_admin')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Super Admin tidak dapat dihapus'
                ], 422);
            }

            // Prevent self-deletion
            if ($user->id === auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak dapat menghapus akun sendiri'
                ], 422);
            }

            $userName = $user->name;
            $user->delete();

            UserActivityLog::log('delete', "Deleted user: {$userName}", 'sistem.users', ['user_id' => $id]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'User berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus user: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getRoles(): JsonResponse
    {
        try {
            $roles = Role::active()->get()->map(function ($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                    'display_name' => $role->display_name,
                    'description' => $role->description
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $roles
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat roles: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getOutlets(): JsonResponse
    {
        try {
            $outlets = $this->getAccessibleOutlets()->map(function ($outlet) {
                return [
                    'id' => $outlet->id_outlet,
                    'name' => $outlet->nama_outlet,
                    'address' => $outlet->alamat
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $outlets
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat outlets: ' . $e->getMessage()
            ], 500);
        }
    }

    public function toggleStatus($id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $user = User::findOrFail($id);

            // Prevent deactivating super admin
            if ($user->hasRole('super_admin')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Super Admin tidak dapat dinonaktifkan'
                ], 422);
            }

            $user->update(['is_active' => !$user->is_active]);

            $status = $user->is_active ? 'activated' : 'deactivated';
            UserActivityLog::log('update', "User {$status}: {$user->name}", 'sistem.users', ['user_id' => $user->id]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Status user berhasil diubah'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah status: ' . $e->getMessage()
            ], 500);
        }
    }
}
