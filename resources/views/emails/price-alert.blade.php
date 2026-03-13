<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; margin: 0; padding: 0; background-color: #f8fafc; }
        .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; }
        .header { background: linear-gradient(135deg, #f59e0b 0%, #ef4444 100%); padding: 32px; text-align: center; }
        .header h1 { color: #ffffff; margin: 0; font-size: 24px; }
        .content { padding: 32px; }
        .alert-box { background-color: #fef3c7; border-left: 4px solid #f59e0b; padding: 16px; margin: 24px 0; border-radius: 8px; }
        .alert-box strong { color: #92400e; }
        .price-table { width: 100%; border-collapse: collapse; margin: 24px 0; }
        .price-table th, .price-table td { padding: 12px; text-align: left; border-bottom: 1px solid #e2e8f0; }
        .price-table th { background-color: #f1f5f9; font-weight: 600; color: #475569; }
        .price-up { color: #10b981; font-weight: bold; }
        .price-down { color: #ef4444; font-weight: bold; }
        .button { display: inline-block; padding: 12px 32px; background: linear-gradient(135deg, #f59e0b 0%, #ef4444 100%); color: #ffffff; text-decoration: none; border-radius: 8px; font-weight: 600; }
        .footer { padding: 24px; text-align: center; color: #94a3b8; font-size: 12px; border-top: 1px solid #e2e8f0; }
        .unsubscribe { color: #64748b; text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔔 Cảnh Báo Biến Động Giá Vàng</h1>
        </div>
        
        <div class="content">
            <p>Xin chào <strong>{{ $user->name }}</strong>,</p>
            
            <div class="alert-box">
                <strong>⚠️ Giá vàng có biến động đáng chú ý!</strong>
                <p style="margin: 8px 0 0 0; color: #78350f;">{{ $priceData['message'] ?? 'Kiểm tra chi tiết bên dưới.' }}</p>
            </div>
            
            @if (isset($priceData['brands']))
                <table class="price-table">
                    <thead>
                        <tr>
                            <th>Thương hiệu</th>
                            <th>Giá mua</th>
                            <th>Giá bán</th>
                            <th>Thay đổi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($priceData['brands'] as $brand)
                            <tr>
                                <td><strong>{{ $brand['name'] }}</strong></td>
                                <td>{{ number_format($brand['buy'], 0, ',', '.') }}đ</td>
                                <td>{{ number_format($brand['sell'], 0, ',', '.') }}đ</td>
                                <td class="{{ $brand['change'] >= 0 ? 'price-up' : 'price-down' }}">
                                    {{ sprintf('%+.2f%%', $brand['change']) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
            
            <p>Thời gian cập nhật: <strong>{{ now()->format('d/m/Y H:i:s') }}</strong></p>
            
            <div style="text-align: center; margin: 32px 0;">
                <a href="{{ url('/') }}" class="button">Xem Chi Tiết Trên Trang Chủ →</a>
            </div>
            
            <p style="color: #64748b; font-size: 14px; padding: 16px; background-color: #f8fafc; border-radius: 8px;">
                💡 <strong>Lưu ý:</strong> Đây là email cảnh báo tự động. Giá vàng có thể biến động nhanh chóng. 
                Hãy kiểm tra trực tiếp trên website để có thông tin chính xác nhất.
            </p>
        </div>
        
        <div class="footer">
            <p>Bạn nhận email này vì đã đăng ký nhận cảnh báo giá từ <strong>Giá Vàng Hôm Nay</strong></p>
            <p><a href="{{ $unsubscribeUrl }}" class="unsubscribe">Hủy đăng ký email cảnh báo</a></p>
        </div>
    </div>
</body>
</html>
