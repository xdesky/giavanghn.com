<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; margin: 0; padding: 0; background-color: #f8fafc; }
        .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; }
        .header { background: linear-gradient(135deg, #8b5cf6 0%, #ec4899 100%); padding: 32px; text-align: center; }
        .header h1 { color: #ffffff; margin: 0; font-size: 24px; }
        .content { padding: 32px; }
        .stat-card { background-color: #faf5ff; border-left: 4px solid #8b5cf6; padding: 16px; margin: 16px 0; border-radius: 8px; }
        .stat-card h3 { margin: 0 0 8px 0; color: #581c87; font-size: 16px; }
        .stat-card .value { font-size: 24px; font-weight: bold; color: #6b21a8; }
        .button { display: inline-block; padding: 12px 32px; background: linear-gradient(135deg, #8b5cf6 0%, #ec4899 100%); color: #ffffff; text-decoration: none; border-radius: 8px; font-weight: 600; }
        .footer { padding: 24px; text-align: center; color: #94a3b8; font-size: 12px; border-top: 1px solid #e2e8f0; }
        .unsubscribe { color: #64748b; text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📈 Báo Cáo Tuần Giá Vàng</h1>
        </div>
        
        <div class="content">
            <p>Xin chào <strong>{{ $user->name }}</strong>,</p>
            
            <p>Đây là bản tổng hợp giá vàng trong tuần {{ now()->weekOfYear }}/{{ now()->year }}:</p>
            
            @if (isset($weeklyData['summary']))
                <div style="background-color: #f1f5f9; padding: 16px; border-radius: 8px; margin: 24px 0;">
                    <p style="margin: 0; color: #475569; line-height: 1.6;">{{ $weeklyData['summary'] }}</p>
                </div>
            @endif
            
            @if (isset($weeklyData['stats']))
                <h3 style="color: #1e293b; margin: 24px 0 16px 0;">📊 Các chỉ số quan trọng</h3>
                
                @foreach ($weeklyData['stats'] as $stat)
                    <div class="stat-card">
                        <h3>{{ $stat['label'] }}</h3>
                        <div class="value">{{ $stat['value'] }}</div>
                        @if (isset($stat['description']))
                            <p style="margin: 8px 0 0 0; color: #7c3aed; font-size: 14px;">{{ $stat['description'] }}</p>
                        @endif
                    </div>
                @endforeach
            @endif
            
            @if (isset($weeklyData['insights']))
                <h3 style="color: #1e293b; margin: 32px 0 16px 0;">💡 Nhận định tuần</h3>
                <ul style="color: #64748b; line-height: 1.8;">
                    @foreach ($weeklyData['insights'] as $insight)
                        <li>{{ $insight }}</li>
                    @endforeach
                </ul>
            @endif
            
            <div style="text-align: center; margin: 32px 0;">
                <a href="{{ url('/') }}" class="button">Xem Biểu Đồ & Phân Tích Chi Tiết →</a>
            </div>
            
            <p style="color: #64748b; font-size: 14px; padding: 16px; background-color: #faf5ff; border-radius: 8px;">
                ⏰ <strong>Báo cáo tiếp theo:</strong> Bạn sẽ nhận báo cáo tuần vào {{ now()->addWeek()->format('d/m/Y') }}
            </p>
        </div>
        
        <div class="footer">
            <p>Bạn nhận email này vì đã đăng ký báo cáo tuần từ <strong>Giá Vàng Hôm Nay</strong></p>
            <p><a href="{{ $unsubscribeUrl }}" class="unsubscribe">Hủy đăng ký báo cáo tuần</a></p>
        </div>
    </div>
</body>
</html>
