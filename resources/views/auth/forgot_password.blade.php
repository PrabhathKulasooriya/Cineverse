@extends('customer_include.main')

@section('pageSpecificStyles')
<link rel="stylesheet" href="{{ asset('css/verifyPage.css') }}">
@endsection

@section('pageSpecificContent')
<div class="verify-page-main">
    <div class="verify-container">

        <div class="verify-header">
            <div class="verify-logo">Cine<span>verse</span></div>
            <p class="verify-tagline">Your cinema, your world</p>
        </div>

        <div class="verify-body">

            <div class="verify-filmstrip">
                <div class="verify-hole active"></div>
                <div class="verify-hole"></div>
                <div class="verify-hole active"></div>
                <div class="verify-hole"></div>
                <div class="verify-hole active"></div>
                <div class="verify-hole"></div>
                <div class="verify-hole active"></div>
            </div>

            <div class="verify-icon-ring">
                <i class="fa fa-key" style="font-size:26px; color:#b22222;"></i>
            </div>

            <h2 class="verify-title">Forgot Password?</h2>
            <p class="verify-desc">
                Enter your registered email address below.<br>
                We'll send you a secure link to reset your password.
            </p>

            <hr class="verify-divider">

            @if(session('success'))
                <div class="verify-sent-msg" style="display: block; position: relative; margin-bottom: 15px;">
                    <i class="fa fa-check-circle"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="verify-resent-alert" style="color: #b22222; background: #ffe6e6; border-color: #b22222; margin-bottom: 15px;">
                    <i class="fa fa-exclamation-circle"></i>
                    {{ session('error') }}
                </div>
            @endif
            
             @if(session('success'))

             @else
            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                
                <input type="email" name="email" class="auth-input" placeholder="Email Address" required oninput="this.value = this.value.toLowerCase();">
                @if($errors->has('email'))
                    <span class="auth-error">{{ $errors->first('email') }}</span>
                @endif

                <button class="btn-verify-resend" type="submit" style="opacity: 1; cursor: pointer;">
                    Send Reset Link
                </button>
            </form>
            @endif

        </div>

        <div class="verify-footer">
            <p class="mb-2">Remembered your password?</p>
            <a href="{{ url('/login') }}" class="verify-logout" style="text-decoration: none; display: inline-block;">
                <i class="fa fa-arrow-left"></i> Back to Login
            </a>
        </div>

    </div>
</div>
@endsection

@section('pageSpecificScript')

<script>
    document.querySelector('form').addEventListener('submit', function() {
        const btn = document.querySelector('.btn-verify-resend');
        btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Sending...';
        btn.style.opacity = '0.7';
        btn.style.cursor = 'not-allowed';
        btn.disabled = true;
    });
</script>

@endsection