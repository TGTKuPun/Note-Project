<?php
session_start();
require_once '../connection.php'; // file kết nối database

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo "Unauthorized";
    exit;
}

$user_id = $_SESSION['user_id'];

// Nhận dữ liệu từ form
$username = trim($_POST['username']);
$firstname = trim($_POST['firstname']);
$lastname = trim($_POST['lastname']);

// Kiểm tra và xử lý avatar (nếu có upload)
$avatar = null;
if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
    $file_tmp = $_FILES['avatar']['tmp_name'];
    $file_name = basename($_FILES['avatar']['name']);
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    // Kiểm tra định dạng file
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
    if (!in_array($file_ext, $allowed)) {
        echo "Chỉ chấp nhận định dạng JPG, JPEG, PNG, WEBP.";
        exit;
    }

    // Tạo tên file mới để tránh trùng lặp
    $new_file_name = uniqid("avatar_", true) . "." . $file_ext;
    $upload_dir = "../../assets/uploads/avatar/";
    $upload_path = $upload_dir . $new_file_name;

    // Di chuyển file
    if (!move_uploaded_file($file_tmp, $upload_path)) {
        echo "Lỗi khi tải ảnh lên.";
        exit;
    }

    $avatar = $new_file_name;
}

// Cập nhật dữ liệu vào CSDL
try {
    $sql = "UPDATE tb_users SET username = ?, firstname = ?, lastname = ?" .
        ($avatar ? ", user_avatar = ?" : "") . " WHERE user_id = ?";
    $stmt = $con->prepare($sql);

    if ($avatar) {
        $stmt->bind_param("ssssi", $username, $firstname, $lastname, $avatar, $user_id);
    } else {
        $stmt->bind_param("sssi", $username, $firstname, $lastname, $user_id);
    }

    if ($stmt->execute()) {
        // Cập nhật session (nếu bạn dùng session lưu tên hiển thị, avatar,...)
        $_SESSION['username'] = $username;
        $_SESSION['firstname'] = $firstname;
        $_SESSION['lastname'] = $lastname;

        if ($avatar) {
            $_SESSION['user_avatar'] = $avatar;
        }

        header("Location: ../../pages/dashboard.php?update=success");
        exit;
    } else {
        echo "Lỗi khi cập nhật: " . $stmt->error;
    }
} catch (Exception $e) {
    echo "Đã xảy ra lỗi: " . $e->getMessage();
}
