function login(event) {
  event.preventDefault();

  var username = document.getElementById("username").value;
  var password = document.getElementById("password").value;

  // Kiểm tra thông tin có đầy đủ không
  if (!username || !password) {
      alert("Vui lòng nhập tên đăng nhập và mật khẩu");
      return;
  }

  var user = localStorage.getItem(username);
  var data = JSON.parse(user);

  // Kiểm tra tên đăng nhập tồn tại và mật khẩu khớp
  if (user == null) {
      alert("Tên đăng nhập không tồn tại");
  } else if (username === data.username && password === data.password) {
      alert("Đăng nhập thành công");

      // Lấy trang trước đó từ document.referrer
      var previousPage = document.referrer;

      // Kiểm tra nếu trang trước không phải là trang đăng ký
      if (previousPage && !previousPage.includes("signup.php")) {
          window.location.href = previousPage; // Quay lại trang trước
      } else {
          window.location.href = "index.php"; // Hoặc chuyển đến trang home
      }
  } else {
      alert("Mật khẩu không đúng");
  }
}