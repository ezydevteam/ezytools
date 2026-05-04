@extends('emails.layout.master')

@section('content')
  <h1>Subscription Cancelled 😢</h1>

  <p class="highlight">Hi {{ $name }},</p>

  <p>Your {{ setting('site_name', 'EzyTools') }} Pro subscription has been cancelled. Don't worry — you can continue to enjoy Pro features until <strong>{{ $expiresAt }}</strong>.</p>

  <div class="info-card warning">
    <h3>📅 Important Information</h3>
    <p>After {{ $expiresAt }}, your account will automatically revert to the Free plan. Your data and history will remain fully preserved.</p>
  </div>

  <p style="font-weight:600;color:#0F172A;margin-bottom:8px;">Pro features you'll lose:</p>
  <div class="feature-list">
    <div class="feature-item"><div class="feature-icon" style="background:#FEF2F2;">❌</div><div class="feature-text" style="color:#94A3B8;">Unlimited AI requests will be disabled</div></div>
    <div class="feature-item"><div class="feature-icon" style="background:#FEF2F2;">❌</div><div class="feature-text" style="color:#94A3B8;">Ads will be displayed again</div></div>
    <div class="feature-item"><div class="feature-icon" style="background:#FEF2F2;">❌</div><div class="feature-text" style="color:#94A3B8;">PDF file limit reduced to 10MB</div></div>
  </div>

  <div class="btn-wrapper">
    <a href="{{ $renewUrl }}" class="btn-primary">Resubscribe Now</a>
    <br>
    <a href="{{ $feedbackUrl }}" class="btn-secondary" style="margin-top:10px;">Tell us why you cancelled →</a>
  </div>

  <p style="font-size:13px;color:#94A3B8;">Cancelled by mistake? Need help? <a href="mailto:support@ezytools.app">support@ezytools.app</a></p>
@endsection
