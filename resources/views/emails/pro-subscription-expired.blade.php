@extends('emails.layout.master')

@section('content')
  <h1>Your Pro Subscription Has Expired 😔</h1>

  <p class="highlight">Hi {{ $name }},</p>

  <p>Your {{ setting('site_name', 'EzyTools') }} Pro subscription expired on <strong>{{ $expiredAt }}</strong>. Your account is now on the Free plan.</p>

  <div class="info-card danger">
    <h3>❌ What You're Missing</h3>
    <p>AI tools limited (3/day) · Ads displayed · PDF limit 10MB · Basic models only</p>
  </div>

  <div class="info-card success">
    <h3>🎁 Come Back — Special Discount!</h3>
    <p>Resubscribe today and get <strong>{{ $returnDiscount }}% off</strong>. Code: <strong style="color:#6366F1;font-size:16px;">{{ $couponCode }}</strong></p>
  </div>

  <div class="btn-wrapper">
    <a href="{{ $renewUrl }}" class="btn-primary">Get Pro Again — With Discount →</a>
  </div>

  <table class="receipt-table">
    <tr><td>Monthly Plan</td><td>৳{{ $monthlyPrice }}/month</td></tr>
    <tr><td>Yearly Plan</td><td>৳{{ $yearlyPrice }}/year <span style="color:#10B981;font-size:12px;">(2 months free)</span></td></tr>
  </table>

  <p style="font-size:13px;color:#94A3B8;text-align:center;">Your data is safe with us. Everything will be restored when you resubscribe.</p>
@endsection
