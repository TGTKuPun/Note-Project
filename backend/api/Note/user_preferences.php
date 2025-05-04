<?php
if (!isset($user_id)) return;

require_once __DIR__ . '../../connection.php';

$query = "SELECT view, theme FROM tb_preferences WHERE user_id = ?";

$stmt = $con->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$preferences = $result->fetch_assoc() ?? [
    'view' => 'list',
    'theme' => 'light'
];

$stmt->close();
$con->close();
