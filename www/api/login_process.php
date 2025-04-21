<?php
session_start();
require_once "connection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);
    $remember = isset($_POST["remember-me"]);

    if (empty($username) || empty($password)) {
        echo "Thiếu thông tin đăng nhập.";
        exit;
    }

    $stmt = $con->prepare("SELECT user_id, username, email, user_pass FROM tb_users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        if ($password === $row['user_pass']) {
            $_SESSION["user_id"] = $row["user_id"];
            $_SESSION["user_email"] = $row["email"];

            if ($remember) {
                setcookie("user_id", $row["user_id"], time() + (86400 * 30), "/");
                setcookie("user_email", $row["email"], time() + (86400 * 30), "/");
            }

            echo "success";
            
        } else {
            echo "Sai mật khẩu.";
        }
    } else {
        echo "Tài khoản không tồn tại.";
    }

    $stmt->close();
    $con->close();
}
?>
