<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class TeamController extends Controller
{
    public function index(): View
    {
        return view('admin.team', ['users' => User::orderBy('role')->orderBy('name')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', Password::min(10)],
            'role' => ['required', 'in:owner,staff'],
        ]);
        User::create($data);

        return back()->with('ok', $data['name'].' can now sign in.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->is($request->user())) {
            return back()->withErrors(['team' => 'You cannot remove yourself.']);
        }
        if ($user->isOwner() && User::where('role', 'owner')->count() <= 1) {
            return back()->withErrors(['team' => 'Keep at least one owner.']);
        }
        $user->delete();

        return back()->with('ok', 'Removed.');
    }
}
