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
        $isDeveloper = (bool) (auth()->user()?->is_developer ?? false);

        // Get all active users with their roles
        $users = User::with('roles')
            ->where('is_active', true)
            ->when(!$isDeveloper, function ($query) {
                $query->where('is_developer', false);
            })
            ->select('id', 'name', 'email', 'profile_photo_path', 'is_developer')
            ->get()
            ->map(function ($user) {
                $user->is_online = Cache::has('user-is-online-' . $user->id);
                $lastSeenRaw = Cache::get('user-last-seen-' . $user->id);
                $user->last_seen_human = $lastSeenRaw
                    ? \Carbon\Carbon::parse($lastSeenRaw)->diffForHumans()
                    : null;
                $roleName = $user->roles->first()?->name ?? ($user->is_developer ? 'developer' : 'user');
                $user->role_label = ucwords(str_replace(['-', '_'], ' ', $roleName));
                return $user;
            });

        $onlineUsers = $users->where('is_online', true)->values();
        $offlineUsers = $users->where('is_online', false)->values();

        return view('livewire.layout.online-users', [
            'onlineUsers' => $onlineUsers,
            'offlineUsers' => $offlineUsers,
            'iconOnly' => $this->iconOnly,
            'direction' => $this->direction,
            'textColor' => $this->textColor,
        ]);
    }
}
