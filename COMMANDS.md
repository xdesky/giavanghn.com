# Danh Sách Lệnh Artisan - GiaVangHN

## Lệnh tự động (Schedule)

| Lệnh | Lịch chạy | Mô tả |
|-------|-----------|-------|
| `crawl:run` | Mỗi 15 phút | Kiểm tra dữ liệu thiếu → crawl tất cả nguồn → đồng bộ lịch sử giá |
| `generate:analysis-article --trigger=auto` | Mỗi 15 phút | Tự động tạo bài phân tích giá vàng (daily + biến động giá) |
| `generate:analysis-article --trigger=summary` | 20:30 hàng ngày | Tạo bài tổng hợp giá vàng cuối ngày "Giá Vàng Ngày dd/mm/yyyy" |
| `sync:sjc-chart-prices` | Mỗi 15 phút | Đồng bộ giá SJC hôm nay từ gold_prices (cập nhật khi giá thay đổi) |

---

## Chi tiết các lệnh

### 1. `crawl:run` — Tổ hợp crawl chính

```bash
php artisan crawl:run
php artisan crawl:run --source=sjc
php artisan crawl:run --threshold=30
php artisan crawl:run --skip-sync
php artisan crawl:run --dry-run
```

| Option | Mặc định | Mô tả |
|--------|----------|-------|
| `--source` | `all` | Nguồn cần crawl: `all`, `sjc`, `doji`, `pnj`, `btmc`, `phuquy`, `mihong`, `baotinmanhhai`, `ngoctham`, `world`, `exchange`, `news`, `dailystats`, `sentiment`, `silver`, `bankrate` |
| `--threshold` | `20` | Số phút gap để kích hoạt recovery |
| `--skip-sync` | — | Bỏ qua đồng bộ price_histories sau crawl |
| `--dry-run` | — | Chỉ kiểm tra trạng thái, không crawl |

Quy trình: **Kiểm tra data gaps → Recovery nguồn thiếu → Crawl mới → Sync price_histories**

---

### 2. `generate:analysis-article` — Tạo bài viết phân tích

```bash
php artisan generate:analysis-article --trigger=auto
php artisan generate:analysis-article --trigger=daily
php artisan generate:analysis-article --trigger=change
php artisan generate:analysis-article --trigger=summary
php artisan generate:analysis-article --trigger=summary --force
```

| Option | Mặc định | Mô tả |
|--------|----------|-------|
| `--trigger` | `auto` | Loại bài: `auto` (daily + change), `daily` (phân tích trong ngày), `change` (biến động giá), `summary` (tổng hợp cuối ngày) |
| `--force` | — | Bỏ qua kiểm tra trùng lặp, luôn tạo bài mới |

Tiêu đề theo loại:
- **daily**: "Bản Tin Phân Tích Giá Vàng Trong Ngày dd/mm/yyyy - SJC xxx triệu/lượng tăng/giảm (±x.xx%)"
- **change**: "Cập Nhật Nhanh Biến Động Giá Vàng dd/mm/yyyy - SJC xxx triệu/lượng tăng/giảm (±x.xx%)"
- **summary**: "Giá Vàng Ngày dd/mm/yyyy"

---

### 3. `crawl:gold` — Crawl đơn lẻ

```bash
php artisan crawl:gold
php artisan crawl:gold --source=sjc
php artisan crawl:gold --source=news --verbose-log
```

| Option | Mặc định | Mô tả |
|--------|----------|-------|
| `--source` | `all` | Nguồn: `all`, `sjc`, `doji`, `pnj`, `btmc`, `phuquy`, `mihong`, `baotinmanhhai`, `ngoctham`, `world`, `exchange`, `news`, `dailystats`, `sentiment` |
| `--verbose-log` | — | Hiển thị log chi tiết |

---

### 4. `crawl:recover` — Khôi phục crawl bị lỗi

```bash
php artisan crawl:recover
php artisan crawl:recover --threshold=30
php artisan crawl:recover --dry-run
```

| Option | Mặc định | Mô tả |
|--------|----------|-------|
| `--threshold` | `20` | Số phút kể từ lần crawl thành công cuối để coi là missed |
| `--dry-run` | — | Chỉ báo cáo, không chạy lại |

---

### 5. `sync:price-histories` — Đồng bộ lịch sử giá

```bash
php artisan sync:price-histories
php artisan sync:price-histories --hours=48
```

| Option | Mặc định | Mô tả |
|--------|----------|-------|
| `--hours` | `24` | Số giờ cần đồng bộ ngược lại |

Đồng bộ dữ liệu từ các bảng giá → `price_histories` (nến 1 giờ).

---

### 6. `sync:sjc-chart-prices` — Đồng bộ biểu đồ SJC

```bash
php artisan sync:sjc-chart-prices --webgia
php artisan sync:sjc-chart-prices --backfill --days=400
php artisan sync:sjc-chart-prices --date=2026-03-12
```

| Option | Mặc định | Mô tả |
|--------|----------|-------|
| `--date` | — | Ngày cụ thể (Y-m-d) |
| `--backfill` | — | Backfill từ gold_prices lịch sử |
| `--days` | `400` | Số ngày backfill |
| `--webgia` | — | Lấy dữ liệu từ webgia.com |

---

### 7. `import:sjc-chart-highcharts` — Import dữ liệu Highcharts

```bash
php artisan import:sjc-chart-highcharts
php artisan import:sjc-chart-highcharts --file=sjc_highcharts_1y.js
```

| Option | Mặc định | Mô tả |
|--------|----------|-------|
| `--file` | `sjc_highcharts_1y.js` | File chứa dữ liệu Highcharts seriesOptions trong `storage/app` |

---

## Xem lịch chạy

```bash
php artisan schedule:list
```
