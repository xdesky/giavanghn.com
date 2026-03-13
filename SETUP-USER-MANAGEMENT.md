# ==============================================
# CẤU HÌNH HỆ THỐNG QUẢN LÝ NGƯỜI DÙNG & THÔNG BÁO
# ==============================================

# OneSignal Push Notifications (https://onesignal.com/)
# Đăng ký tài khoản OneSignal và tạo app mới để lấy credentials
ONESIGNAL_APP_ID=your-onesignal-app-id
ONESIGNAL_API_KEY=your-onesignal-rest-api-key

# Google OAuth (https://console.cloud.google.com/)
# Tạo OAuth 2.0 Client ID trong Google Cloud Console
# Authorized redirect URIs: http://your-domain.com/auth/google/callback
GOOGLE_CLIENT_ID=your-google-client-id.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=your-google-client-secret

# Facebook OAuth (https://developers.facebook.com/)
# Tạo Facebook App và lấy App ID & App Secret
# Valid OAuth Redirect URIs: http://your-domain.com/auth/facebook/callback
FACEBOOK_CLIENT_ID=your-facebook-app-id
FACEBOOK_CLIENT_SECRET=your-facebook-app-secret

# Apple Sign In (https://developer.apple.com/)
# Cấu hình Sign in with Apple trong Apple Developer account
# Return URL: http://your-domain.com/auth/apple/callback
APPLE_CLIENT_ID=your.apple.service.id
APPLE_CLIENT_SECRET=your-apple-client-secret
APPLE_TEAM_ID=your-apple-team-id
APPLE_KEY_ID=your-apple-key-id
APPLE_PRIVATE_KEY=path/to/your/AuthKey_XXXXX.p8

# Email Configuration (để gửi thông báo email)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@giavang.vn
MAIL_FROM_NAME="Giá Vàng Hôm Nay"

# ==============================================
# HƯỚNG DẪN CÀI ĐẶT
# ==============================================

## 1. Cài đặt dependencies
composer install
npm install

## 2. Chạy migrations và seeders
php artisan migrate --seed
php artisan db:seed --class=RolesAndPermissionsSeeder

## 3. Tạo storage symlink
php artisan storage:link

## 4. Build assets
npm run build

## 5. Chạy queue worker (để xử lý email notifications)
php artisan queue:work

## 6. Đăng nhập admin
Email: admin@giavang.vn
Password: Admin@123456

# ==============================================
# TÍNH NĂNG CHÍNH
# ==============================================

1. Đăng nhập/Đăng ký:
   - Đăng ký bằng email/password
   - Đăng nhập nhanh bằng Google, Facebook, Apple

2. Phân quyền:
   - Admin: Toàn quyền quản lý hệ thống
   - Editor: Quản lý bài viết
   - User: Người dùng thường

3. Thông báo:
   - Email: Cảnh báo giá, báo cáo hàng ngày/tuần
   - Push Notification (OneSignal): Cảnh báo biến động giá

4. Quản trị:
   - Quản lý người dùng (CRUD, phân quyền, khóa/mở)
   - Quản lý bài viết phân tích
   - Thống kê dashboard

# ==============================================
# ROUTES CHÍNH
# ==============================================

Đăng ký/Đăng nhập:
  /register              - Đăng ký
  /login                 - Đăng nhập
  /logout                - Đăng xuất
  /auth/{provider}/redirect   - OAuth redirect (google, facebook, apple)
  /auth/{provider}/callback   - OAuth callback

User Dashboard:
  /dashboard             - Dashboard người dùng
  /dashboard/profile     - Hồ sơ cá nhân
  /dashboard/subscription - Cài đặt thông báo

Admin:
  /admin/dashboard       - Dashboard quản trị
  /admin/users           - Quản lý người dùng
  /admin/articles        - Quản lý bài viết

# ==============================================
# MIDDLEWARE
# ==============================================

auth                   - Yêu cầu đăng nhập
role:admin             - Chỉ admin
role:admin,editor      - Admin hoặc editor
permission:users.edit  - Kiểm tra permission cụ thể
