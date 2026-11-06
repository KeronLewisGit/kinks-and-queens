{{-- Top-of-form summary. A form-level message (rate limit, expired session, orders paused) wins over the generic one. --}}
@if($errors->any())
    <div {{ $attributes->merge(['class' => 'rounded-2xl border border-rose/40 bg-rose/5 p-5']) }} role="alert" data-error-summary>
        <p class="font-semibold text-rose">{{ $errors->first('form') ?: 'Please check the highlighted fields.' }}</p>
    </div>
@endif
