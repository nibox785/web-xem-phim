const MOVIES_PER_PAGE = 15;
const movieContainer = document.getElementById("movie-list");
const allMovies = Array.from(movieContainer.children);
const totalPages = Math.ceil(allMovies.length / MOVIES_PER_PAGE);

function showPage(page) {
  // Ẩn tất cả phim trước
  allMovies.forEach((movie, index) => {
    movie.style.display = "none";
    if (
      index >= (page - 1) * MOVIES_PER_PAGE &&
      index < page * MOVIES_PER_PAGE
    ) {
      movie.style.display = "block";
    }
  });

  // Cập nhật active trong phân trang
  document.querySelectorAll(".pagination a").forEach((btn, idx) => {
    btn.classList.toggle("active", idx + 1 === page);
  });
}

// Tạo phân trang động
const paginationContainer = document.querySelector(".pagination");
paginationContainer.innerHTML = ""; // Xoá nội dung cũ

for (let i = 1; i <= totalPages; i++) {
  const pageLink = document.createElement("a");
  pageLink.textContent = i;
  pageLink.href = "#";
  if (i === 1) pageLink.classList.add("active");

  pageLink.addEventListener("click", (e) => {
    e.preventDefault();
    showPage(i);
  });

  paginationContainer.appendChild(pageLink);
}

showPage(1); // Mặc định hiển thị trang 1

//Xử lý cuộn về đầu trang sau khi click chọn trang

document.querySelectorAll(".pagination a").forEach(function (link) {
  link.addEventListener("click", function (e) {
    e.preventDefault();

    // Xử lý phân trang ở đây (tùy bạn đang ẩn/hiện phim thế nào)

    // Sau khi xử lý xong, cuộn về đầu trang (hoặc đầu phần phim)
    window.scrollTo({
      top: 0,
      behavior: "smooth",
    });
  });
});

// // Nút để chuyển đến trang chi tiết phim
// document.querySelectorAll(".movie-list-item").forEach((movie) => {
//   movie.addEventListener("click", (e) => {
//     //  Nếu nhấp vào nút xem phim, chặn điều hướng
//     if (e.target.classList.contains("movie-list-item-button")) {
//       return;
//     }

//     // Lấy tiêu đề phim
//     const movieTitle = movie.querySelector(
//       ".movie-list-item-title"
//     ).textContent;

//     //Sử dụng tham số URL với bản thử
//     window.location.href = `movie-details.html?movie=${encodeURIComponent(
//       movieTitle
//     )}`;
//   });
// });

// //Cho trang chi tiết phim
// if (window.location.pathname.includes("movie-details.html")) {
//   // Lấy tiêu đề phim thông qua URL
//   const urlParams = new URLSearchParams(window.location.search);
//   const movieTitle = urlParams.get("movie");

//   // Cập nhật trang với thông tin phim
//   if (movieTitle) {
//     document.querySelector(".movie-title").textContent = movieTitle;
//   }

//   //Thêm trailer
//   document.querySelectorAll(".trailer-item").forEach((trailer) => {
//     trailer.addEventListener("click", () => {
//       // Lấy dự liệu trailer
//       const trailerSrc =
//         trailer.dataset.src || "https://www.youtube.com/embed/dQw4w9WgXcQ";
//       const trailerTitle =
//         trailer.querySelector(".trailer-title")?.textContent || "Movie Trailer";

//       // Create video modal
//       const trailerModal = document.createElement("div");
//       trailerModal.className = "trailer-modal";
//       trailerModal.innerHTML = `
//         <div class="trailer-modal-content">
//           <div class="trailer-modal-header">
//             <h3>${trailerTitle}</h3>
//             <button class="close-trailer">&times;</button>
//           </div>
//           <div class="trailer-modal-body">
//             <iframe
//               width="100%"
//               height="100%"
//               src="${trailerSrc}"
//               frameborder="0"
//               allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
//               allowfullscreen>
//             </iframe>
//           </div>
//         </div>
//       `;
//       document.body.appendChild(trailerModal);

//       // Hiển thị phương thức
//       setTimeout(() => {
//         trailerModal.classList.add("active");
//       }, 10);

//       // Đóng phương thức
//       trailerModal
//         .querySelector(".close-trailer")
//         .addEventListener("click", () => {
//           trailerModal.classList.remove("active");
//           setTimeout(() => {
//             trailerModal.remove();
//           }, 300); // Xóa sau khi quá trình chuyển đổi hoàn tất
//         });

//       // Đóng khi nhấp vào bên ngoài nội dung phương thức
//       trailerModal.addEventListener("click", (e) => {
//         if (e.target === trailerModal) {
//           trailerModal.classList.remove("active");
//           setTimeout(() => {
//             trailerModal.remove();
//           }, 300);
//         }
//       });
//     });
//   });

//   //Chức năng nút quay lại
//   document.querySelector(".back-button")?.addEventListener("click", () => {
//     window.history.back();
//   });
// }
