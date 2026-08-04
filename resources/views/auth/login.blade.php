@extends('layouts.app')

@section('title', 'Sign In - TaskSpace')

@section('content')
<div class="row justify-content-center align-items-center" style="min-height: 75vh;">
    <div class="col-12 col-md-6 col-lg-5">
        
        <!-- Logo/Brand area -->
        <div class="text-center mb-4">
            <div class="d-inline-flex align-items-center justify-content-center mb-3" style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); width: 50px; height: 50px; border-radius: 14px; box-shadow: 0 4px 14px rgba(99, 102, 241, 0.4);">
                <i class="fa-solid fa-list-check text-white" style="font-size: 1.5rem;"></i>
            </div>
            <h3 class="font-weight-bold text-white mb-1">Welcome to TaskSpace</h3>
            <p class="text-secondary small">Manage your personal tasks efficiently</p>
        </div>

        <!-- Glassmorphic Login Card -->
        <div class="glass-card p-4">
            <h5 class="font-weight-bold mb-4 text-white text-center">Sign In</h5>
            
            <form action="{{ route('login') }}" method="POST">
                @csrf
                
                <div class="form-group mb-3">
                    <label for="email" class="font-weight-bold text-white small">Email Address</label>
                    <div class="position-relative">
                        <input type="email" id="email" name="email" class="form-control glass-input w-100" placeholder="admin@gmail.com" value="{{ old('email') }}" required autofocus autocomplete="email">
                    </div>
                </div>

                <div class="form-group mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label for="password" class="font-weight-bold text-white small m-0">Password</label>
                    </div>
                    <div class="position-relative">
                        <input type="password" id="password" name="password" class="form-control glass-input w-100" placeholder="••••••" required autocomplete="current-password">
                    </div>
                </div>

                <div class="form-group form-check mb-4 d-flex align-items-center">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember" style="accent-color: #6366f1; width: 16px; height: 16px; margin-top: 1px;">
                    <label class="form-check-label text-secondary small ml-2" for="remember" style="user-select: none;">Remember me</label>
                </div>

                <button type="submit" class="btn btn-premium btn-block py-2 mb-3">
                    <i class="fa-solid fa-right-to-bracket mr-2"></i> Log In
                </button>
            </form>

            <!-- Prefilled Helper Card -->
            <div class="p-3 rounded mt-3" style="background: rgba(99, 102, 241, 0.05); border: 1px dashed rgba(99, 102, 241, 0.25);">
                <div class="d-flex align-items-center mb-1 text-indigo" style="color: #818cf8; font-size: 0.8rem; font-weight: 600;">
                    <i class="fa-solid fa-circle-info mr-2"></i> Quick Demo Access
                </div>
                <div class="small text-secondary" style="font-size: 0.75rem;">
                    <strong>Email:</strong> <code style="color: #f8fafc;">admin@gmail.com</code><br>
                    <strong>Password:</strong> <code style="color: #f8fafc;">123456</code>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
