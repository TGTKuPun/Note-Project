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
    $remember = ($_POST["remember-me"] ?? 0) == 1;
    if (empty($firstname) || empty($lastname) || empty($email) || empty($username) || empty($raw_password)) {
        echo json_encode([
            "status" => "error",
            "message" => "Insufficient information."
        ]);
        exit;
    }

    $email_token = bin2hex(random_bytes(32));

    $stmt = $con->prepare("INSERT INTO tb_users (firstname, lastname, email, username, user_pass, email_token) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $firstname, $lastname, $email, $username, $hashed_password, $email_token);

    if ($stmt->execute()) {

        $new_user_id = $stmt->insert_id;

        $stmt_1 = $con->prepare("INSERT INTO tb_preferences (user_id) VALUES (?)");
        $stmt_1->bind_param("i", $new_user_id);
        $stmt_1->execute();

        $select_stmt = $con->prepare("SELECT * FROM tb_users WHERE user_id = ?");
        $select_stmt->bind_param("i", $new_user_id);
        $select_stmt->execute();
        $result = $select_stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $_SESSION["user_id"] = $row["user_id"];
            $_SESSION["user_email"] = $row["email"];
            $_SESSION["username"] = $row["username"];
            $_SESSION["firstname"] = $row["firstname"];
            $_SESSION["lastname"] = $row["lastname"];
            $_SESSION["user_avatar"] = $row["user_avatar"];

            if ($remember) {
                setcookie("user_id", $row["user_id"], time() + 86400, "/");
                setcookie("user_email", $row["email"], time() + 86400, "/");
                setcookie("username", $row['username'], time() + 86400, "/");
            }

            echo json_encode([
                "status" => "success",
                "message" => "Registration successful.",
            ]);
        }
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Error: " . $stmt->error,
        ]);
    }

    $stmt->close();
    $con->close();
}
