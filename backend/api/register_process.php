<?php
session_start();
require_once "connection.php";
require_once "config.php";
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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

    $check_email = $con->prepare("SELECT COUNT(*) FROM tb_users WHERE email = ?");
    $check_email->bind_param("s", $email);
    $check_email->execute();
    $check_email->bind_result($email_count);
    $check_email->fetch();
    $check_email->close();

    if ($email_count > 0) {
        echo json_encode([
            "status" => "error",
            "message" => "Email has been registerd",
        ]);
        exit;
    }

    $otp_code = rand(100000, 999999);
    $otp_expiry = date('Y-m-d H:i:s', strtotime('+10 minutes'));

    $email_token = bin2hex(random_bytes(16));

    $stmt = $con->prepare("INSERT INTO tb_users (firstname, lastname, email, username, user_pass, activated, otp_code, otp_expiry, email_token) VALUES (?, ?, ?, ?, ?, b'0', ?, ?, ?)");
    $stmt->bind_param("ssssssss", $firstname, $lastname, $email, $username, $hashed_password, $otp_code, $otp_expiry, $email_token);

    if ($stmt->execute()) {
        $new_user_id = $stmt->insert_id;

        $stmt_1 = $con->prepare("INSERT INTO tb_preferences (user_id) VALUES (?)");
        $stmt_1->bind_param("i", $new_user_id);
        $stmt_1->execute();

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = SMTP_USER;
            $mail->Password   = SMTP_PASS;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Recipients
            $mail->setFrom(SMTP_USER, 'Personal Note');
            $mail->addAddress($email, "$firstname $lastname");

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Your OTP Code to active your account';
            $mail->Body    = "
                <h3>Hi $firstname,</h3>
                <p>Your OTP code is: <strong>$otp_code</strong></p>
                <p>This code will expire in 10 minutes.</p>
            ";

            $mail->send();
            echo json_encode([
                "status" => "success",
                "message" => "Registration successful. Please enter the OTP to verify your account.",
                "otp" => $otp_code
            ]);
        } catch (Exception $e) {
            echo json_encode([
                "status" => "error",
                "message" => "Failed to send OTP email. Please try again later."
            ]);
        }
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
