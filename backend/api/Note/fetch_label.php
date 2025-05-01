<?php
    
    require_once ("../connection.php");
    
    header("Content-Type: application/json");

    $query = "SELECT label_id, label_name FROM tb_labels";

    $stmt = $con->prepare($query);
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