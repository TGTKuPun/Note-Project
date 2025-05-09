<?php

session_start();
require_once 'connection.php';

header("Content-Type: application/json");

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $current_password = trim($_POST['current_password']);
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    $query = "SELECT user_pass FROM tb_users WHERE user_id = ?";

    $stmt = $con->prepare($query);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $stmt->bind_result($stored_password);
    $stmt->fetch();
    $stmt->close();

    if (!password_verify($current_password, $stored_password)) {
        echo json_encode(["status" => "error", "message" => "Current password is not correct."]);
        exit;
    }

    if ($new_password != $confirm_password) {
        echo json_encode(["status" => "error", "message" => "Password must be similar"]);
        exit;
    }

    $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    $update_query = "UPDATE tb_users SET user_pass = ? WHERE user_id = ?";
    $update_stmt = $con->prepare($update_query);
    $update_stmt->bind_param("si", $new_hashed_password, $user_id);

    if ($update_stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Update password successfully."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error occurred!!!"]);
    }

    $update_stmt->close();
    $con->close();
}
