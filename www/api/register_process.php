<?php
    session_start();
    require_once "connection.php";

    header("Content-Type: application/json");

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $firstname = trim($_POST["firstname"]);
        $lastname = trim($_POST["lastname"]);
        $email = trim($_POST["email"]);
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

        $stmt = $con->prepare("INSERT INTO tb_users (firstname, lastname, email, username, user_pass, user_role) VALUES (?, ?, ?, ?, ?, 'User')");
        $stmt->bind_param("sssss", $firstname, $lastname, $email, $username, $hashed_password);
        
        if ($stmt->execute()) {
            $new_user_id = $stmt->insert_id;

            $_SESSION["user_id"] = $new_user_id;
            $_SESSION["user_email"] = $email;

            if ($remember) {
                setcookie("user_id", $row["user_id"], time() + (86400 * 30), "/");
                setcookie("user_email", $row["email"], time() + (86400 * 30), "/");
            }

            echo json_encode([
                "status" => "success",
                "message" => "Registration successful.",
            ]);
        } 
        else {
            echo json_encode([
                "status" => "error",
                "message" => "Error: " . $stmt->error,
            ]);
        }

        $stmt->close();
        $con->close();
    }
?>