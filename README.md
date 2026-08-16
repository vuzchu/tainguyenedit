# ShareIt — Nền tảng chia sẻ tài nguyên editing

PHP + MySQL (PDO), giao diện đen trắng, dựa trên schema `if0_37413994_sharedit`.

## Tính năng

- Trang chủ + danh sách tài nguyên, lọc theo danh mục, tìm kiếm, phân trang.
- Trang chi tiết tài nguyên (ảnh, mô tả, tác giả, nút tải xuống, tài nguyên liên quan).
- Đăng ký / đăng nhập bằng tài khoản, và đăng nhập bằng Google (OAuth2).
- Yêu thích tài nguyên (AJAX).
- Trang đăng tài nguyên mới cho **staff/admin**, ảnh bìa upload qua ImgBB.
- Trang quản trị cho **admin**: quản lý người dùng (đổi quyền/xóa), quản lý danh mục, xem/xóa tất cả tài nguyên, xem góp ý.

## Cài đặt

1. Import `db/schema.sql` vào MySQL (tạo database trước, ví dụ `if0_37413994_sharedit`).
   - File này **không** chứa dữ liệu người dùng thật từ bản export gốc (email, mật khẩu) vì lý do bảo mật/riêng tư — chỉ giữ lại cấu trúc bảng và dữ liệu công khai (category, project).
   - Có sẵn 1 tài khoản admin: `admin` / `ChangeMe123!` — **đổi mật khẩu ngay sau khi đăng nhập lần đầu**.
2. Cấu hình secrets — **không** hardcode secrets trong file đã commit (GitHub push protection sẽ chặn). Chọn 1 trong 2 cách:
   - **Cách A (khuyên dùng cho InfinityFree):** copy `config/config.local.php.example` thành `config/config.local.php` (đã có trong `.gitignore`, sẽ không bao giờ bị commit) và điền giá trị thật: `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`, `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET`, `IMGBB_API_KEY`.
   - **Cách B:** set biến môi trường cùng tên trên hosting nếu hosting hỗ trợ (`DB_HOST`, `DB_PASS`, `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET`, `IMGBB_API_KEY`, ...).
3. Deploy code lên hosting hỗ trợ PHP 8+ với extension `pdo_mysql` và `curl` (InfinityFree đáp ứng yêu cầu này).

## Google OAuth Login

Lấy `GOOGLE_CLIENT_ID` / `GOOGLE_CLIENT_SECRET` từ Google Cloud Console và điền vào `config/config.local.php` (xem trên). Trong Google Cloud Console, thêm Authorized redirect URI:

```
https://<your-domain>/auth/google_callback.php
```

Có thể override qua biến môi trường/`config.local.php` `GOOGLE_REDIRECT_URI` nếu domain khác `SITE_URL` mặc định.

## ImgBB (ảnh bìa tài nguyên)

Điền `IMGBB_API_KEY` vào `config/config.local.php`. Key chỉ được dùng ở server-side (`includes/imgbb.php`), không bao giờ gửi ra trình duyệt.

## ⚠️ Lưu ý bảo mật

- **Không bao giờ** commit `config/config.local.php` hay bất kỳ file nào chứa secret thật — nó đã nằm trong `.gitignore`. GitHub push protection sẽ tự động chặn commit chứa Google Client Secret / API key trần.
- Nếu bạn có file export MySQL đầy đủ (kèm bảng `users` thật), **không commit** file đó lên Git — nó chứa email và một số mật khẩu dạng plaintext của người dùng thật.

## Cấu trúc thư mục

```
config/        cấu hình DB, Google OAuth, ImgBB
includes/      bootstrap, db, auth, functions, imgbb, header/footer
assets/        css, js, ảnh placeholder
auth/          đăng nhập, đăng ký, đăng xuất, Google OAuth
staff/         quản lý tài nguyên của staff/admin (đăng mới, sửa, xóa)
admin/         quản trị: người dùng, danh mục, tất cả tài nguyên, dashboard
db/schema.sql  cấu trúc DB + seed an toàn (roles, categories, projects)
index.php      trang chủ / danh sách
project.php    trang chi tiết
favorite.php   endpoint AJAX yêu thích
favorites.php  trang mục yêu thích của tôi
feedback.php   form gửi góp ý
```
