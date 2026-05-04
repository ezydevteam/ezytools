@extends('emails.layout.master')

@section('content')
  <h1>Reset Your Password 🔑</h1>

  <p class="highlight">Hello {{ $name }},</p>

  <p>We received a request to reset your {{ setting('site_name', 'EzyTools') }} account password. Use the OTP code below to set a new password.</p>

  <div class="otp-box">
    <div class="otp-label">Password Reset Code</div>
    <div class="otp-code">{{ $otp }}</div>
    <div class="otp-expiry">⏰ Valid for <strong>{{ $expiryMinutes }} minutes</strong> only</div>
  </div>

  <div class="btn-wrapper">
    <a href="{{ $resetUrl }}" class="btn-primary">Set New Password</a>
  </div>

  <div class="info-card danger">
    <h3>🚨 Wasn't You?</h3>
    <p>If you did not request a password reset, please contact us immediately at <a href="mailto:support@ezytools.app">support@ezytools.app</a>.</p>
  </div>

  <p style="font-size:13px;color:#94A3B8;margin-top:16px;">This code will expire in {{ $expiryMinutes }} minutes. Never share your OTP with anyone.</p>
@endsection
