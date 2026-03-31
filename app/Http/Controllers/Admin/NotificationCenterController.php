<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationCenterController extends Controller
{
    /**
     * Display a full listing of system alerts.
     */
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'all');
        $unreadOnly = $request->has('unread');

        $query = $unreadOnly 
            ? auth()->user()->unreadNotifications() 
            : auth()->user()->notifications();

        // Separate grouping logic based on the 'type' data field
        $notifications = $query->latest()->paginate(20);

        if ($filter === 'vehicles') {
            $notifications = $query->whereIn('data->type', ['vehicle_maintenance', 'legal_expiry', 'maintenance_request'])
                                   ->latest()->paginate(20);
        } elseif ($filter === 'office') {
            $notifications = $query->whereIn('data->type', ['agreement_expiry', 'rent_payment_due'])
                                   ->latest()->paginate(20);
        }

        return view('admin.notifications.index', compact('notifications', 'filter', 'unreadOnly'));
    }

    /**
     * Mark a specific notification as read.
     */
    public function markAsRead($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return back()->with('success', 'Alert marked as read.');
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllRead()
    {
        auth()->user()->unreadNotifications->markAsRead();
        return back()->with('success', 'All alerts cleared.');
    }
}
