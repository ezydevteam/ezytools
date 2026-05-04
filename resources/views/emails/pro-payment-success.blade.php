@extends('emails.layout.master')

@section('content')
  <h1>Payment Successful! 🎊</h1>

  <p class="highlight">Hi {{ $name }},</p>

  <p>Your {{ setting('site_name', 'EzyTools') }} <span class="badge badge-pro">✨ Pro</span> subscription is now active. Thank you for upgrading!</p>

  <div class="info-card success">
    <h3>✅ Payment Receipt</h3>
  </div>

  <table class="receipt-table">
    <tr><td>Plan</td><td>{{ setting('site_name', 'EzyTools') }} Pro — {{ $planLabel }}</td></tr>
    <tr><td>Transaction ID</td><td>{{ $transactionId }}</td></tr>
    <tr><td>Payment Method</td><td>{{ $paymentMethod }}</td></tr>
    <tr><td>Payment Date</td><td>{{ $paidAt }}</td></tr>
    <tr><td>Valid Until</td><td>{{ $expiresAt }}</td></tr>
    <tr class="total"><td>Total Amount</td><td>৳{{ number_format($amount) }}</td></tr>
  </table>

  <div class="feature-list">
    <div class="feature-item"><div class="feature-icon">♾️</div><div class="feature-text"><strong>Unlimited AI requests</strong> — Access all AI tools</div></div>
    <div class="feature-item"><div class="feature-icon">🚫</div><div class="feature-text"><strong>Ad-free experience</strong> — No advertisements</div></div>
    <div class="feature-item"><div class="feature-icon">📄</div><div class="feature-text"><strong>All PDF features</strong> — Up to 50MB</div></div>
    <div class="feature-item"><div class="feature-icon">⚡</div><div class="feature-text"><strong>Better AI models</strong> — GPT-4o, Gemini Pro</div></div>
  </div>

  <div class="btn-wrapper">
    <a href="{{ $dashboardUrl }}" class="btn-success">Go to Pro Dashboard →</a>
  </div>

  <p style="font-size:13px;color:#94A3B8;">Please save this receipt for your records. For any issues: <a href="mailto:support@ezytools.app">support@ezytools.app</a></p>
@endsection
