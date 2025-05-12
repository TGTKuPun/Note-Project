<?php
require_once('connection.php');

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (
        isset($_POST['email'], $_POST['new-password'], $_POST['confirm-password'], $_POST['email-token']) &&
        !empty($_POST['email']) && !empty($_POST['new-password']) && !empty($_POST['confirm-password']) && !empty($_POST['email-token'])
    ) {
        $email = trim($_POST['email']);
        $new_password = $_POST['new-password'];
        $confirm_password = $_POST['confirm-password'];
        $email_token = trim($_POST['email-token']);

        if ($new_password !== $confirm_password) {
            echo json_encode(['status' => 'error', 'message' => 'Passwords do not match']);
            exit;
        }

        $check_token = $con->prepare("SELECT COUNT(*) FROM tb_users WHERE email = ? AND email_token = ?");
        $check_token->bind_param("ss", $email, $email_token);
        $check_token->execute();
        $check_token->bind_result($token_valid);
        $check_token->fetch();
        $check_token->close();

        $new_email_token = bin2hex(random_bytes(16));

        $reset_token = $con->prepare("UPDATE tb_users SET email_token = ? WHERE email = ?");
        $reset_token->bind_param("ss", $new_email_token, $email);
        $reset_token->execute();

        if ($token_valid == 0) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid token or email']);
            exit;
        }

        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
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
