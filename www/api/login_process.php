<?php
    session_start();
    require_once "connection.php";

    header("Content-Type: application/json");

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $username = trim($_POST["username"]);
        $password = trim($_POST["password"]);
        $remember = ($_POST["remember-me"] ?? 0) == 1;

        if (empty($username) || empty($password)) {
            echo json_encode([
                "status" => "error",
                "message" => "Insufficient information."
            ]);
            exit;
        }

        $stmt = $con->prepare("SELECT user_id, username, email, user_pass FROM tb_users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['user_pass']) || $password === $row['user_pass']) {
                $_SESSION["user_id"] = $row["user_id"];
                $_SESSION["user_email"] = $row["email"];

                if ($remember) {
                    setcookie("user_id", $row["user_id"], time() + (86400 * 30), "/");
                    setcookie("user_email", $row["email"], time() + (86400 * 30), "/");
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
?>
