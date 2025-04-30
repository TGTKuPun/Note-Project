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

    if (isset($data["note_id"])) {
        $note_id = $data["note_id"];

        $query = "DELETE FROM tb_notes WHERE note_id = ?";

        $stmt = $con->prepare($query);
        $stmt->bind_param("i", $note_id);
        
        if($stmt->execute()) {
            echo json_encode([
                "status" => "success",
                "message" => "Note was deleted successfully"
            ]);
        }
        else {
            echo json_encode([
                "status" => "error",
                "message" => "Failed to delete note"
            ]);
        }

        $stmt->close();
    }

    $con->close();
?>