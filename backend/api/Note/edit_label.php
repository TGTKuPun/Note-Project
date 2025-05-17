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

if (!isset($data['label_id']) || !isset($data['label_name']) || empty(trim($data['label_name']))) {
    echo json_encode(["status" => "error", "message" => "Missing label ID or empty label name"]);
    http_response_code(400);
    exit();
}

$label_id = $data['label_id'];
$label_name = trim($data['label_name']);
$user_id = $_SESSION['user_id'];

// Check if label exists and belongs to the user
$query = "SELECT label_id, label_name FROM tb_labels WHERE label_id = ? AND user_id = ?";
$stmt = $con->prepare($query);
$stmt->bind_param("ii", $label_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Check if label is a public label
    $query = "SELECT label_id FROM tb_labels WHERE label_id = ? AND label_name IN ('Work', 'Study', 'Business', 'Personal')";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $label_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        echo json_encode(["status" => "error", "message" => "Cannot edit public label"]);
        http_response_code(403);
        exit();
    }
    echo json_encode(["status" => "error", "message" => "Label does not exist or you do not have permission"]);
    http_response_code(404);
    exit();
}

// Check if label name already exists
$query = "SELECT label_id FROM tb_labels WHERE label_name = ? AND label_id != ? AND (user_id = ? OR label_name IN ('Work', 'Study', 'Business', 'Personal'))";
$stmt = $con->prepare($query);
$stmt->bind_param("sii", $label_name, $label_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(["status" => "error", "message" => "Label name already exists"]);
    http_response_code(400);
    exit();
}

// Update label
$query = "UPDATE tb_labels SET label_name = ? WHERE label_id = ? AND user_id = ?";
$stmt = $con->prepare($query);
$stmt->bind_param("sii", $label_name, $label_id, $user_id);

if ($stmt->execute()) {
    echo json_encode([
        "status" => "success",
        "message" => "Label updated successfully",
        "label" => ["label_id" => $label_id, "label_name" => $label_name, "user_id" => $user_id]
    ]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to update label"]);
    http_response_code(500);
}

$stmt->close();
$con->close();
?>