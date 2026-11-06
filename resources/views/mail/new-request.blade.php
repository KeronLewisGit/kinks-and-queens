{{-- Plain-text email: printed raw on purpose, it is never rendered as HTML. --}}
New {!! $order->isClipInService() ? 'clip-in service' : 'pre-order' !!} request: {!! $order->reference !!}

{!! $order->title() !!}
@if($order->lengthsLabel())Length: {!! $order->lengthsLabel() !!}
@endif
@if($order->quoted_price)List price: TT${!! number_format($order->quoted_price) !!}
@endif
@foreach(($order->details ?? []) as $k => $v){!! \Illuminate\Support\Str::headline($k) !!}: {!! $v !!}
@endforeach

Name: {!! $order->name !!}
Phone: {!! $order->phone !!}
Instagram: {!! $order->instagram ? '@'.$order->instagram : '—' !!}
Email: {!! $order->email ?: '—' !!}
Area: {!! $order->area ?: '—' !!}
Prefers: {!! $order->contact_via !!}

{!! $order->notes !!}

Open in the admin: {!! route('admin.orders.show', $order) !!}
