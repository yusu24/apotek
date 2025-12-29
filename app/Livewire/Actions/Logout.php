<?php

namespace App\Livewire\Actions;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class Logout
{
    /**
     * Log the current user out of the application.
     */
    public function __invoke(): void
    {
        \App\Models\ActivityLog::log([
            'action' => 'logout',
            'module' => 'users',
            'description' => 'User berhasil logout dari sistem'
        ]);

        Auth::guard('web')->logout();

        Session::invalidate();
        Session::regenerateToken();
    }
}
