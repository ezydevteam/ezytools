@extends('emails.layout.master')

@section('content')
  <h1>Verify Your Account ✅</h1>

  <p class="highlight">Hello {{ $name }},</p>

  <p>Welcome to {{ setting('site_name', 'EzyTools') }}! Please use the OTP code below to complete your registration.</p>

  <div class="otp-box">
    <div class="otp-label">Your Verification Code</div>
    <div class="otp-code">{{ $otp }}</div>
    <div class="otp-expiry">⏰ This code is valid for <strong>{{ $expiryMinutes }} minutes</strong></div>
  </div>

  <div class="info-card warning">
    <h3>⚠️ Security Notice</h3>
    <p>Do not share this OTP with anyone. {{ setting('site_name', 'EzyTools') }} will never ask for your OTP via phone or message.</p>
  </div>

  <p style="font-size:13px;color:#94A3B8;">If you did not initiate this registration, please ignore this email.</p>
@endsection
