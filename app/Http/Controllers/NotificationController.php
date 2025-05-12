<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display all notifications for the authenticated user.
     */
    public function index()
    {
        $notifications = Auth::user()->notifications()->paginate(10);
        
        return view('notifications.index', compact('notifications'));
    }
    
    /**
     * Mark a specific notification as read.
     */
    public function markAsRead(DatabaseNotification $notification)
    {
        // Check if notification belongs to the authenticated user
        if ($notification->notifiable_id != Auth::id()) {
            abort(403);
        }
        
        $notification->markAsRead();
        
        return back()->with('success', 'Notification marked as read.');
    }
    
    /**
     * Mark all notifications as read for the authenticated user.
     */
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        
        return back()->with('success', 'All notifications marked as read.');
    }
}
