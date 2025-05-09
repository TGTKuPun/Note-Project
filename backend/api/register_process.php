<?php
session_start();
require_once "connection.php";

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $firstname = trim($_POST["firstname"]);
    $lastname = trim($_POST["lastname"]);
    $email = trim($_POST["new-email"]);
    $username = trim($_POST["new-username"]);
    $raw_password = trim($_POST["new-password"]);
    $hashed_password = password_hash($raw_password, PASSWORD_DEFAULT);

    if (empty($firstname) || empty($lastname) || empty($email) || empty($username) || empty($raw_password)) {
        echo json_encode([
            "status" => "error",
            "message" => "Insufficient information."
        ]);
        exit;
    }

    // Tạo mã OTP ngẫu nhiên
    $otp_code = rand(100000, 999999);  // Tạo mã OTP 6 chữ số
    $otp_expiry = date('Y-m-d H:i:s', strtotime('+10 minutes'));  // Thời gian hết hạn OTP là 10 phút sau

    // Thực hiện câu lệnh INSERT để thêm người dùng mới vào bảng tb_users
    $stmt = $con->prepare("INSERT INTO tb_users (firstname, lastname, email, username, user_pass, activated, otp_code, otp_expiry) VALUES (?, ?, ?, ?, ?, b'0', ?, ?)");
    $stmt->bind_param("sssssss", $firstname, $lastname, $email, $username, $hashed_password, $otp_code, $otp_expiry);

    if ($stmt->execute()) {
        $new_user_id = $stmt->insert_id;

        // Câu lệnh INSERT vào bảng tb_preferences
        $stmt_1 = $con->prepare("INSERT INTO tb_preferences (user_id) VALUES (?)");
        $stmt_1->bind_param("i", $new_user_id);
        $stmt_1->execute();

        echo json_encode([
            "status" => "success",
            "message" => "Registration successful. Please enter the OTP to verify your account.",
            "otp" => $otp_code  // Trả mã OTP về giao diện
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Error: " . $stmt->error,
        ]);
        exit();
    }

    $stmt->close();
    $con->close();
}
