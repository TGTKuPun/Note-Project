<?php
session_start();
require_once "connection.php";

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $remember = ($_POST["remember-me"] ?? 0) == 1;

    if (empty($email) || empty($password)) {
        echo json_encode([
            "status" => "error",
            "message" => "Insufficient information."
        ]);
        exit;
    }

    $query = "SELECT user_id, username, email, user_pass, activated, firstname, lastname, user_avatar FROM tb_users WHERE email = ?";

    $stmt = $con->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        // Verify activation of account
        if ($row["activated"] != 1) {
            echo json_encode([
                "status" => "error",
                "message" => "Account not verified. Please check your email."
            ]);
            exit;
        }

        // Verify password
        if (password_verify($password, $row['user_pass'])) {
            $_SESSION["user_id"] = $row["user_id"];
            $_SESSION["user_email"] = $row["email"];
            $_SESSION["username"] = $row["username"];
            $_SESSION["firstname"] = $row["firstname"];
            $_SESSION["lastname"] = $row["lastname"];
            $_SESSION["user_avatar"] = $row["user_avatar"];

            if ($remember) {
                setcookie("user_id", $row["user_id"], time() + 86400, "/");
                setcookie("user_email", $row["email"], time() + 86400, "/");
                setcookie("username", $row["username"], time() + 86400, "/");
            }

            echo json_encode([
                "status" => "success",
                "message" => "Login successful."
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Invalid password."
            ]);
        }
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "No such account found."
        ]);
    }

    $stmt->close();
    $con->close();
}
