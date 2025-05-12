<?php
require_once('connection.php');
require('vendor/autoload.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (isset($_POST['email']) && !empty(trim($_POST['email']))) {
        $email = trim($_POST['email']);

        $query = "SELECT COUNT(*) FROM tb_users WHERE email = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count > 0) {
            $get_token = $con->prepare("SELECT email_token FROM tb_users WHERE email = ?");
            $get_token->bind_param("s", $email);
            $get_token->execute();
            $get_token->bind_result($email_token);
            $get_token->fetch();
            $get_token->close();

            $mail = new PHPMailer(true);

            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'trangiathanh0205@gmail.com';
                $mail->Password   = 'fchqzhhupsgfxyod';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                $mail->setFrom('trangiathanh0205@gmail.com', 'Personal Note');
                $mail->addAddress($email);
                $mail->isHTML(true);
                $mail->Subject = 'Your Password Has Been Reset';
                $mail->Body = "
                <html>
                <head>
                    <style>
                        body {
                            font-family: Arial, sans-serif;
                            color: #996d43;
                            background-color: #f2f2f2;
                            margin: 0;
                            padding: 0;
                        }
                
                        .container {
                            width: 100%;
                            max-width: 600px;
                            margin: 30px auto;
                            background-color: #ffffff;
                            padding: 20px;
                            border-radius: 8px;
                            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
                        }
                
                        .header {
                            background-color: #f3da54;
                            color: #996d43;
                            padding: 15px;
                            text-align: center;
                            border-radius: 8px 8px 0 0;
                        }
                
                        .content {
                            padding: 20px;
                        }
                
                        .footer {
                            text-align: center;
                            font-size: 12px;
                            color: #575757;
                            margin-top: 30px;
                            border-top: 1px solid #ddd;
                            padding-top: 15px;
                        }
                
                        .token-wrapper {
                            text-align: center;
                            margin-top: 10px;
                        }
                
                        .token {
                            font-size: 22px;
                            font-weight: bold;
                            color: #ffffff;
                            background-color: #575757;
                            padding: 10px 15px;
                            border-radius: 6px;
                            display: inline-block;
                            letter-spacing: 2px;
                        }
                
                        p {
                            margin: 12px 0;
                            line-height: 1.5;
                        }
                    </style>
                </head>
                <body>
                    <div class='container'>
                        <div class='header'>
                            <h2>Password Reset</h2>
                        </div>
                        <div class='content'>
                            <p>Hi,</p>
                            <p>Your password for <strong>Note Dashboard</strong> has just been changed.</p>
                            <p>If this wasn’t you, please reset your password immediately or contact our support.</p>
                            <p>Your email token is:</p>
                            <div class='token-wrapper'>
                                <p class='token'>$email_token</p>
                            </div>
                            <p>Regards,<br>Note Dashboard Team</p>
                        </div>
                        <div class='footer'>
                            <p>This is an automated message. Please do not reply.</p>
                        </div>
                    </div>
                </body>
                </html>
                ";

                $mail->send();
                echo json_encode(['status' => 'success', 'message' => 'Email token sent successfully.']);
            } catch (Exception $e) {
                echo json_encode(['status' => 'error', 'message' => 'Failed to send email.']);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Email is invalid or does not exist."]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Email is required."]);
    }
}
