@extends('emails.layout.master')

@section('content')
  <h1>{{ $heading }}</h1>

  <p class="highlight">Hi {{ $name }},</p>

  {!! $bodyContent !!}

  @if($showOfferBox)
  <div class="info-card" style="background:linear-gradient(135deg,#EEF2FF,#F5F3FF);border-left-color:#6366F1;">
    <h3>🎁 {{ $offerTitle }}</h3>
    <p>{{ $offerDescription }}</p>
    @if($couponCode)
    <p style="margin-top:10px;">
      Coupon Code:
      <span style="background:#6366F1;color:white;padding:4px 12px;border-radius:6px;font-weight:700;font-size:15px;letter-spacing:2px;">{{ $couponCode }}</span>
    </p>
    @endif
    @if($offerExpiry)
    <p style="font-size:12px;color:#94A3B8;margin-top:8px;">Offer ends: {{ $offerExpiry }}</p>
    @endif
  </div>
  @endif

  @if($ctaText && $ctaUrl)
  <div class="btn-wrapper">
    <a href="{{ $ctaUrl }}" class="btn-primary">{{ $ctaText }}</a>
  </div>
  @endif

  @if($disclaimer)
  <p style="font-size:12px;color:#94A3B8;text-align:center;">{{ $disclaimer }}</p>
  @endif
@endsection
