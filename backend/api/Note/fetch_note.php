<?php
session_start();
require_once "../connection.php";

header("Content-Type: application/json");

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "Unauthorized access"]);
    http_response_code(401);
    exit();
}

$user_id = $_SESSION['user_id'];

$query = "
    SELECT 
        n.note_id, n.note_title, n.note_desc, n.note_date, n.is_pinned,
        n.access, n.user_id, n.pass, l.label_name, u.username, u.user_avatar
    FROM tb_notes n
    LEFT JOIN tb_labels l ON n.label_id = l.label_id
    JOIN tb_users u ON n.user_id = u.user_id
    WHERE (n.access = 'public') 
       OR (n.access = 'protect') 
       OR (n.access = 'private' AND n.user_id = ?)
    ORDER BY n.is_pinned , n.note_date DESC
";

$stmt = $con->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$notes = [];
while ($row = $result->fetch_assoc()) {
    $notes[] = $row;
}

echo json_encode($notes);

$stmt->close();
$con->close();
