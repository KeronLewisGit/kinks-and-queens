@extends('layouts.admin', ['title' => $texture->name])

@section('content')
<a href="{{ route('admin.prices.index') }}" class="link inline-flex min-h-11 items-center">← Price lists</a>
<h1 class="mt-2 text-4xl">{{ $texture->name }}</h1>

<form method="POST" action="{{ route('admin.prices.update', $texture) }}" class="mt-8 grid gap-6 xl:grid-cols-[1fr_1.1fr] [&>*]:min-w-0">
    @csrf @method('PUT')
    <section class="card grid content-start gap-4 bg-white p-6">
        <h2 class="text-2xl">Texture details</h2>
        <label class="field"><span>Best for</span><input class="input" name="best_for" value="{{ old('best_for', $texture->best_for) }}" required></label>
        <label class="field"><span>Tagline</span><input class="input" name="tagline" value="{{ old('tagline', $texture->tagline) }}" required></label>
        <label class="field"><span>Description</span><textarea class="input min-h-40" name="description" required>{{ old('description', $texture->description) }}</textarea></label>
        @can('owner')
        <div><p class="label">Available as</p>
            <div class="mt-2 flex flex-wrap gap-4">
                @foreach(\App\Models\PreOrder::FORMATS as $k => $l)
                    <label class="flex min-h-11 items-center gap-2"><input type="checkbox" name="formats[]" value="{{ $k }}" class="size-5 accent-[#2b1a11]" @checked($texture->offers($k))> {{ $l }}</label>
                @endforeach
            </div>
        </div>
        <label class="flex min-h-11 items-center gap-2"><input type="hidden" name="is_active" value="0"><input type="checkbox" name="is_active" value="1" class="size-5 accent-[#2b1a11]" @checked($texture->is_active)> Show this texture on the website</label>
        @else
        <p class="hint">Available as {{ collect($texture->formats)->map(fn ($f) => strtolower(\App\Models\PreOrder::FORMATS[$f] ?? $f))->join(', ', ' and ') }}. {{ $texture->is_active ? 'Shown on the website.' : 'Hidden from the website.' }} Only the owner can change these.</p>
        @endcan
    </section>
    <section class="card bg-white p-6">
        <h2 class="text-2xl">Sets and prices</h2>
        <div class="mt-4 overflow-x-auto">
            <table class="adm-table">
                <thead><tr><th>Set</th><th>Price (TT$)</th><th>On sale</th></tr></thead>
                <tbody>
                    @foreach($texture->priceSets as $s)
                        <tr>
                            <td class="font-semibold">{{ $s->label() }}</td>
                            <td><input class="input !min-h-10 !w-32 !py-1.5" type="number" min="1" name="sets[{{ $s->id }}][price]" value="{{ $s->price }}" aria-label="Price for {{ $s->label() }}"></td>
                            <td><input type="hidden" name="sets[{{ $s->id }}][is_active]" value="0"><input type="checkbox" name="sets[{{ $s->id }}][is_active]" value="1" class="size-5 accent-[#2b1a11]" @checked($s->is_active) aria-label="{{ $s->label() }} on sale"></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
    <div class="xl:col-span-2"><button class="btn btn-primary">Save {{ $texture->pattern }}</button></div>
</form>

<form method="POST" action="{{ route('admin.prices.add', $texture) }}" class="card mt-8 flex flex-wrap items-end gap-3 bg-white p-6">
    @csrf
    <h2 class="w-full text-2xl">Add a set</h2>
    <label class="field"><span>Lengths in inches</span><input class="input" name="lengths" placeholder="16,18,20" required></label>
    <label class="field"><span>Price (TT$)</span><input class="input" type="number" min="1" name="price" required></label>
    <button class="btn btn-ghost">Add set</button>
</form>
@endsection
