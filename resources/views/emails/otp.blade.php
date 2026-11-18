<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SKM Saller OTP Verification</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #0b0f19;
            color: #e2e8f0;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 520px;
            margin: 0 auto;
            background: #111827;
            border: 1px solid #1e293b;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.4);
        }
        .header {
            background: #090d16;
            padding: 24px;
            text-align: center;
            border-bottom: 2px solid #dc2626;
        }
        .logo {
            font-size: 24px;
            font-weight: 800;
            letter-spacing: 2px;
            color: #ffffff;
            text-decoration: none;
        }
        .logo span {
            color: #dc2626;
        }
        .body {
            padding: 32px 24px;
        }
        h2 {
            font-size: 20px;
            font-weight: 700;
            margin-top: 0;
            margin-bottom: 12px;
            color: #ffffff;
        }
        p {
            font-size: 14px;
            line-height: 1.6;
            color: #94a3b8;
            margin-top: 0;
            margin-bottom: 18px;
        }
        .otp-box {
            background: #161f30;
            border: 2px dashed #dc2626;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin: 24px 0;
        }
        .otp-code {
            font-size: 36px;
            font-weight: 800;
            letter-spacing: 8px;
            color: #ffffff;
            font-family: monospace;
        }
        .expiry-note {
            font-size: 12px;
            color: #f87171;
            margin-top: 8px;
            margin-bottom: 0;
        }
        .footer {
            background: #090d16;
            padding: 18px 24px;
            text-align: center;
            font-size: 12px;
            color: #64748b;
            border-top: 1px solid #1e293b;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">SKM <span>SALLER</span></div>
            <div style="font-size: 11px; color: #94a3b8; letter-spacing: 1px; margin-top: 4px;">PREMIUM AUTOMOTIVE MONOGRAMS & EMBLEMS</div>
        </div>
        <div class="body">
            <h2>Hello {{ $recipientName }},</h2>
            @if($action === 'login')
                <p>You requested a one-time login code to access your SKM Saller account. Use the code below to complete your sign-in:</p>
            @else
                <p>Thank you for registering with SKM Saller. To complete your account registration and verify your email address, please use the verification code below:</p>
            @endif

            <div class="otp-box">
                <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 1.5px; color: #94a3b8; margin-bottom: 6px;">One-Time Password (OTP)</div>
                <div class="otp-code">{{ $otp }}</div>
                <p class="expiry-note">&#9200; Valid for {{ $expiryMinutes }} minutes only</p>
            </div>

            <p style="font-size: 13px; color: #cbd5e1;">
                <strong>Security Notice:</strong> Never share this code with anyone. SKM Saller representatives will never ask you for your OTP.
            </p>
            <p style="font-size: 12px; color: #64748b; margin-bottom: 0;">
                If you did not request this verification code, please ignore this email or contact support.
            </p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} SKM Saller. All rights reserved.
        </div>
    </div>
</body>
</html>
