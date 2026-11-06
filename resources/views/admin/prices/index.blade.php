@extends('layouts.admin', ['title' => 'Price lists'])

@section('content')
<h1 class="text-4xl">Price lists</h1>
<p class="mt-2 text-cocoa">These are the prices on the website and the pre-order form. All prices are in TT$.</p>
<div class="mt-8 grid gap-5 md:grid-cols-3">
    @foreach($textures as $t)
        <a href="{{ route('admin.prices.edit', $t) }}" class="card bg-white p-6 hover:border-gold">
            <img src="{{ asset($t->image) }}" alt="" class="aspect-[3/2] w-full rounded-xl object-cover">
            <h2 class="mt-4 text-2xl">{{ $t->name }}</h2>
            <p class="hint">{{ $t->price_sets_count }} sets · {{ $t->is_active ? 'on the website' : 'hidden' }}</p>
            <p class="mt-2 font-semibold">From <x-money :amount="$t->priceSets->where('is_active', true)->min('price') ?? 0"/></p>
        </a>
    @endforeach
</div>
@endsection
