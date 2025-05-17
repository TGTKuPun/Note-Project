<?php
session_start();
require_once('../connection.php');

header("Content-Type: application/json");

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "Unauthorized access"]);
    http_response_code(401);
    exit();
}

$user_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data["note_id"])) {
    echo json_encode(["status" => "error", "message" => "Note ID required"]);
    http_response_code(400);
    exit();
}

$note_id = $data["note_id"];

// Check if note belongs to the user
$query = "SELECT is_pinned FROM tb_notes WHERE note_id = ? AND user_id = ?";
$stmt = $con->prepare($query);
$stmt->bind_param("ii", $note_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["status" => "error", "message" => "Note not found or unauthorized"]);
    http_response_code(404);
    exit();
}

$row = $result->fetch_assoc();
$current_pinned_status = $row['is_pinned'];
$new_pinned_status = $current_pinned_status ? 0 : 1;

// Update pin status
$query = "UPDATE tb_notes SET is_pinned = ? WHERE note_id = ? AND user_id = ?";
$stmt = $con->prepare($query);
$stmt->bind_param("iii", $new_pinned_status, $note_id, $user_id);

if ($stmt->execute()) {
    echo json_encode([
        "status" => "success",
        "message" => $new_pinned_status ? "Note pinned successfully" : "Note unpinned successfully",
        "is_pinned" => $new_pinned_status
    ]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to update pin status"]);
    http_response_code(500);
}

$stmt->close();
$con->close();
?>