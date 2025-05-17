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
$query = "SELECT label_id, label_name, user_id 
          FROM tb_labels 
          WHERE user_id = ? OR label_name IN ('Work', 'Study', 'Business', 'Personal') 
          ORDER BY label_name ASC";
$stmt = $con->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$labels = [];
while ($row = $result->fetch_assoc()) {
    $labels[] = $row;
}

echo json_encode($labels);

$stmt->close();
$con->close();
?>