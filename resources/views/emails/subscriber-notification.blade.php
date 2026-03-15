<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $emailContent }}</title>
    <style>
        body { margin: 0; padding: 0; background: #f4f4f5; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; color: #333; }
        .wrapper { max-width: 600px; margin: 0 auto; background: #fff; }
        .header { background: #001061; padding: 24px 32px; text-align: center; }
        .header img { height: 36px; }
        .header h1 { color: #fff; font-size: 14px; margin: 8px 0 0; font-weight: 400; opacity: .8; }
        .body { padding: 32px; }
        .body h2 { color: #001061; font-size: 20px; margin: 0 0 16px; }
        .content { font-size: 15px; line-height: 1.7; color: #475569; }
        .content p { margin: 0 0 12px; }
        .footer { padding: 24px 32px; background: #f8fafc; border-top: 1px solid #e2e8f0; text-align: center; font-size: 12px; color: #94a3b8; }
        .footer a { color: #64748b; text-decoration: underline; }
        .badge { display: inline-block; background: #ffc300; color: #001061; font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 3px; text-transform: uppercase; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <img src="{{ url('/images/logo.svg') }}" alt="GiaVangHN">
        <h1>Thông tin giá vàng</h1>
    </div>
    <div class="body">
        <p style="margin:0 0 8px;font-size:14px;color:#64748b;">Xin chào <strong style="color:#333;">{{ $subscriber->name ?? 'bạn' }}</strong>,</p>
        <div class="content">
            {!! $emailContent !!}
        </div>
    </div>
    <div class="footer">
        <p>Bạn nhận email này vì đã đăng ký nhận thông tin tại <a href="{{ url('/') }}">giavanghn.com</a></p>
        <p style="margin:8px 0 0;"><a href="{{ $unsubscribeUrl }}">Hủy đăng ký nhận thông tin</a></p>
    </div>
</div>
</body>
</html>
