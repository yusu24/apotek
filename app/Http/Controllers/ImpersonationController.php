<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ImpersonationController extends Controller
{
    public function leave()
    {
        if (!session()->has('impersonator_id')) {
            return redirect()->route('dashboard');
        }

        $originalUserId = session()->pull('impersonator_id');
        $originalUser = User::find($originalUserId);

        if ($originalUser) {
            Auth::login($originalUser);
            session()->flash('message', 'Kembali ke akun Admin.');
        }

        return redirect()->route('admin.users.index');
    }
}
