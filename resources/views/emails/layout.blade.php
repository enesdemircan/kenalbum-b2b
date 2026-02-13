<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('app.name'))</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #212121;
            max-width: 600px;
            margin: 0 auto;
            padding: 24px;
            background-color: #fafafa;
        }
        .email-wrapper {
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .email-header {
            background-color: #1976d2;
            color: white;
            padding: 24px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .email-header h1 {
            margin: 0 0 8px 0;
            font-size: 24px;
            font-weight: 500;
        }
        .email-header p {
            margin: 0;
            font-size: 14px;
            opacity: 0.9;
        }
        .email-content {
            padding: 24px;
            background-color: #ffffff;
        }
        .email-content h2 {
            margin: 0 0 16px 0;
            font-size: 20px;
            font-weight: 500;
            color: #212121;
        }
        .email-content h3 {
            margin: 24px 0 12px 0;
            font-size: 16px;
            font-weight: 500;
            color: #424242;
        }
        .email-content p {
            margin: 0 0 12px 0;
            color: #424242;
        }
        .email-card {
            background-color: #fafafa;
            border-radius: 8px;
            padding: 16px;
            margin: 16px 0;
            border-left: 4px solid #1976d2;
        }
        .email-warning {
            background-color: rgba(245, 124, 0, 0.1);
            border-left: 4px solid #f57c00;
            color: #e65100;
        }
        .email-success {
            background-color: rgba(56, 142, 60, 0.1);
            border-left: 4px solid #388e3c;
        }
        .email-info {
            background-color: rgba(2, 136, 209, 0.1);
            border-left: 4px solid #0288d1;
        }
        .email-danger {
            background-color: rgba(211, 47, 47, 0.1);
            border-left: 4px solid #d32f2f;
        }
        .btn-material {
            display: inline-block;
            padding: 12px 24px;
            background-color: #1976d2;
            color: white !important;
            text-decoration: none;
            border-radius: 4px;
            font-weight: 500;
            font-size: 14px;
            margin: 12px 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        .btn-material:hover {
            background-color: #0d47a1;
        }
        .btn-success { background-color: #388e3c !important; }
        .btn-warning { background-color: #f57c00 !important; }
        .btn-danger { background-color: #d32f2f !important; }
        .btn-info { background-color: #0288d1 !important; }
        .email-footer {
            margin-top: 24px;
            padding: 16px;
            background-color: #f5f5f5;
            border-radius: 0 0 8px 8px;
            font-size: 12px;
            color: #757575;
        }
        .email-footer p {
            margin: 0 0 4px 0;
        }
    </style>
</head>
<body>
    @php
        $siteSettings = $siteSettings ?? \App\Models\SiteSetting::first();
    @endphp
    <div class="email-wrapper">
        @hasSection('header')
            <div class="email-header" style="background-color: @yield('header_color', '#1976d2');">
                @if($siteSettings && $siteSettings->logo)
                    @php $logoUrl = str_starts_with($siteSettings->logo, 'http') ? $siteSettings->logo : asset($siteSettings->logo); @endphp
                    <img src="{{ $logoUrl }}" alt="{{ config('app.name') }}" style="max-width: 140px; height: auto; margin-bottom: 16px; display: block; margin-left: auto; margin-right: auto;">
                @endif
                @yield('header')
            </div>
        @endif
        <div class="email-content">
            @yield('content')
        </div>
        <div class="email-footer">
            @php $companyName = ($siteSettings && ($siteSettings->company_title ?? $siteSettings->title)) ? ($siteSettings->company_title ?? $siteSettings->title) : config('app.name'); @endphp
            <p>Bu e-posta {{ $companyName }} tarafından gönderilmiştir.</p>
            <p>© {{ date('Y') }} {{ $companyName }}</p>
        </div>
    </div>
</body>
</html>
