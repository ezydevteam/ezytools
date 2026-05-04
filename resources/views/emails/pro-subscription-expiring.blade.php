@extends('emails.layout.master')

@section('content')
  <h1>Your Pro Subscription is Expiring Soon ⏰</h1>

  <p class="highlight">Hi {{ $name }},</p>

  <p>Your {{ setting('site_name', 'EzyTools') }} <span class="badge badge-pro">✨ Pro</span> subscription has only <strong style="color:#F59E0B;">{{ $daysLeft }} days</strong> remaining.</p>

  <div class="warning-banner">
    <div class="icon">⚠️</div>
    <p>Your Pro benefits will end on <strong>{{ $expiresAt }}</strong>. Renew now to enjoy uninterrupted service.</p>
  </div>

  @if($hasOffer)
  <div class="info-card success">
    <h3>🎁 Special Offer Just for You!</h3>
    <p>Renew now and get <strong>{{ $offerDiscount }}% off</strong>. Offer valid until <strong>{{ $offerExpiry }}</strong>.</p>
  </div>
  @endif

  <div class="btn-wrapper">
    <a href="{{ $renewUrl }}" class="btn-primary">Renew Now →</a>
  </div>

  <div class="feature-list">
    <div class="feature-item"><div class="feature-icon">♾️</div><div class="feature-text">Unlimited AI — Writing, Translation, Summarization</div></div>
    <div class="feature-item"><div class="feature-icon">🚫</div><div class="feature-text">Completely ad-free experience</div></div>
    <div class="feature-item"><div class="feature-icon">📄</div><div class="feature-text">All PDF features — Up to 50MB</div></div>
  </div>

  <p style="font-size:13px;color:#94A3B8;">Have questions? <a href="mailto:support@ezytools.app">support@ezytools.app</a></p>
@endsection
