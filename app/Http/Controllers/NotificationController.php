<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /**
     * Display a listing of the user's notifications.
     */
    public function index(Request $request): View
    {
        $filter = $request->get('filter', 'all'); // all, unread, read
        $search = $request->get('search');

        $query = auth()->user()->notifications();

        // Apply filters
        if ($filter === 'unread') {
            $query->whereNull('read_at');
        } elseif ($filter === 'read') {
            $query->whereNotNull('read_at');
        }

        // Apply search
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(JSON_EXTRACT(data, "$.title")) LIKE ?', ['%' . strtolower($search) . '%'])
                    ->orWhereRaw('LOWER(JSON_EXTRACT(data, "$.message")) LIKE ?', ['%' . strtolower($search) . '%']);
            });
        }

        $notifications = $query->paginate(20);

        return view('pages.notifications.index', compact('notifications', 'filter', 'search'));
    }

    /**
     * Display the specified notification and mark as read.
     */
    public function show(string $id): RedirectResponse
    {
        $notification = auth()->user()->notifications()->findOrFail($id);

        // Mark as read
        if (is_null($notification->read_at)) {
            $notification->markAsRead();
        }

        // Redirect to action URL if available
        $actionUrl = $notification->data['action_url'] ?? route('dashboard');

        return redirect($actionUrl);
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead(string $id): RedirectResponse
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return back()->with('success', 'Notification marked as read.');
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(): RedirectResponse
    {
        auth()->user()->unreadNotifications->markAsRead();

        return back()->with('success', 'All notifications marked as read.');
    }

    /**
     * Remove the specified notification.
     */
    public function destroy(string $id): RedirectResponse
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->delete();

        return back()->with('success', 'Notification deleted.');
    }

    /**
     * Get unread notification count (for AJAX).
     */
    public function getUnreadCount(): array
    {
        return [
            'count' => auth()->user()->unreadNotifications->count(),
        ];
    }
}
