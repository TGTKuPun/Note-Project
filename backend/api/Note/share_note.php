<?php
    session_start();
    require_once ('../connection.php');

    header("Content-Type: application/json");

    $user_id = $_SESSION['user_id'];

    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data["note_id"]) && isset($data["share_type"])) {
        $note_id = $data["note_id"];
        $share_type = $data["share_type"];
        
        if ($share_type === 'protect' && isset($data["pass"]) && !empty($data["pass"])) {
            $pass = password_hash($data["pass"], PASSWORD_DEFAULT);        
            
            $query = "UPDATE tb_notes SET access = ?, pass = ? WHERE note_id = ? AND user_id = ?";
            $stmt = $con->prepare($query);
            $stmt->bind_param("ssii", $share_type, $pass, $note_id, $user_id);
        } else {
            // Không phải protect → xoá mật khẩu cũ nếu có
            $query = "UPDATE tb_notes SET access = ?, pass = NULL WHERE note_id = ? AND user_id = ?";
            $stmt = $con->prepare($query);
            $stmt->bind_param("sii", $share_type, $note_id, $user_id);
        }
        

        if ($stmt->execute()) {
            echo json_encode([
                "status" => "success",
                "message" => "Note shared successfully as '$share_type'",
                "note_id" => $note_id,
                "share_type" => $share_type
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Failed to share note"
            ]);
        }

        $stmt->close();
    
    }

    $con->close();
?>