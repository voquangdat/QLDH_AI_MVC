function signup(event) {
    event.preventDefault();

    var username = document.getElementById("username").value;
    var email = document.getElementById("email").value;
    var password = document.getElementById("password").value;
    var confirmPassword = document.getElementById("confirm_password").value;

    // Kiểm tra đầy đủ thông tin
    if (!username || !email || !password || !confirmPassword) {
        alert("Vui lòng nhập đầy đủ thông tin");
        return;
    }

    // Kiểm tra email hợp lệ
    if (!email.includes("@")) {
        alert("Vui lòng nhập email hợp lệ");
        return;
    }

    // Kiểm tra mật khẩu và xác nhận mật khẩu khớp nhau
    if (password !== confirmPassword) {
        alert("Mật khẩu và xác nhận mật khẩu không khớp");
        return;
    }

    var user = {
        username: username,
        email: email,
        password: password,
    };

    var json = JSON.stringify(user);
    localStorage.setItem(username, json);
    alert("Đăng ký thành công");
    window.location.href = "login.php";
}