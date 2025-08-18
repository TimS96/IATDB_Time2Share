<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UserController extends Controller
{
    use AuthorizesRequests;

    public function toggleBlock(User $user): RedirectResponse
    {
      
        $this->authorize('admin');

        if (auth()->id() === $user->id) {
            return back()->with('status', 'Je kunt jezelf niet blokkeren of deblokkeren.');
        }

        $user->blocked_at = $user->blocked_at ? null : now();
        $user->save();

        return back()->with('status', $user->blocked_at
            ? 'Gebruiker geblokkeerd.'
            : 'Gebruiker gedeblokkeerd.');
    }


    public function block(User $user): RedirectResponse
    {

        if (is_null($user->blocked_at)) {
            return $this->toggleBlock($user);
        }
        return back()->with('status', 'Gebruiker is al geblokkeerd.');
    }

    public function unblock(User $user): RedirectResponse
    {
       
        if (!is_null($user->blocked_at)) {
            return $this->toggleBlock($user);
        }
        return back()->with('status', 'Gebruiker is al gedeblokkeerd.');
    }
}
