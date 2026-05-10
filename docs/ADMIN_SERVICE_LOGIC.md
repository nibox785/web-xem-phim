# Admin Service Logic (Phase 4)

Tài liệu này mô tả kiến trúc và luồng xử lý admin sau khi tách lớp theo mô hình Controller -> Service -> Repository.

## 1. Mục tiêu

- Chuẩn hóa tất cả thao tác admin qua một router thống nhất.
- Tập trung logic vào `AdminService` để dễ bảo trì và mở rộng.
- Dùng CSRF cho tất cả thao tác ghi/xóa dữ liệu.

## 2. Kiến trúc admin hiện tại

### 2.1 Thành phần chính

- `app/Controllers/AdminController.php`
- `app/Services/AdminService.php`
- `app/Repositories/MovieRepository.php`
- `app/Repositories/UserRepository.php`
- `app/Repositories/GenreRepository.php`
- `app/Views/admin/*`
- `app/Views/shared/admin_header.php`
- `app/Views/shared/admin_footer.php`
- `assets/js/admin.js`

### 2.2 Luồng tổng quát

1. Request vào `public/index.php`.
2. Router nhận `?page=admin&action=...`.
3. Kiểm tra quyền admin qua session (`requireAdmin()`).
4. Khởi tạo `AdminService` với các repository cần thiết.
5. `AdminController` xử lý input, verify CSRF, gọi service.
6. Service gọi repository để truy vấn/cập nhật DB.
7. Controller render view tương ứng hoặc redirect về danh sách.

## 3. Router admin

Các action chính trong router:

- `admin_dashboard`
- `admin_movies`
- `admin_add_movie`
- `admin_edit_movie`
- `admin_delete_movie`
- `admin_users`
- `admin_delete_user`
- `admin_genres`
- `admin_add_genre`
- `admin_delete_genre`
- `admin_logout`

Mẫu URL chuẩn:

- `index.php?page=admin&action=admin_dashboard`
- `index.php?page=admin&action=admin_movies`

## 4. AdminController

`AdminController` chịu trách nhiệm:

- Nhận request và parse dữ liệu vào.
- Verify CSRF cho POST action.
- Gọi method tương ứng trong `AdminService`.
- Truyền data sang view.
- Đặt flash message qua `$_SESSION`.
- Redirect sau thao tác thành công/thất bại.

Nhóm method chính:

- Dashboard: `dashboard()`
- Movies: `movies()`, `addMovieForm()`, `addMovie()`, `editMovieForm()`, `editMovie()`, `deleteMovie()`
- Users: `users()`, `deleteUser()`
- Genres: `genres()`, `addGenre()`, `deleteGenre()`

## 5. AdminService

`AdminService` là nơi xử lý nghiệp vụ admin, không render HTML.

### 5.1 Dashboard

- `getDashboardStats()`
- Trả về:
  - tổng số phim
  - tổng số user
  - tổng số genre
  - danh sách phim mới nhất

### 5.2 Movies

- `listMovies(page, perPage)`
- `addMovie(data)`
- `getMovieForEdit(movieId)`
- `updateMovie(movieId, data)`
- `deleteMovie(movieId)`
- `getMovieGenres(movieId)`

Quy tắc chính:

- Validate `title`, `release_year`.
- `release_year` hợp lệ trong khoảng hợp lý.
- Thêm/sửa movie xong sẽ cập nhật bảng `movie_genres`.
- Xóa movie xử lý đồng bộ dữ liệu liên quan qua repository.

### 5.3 Users

- `listUsers()`
- `deleteUser(userId)`

Quy tắc chính:

- Không cho xóa tài khoản đang đăng nhập.
- Xóa user bao gồm dữ liệu phụ thuộc liên quan.

### 5.4 Genres

- `listGenres()`
- `addGenre(name)`
- `deleteGenre(genreId)`
- `getAllGenresForSelect()`

Quy tắc chính:

- Tên genre không được rỗng.
- Xóa genre sẽ xóa liên kết trong bảng trung gian trước.

## 6. Repository responsibilities

### 6.1 MovieRepository

Các method dùng cho admin:

- `countAll()`
- `getRecent(limit)`
- `getAllWithGenres(limit, offset)`
- `getById(id)`
- `create(data)`
- `update(id, data)`
- `delete(id)`
- `getGenres(movieId)`
- `addGenre(movieId, genreId)`
- `removeAllGenres(movieId)`

### 6.2 UserRepository

- `countAll()`
- `getAllWithRoles()`
- `getById(id)`
- `delete(id)`

### 6.3 GenreRepository

- `countAll()`
- `getAll()`
- `create(name)`
- `delete(id)`

## 7. Admin views

Cấu trúc view:

- `app/Views/admin/dashboard.php`
- `app/Views/admin/movies/index.php`
- `app/Views/admin/movies/add.php`
- `app/Views/admin/movies/edit.php`
- `app/Views/admin/users/index.php`
- `app/Views/admin/genres/index.php`

Layout dùng chung:

- `app/Views/shared/admin_header.php`
- `app/Views/shared/admin_footer.php`

## 8. JavaScript admin

File dùng chính:

- `assets/js/admin.js`

Logic hiện tại:

- Dùng delegated event cho phần tử có `data-confirm`.
- Không còn selector rộng (`.btn-primary, .btn-info`) để tránh confirm sai ngữ cảnh.
- Không phụ thuộc endpoint refresh cũ không tồn tại.

## 9. Auth login/logout liên quan admin

- `auth/login.php`
  - Sau login thành công, nếu role admin thì redirect về dashboard admin theo route mới.
- `auth/admin_login.php`
  - Điều hướng vào route admin đúng chuẩn.
- `auth/admin_logout.php`
  - Xóa token, xóa session, redirect về trang chủ.

## 10. Ghi chú vận hành

- Khi thêm action admin mới, cần cập nhật:
  1. Router trong `public/index.php`
  2. Method trong `AdminController`
  3. Method nghiệp vụ trong `AdminService`
  4. Repository nếu cần query mới
  5. View tương ứng

- Ưu tiên giữ nguyên nguyên tắc:
  - Controller không viết SQL.
  - Service không render HTML.
  - Repository chỉ xử lý dữ liệu DB.
