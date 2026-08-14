<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alcatt Portal — Verification Code</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e0e0e0; border-radius: 8px; }
        .header { background: #4a90e2; color: white; padding: 20px; border-radius: 8px 8px 0 0; text-align: center; }
        .content { padding: 20px; background: #f9f9f9; }
        .otp-box { background: white; padding: 30px; text-align: center; margin: 20px 0; border-radius: 5px; font-size: 24px; font-weight: bold; color: #4a90e2; letter-spacing: 4px; }
        .footer { font-size: 12px; color: #666; text-align: center; padding: 20px; }
        .note { font-size: 14px; color: #666; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Alcatt Portal</h2>
        </div>
        <div class="content">
            <p>Hello,</p>
            <p>Your 6-digit verification code for Alcatt Portal is:</p>
            
            <div class="otp-box">{{ $otp }}</div>
            
            <p>This code will expire in 10 minutes.</p>
            <p>If you didn't request this code, please ignore this email or contact support.</p>
            
            <div class="note">
                This is an automated message from Alcatt Portal. Please do not reply to this email.
            </div>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Alcatt Portal. All rights reserved.
        </div>
    </div>
</body>
</html>