<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>{{ $subject ?? setting('site_name', 'EzyTools') }}</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
      font-family: 'Segoe UI', Arial, sans-serif;
      background-color: #F1F5F9;
      color: #1E293B;
      -webkit-font-smoothing: antialiased;
    }
    a { color: #6366F1; text-decoration: none; }
    img { border: 0; display: block; }

    .email-wrapper { width: 100%; background-color: #F1F5F9; padding: 40px 16px; }
    .email-container {
      max-width: 580px; margin: 0 auto; background: #FFFFFF;
      border-radius: 16px; overflow: hidden;
      box-shadow: 0 4px 24px rgba(0,0,0,0.08);
    }

    .email-header {
      background: linear-gradient(135deg, #6366F1 0%, #8B5CF6 100%);
      padding: 32px 40px; text-align: center;
    }
    .email-header .logo { font-size: 24px; font-weight: 700; color: #FFFFFF; letter-spacing: -0.5px; }
    .email-header .logo span { color: #C7D2FE; }
    .email-header .tagline { font-size: 12px; color: #C7D2FE; margin-top: 4px; }

    .email-body { padding: 40px 40px 32px; }
    .email-body h1 { font-size: 22px; font-weight: 700; color: #0F172A; margin-bottom: 12px; line-height: 1.3; }
    .email-body p { font-size: 15px; color: #475569; line-height: 1.7; margin-bottom: 16px; }
    .email-body p.highlight { color: #1E293B; font-weight: 500; }

    .otp-box {
      background: #F8FAFC; border: 2px dashed #C7D2FE; border-radius: 12px;
      padding: 24px; text-align: center; margin: 24px 0;
    }
    .otp-box .otp-label { font-size: 12px; color: #94A3B8; text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 8px; }
    .otp-box .otp-code { font-size: 40px; font-weight: 800; color: #6366F1; letter-spacing: 10px; font-family: 'Courier New', monospace; }
    .otp-box .otp-expiry { font-size: 12px; color: #94A3B8; margin-top: 10px; }

    .btn-primary {
      display: inline-block; background: linear-gradient(135deg, #6366F1, #8B5CF6);
      color: #FFFFFF !important; font-size: 15px; font-weight: 600;
      padding: 14px 32px; border-radius: 10px; text-decoration: none; margin: 8px 0; text-align: center;
    }
    .btn-secondary {
      display: inline-block; background: #F1F5F9; color: #475569 !important;
      font-size: 14px; font-weight: 500; padding: 12px 28px; border-radius: 10px;
      text-decoration: none; border: 1px solid #E2E8F0;
    }
    .btn-danger {
      display: inline-block; background: linear-gradient(135deg, #EF4444, #DC2626);
      color: #FFFFFF !important; font-size: 15px; font-weight: 600;
      padding: 14px 32px; border-radius: 10px; text-decoration: none;
    }
    .btn-success {
      display: inline-block; background: linear-gradient(135deg, #10B981, #059669);
      color: #FFFFFF !important; font-size: 15px; font-weight: 600;
      padding: 14px 32px; border-radius: 10px; text-decoration: none;
    }
    .btn-wrapper { text-align: center; margin: 28px 0; }

    .info-card {
      background: #F8FAFC; border-radius: 12px; padding: 20px 24px;
      margin: 20px 0; border-left: 4px solid #6366F1;
    }
    .info-card.success { border-left-color: #10B981; background: #F0FDF4; }
    .info-card.warning { border-left-color: #F59E0B; background: #FFFBEB; }
    .info-card.danger  { border-left-color: #EF4444; background: #FEF2F2; }
    .info-card h3 { font-size: 13px; font-weight: 600; color: #0F172A; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px; }
    .info-card p { font-size: 14px; color: #475569; margin: 0; line-height: 1.6; }

    .feature-list { margin: 20px 0; }
    .feature-item { display: flex; align-items: flex-start; gap: 12px; padding: 10px 0; border-bottom: 1px solid #F1F5F9; }
    .feature-item:last-child { border-bottom: none; }
    .feature-icon { width: 28px; height: 28px; background: #EEF2FF; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0; }
    .feature-text { font-size: 14px; color: #334155; line-height: 1.5; padding-top: 4px; }

    .receipt-table { width: 100%; border-collapse: collapse; margin: 20px 0; font-size: 14px; }
    .receipt-table td { padding: 12px 0; border-bottom: 1px solid #F1F5F9; color: #475569; }
    .receipt-table td:last-child { text-align: right; font-weight: 600; color: #0F172A; }
    .receipt-table tr.total td { border-bottom: none; border-top: 2px solid #E2E8F0; padding-top: 16px; color: #0F172A; font-weight: 700; font-size: 16px; }

    .badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
    .badge-pro { background: linear-gradient(135deg, #6366F1, #8B5CF6); color: white; }
    .badge-free { background: #F1F5F9; color: #64748B; }
    .badge-expired { background: #FEF2F2; color: #EF4444; }

    .warning-banner {
      background: linear-gradient(135deg, #FEF3C7, #FDE68A); border-radius: 10px;
      padding: 16px 20px; margin: 20px 0; text-align: center;
    }
    .warning-banner .icon { font-size: 28px; margin-bottom: 6px; }
    .warning-banner p { font-size: 14px; color: #92400E; font-weight: 500; margin: 0; }

    .divider { border: none; border-top: 1px solid #F1F5F9; margin: 24px 0; }

    .email-footer { background: #F8FAFC; padding: 24px 40px; text-align: center; border-top: 1px solid #F1F5F9; }
    .email-footer p { font-size: 12px; color: #94A3B8; line-height: 1.6; margin-bottom: 8px; }
    .email-footer a { color: #94A3B8; text-decoration: underline; font-size: 12px; }
    .social-links { margin: 12px 0; }
    .social-links a { display: inline-block; margin: 0 6px; font-size: 12px; color: #6366F1; text-decoration: none; }
    .unsubscribe { font-size: 11px; color: #CBD5E1; margin-top: 8px; }

    @media (max-width: 600px) {
      .email-body { padding: 28px 24px 24px; }
      .email-header { padding: 24px 24px; }
      .email-footer { padding: 20px 24px; }
      .otp-box .otp-code { font-size: 32px; letter-spacing: 6px; }
      .email-body h1 { font-size: 20px; }
    }
  </style>
</head>
<body>
<div class="email-wrapper">
  <div class="email-container">

    <div class="email-header">
      @php
        $siteName = setting('site_name', 'EzyTools');
        $siteTagline = setting('site_tagline', 'All tools in one place');
        $siteFavicon = setting('site_favicon');
      @endphp
      @if($siteFavicon)
        <img src="{{ asset('storage/' . $siteFavicon) }}" alt="{{ $siteName }}" style="height:36px;margin:0 auto 8px;">
      @endif
      <div class="logo">{{ $siteName }}</div>
      <div class="tagline">{{ $siteTagline }}</div>
    </div>

    <div class="email-body">
      @yield('content')
    </div>

    <div class="email-footer">
      <div class="social-links">
        @if(setting('social_facebook'))
          <a href="{{ setting('social_facebook') }}">Facebook</a>
        @endif
        @if(setting('social_youtube'))
          <a href="{{ setting('social_youtube') }}">YouTube</a>
        @endif
        @if(setting('social_twitter'))
          <a href="{{ setting('social_twitter') }}">Twitter</a>
        @endif
        @if(setting('social_instagram'))
          <a href="{{ setting('social_instagram') }}">Instagram</a>
        @endif
        @if(setting('social_linkedin'))
          <a href="{{ setting('social_linkedin') }}">LinkedIn</a>
        @endif
      </div>
      <p>
        {{ $siteName }} — {{ $siteTagline }}<br>
        <a href="{{ config('app.url') }}">{{ str_replace(['https://', 'http://'], '', config('app.url')) }}</a>
      </p>
      <p class="unsubscribe">
        You received this email because you have an account on {{ $siteName }}.<br>
        <a href="{{ $unsubscribeUrl ?? '#' }}">Unsubscribe</a> ·
        <a href="{{ config('app.url') }}/privacy">Privacy Policy</a>
      </p>
    </div>

  </div>

  <p style="text-align:center;font-size:11px;color:#94A3B8;margin-top:16px;">
    © {{ date('Y') }} {{ $siteName }}. All rights reserved.
  </p>
</div>
</body>
</html>
