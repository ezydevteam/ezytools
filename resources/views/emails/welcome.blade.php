@extends('emails.layout.master')

@section('content')
  <h1>Welcome to {{ setting('site_name', 'EzyTools') }}! 🎉</h1>

  <p class="highlight">Hi {{ $name }},</p>

  <p>Your account has been created successfully. You can now access all free tools on {{ setting('site_name', 'EzyTools') }}.</p>

  <div class="feature-list">
    <div class="feature-item">
      <div class="feature-icon">📝</div>
      <div class="feature-text"><strong>60+ Free Tools</strong> — Text, Calculator, PDF, AI & more</div>
    </div>
    <div class="feature-item">
      <div class="feature-icon">🤖</div>
      <div class="feature-text"><strong>AI Tools</strong> — Writing, Translation, Summarization</div>
    </div>
    <div class="feature-item">
      <div class="feature-icon">📄</div>
      <div class="feature-text"><strong>PDF Tools</strong> — Merge, Compress, Password Protect</div>
    </div>
    <div class="feature-item">
      <div class="feature-icon">⚡</div>
      <div class="feature-text"><strong>Fast & Secure</strong> — All tools work right in your browser</div>
    </div>
  </div>

  <div class="btn-wrapper">
    <a href="{{ $dashboardUrl }}" class="btn-primary">Start Using Tools →</a>
  </div>

  <hr class="divider">

  <div class="info-card" style="background:#EEF2FF;border-left-color:#6366F1;">
    <h3>💎 Upgrade to Pro</h3>
    <p>Unlimited AI requests, ad-free experience, all PDF features, and much more. <a href="{{ $proUrl }}" style="color:#6366F1;font-weight:600;">Learn more →</a></p>
  </div>

  <p style="font-size:13px;color:#64748B;">Need help? Contact us at <a href="mailto:support@ezytools.app">support@ezytools.app</a></p>
@endsection
