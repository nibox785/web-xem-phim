$(document).ready(function () {
  // 1. Xác nhận hành động chỉ cho nút sửa/xem trong bảng (với data-confirm attribute)
  $(document).on('click', '[data-confirm]', function (e) {
    const message = $(this).data('confirm');
    if (!confirm(message)) {
      e.preventDefault();
    }
  });

  // 2. Xác nhận xóa với inline form (form có onsubmit)
  // Note: Xóa được xử lý bằng onsubmit trong HTML form, đây chỉ là backup
});
