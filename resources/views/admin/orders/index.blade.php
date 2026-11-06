@extends('layouts.admin', ['title' => 'Requests'])

@section('content')
<h1 class="text-4xl">Requests</h1>
<form method="GET" class="mt-6 flex flex-wrap items-end gap-3">
    <label class="field"><span>Search</span><input class="input !min-h-11 !py-2" name="q" value="{{ $q }}" placeholder="Reference, name, phone, @handle"></label>
    <label class="field"><span>Status</span>
        <select class="input !min-h-11 !py-2" name="status">
            <option value="">All</option><option value="open" @selected($status === 'open')>Open</option>
            @foreach(\App\Models\PreOrder::STATUSES as $k => $s)<option value="{{ $k }}" @selected($status === $k)>{{ $s['label'] }}</option>@endforeach
        </select>
    </label>
    <label class="field"><span>Type</span>
        <select class="input !min-h-11 !py-2" name="kind">
            <option value="">All</option><option value="hair" @selected($kind === 'hair')>Hair pre-orders</option><option value="clip_in_service" @selected($kind === 'clip_in_service')>Clip-in service</option>
        </select>
    </label>
    <button class="btn btn-primary !min-h-11">Filter</button>
</form>

<div class="card mt-6 overflow-x-auto bg-white">
    <table class="adm-table min-w-[760px]">
        <thead><tr><th>Ref</th><th>Customer</th><th>Request</th><th>Price</th><th>Status</th><th>Received</th></tr></thead>
        <tbody>
            @forelse($orders as $o)
                <tr>
                    <td><a class="link" href="{{ route('admin.orders.show', $o) }}">{{ $o->reference }}</a></td>
                    <td><span class="font-semibold">{{ $o->name }}</span><br><span class="hint">{{ $o->phone }}@if($o->instagram) · {{ '@'.$o->instagram }}@endif</span></td>
                    <td>{{ $o->title() }}@if($o->lengthsLabel())<br><span class="hint">{{ $o->lengthsLabel() }}</span>@endif</td>
                    <td>@if($o->quoted_price)<x-money :amount="$o->quoted_price"/>@else<span class="hint">To quote</span>@endif</td>
                    <td><span class="status status-{{ $o->status }}">{{ $o->statusLabel() }}</span></td>
                    <td class="hint">{{ $o->created_at->format('j M, g:ia') }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="py-10 text-center text-cocoa">No requests match.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-6">{{ $orders->links() }}</div>
@endsection
