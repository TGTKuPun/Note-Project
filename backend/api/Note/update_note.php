<?php
session_start();
require_once('../connection.php');

header("Content-Type: application/json");

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "Unauthorized access"]);
    exit();
}

$user_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents("php://input"), true);

if (isset($data["note_id"]) && isset($data["note_title"]) && isset($data["note_desc"]) && isset($data["label_name"]) && isset($data["note_date"])) {
    $note_id = $data["note_id"];
    $note_title = $data["note_title"];
    $note_desc = $data["note_desc"];
    $label_name = $data["label_name"];
    $note_date = $data["note_date"];
    $label_id = null;

    $query = "SELECT label_id FROM tb_labels WHERE label_name = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("s", $label_name);
    $stmt->execute();
    $stmt->bind_result($label_id);
    $stmt->fetch();
    $stmt->close();

    // Preserve is_pinned by not updating it
    $query_2 = "UPDATE tb_notes 
                SET note_title = ?, note_desc = ?, label_id = ?, note_date = ?
                WHERE note_id = ? AND user_id = ?";
    $stmt = $con->prepare($query_2);
    $stmt->bind_param("ssisii", $note_title, $note_desc, $label_id, $note_date, $note_id, $user_id);
    
    if ($stmt->execute()) {
        echo json_encode([
            "note_id" => $note_id,
            "status" => "success",
            "message" => "Note updated successfully",
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Failed to update note",
        ]);
    }

    $stmt->close();
}

$con->close();
?>