<!-- resources/views/login.blade.php -->
@extends('layouts.app')

@section('title', 'Login')

@section('content')
    <h1>Login</h1>
    <form method="POST" action="{{ route('login.submit') }}">
        @csrf
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="password">Senha:</label>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Login</button>
    </form>
@endsection
