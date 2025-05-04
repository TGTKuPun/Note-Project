<?php
session_start();
require_once('../connection.php');

header("Content-Type: application/json");

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}

$user_id = $_SESSION['user_id'];

$data = json_decode(file_get_contents("php://input"), true);

$curr_view = null;

$query = "SELECT view FROM tb_preferences WHERE user_id = ?";

$stmt = $con->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($curr_view);
$stmt->fetch();
$stmt->close();

if ($data["view"] != $curr_view) {
    $query_2 = "UPDATE tb_preferences
                SET view = ?
                WHERE user_id = ?";
    $stmt = $con->prepare($query_2);
    $stmt->bind_param("si", $data["view"], $user_id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Update layout successfully"]);
    } else {
        echo json_encode(["error" => "Error occurred during update process"]);
    }
    $stmt->close();
} else {
    echo json_encode(["success" => true, "message" => "Update is not necessary"]);
}

$con->close();
?>
