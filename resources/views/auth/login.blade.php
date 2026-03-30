@extends('layouts.guest')

@section('title', 'Login | Inventaris IT')

@section('content')
    <div class="auth-card">
        <div class="auth-card__brand">
            <img src="{{ asset('brand/yulie-sekuritas-logo.png') }}" alt="Yulie Sekuritas" class="auth-card__logo">
            <div class="auth-card__eyebrow">Internal Access</div>
            <h1 class="auth-card__title">Masuk ke Sistem Inventaris IT</h1>
        </div>

        <form action="{{ route('login.store') }}" method="POST" class="form-grid">
            @csrf

            <div class="form-field">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" name="email" class="form-input" value="{{ old('email') }}" required autofocus inputmode="email" autocomplete="username">
                @error('email')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-field">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password" class="form-input" required autocomplete="current-password">
                @error('password')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <label class="auth-card__remember">
                <input type="checkbox" name="remember" value="1">
                Ingat sesi login ini
            </label>

            <button type="submit" class="btn btn--primary auth-card__submit">Masuk</button>
        </form>
    </div>
@endsection
