<?php
session_start();
require_once('../connection.php');

header("Content-Type: application/json");

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized access"]);
    http_response_code(401);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['label_name']) || empty(trim($data['label_name']))) {
    echo json_encode(["status" => "error", "message" => "Label name cannot be empty"]);
    http_response_code(400);
    exit();
}

$label_name = trim($data['label_name']);
$user_id = $_SESSION['user_id'];

// Check if label already exists for the user or is a public label
$query = "SELECT label_id FROM tb_labels WHERE label_name = ? AND (user_id = ? OR label_name IN ('Work', 'Study', 'Business', 'Personal'))";
$stmt = $con->prepare($query);
$stmt->bind_param("si", $label_name, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(["status" => "error", "message" => "Label already exists"]);
    http_response_code(400);
    exit();
}

// Add new label with user_id
$query = "INSERT INTO tb_labels (label_name, user_id) VALUES (?, ?)";
$stmt = $con->prepare($query);
$stmt->bind_param("si", $label_name, $user_id);

if ($stmt->execute()) {
    $label_id = $con->insert_id;
    echo json_encode([
        "status" => "success",
        "message" => "Label added successfully",
        "label" => ["label_id" => $label_id, "label_name" => $label_name, "user_id" => $user_id]
    ]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to add label"]);
    http_response_code(500);
}

$stmt->close();
$con->close();
?>