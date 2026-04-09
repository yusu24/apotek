<?php

namespace App\Livewire\Layout;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class NotificationBell extends Component
{
    public $iconOnly = false;
    public $direction = 'up';

    public function getUnreadCountProperty()
    {
        return Auth::user()->unreadNotifications()->count();
    }

    public function getRecentNotificationsProperty()
    {
        return Auth::user()->notifications()->limit(5)->get();
    }

    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->find($id);
        if ($notification) {
            $notification->markAsRead();
        }
    }

    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
    }

    public function render()
    {
        return view('livewire.layout.notification-bell');
    }
}
