<?php
    session_start();
    require_once ('../connection.php');

    header("Content-Type: application/json");

    if (!isset($_SESSION['user_id'])) {
        echo json_encode(["error" => "Unauthorized"]);
        exit();
    }

    $user_id = $_SESSION['user_id'];

    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['note_title']) && isset($data['note_desc']) && isset($data['label_name'])) {
        $title = trim($data['note_title']);
        $desc = trim($data['note_desc']);
        $label = trim($data['label_name']);
        $date = trim($data['note_date']);
        $label_id = null;
        
        $query = "SELECT label_id FROM tb_labels WHERE label_name = ?";
        
        $stmt = $con->prepare($query);
        $stmt->bind_param("s", $label);
        $stmt->execute();
        $stmt->bind_result($label_id);
        $stmt->fetch();
        $stmt->close();

        if (is_null($label_id)) {
            $insertLabelQuery = "INSERT INTO tb_labels (label_name) VALUES (?)";
            if ($stmt = $con->prepare($insertLabelQuery)) {
                $stmt->bind_param("s", $label);
                $stmt->execute();
                $label_id = $stmt->insert_id;
                $stmt->close();
            }
        }

        $query_2 = "INSERT INTO tb_notes (note_title, note_desc, note_date, user_id, label_id) VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $con->prepare($query_2);
        $stmt->bind_param("sssii", $title, $desc, $date, $user_id, $label_id);

        if ($stmt->execute()) {
            $note_id = $stmt->insert_id;
            echo json_encode([
                "status"=>"success",
                "message"=>"Note saved successfully",
                "note_id"=>$note_id
            ]);
        } else {
            echo json_encode([
                "status"=>"error",
                "message"=>"Failed to save note",
            ]);
        }

        $stmt->close();
    } else {
        echo json_encode([
            "status" => "error", 
            "message" => "Invalid data",
        ]);
    }

    $con->close();
?>