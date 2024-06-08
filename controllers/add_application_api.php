<?php

header('Content-Type: application/json');

try {
    $db = new PDO('sqlite:../lw1.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $data = json_decode(file_get_contents('php://input'), true);

    // Дополнительный вывод для отладки
    error_log(print_r($data, true));

    if (isset($data['user_id']) && isset($data['vacancy_id']) && isset($data['description'])) {
        $user_id = $data['user_id'];
        $vacancy_id = $data['vacancy_id'];
        $description = $data['description'];

        // Fetch the company_id from the vacancy table based on vacancy_id
        $query = "SELECT company_id FROM vacancy WHERE vacancy_id = :vacancy_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':vacancy_id', $vacancy_id, PDO::PARAM_INT);
        $stmt->execute();
        
        $company_id = $stmt->fetchColumn();

        if ($company_id) {
            // Insert into application table including company_id
            $sql = "INSERT INTO application (worker_id, vacancy_id, description, company_id) VALUES (:user_id, :vacancy_id, :description, :company_id)";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':vacancy_id', $vacancy_id, PDO::PARAM_INT);
            $stmt->bindParam(':description', $description, PDO::PARAM_STR);
            $stmt->bindParam(':company_id', $company_id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                echo json_encode(['message' => 'New record created successfully']);
            } else {
                http_response_code(500);
                echo json_encode(['message' => 'Error creating record']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'Invalid vacancy_id']);
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
