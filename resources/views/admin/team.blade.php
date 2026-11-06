@extends('layouts.admin', ['title' => 'Team'])

@section('content')
<h1 class="text-4xl">Team</h1>
<p class="mt-2 text-cocoa">Owners can change prices, settings and the team. Staff can manage requests, prices and photos.</p>
<div class="card mt-8 overflow-x-auto bg-white">
    <table class="adm-table">
        <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Last sign-in</th><th></th></tr></thead>
        <tbody>
            @foreach($users as $u)
                <tr>
                    <td class="font-semibold">{{ $u->name }}</td><td>{{ $u->email }}</td><td>{{ ucfirst($u->role) }}</td>
                    <td class="hint">{{ $u->last_login_at?->diffForHumans() ?? 'Never' }}</td>
                    <td>@unless($u->is(auth()->user()))<form method="POST" action="{{ route('admin.team.destroy', $u) }}" onsubmit="return confirm('Remove {{ $u->name }}?')">@csrf @method('DELETE')<button class="inline-flex min-h-11 items-center text-[.9rem] font-semibold text-rose underline">Remove</button></form>@endunless</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<form method="POST" action="{{ route('admin.team.store') }}" class="card mt-8 grid max-w-[760px] gap-4 bg-white p-6 sm:grid-cols-2">
    @csrf
    <h2 class="text-2xl sm:col-span-2">Add someone</h2>
    <label class="field"><span>Name</span><input class="input" name="name" required></label>
    <label class="field"><span>Email</span><input class="input" type="email" name="email" required></label>
    <label class="field"><span>Temporary password (10+ characters)</span><input class="input" type="password" name="password" required minlength="10" autocomplete="new-password"></label>
    <label class="field"><span>Role</span><select class="input" name="role"><option value="staff">Staff</option><option value="owner">Owner</option></select></label>
    <div class="sm:col-span-2"><button class="btn btn-primary">Add</button></div>
</form>
@endsection
