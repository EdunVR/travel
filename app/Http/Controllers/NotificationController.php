<?php

namespace App\Http\Controllers;

use App\Services\NotificationService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Get unread notifications for current user
     * Requirement 18.6
     */
    public function getUnread()
    {
        $notifications = $this->notificationService->getUnreadNotifications(
            auth()->id(),
            10
        );

        return response()->json([
            'success' => true,
            'notifications' => $notifications,
            'count' => $this->notificationService->getUnreadCount(auth()->id())
        ]);
    }

    /**
     * Get unread count for current user
     */
    public function getUnreadCount()
    {
        return response()->json([
            'success' => true,
            'count' => $this->notificationService->getUnreadCount(auth()->id())
        ]);
    }

    /**
     * Mark notification as read
     * Requirement 18.7
     */
    public function markAsRead($id)
    {
        $success = $this->notificationService->markAsRead($id);

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Notification marked as read' : 'Notification not found'
        ]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        $this->notificationService->markAllAsRead(auth()->id());

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read'
        ]);
    }

    /**
     * Get all notifications for current user
     */
    public function index()
    {
        $notifications = auth()->user()
            ->notifications()
            ->recent()
            ->paginate(20);

        return view('admin.notifications.index', compact('notifications'));
    }
}
