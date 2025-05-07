<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

$mail = new PHPMailer(true);

try {
    // Cấu hình server
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com'; // SMTP của Gmail hoặc dịch vụ khác
    $mail->SMTPAuth = true;
    $mail->Username = 'your_email@gmail.com';    // 🔒 Thay bằng email thật
    $mail->Password = 'your_app_password';       // 🔒 App password, KHÔNG phải password thường
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // Người gửi và người nhận
    $mail->setFrom('your_email@gmail.com', 'Mailer Test');
    $mail->addAddress('recipient@example.com', 'Người nhận');

    // Nội dung email
    $mail->isHTML(true);
    $mail->Subject = 'Test PHPMailer';
    $mail->Body    = '<b>PHPMailer đang hoạt động!</b>';
    $mail->AltBody = 'PHPMailer đang hoạt động! (plain text)';

    $mail->send();
    echo '✅ Email đã được gửi thành công';
} catch (Exception $e) {
    echo "❌ Gửi email thất bại. Lỗi: {$mail->ErrorInfo}";
}
