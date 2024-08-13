<!-- resources/views/home.blade.php -->
@extends('layouts.app')

@section('title', 'Home')

@section('content')
    <h1>Página Inicial</h1>
    <ul class="list-group">
        <li class="list-group-item"><a href="{{ route('login') }}">Login</a></li>
        
    </ul>
@endsection
