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

if (!isset($data['label_id'])) {
    echo json_encode(["status" => "error", "message" => "Missing label ID"]);
    http_response_code(400);
    exit();
}

$label_id = $data['label_id'];
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
        echo json_encode(["status" => "error", "message" => "Cannot delete public label"]);
        http_response_code(403);
        exit();
    }
    echo json_encode(["status" => "error", "message" => "Label does not exist or you do not have permission"]);
    http_response_code(404);
    exit();
}

// Check if label is in use
$query = "SELECT COUNT(*) as count FROM tb_notes WHERE label_id = ? AND user_id = ?";
$stmt = $con->prepare($query);
$stmt->bind_param("ii", $label_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row['count'] > 0) {
    echo json_encode(["status" => "error", "message" => "Cannot delete label as it is used in notes"]);
    http_response_code(400);
    exit();
}

// Delete label
$query = "DELETE FROM tb_labels WHERE label_id = ? AND user_id = ?";
$stmt = $con->prepare($query);
$stmt->bind_param("ii", $label_id, $user_id);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Label deleted successfully"]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to delete label"]);
    http_response_code(500);
}

$stmt->close();
$con->close();
?>