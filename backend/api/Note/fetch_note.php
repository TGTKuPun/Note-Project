<?php
    session_start();
    require_once "../connection.php";

    header("Content-Type: application/json");

    if (!isset($_SESSION['user_id'])) {
        echo json_encode(["error" => "Unauthorized"]);
        http_response_code(401);
        exit();
    }

    $user_id = $_SESSION['user_id'];

    $query = "SELECT n.note_id, n.note_title, n.note_desc, n.note_date, l.label_name
            FROM tb_notes n
            JOIN tb_labels l ON n.label_id = l.label_id
            WHERE n.user_id = ?";

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
?>
