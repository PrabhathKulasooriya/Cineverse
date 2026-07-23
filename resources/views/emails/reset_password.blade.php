<!DOCTYPE html>
<html>
<head>
    <title>Password Reset</title>
</head>
<body style="font-family: Arial, sans-serif; text-align: center;">
    <div style="max-width: 430px; margin: 0 auto; border: 2px dashed #333; padding: 20px;">
        <h2>Password Reset</h2>
        <p>You requested a password reset. Click the button below to set a new password for your Cineverse account.</p>
        
        <br>
        
        <a href="{{ url('/reset-password/' . $token . '?email=' . urlencode($email)) }}" 
           style="background: #B22222; color: #fff; padding: 10px 20px; margin: 20px 0; text-decoration: none; border-radius: 10px; font-weight: bold; display: inline-block;">
            Reset Password
        </a>
        <br><br>
        
        <p style="font-size: 12px; color: #666; margin-top: 20px;">
            If you did not request a password reset, no further action is required.
        </p>
    </div>
</body>
</html>