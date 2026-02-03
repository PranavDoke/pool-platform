@extends('layouts.app')

@section('title', 'Login - Live Poll Platform')

@section('content')
<div class="row justify-content-center" style="margin-top: 80px;">
    <div class="col-md-5">
        <div class="card">
            <div class="card-body p-5">
                <div class="text-center mb-4">
                    <i class="bi bi-bar-chart-fill" style="font-size: 48px; color: #667eea;"></i>
                    <h2 class="mt-3 fw-bold">Live Poll Platform</h2>
                    <p class="text-muted">Sign in to participate in polls</p>
                </div>

                <form method="POST" action="{{ route('login.post') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" 
                               class="form-control form-control-lg @error('email') is-invalid @enderror" 
                               id="email" 
                               name="email" 
                               value="{{ old('email') }}" 
                               required 
                               autofocus
                               placeholder="Enter your email">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" 
                               class="form-control form-control-lg @error('password') is-invalid @enderror" 
                               id="password" 
                               name="password" 
                               required
                               placeholder="Enter your password">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label class="form-check-label" for="remember">
                            Remember me
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100">
                        <i class="bi bi-box-arrow-in-right"></i> Sign In
                    </button>
                </form>

                <hr class="my-4">

                <div class="alert alert-info mb-0">
                    <strong>Demo Credentials:</strong><br>
                    <small>Email: admin@poll.com | Password: password (Admin)</small><br>
                    <small>Email: user@poll.com | Password: password (User)</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
