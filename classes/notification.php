<?php

namespace pages {

    use SQLite3;
    use Exception;
    use DateTime;
    use PDO;

    require 'page.php';

    class Notification extends Page {

        // Функція для відправлення сповіщення користувачеві
        public function sendNotification($userId, $message) {
            try {
                // Підключення до бази даних SQLite
                $db = new SQLite3('C:\\OSPanel\\home\\WorkStream\\lw1.db');
                
                // Використання підготовлених запитів для безпеки
                $stmt = $db->prepare('INSERT INTO message (application_id, text, sender, creation_date) VALUES (:userId, :message, :sender, :date)');
                $stmt->bindValue(':userId', $userId, SQLITE3_INTEGER);
                $stmt->bindValue(':message', $message, SQLITE3_TEXT);
                $stmt->bindValue(':sender', 'Admin', SQLITE3_TEXT);
                $stmt->bindValue(':date', (new DateTime())->format('Y-m-d H:i:s'), SQLITE3_TEXT);
                
                // Виконання запиту
                $stmt->execute();
                
                echo "Notification sent successfully.";
            } catch (Exception $e) {
                echo "Error: " . $e->getMessage();
            }
        }

        // Приклад функції для сповіщення всіх користувачів про нову статтю
        public function notifyUsersAboutNewArticle($articleId) {
            try {
                // Підключення до бази даних SQLite
                $db = new SQLite3('C:\\OSPanel\\home\\WorkStream\\lw1.db');
                
                // Вибір усіх користувачів, які не заблоковані
                $users = $db->query('SELECT user_id FROM user WHERE is_blocked = 0');

                // По черзі відправляємо сповіщення кожному користувачеві
                while ($user = $users->fetchArray()) {
                    $this->sendNotification($user['user_id'], "A new article has been published! Check it out.");
                }
                
                echo "Notifications sent to all users.";
            } catch (Exception $e) {
                echo "Error: " . $e->getMessage();
            }
        }
    }

    // Створення об'єкту класу Notification
    $notification = new Notification();

    // Обробка введених користувачем даних
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $userId = $_POST["userId"];
        $message = $_POST["message"];
        
        // Виклик функції sendNotification з введеними користувачем даними
        $notification->sendNotification($userId, $message);
    }

    // HTML форма для введення даних користувачем
    ?>
    <form method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
        <label for="userId">User ID:</label><br>
        <input type="text" id="userId" name="userId"><br>
        <label for="message">Message:</label><br>
        <textarea id="message" name="message"></textarea><br><br>
        <input type="submit" value="Send Notification">
    </form>
    <?php
}

?>
