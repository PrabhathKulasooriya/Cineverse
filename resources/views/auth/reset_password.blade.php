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
                <i class="fa fa-lock" style="font-size:26px; color:#b22222;"></i>
            </div>

            <h2 class="verify-title">Set New Password</h2>
            <p class="verify-desc">
                Create a strong, new password for your account.
            </p>

            <hr class="verify-divider">

            @if(session('error'))
                <div class="verify-resent-alert" style="color: #b22222; background: #ffe6e6; border-color: #b22222; margin-bottom: 15px;">
                    <i class="fa fa-exclamation-circle"></i>
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <input type="email" name="email" class="auth-input" value="{{ $email ?? old('email') }}" readonly required>
                
                <input type="password" name="password" class="auth-input" placeholder="New Password (min 6 characters)" required>
                @if($errors->has('password'))
                    <span class="auth-error">{{ $errors->first('password') }}</span>
                @endif

                <input type="password" name="password_confirmation" class="auth-input" placeholder="Confirm New Password" required>

                <button class="btn-verify-resend" type="submit" style="opacity: 1; cursor: pointer;">
                    Update Password
                </button>
            </form>

        </div>

        <div class="verify-footer">
            <p class="mb-2">Changed your mind?</p>
            <a href="{{ url('/login') }}" class="verify-logout" style="text-decoration: none; display: inline-block;">
                <i class="fa fa-times"></i> Cancel
            </a>
        </div>

    </div>
</div>
@endsection

@section('pageSpecificScript')
@endsection