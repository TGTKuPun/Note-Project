<?php
require_once('connection.php');

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (
        isset($_POST['email']) && isset($_POST['new-password']) && isset($_POST['confirm-password'])
        && !empty($_POST['email']) && !empty($_POST['new-password']) && !empty($_POST['confirm-password'])
    ) {
        $email = $_POST['email'];
        $new_password = $_POST['new-password'];
        $confirm_password = $_POST['confirm-password'];

        if ($new_password !== $confirm_password) {
            echo json_encode(['status' => 'error', 'message' => 'The password must be the same']);
            exit;
        }

        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Update password in database
        $stmt = $con->prepare("UPDATE tb_users SET user_pass = ? WHERE email = ?");
        $stmt->bind_param("ss", $hashed_password, $email);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Password updated successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update password']);
        }

        $stmt->close();
        $con->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
        exit();
    }
}
