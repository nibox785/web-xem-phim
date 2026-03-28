# 🎬 NiBoXMoVie - Nền Tảng Xem Phim Marvel & DC

## 📋 Tổng Quan Dự Án

**NiBoXMoVie** là một nền tảng xem phim full-stack PHP/MySQL được thiết kế để duyệt và xem các bộ phim Marvel & DC. Ứng dụng này cung cấp giao diện hiện đại, thân thiện người dùng cho người dùng thường xuyên khám phá phim và bảng điều khiển quản trị trực quan để quản lý nội dung.

### 🎯 Mục Tiêu Dự Án
- Cung cấp nền tảng tập trung cho nội dung phim Marvel & DC
- Cho phép người dùng duyệt, tìm kiếm và xem phim
- Quản lý thư viện phim thông qua bảng điều khiển quản trị
- Theo dõi bình luận và tương tác người dùng
- Hỗ trợ kiểm soát truy cập dựa trên vai trò (Người dùng vs Admin)

---

## 🛠️ Công Nghệ Sử Dụng

### Backend
- **Ngôn Ngữ**: PHP 8.2.12
- **Máy Chủ**: Apache/PHP-FPM
- **Cơ Sở Dữ Liệu**: MariaDB 10.4.32 (Tương thích MySQL)
- **Quản Lý Session**: PHP Native Sessions + Xác thực Token
- **Bảo Mật**: Token CSRF, Mã Hóa Mật Khẩu (bcrypt), SQL Prepared Statements

### Frontend
- **Markup**: HTML5
- **Styling**: CSS3 (Custom + Thiết Kế Responsive)
- **Tương Tác**: Vanilla JavaScript (ES6+)
- **Font**: Google Fonts (Roboto, Sen)
- **Biểu Tượng**: Font Awesome 5
- **Thiết Kế Responsive**: Phương Pháp Mobile-First

### Công Cụ Phát Triển
- **Trình Quản Lý Gói**: npm (cài đặt tối thiểu trong package.json)
- **Công Cụ Cơ Sở Dữ Liệu**: phpMyAdmin 5.2.1
- **Kiểm Soát Phiên Bản**: Cấu trúc sẵn sàng cho Git

---

## 📊 Tính Năng Chính

### 👤 Tính Năng Người Dùng
✅ **Hệ Thống Xác Thực**
- Đăng ký người dùng với xác thực email
- Đăng nhập bảo mật với chức năng ghi nhớ
- Chức năng đặt lại mật khẩu
- Đăng nhập tự động dựa trên token từ cookie

✅ **Duyệt Nội Dung**
- Trang chủ với thanh trượt phim nổi bật
- Duyệt theo vũ trụ (Marvel, DC, Khác)
- Lọc dựa trên thể loại
- Chức năng tìm kiếm toàn văn bản
- Chi tiết phim với mô tả

✅ **Tương Tác**
- Trang xem phim với trình phát video
- Hệ thống bình luận với quy trình phê duyệt
- Quản lý hồ sơ người dùng
- Hệ thống đánh giá

✅ **Trang Thông Tin**
- Trang Giới Thiệu
- Điều Khoản Sử Dụng
- Chính Sách Riêng Tư
- Trang Liên Hệ

### 🔐 Tính Năng Admin
✅ **Bảng Điều Khiển**
- Thống kê (Số phim, Số người dùng, Số thể loại)
- Danh sách phim gần đây
- Truy cập nhanh vào các phần quản lý

✅ **Quản Lý Phim**
- Thêm phim mới với siêu dữ liệu
- Chỉnh sửa phim hiện có
- Xóa phim (với xóa liên tầng)
- Gán thể loại và vũ trụ
- Đặt trạng thái nổi bật/xu hướng

✅ **Quản Lý Người Dùng**
- Xem tất cả người dùng
- Quản lý tài khoản người dùng
- Theo dõi tương tác người dùng

✅ **Quản Lý Thể Loại**
- Tạo thể loại mới
- Chỉnh sửa thể loại hiện có
- Xóa thể loại không sử dụng

---

## 📁 Cấu Trúc Dự Án

```
web_project/
├── index.php                      # Điểm vào chính & bộ định tuyến
├── package.json                   # Siêu dữ liệu dự án
├── marvel_dc_movies.sql          # Dump cơ sở dữ liệu
│
├── include/                       # Logic ứng dụng cốt lõi
│   ├── config.php                # Cấu hình & hằng số
│   ├── db.php                    # Kết nối cơ sở dữ liệu
│   ├── functions.php             # Hàm tiện ích
│   ├── header.php                # Thành phần header
│   ├── footer.php                # Thành phần footer
│   ├── slider.php                # Thanh trượt trang chủ
│   ├── marvel.php                # Danh sách phim Marvel
│   ├── dcu.php                   # Danh sách phim DC Universe
│   ├── other.php                 # Danh sách phim khác
│   ├── featured.php              # Phim nổi bật
│   ├── genres.php                # Lọc theo thể loại
│   └── search.php                # Chức năng tìm kiếm
│
├── admin/                        # Bảng điều khiển admin
│   ├── dashboard.php             # Bảng điều khiển admin
│   ├── add_movie.php             # Biểu mẫu thêm phim
│   ├── edit_movie.php            # Biểu mẫu chỉnh sửa phim
│   ├── movies.php                # Quản lý phim
│   ├── users.php                 # Quản lý người dùng
│   ├── genres.php                # Quản lý thể loại
│   ├── check_admin.php           # Kiểm tra xác thực admin
│   ├── admin_header.php          # Header admin
│   └── admin_footer.php          # Footer admin
│
├── auth/                         # Trang xác thực
│   ├── login.php                 # Đăng nhập người dùng
│   ├── register.php              # Đăng ký người dùng
│   ├── reset.php                 # Đặt lại mật khẩu
│   ├── user_login.php            # Trình xử lý đăng nhập cũ
│   ├── admin_login.php           # Trình xử lý đăng nhập admin
│   ├── user_logout.php           # Đăng xuất người dùng
│   └── admin_logout.php          # Đăng xuất admin
│
├── user/                         # Tính năng người dùng
│   ├── watch.php                 # Trang xem phim
│   └── profile.php               # Quản lý hồ sơ người dùng
│
├── contact/                      # Trang thông tin
│   ├── lienhe.php                # Trang liên hệ
│   ├── gioithieu.php             # Trang giới thiệu
│   ├── dieu-khoan.php            # Điều khoản sử dụng
│   ├── chinh-sach.php            # Chính sách riêng tư
│   └── contact.css               # CSS trang liên hệ
│
├── assets/                       # Tài sản tĩnh
│   ├── style.css                 # Kiểu chính
│   ├── auth.css                  # Kiểu xác thực
│   ├── admin.css                 # Kiểu admin
│   ├── responsive.css            # Thiết kế responsive
│   └── img/                      # Hình ảnh bổ sung
│
├── images/                       # Ảnh thu nhỏ phim & hình ảnh
│   ├── slider/                   # Hình ảnh thanh trượt
│   ├── episodes/                 # Hình ảnh tập phim
│   ├── trending/                 # Phim xu hướng
│   ├── suggested/                # Phim gợi ý
│   ├── favorite/                 # Phim yêu thích
│   ├── top-10/                   # Top 10 phim
│   └── [by-genre]/               # Thư mục theo thể loại
│
├── js/                           # Tệp JavaScript
│   ├── app.js                    # JavaScript ứng dụng chính
│   ├── slider.js                 # Chức năng thanh trượt
│   ├── hamburger.js              # Bật/tắt menu di động
│   ├── watch.js                  # Logic trang xem
│   └── admin.js                  # JavaScript bảng điều khiển
│
└── img/                          # Hình ảnh chung
    └── profile.jpg               # Ảnh hồ sơ mặc định
```

---

## 🗄️ Lược Đồ Cơ Sở Dữ Liệu

### Bảng Cốt Lõi
- **users** - Tài khoản người dùng thường xuyên
- **admins** - Tài khoản admin với vai trò
- **movies** - Siêu dữ liệu phim (61+ phim)
- **genres** - Thể loại phim (9 thể loại)
- **movie_genres** - Mối quan hệ nhiều-nhiều
- **actors** - Thông tin diễn viên (80+ diễn viên)
- **movie_actors** - Liên kết dàn diễn viên
- **universes** - MCU, DCU, Khác (3 vũ trụ)

### Bảng Bổ Sung
- **comments** - Bình luận người dùng trên phim với phê duyệt
- **ratings** - Đánh giá/Bình luận phim
- **user_tokens** - Token ghi nhớ cho đăng nhập tự động

---

## 🚀 Bắt Đầu

### Yêu Cầu
- PHP 8.2+
- MariaDB/MySQL 10.4+
- Apache với mod_rewrite
- Composer (tùy chọn, phụ thuộc tối thiểu)

### Cài Đặt

1. **Sao Chép/Thiết Lập Dự Án**
   ```bash
   # Điều hướng đến thư mục dự án
   cd web_project
   ```

2. **Cấu Hình Cơ Sở Dữ Liệu**
   - Chỉnh sửa `include/config.php` với thông tin đăng nhập cơ sở dữ liệu của bạn
   - Mặc định: localhost, người dùng root, cơ sở dữ liệu: marvel_dc_movies

3. **Nhập Cơ Sở Dữ Liệu**
   ```bash
   mysql -u root -p marvel_dc_movies < marvel_dc_movies.sql
   ```

4. **Khởi Động Máy Chủ Phát Triển**
   ```bash
   # Sử dụng máy chủ tích hợp PHP
   php -S localhost:8000
   
   # Hoặc sử dụng Apache với XAMP/WAMP
   ```

5. **Truy Cập Ứng Dụng**
   - Giao diện người dùng: `http://localhost:8000`
   - Bảng điều khiển admin: Đăng nhập bằng tài khoản admin

### Thông Tin Đăng Nhập Admin Mặc Định
```
Tên người dùng: admin1 hoặc admin2
(Hãy nhớ thay đổi ngay lập tức trong production)
```

---

## 📊 Thống Kê Cơ Sở Dữ Liệu

- **Tổng Số Phim**: 61+ mục
- **Thể Loại**: 9 thể loại
- **Diễn Viên**: 80+ diễn viên
- **Vũ Trụ**: 3 (Marvel, DC, Khác)
- **Người Dùng**: Có thể mở rộng
- **Bình Luận**: Theo dõi tương tác hoạt động

---

## 🔐 Tính Năng Bảo Mật

✅ **Xác Thực & Phép Cấp**
- Xác thực dựa trên session
- Chức năng ghi nhớ dựa trên token
- Kiểm soát truy cập dựa trên vai trò (Người dùng/Admin/Super Admin)
- Xác thực token CSRF

✅ **Bảo Vệ Dữ Liệu**
- SQL Prepared Statements (ngăn chặn SQL Injection)
- Mã hóa mật khẩu bằng bcrypt
- Xác thực email
- Tinh sạch đầu vào

✅ **Tiêu Đề Bảo Mật HTTP**
- X-Frame-Options: DENY
- X-Content-Type-Options: nosniff
- X-XSS-Protection: 1; mode=block

---

## 🎨 Luồng Code - Hành Trình Người Dùng

```
1. Người dùng truy cập index.php (bộ định tuyến)
   ↓
2. Kiểm tra session/xác thực
   ↓
3. Định tuyến đến trang thích hợp dựa trên tham số ?page
   ↓
4. Lấy dữ liệu từ cơ sở dữ liệu
   ↓
5. Kết xuất mẫu HTML
   ↓
6. Tải tài sản CSS/JS
   ↓
7. Hiển thị trang responsive cho người dùng
```

---

## 🎨 Luồng Code - Hành Trình Admin

```
1. Admin truy cập admin/dashboard.php
   ↓
2. check_admin.php xác thực session admin
   ↓
3. Hiển thị bảng điều khiển admin với thống kê
   ↓
4. Truy cập trang quản lý:
   - add_movie.php → Tạo phim mới
   - edit_movie.php → Sửa đổi hiện có
   - movies.php → Xóa/quản lý
   - users.php → Quản lý người dùng
   - genres.php → Quản lý thể loại
```

---

## 📝 Tham Chiếu API/Hàm

### Hàm Cốt Lõi (functions.php)
```php
getMoviesByGenre($conn, $genreId, $limit, $offset)
getMovieDetails($conn, $movieId)
getAllGenres($conn)
getAllMovies($conn, $limit, $offset, $universe)
getFeaturedMovies($limit, $offset)
getMovieById($conn, $id)
```

### Hàm Header
```php
generateCsrfToken()
getGenres($conn)
```

---

## 🛠️ Hướng Dẫn Phát Triển

### Thêm Tính Năng Mới
1. Tạo các hàm logic trong `include/functions.php`
2. Tạo thành phần UI trong thư mục thích hợp
3. Cập nhật tuyến đường trong `index.php` (hoặc bộ định tuyến admin)
4. Thêm bảng/cột cơ sở dữ liệu nếu cần
5. Ghi lại các thay đổi trong README này

### Sửa Đổi Cơ Sở Dữ Liệu
- Luôn sử dụng prepared statements
- Thêm migrations vào kiểm soát phiên bản
- Cập nhật `marvel_dc_movies.sql` sau khi thay đổi lược đồ

### Thay Đổi Frontend
- Cập nhật các tệp CSS liên quan trong `assets/`
- Kiểm tra thiết kế responsive trên di động
- Xác thực đầu ra HTML

---

## 📈 Xem Xét Hiệu Suất

- **Cơ Sở Dữ Liệu**: Truy vấn có chỉ mục để tra cứu nhanh
- **Bộ Nhớ Đệm**: Triển khai Redis cho lưu trữ session (tùy chọn)
- **Hình Ảnh**: Tải lười cho ảnh thu nhỏ
- **CSS**: Minify trong production
- **JavaScript**: Hoãn các tập lệnh không quan trọng

---

## 🐛 Giới Hạn Hiện Tại & TODOs

- Không tích hợp thanh toán (không cần cho duyệt)
- Xác thực email cho đăng ký tùy chọn
- Tùy chỉnh trình phát video hạn chế
- Không có thuật toán khuyến nghị
- Không theo dõi phân tích

---

## 📄 Giấy Phép

Dự án này là độc quyền. Tất cả các quyền được bảo lưu.

---

## 👨‍💻 Ghi Chú Nhóm Phát Triển

- **Bộ Định Tuyến Chính**: `index.php`
- **Trình Quản Lý Session**: Kiểm tra `auth/login.php` để xử lý session
- **Trình Xử Lý Cơ Sở Dữ Liệu**: `include/db.php` + `include/config.php`
- **Bảo Vệ Admin**: `admin/check_admin.php`

---

## 🔗 Liên Kết Nhanh

- [Tài Liệu Kiến Trúc](README_ARCHITECTURE.md)
- [Chi Tiết Cấu Trúc Thư Mục](FOLDERS_STRUCTURE.md)
- [Lược Đồ Cơ Sở Dữ Liệu](DATABASE_SCHEMA.md)
- [Hướng Dẫn Claude AI](copilot-instructions.md)

---

**Cập Nhật Lần Cuối**: 22 Tháng 3 Năm 2026
**Trạng Thái**: Phát Triển Hoạt Động
"# web-xem-phim" 
