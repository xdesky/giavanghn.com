# GiaVangHN

Website Laravel cho chuyên trang giá vàng Việt Nam và thế giới, thiết kế theo hướng Figma-ready (dễ map component, token màu và typography).

## Stack

- Laravel 12
- Blade + Tailwind CSS v4 (qua Vite)
- PHP 8.2

## Chạy local

```bash
composer install
npm install
php artisan serve
npm run dev
```

Truy cập: `http://127.0.0.1:8000`

## Kiểm tra build/test

```bash
npm run build
php artisan test
```

## Cấu trúc chính

- `app/Http/Controllers/GoldPriceController.php`: dữ liệu mẫu và logic render trang chủ.
- `resources/views/gold/home.blade.php`: giao diện landing page giá vàng (VN + thế giới + highlights).
- `resources/css/app.css`: design tokens, style module, hiệu ứng chuyển động nhẹ.
- `routes/web.php`: route trang chủ `home`.

## Mapping sang Figma

Bạn có thể thay theo frame/component Figma theo từng khối:

- `Hero`: tiêu đề lớn, số liệu nhanh 3 thẻ (`metric-card`).
- `Bảng giá Việt Nam`: section `#gia-vang-vn`, table có cột mua/bán/biến động.
- `Thị trường thế giới`: section `#gia-vang-the-gioi`, card theo cặp tiền/tỷ giá.
- `Tin nóng`: section `#tin-nong`, danh sách tin dạng card nhỏ.

Điểm đồng bộ thiết kế nhanh:

- Font chính: `Be Vietnam Pro`.
- Font tiêu đề: `Cormorant Garamond`.
- Token nền kính: `--glass-bg`, `--glass-border` trong `resources/css/app.css`.
- Hiệu ứng vào trang: `.animate-rise`, `.animate-rise-delayed`.

## Mở rộng tiếp theo

- Gắn API giá vàng realtime vào `GoldPriceController` hoặc service riêng.
- Lưu dữ liệu lịch sử vào database để vẽ chart thật.
- Tạo trang chi tiết theo từng thương hiệu vàng (SJC, DOJI, PNJ...).
