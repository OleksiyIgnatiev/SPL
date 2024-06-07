<?php

namespace pages {

    use SQLite3;
    use Exception;
    use DateTime;
    use PDO;

    require 'page.php';

    class Summary extends Page {

        public function displayBodyContent(): void {
            try {
                $db = new SQLite3('your_database.db'); // замените на ваш файл базы данных

                // Получаем информацию о заявке
                $applicationId = $_GET['application_id'] ?? null;
                if ($applicationId !== null) {
                    $stmt = $db->prepare('SELECT * FROM application WHERE application_id = :application_id');
                    $stmt->bindValue(':application_id', $applicationId, SQLITE3_INTEGER);
                    $result = $stmt->execute();
                    $application = $result->fetchArray(SQLITE3_ASSOC);
                    
                    // Выводим информацию о заявке
                    echo '<h2>Application Info</h2>';
                    echo '<p>Application ID: ' . $application['application_id'] . '</p>';
                    echo '<p>Worker ID: ' . $application['worker_id'] . '</p>';
                    echo '<p>Vacancy ID: ' . $application['vacancy_id'] . '</p>';
                    echo '<p>Description: ' . $application['description'] . '</p>';
                    echo '<p>Creation Date: ' . $application['creation_date'] . '</p>';
                }

                // Получаем информацию о вакансии, на которую подана заявка
                $vacancyId = $application['vacancy_id'] ?? null;
                if ($vacancyId !== null) {
                    $stmt = $db->prepare('SELECT * FROM vacancy WHERE vacancy_id = :vacancy_id');
                    $stmt->bindValue(':vacancy_id', $vacancyId, SQLITE3_INTEGER);
                    $result = $stmt->execute();
                    $vacancy = $result->fetchArray(SQLITE3_ASSOC);
                    
                    // Выводим информацию о вакансии
                    echo '<h2>Vacancy Info</h2>';
                    echo '<p>Vacancy ID: ' . $vacancy['vacancy_id'] . '</p>';
                    echo '<p>Description: ' . $vacancy['description'] . '</p>';
                    echo '<p>Is Remote: ' . ($vacancy['is_remote'] ? 'Yes' : 'No') . '</p>';
                    echo '<p>Monthly Salary: ' . $vacancy['monthly_salary'] . '</p>';
                    echo '<p>Worker Competence: ' . $vacancy['worker_competence'] . '</p>';
                    echo '<p>Profile Requirement: ' . $vacancy['profile_requirement'] . '</p>';
                    echo '<p>Language: ' . $vacancy['language'] . '</p>';
                    echo '<p>Is Blocked: ' . ($vacancy['is_blocked'] ? 'Yes' : 'No') . '</p>';
                    echo '<p>Creation Date: ' . $vacancy['creation_date'] . '</p>';
                    echo '<p>Is Closed: ' . ($vacancy['is_closed'] ? 'Yes' : 'No') . '</p>';
                    echo '<p>Location: ' . $vacancy['location'] . '</p>';
                }
                
                $db->close();
            } catch (Exception $e) {
                echo 'Error: ' . $e->getMessage();
            }
        }
    }
}
?>
