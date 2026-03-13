<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; margin: 0; padding: 0; background-color: #f8fafc; }
        .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; }
        .header { background: linear-gradient(135deg, #3b82f6 0%, #6366f1 100%); padding: 32px; text-align: center; }
        .header h1 { color: #ffffff; margin: 0; font-size: 24px; }
        .content { padding: 32px; }
        .article-title { font-size: 20px; font-weight: bold; color: #1e293b; margin: 0 0 16px 0; }
        .article-summary { color: #64748b; line-height: 1.6; margin: 0 0 24px 0; }
        .button { display: inline-block; padding: 12px 32px; background: linear-gradient(135deg, #3b82f6 0%, #6366f1 100%); color: #ffffff; text-decoration: none; border-radius: 8px; font-weight: 600; }
        .footer { padding: 24px; text-align: center; color: #94a3b8; font-size: 12px; border-top: 1px solid #e2e8f0; }
        .unsubscribe { color: #64748b; text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📊 Báo Cáo Giá Vàng Hôm Nay</h1>
        </div>
        
        <div class="content">
            <p>Xin chào <strong>{{ $user->name }}</strong>,</p>
            
            <h2 class="article-title">{{ $article->title }}</h2>
            
            @if ($article->summary)
                <p class="article-summary">{{ $article->summary }}</p>
            @endif
            
            <p>Bài phân tích chi tiết về giá vàng hôm nay đã có. Xem ngay để cập nhật thông tin mới nhất về thị trường vàng trong nước và thế giới.</p>
            
            <div style="text-align: center; margin: 32px 0;">
                <a href="{{ url('/') }}" class="button">Xem Báo Cáo Chi Tiết →</a>
            </div>
            
            <p style="color: #64748b; font-size: 14px;">
                <strong>Thông tin nổi bật:</strong><br>
                • Số từ: {{ number_format($article->word_count) }}<br>
                • Ngày phân tích: {{ \Carbon\Carbon::parse($article->analysis_date)->format('d/m/Y') }}<br>
                • Loại: {{ $article->trigger_type === 'daily' ? 'Báo cáo hàng ngày' : 'Cảnh báo biến động' }}
            </p>
        </div>
        
        <div class="footer">
            <p>Bạn nhận email này vì đã đăng ký nhận thông báo từ <strong>Giá Vàng Hôm Nay</strong></p>
            <p><a href="{{ $unsubscribeUrl }}" class="unsubscribe">Hủy đăng ký email này</a></p>
        </div>
    </div>
</body>
</html>
