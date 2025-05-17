<?php
session_start();
require_once('../connection.php');

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['note_id']) || !isset($data['password'])) {
    echo json_encode(["success" => false, "error" => "Undefined note_id or password"]);
    exit;
}

$note_id = $data['note_id'];
$password = $data['password'];
if (!$note_id || !$password) {
    echo json_encode(["success" => false, "error" => "Missing data"]);
    exit;
}

try {
    $stmt = $con->prepare("SELECT note_title, note_desc, pass FROM tb_notes WHERE note_id = ?");
    $stmt->bind_param("i", $note_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $note = $result->fetch_assoc();

    if ($note && password_verify($password, $note['pass'])) {
        echo json_encode([
            "success" => true,
            "note_title" => $note["note_title"],
            "note_desc" => $note["note_desc"]
        ]);
    } else {
        echo json_encode(["success" => false, "error" => "Wrong password"]);
    }

    $stmt->close();
} catch (Exception $e) {
    echo json_encode(["success" => false, "error" => "Error server: " . $e->getMessage()]);
}
?>
