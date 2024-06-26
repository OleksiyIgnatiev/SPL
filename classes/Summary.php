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
                // Получаем ID компании из куки
                if(isset($_COOKIE['company_id'])) {
                    $companyId = $_COOKIE['company_id'];
                } else {
                    // Обработка случая, если куки с ID компании не установлено
                    echo "Куки с ID компании не установлено.";
                    return;
                }

                // Подключение к базе данных SQLite
                $db = new PDO('sqlite:lw1.db');
                $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                // Выполнение запроса на выборку данных из таблицы Application с объединением таблицы Vacancy
                $stmt = $db->prepare('SELECT a.worker_id, a.application_id, u.fullname as worker_name, v.description as vacancy_description, a.description, a.creation_date 
                FROM application a 
                INNER JOIN vacancy v ON a.vacancy_id = v.vacancy_id 
                INNER JOIN user u ON a.worker_id = u.user_id
                WHERE a.company_id = :company_id');

                $stmt->bindParam(':company_id', $companyId, PDO::PARAM_INT);
                $stmt->execute();

                // Вывод данных в столбик с добавленными классами
                echo '<div class="summary-container">';
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo '<div class="application-item">';

echo '<span class="vacancy-description">Vacancy: ' . htmlspecialchars($row['vacancy_description']) . '</span><br>';
echo '<span class="description">Description: ' . htmlspecialchars($row['description']) . '</span><br>';
echo '<span class="worker-name">User: ' . htmlspecialchars($row['worker_name']) . '</span><br>'; // Вывод имени пользователя
echo '<span class="creation-date">Creation Date: ' . htmlspecialchars($row['creation_date']) . '</span><br>';

echo '<div class="button-container">'; // Начало контейнера для кнопки
echo '<a class="view-button" href="/profileS/' . $row['application_id'] . '">View</a>';
echo '</div>'; // Закрываем контейнер для кнопки
echo '</div>'; // Закрываем application-item

                }
                echo '</div>'; // Закрываем summary-container


                // Закрытие соединения с базой данных
                $db = null;
            } catch (PDOException $e) {
                // Обработка ошибок соединения с базой данных
                echo 'Ошибка соединения с базой данных: ' . $e->getMessage();
            } catch (Exception $e) {
                // Обработка других исключений
                echo 'Произошла ошибка: ' . $e->getMessage();
            }
        }
    }
}
?>
