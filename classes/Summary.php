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
                // Подключение к базе данных SQLite
                $db = new PDO('sqlite:lw1.db');
                $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                // Выполнение запроса на выборку данных из таблицы Application с объединением таблицы Vacancy
                $stmt = $db->prepare('SELECT a.worker_id, v.description as vacancy_description, a.description, a.creation_date FROM application a INNER JOIN vacancy v ON a.vacancy_id = v.vacancy_id');
                $stmt->execute();

                // Вывод данных в столбик с добавленными классами
                echo '<div class="summary-container">';
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo '<div class="application-item">';
                    echo '<span class="vacancy-description">Vacancy: ' . $row['vacancy_description'] . '</span><br>';
                    echo '<span class="description">Description: ' . $row['description'] . '</span><br>';
                    echo '<span class="creation-date">Creation Date: ' . $row['creation_date'] . '</span><br>';
                    echo '<div class="button-container">';
                    echo '<button class="approve-button">Approve</button>';
                    echo '<button class="view-button">View</button>';
                    echo '</div>'; // Закрываем button-container
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
