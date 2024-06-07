<?php

header('Content-Type: application/json');

try {
    $db = new PDO('sqlite:../lw1.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $data = json_decode(file_get_contents('php://input'), true);

    // Додатковий вивід для налагодження
    error_log(print_r($data, true));

    if (isset($data['vacancy_id']) && isset($data['description'])) {
        $vacancy_id = $data['vacancy_id'];
        $description = $data['description'];
        $is_remote = isset($data['is_remote']) ? 1 : 0;
        $monthly_salary = $data['monthly_salary'] ?? null;
        $worker_competence = $data['worker_competence'] ?? null;
        $profile_requirement = $data['profile_requirement'] ?? null;
        $language = $data['language'] ?? null;
        $location = $data['location'] ?? null;

        $sql = "UPDATE vacancy 
                SET description = :description, 
                    is_remote = :is_remote, 
                    monthly_salary = :monthly_salary, 
                    worker_competence = :worker_competence, 
                    profile_requirement = :profile_requirement, 
                    language = :language, 
                    location = :location 
                WHERE vacancy_id = :vacancy_id";

        $stmt = $db->prepare($sql);
        $stmt->bindParam(':vacancy_id', $vacancy_id, PDO::PARAM_INT);
        $stmt->bindParam(':description', $description, PDO::PARAM_STR);
        $stmt->bindParam(':is_remote', $is_remote, PDO::PARAM_INT);
        $stmt->bindParam(':monthly_salary', $monthly_salary, PDO::PARAM_STR);
        $stmt->bindParam(':worker_competence', $worker_competence, PDO::PARAM_STR);
        $stmt->bindParam(':profile_requirement', $profile_requirement, PDO::PARAM_STR);
        $stmt->bindParam(':language', $language, PDO::PARAM_STR);
        $stmt->bindParam(':location', $location, PDO::PARAM_STR);

        if ($stmt->execute()) {
            echo json_encode(['message' => 'Record updated successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Error updating record']);
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
