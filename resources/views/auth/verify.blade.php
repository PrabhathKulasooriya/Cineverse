<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email - Cineverse</title>
    <style>
        body { font-family: sans-serif; background-color: #f4f4f4; padding: 50px; text-align: center; }
        .card { background: #fff; padding: 30px; border-radius: 8px; max-width: 500px; margin: 0 auto; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .success-msg { color: #155724; background-color: #d4edda; padding: 10px; border-radius: 4px; margin-bottom: 20px; }
        button { background: none; border: none; color: #007bff; text-decoration: underline; cursor: pointer; font-size: 16px; padding: 0; }
    </style>
</head>
<body>

    <div class="card">
        <h2>Verify Your Email Address</h2>

        @if (session('resent'))
            <div class="success-msg">
                A fresh verification link has been sent to your email address.
            </div>
        @endif

        <p>Before proceeding, please check your email for a verification link.</p>
        <p>If you did not receive the email,</p>
        
       <a href="{{ route('verification.resend') }}" style="color: #007bff; text-decoration: underline; cursor: pointer; font-size: 16px;">
            click here to request another link
        </a>.
        
        <br><br>
        <a href="{{ route('logout') }}" style="color: #dc3545; text-decoration: none;">Log Out</a>
    </div>

</body>
</html>