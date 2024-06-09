<?php
namespace pages {

    use PDO;
    use Exception;

    require 'page.php';

    class Result extends Page {

        public function displayBodyContent(): void {
            try {
                // Получаем user_id из куки
                if(isset($_COOKIE['user_id'])) {
                    $userId = $_COOKIE['user_id'];
                } else {
                    // Обработка случая, если куки с user_id не установлено
                    echo "Куки с user_id не установлено.";
                    return;
                }

                // Подключение к базе данных SQLite
                $db = new PDO('sqlite:lw1.db'); // Убедитесь, что путь к вашей базе данных правильный
                $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                // Выполнение запроса на выборку данных из таблицы invitation
                $stmt = $db->prepare('SELECT i.invitation_id, i.application_id, i.user_id, i.comment, i.creation_date, v.description AS vacancy_description
                      FROM invitation AS i
                      JOIN application AS a ON i.application_id = a.application_id
                      JOIN vacancy AS v ON a.vacancy_id = v.vacancy_id
                      WHERE i.user_id = :user_id');

                $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
                $stmt->execute();

                // Вывод данных в прямоугольниках с добавленными классами
                echo '<div class="summary-container">';
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo '<div class="application-item2">';
echo '<div class="left-content">'; // контейнер для описания вакансии и даты создания
echo '<span class="vacancy-description">Vacancy Description: ' . htmlspecialchars($row['vacancy_description']) . '</span><br>';
echo '<span class="creation-date2">Creation Date: ' . htmlspecialchars($row['creation_date']) . '</span><br>';
echo '</div>'; // Закрываем left-content
echo '<div class="right-content">'; // контейнер для комментария
echo '<span class="comment2">' . htmlspecialchars($row['comment']) . '</span><br>';
echo '</div>'; // Закрываем right-content
echo '</div>'; // Закрываем application-item

                }
                
                echo '</div>'; // Закрываем summary-container

                // Закрытие соединения с базой данных
                $db = null;
            } catch (PDOException $e) {
                // Обработка ошибок соединения с базой данных
                echo 'Ошибка соединения с базой данных: ' . htmlspecialchars($e->getMessage());
            } catch (Exception $e) {
                // Обработка других исключений
                echo 'Произошла ошибка: ' . htmlspecialchars($e->getMessage());
            }
        }
    }
}
?>
