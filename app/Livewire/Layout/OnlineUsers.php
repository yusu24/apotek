<?php

namespace App\Livewire\Layout;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class OnlineUsers extends Component
{
    public bool $iconOnly = false;
    public string $direction = 'up';
    public string $textColor = 'text-white';

    public function render()
    {
        // Get all users
        $users = User::select('id', 'name', 'email', 'profile_photo_path')->get();
        
        // Filter users who have the active cache key
        $onlineUsers = $users->filter(function ($user) {
            return Cache::has('user-is-online-' . $user->id);
        });

        return view('livewire.layout.online-users', [
            'onlineUsers' => $onlineUsers,
            'iconOnly' => $this->iconOnly,
            'direction' => $this->direction,
            'textColor' => $this->textColor,
        ]);
    }
}
