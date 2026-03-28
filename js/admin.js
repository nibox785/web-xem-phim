$(document).ready(function () {
  // 1. Xác nhận hành động khi nhấn nút Sửa hoặc Xem
  $(".btn-primary, .btn-info").on("click", function (e) {
    const action = $(this).text() === "Sửa" ? "sửa" : "xem";
    const movieTitle = $(this).closest("tr").find("td:nth-child(3)").text(); 
    if (!confirm(`Bạn có chắc muốn ${action} phim "${movieTitle}" không?`)) {
      e.preventDefault(); // Ngăn hành động nếu người dùng hủy
    }
  });

  // 2. Tìm kiếm nhanh trong bảng phim
  $("#search-movie").on("keyup", function () {
    const value = $(this).val().toLowerCase();
    $(".admin-table tbody tr").filter(function () {
      $(this).toggle(
        $(this).find("td:nth-child(3)").text().toLowerCase().indexOf(value) > -1 
      );
    });
  });

  // 3. Làm mới bảng phim
  $("#refresh-movies").on("click", function () {
    $.ajax({
      url: "fetch_recent_movies.php",
      method: "GET",
      dataType: "json",
      success: function (data) {
        const tbody = $(".admin-table tbody");
        tbody.empty();
        if (data.length > 0) {
          data.forEach((movie) => {
            tbody.append(`
                          <tr>
                              <td>${movie.id}</td>
                              <td>${
                                movie.thumbnail
                                  ? `<img src="${movie.thumbnail}" alt="${movie.title}" width="50">`
                                  : '<span class="no-image">No Image</span>'
                              }</td>
                              <td>${movie.title}</td>
                              <td>${movie.release_year}</td>
                              <td>${movie.genres || "N/A"}</td>
                              <td>${movie.universe_name || "N/A"}</td>
                              <td>
                                  <div class="action-buttons">
                                      <a href="edit_movie.php?id=${
                                        movie.id
                                      }" class="btn btn-sm btn-primary">Sửa</a>
                                      <a href="movies.php?delete=${
                                        movie.id
                                      }" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa phim này? Các bình luận và đánh giá liên quan cũng sẽ bị xóa.');">Xóa</a>
                                      <a href="../index.php?page=watch&id=${
                                        movie.id
                                      }" class="btn btn-sm btn-info" target="_blank">Xem</a>
                                  </div>
                              </td>
                          </tr>
                      `);
          });
        } else {
          tbody.append(
            '<tr><td colspan="7" class="text-center">Không có phim nào trong cơ sở dữ liệu</td></tr>'
          );
        }
      },
      error: function () {
        alert("Lỗi khi tải lại danh sách phim.");
      },
    });
  });
});
