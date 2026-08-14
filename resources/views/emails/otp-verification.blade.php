<x-mail::layout>
<x-slot:header>
<x-mail::header :url="config('app.url')" />
</x-slot:header>

<div style="font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #374151; font-size: 15px; line-height: 1.6;">
<h1 style="margin: 0 0 18px; color: #2B2D7E; font-size: 24px; line-height: 1.25; font-weight: 700; letter-spacing: -0.2px;">
Verify your email address
</h1>

<p style="margin: 0 0 14px;">Hello,</p>

<p style="margin: 0 0 22px;">
Use the verification code below to confirm your email address for
<strong style="color: #2B2D7E; font-weight: 700;">Alcatt Portal</strong>.
</p>

<table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="margin: 0 0 22px; border: 1px solid #E5E7EB; border-radius: 12px; background-color: #F8FAFC;">
<tr>
<td align="center" style="padding: 24px 20px 26px;">
<div style="margin-bottom: 8px; color: #6B7280; font-size: 11px; line-height: 1.4; font-weight: 700; letter-spacing: 1.4px; text-transform: uppercase;">
Verification code
</div>
<div style="color: #2B2D7E; font-size: 34px; line-height: 1.2; font-weight: 800; letter-spacing: 8px;">
{{ $otp }}
</div>
</td>
</tr>
</table>

<p style="margin: 0 0 14px;">
This code expires in
<strong style="color: #2B2D7E; font-weight: 700;">{{ config('auth.verification_otp.expire', 10) }} minutes</strong>.
</p>

<p style="margin: 0 0 22px; color: #6B7280; font-size: 13px; line-height: 1.6;">
If you did not request this code, you can safely ignore this email or contact the system administrator.
</p>

<p style="margin: 0; padding-top: 16px; border-top: 1px solid #E5E7EB; color: #9CA3AF; font-size: 12px; line-height: 1.5;">
This is an automated message from the Alcatt Portal. Please do not reply to this email.
</p>
</div>

<x-slot:footer>
<x-mail::footer />
</x-slot:footer>
</x-mail::layout>
