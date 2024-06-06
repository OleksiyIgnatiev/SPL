<?php

header('Content-Type: application/json');

try {
    $db = new PDO('sqlite:../lw1.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $data = json_decode(file_get_contents('php://input'), true);

    // Дополнительный вывод для отладки
    error_log(print_r($data, true));

    if (/*isset($data['worker_id']) && */isset($data['vacancy_id']) && isset($data['description'])) {
        $worker_id = 1;
        $vacancy_id = $data['vacancy_id'];
        $description = $data['description'];

        $sql = "INSERT INTO application (worker_id, vacancy_id, description) VALUES (:worker_id, :vacancy_id, :description)";

        $stmt = $db->prepare($sql);
        $stmt->bindParam(':worker_id', $worker_id, PDO::PARAM_INT);
        $stmt->bindParam(':vacancy_id', $vacancy_id, PDO::PARAM_INT);
        $stmt->bindParam(':description', $description, PDO::PARAM_STR);

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
