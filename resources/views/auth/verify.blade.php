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
                <i class="fa fa-envelope" style="font-size:26px; color:#b22222;"></i>
            </div>

            <h2 class="verify-title">Verify Your Email Address</h2>
            <p class="verify-desc">
                We sent a verification link to your inbox.<br>
                Click it to activate your account and start booking.
                The verify link will be expire in 1 hour.
            </p>

            <hr class="verify-divider">

            @if (session('resent'))
                <div class="verify-resent-alert">
                    <i class="fa fa-check-circle"></i>
                    A fresh verification link has been sent to your email address.
                </div>
            @endif

            <p class="verify-resend-label">Didn't receive the email?</p>

            <form method="GET" action="{{ route('verification.resend') }}">
                @csrf
                <button class="btn-verify-resend" id="resendBtn" type="submit" disabled>
                    <i class="fa fa-refresh"></i>
                    Resend Link
                </button>
            </form>

            <div class="verify-countdown" id="countdownText">
                You can resend in <strong><span id="countdownNum">60</span>s</strong>
            </div>

            <div class="verify-sent-msg" id="sentMsg">
                <i class="fa fa-check-circle"></i>
                Verification email sent!
            </div>

        </div>

        <div class="verify-footer">
            <p class="mb-1">Please check your spam folder if you don't see the email</p>
            <p class="mb-2">Contact us : info@cineverse.com &nbsp;|&nbsp; 0115123456</p>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="verify-logout">
                    <i class="fa fa-sign-out"></i> Log Out
                </button>
            </form>
        </div>

    </div>
</div>
@endsection

@section('pageSpecificScript')

<script>
    let seconds = 60;
    const btn = document.getElementById('resendBtn');
    const countdownText = document.getElementById('countdownText');
    const countdownNum = document.getElementById('countdownNum');

    const timer = setInterval(() => {
        seconds--;
        countdownNum.textContent = seconds;
        if (seconds <= 0) {
            clearInterval(timer);
            btn.disabled = false;
            countdownText.textContent = '';
        }
    }, 1000);
</script>
@endsection