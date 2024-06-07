<?php

header('Content-Type: application/json');

try {
    $db = new PDO('sqlite:../lw1.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $data = json_decode(file_get_contents('php://input'), true);

    // Дополнительный вывод для отладки
    error_log(print_r($data, true));

    if (isset($data['application_id']) && isset($data['comment'])) {
        $application_id = $data['application_id'];
        $comment = $data['comment'];

        $sql = "INSERT INTO invitation (application_id, comment) VALUES (:application_id, :comment)";

        $stmt = $db->prepare($sql);
        $stmt->bindParam(':application_id', $application_id, PDO::PARAM_INT);
        $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);

        if ($stmt->execute()) {
            echo json_encode(['message' => 'New record created successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Error creating record']);
        }
    } else {
        http_response_code(400);
        echo json_encode(['message' => 'Invalid input']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['message' => 'Error: ' . $e->getMessage()]);
}
?>
