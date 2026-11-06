@extends('errors.layout')
@section('title', 'Page not found')
@section('code', 'Error 404')
@section('heading', 'We can’t find that page.')
@section('message', 'The link may be old or mistyped. Everything we sell is on the textures page, and you can track an order with your reference.')
@section('actions')
    <a href="{{ url('/textures') }}" class="btn btn-primary">See the textures</a>
    <a href="{{ url('/track') }}" class="btn btn-ghost">Track an order</a>
@endsection
