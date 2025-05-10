<?php

require_once('connection.php');

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
            echo json_encode(["status" => "success", "message" => "Email is available."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Email is invalid or not existed"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Email is required."]);
    }
}
