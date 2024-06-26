<?php

namespace pages {

    use SQLite3;
    use Exception;
    use DateTime;
    use PDO;

    class Page
    {

        public function displayHeader()
        {

            echo '<pre>';
            print_r($_COOKIE);
            echo '</pre>';
            // Получаем тип пользователя из куки
            $userType = isset($_COOKIE['type']) ? $_COOKIE['type'] : '';

            echo '
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Сайт</title>
                <link rel="stylesheet" href="../style.css">
            </head>
            <header>
                <div class="header-content">
                    <div class="wrap-logo">
                        <a href="/" class="logo">
                            <img src="/assets/images/Логотип_ДСЗУ.png" alt="WorkStream Logo">
                        </a>
                    </div>
                    <nav>
                        <div class="navcontainer">
                            <a class="nav-item active" href="/">Головна</a>
                            <a class="nav-item" href="/vacancies">Вакансії</a>';

            // Проверяем тип пользователя и отображаем или скрываем пункт "Заявки"
            if ($userType === 'company') {
                echo '<a class="nav-item" href="/Summary">Заявки</a>';
            }

            // Проверяем тип пользователя и отображаем или скрываем пункт "Категория результат"
            if ($userType === 'user') {
                echo '<a class="nav-item" href="/Result">Результат</a>';
            }

            echo '
                            <a class="nav-item" href="/donat">Підтримати проект</a>
                            <a class="nav-item" href="/block3task8">Завдання 8 блок 3</a>
                            <a class="nav-item" href="/VacanciesXML">VacanciesXML</a>
                            <a class="nav-item" href="/TestXML">TestXML</a>
                            <a class="nav-item" href="/Chat">Chat</a>
                        </div>';

            echo '<div class="logout-container">';
            if (isset($_COOKIE['login'])) {
                $login = $_COOKIE['login'];
                echo "<a href=\"/profile\">Профіль $login</a>";
                echo "<form action=\"\" method=\"post\">
                        <input type=\"submit\" name=\"logout\" value=\"Розлогінитися\" class=\"logout-button\">
                      </form>";
            } else {
                echo '<a href="/regist">Реєстрація</a>
                      <a href="/login">Логін</a>';
            }
            echo '</div>';

            echo '
                    </nav>
                </div>
            </header>';

            // Обработка разлогинивания
            if (isset($_POST['logout'])) {
                // Удаление куки 'user_id'
                if (isset($_COOKIE['user_id'])) {
                    setcookie('user_id', '', time() - 3600, '/');
                }

                // Удаление куки 'company_id'
                if (isset($_COOKIE['company_id'])) {
                    setcookie('company_id', '', time() - 3600, '/');
                }

                // Удаление куки 'login'
                if (isset($_COOKIE['login'])) {
                    setcookie('login', '', time() - 3600, '/');
                }

                // Удаление куки 'type'
                if (isset($_COOKIE['type'])) {
                    setcookie('type', '', time() - 3600, '/');
                }

                // Перенаправление на главную страницу или любую другую страницу
                header("Location: index.php"); // Измените index.php на путь к вашей главной странице, если нужно
            }
        }






        public function displayBody()
        {
            echo '<main>';
            echo '<div class = "container">';
            $this->displayBodyContent();
            echo '</div>';
            echo '</main>';
        }

        public function displayFooter()
        {
            // Подключаемся к базе данных SQLite
            $db = new SQLite3('lw1.db');

            // Запрос для получения количества непрочитанных сообщений
            $unreadMessagesQuery = "SELECT COUNT(*) FROM message WHERE is_read = 0";
            $unreadMessagesResult = $db->querySingle($unreadMessagesQuery);

            // Закрываем соединение с базой данных
            $db->close();

            // Определяем класс для кнопки уведомлений в зависимости от количества непрочитанных сообщений
            $buttonClass = ($unreadMessagesResult == 0) ? 'notification-button-empty' : 'notification-button';

            // Отображаем HTML с количеством непрочитанных сообщений и кнопкой уведомлений
            echo '
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Сайт</title>
    <link rel="stylesheet" href="style.css">
    </head>
    <footer>
    <div class="footer-wrapper">
        <div class="footer-section">
            <h3>Контакти</h3>
            <p>Телефон: +380681239070</p>
            <p>Email: info@jobexchange.com
            </p>
            <p>Адреса: вул. Науки, 14, м. Харків
            </p>
        </div>
        <div class="footer-section">
            <h3>Корисні посилання</h3>
            <ul>
                <li><a href="#home">Головна</a></li>
                <li><a href="#about">Про нас</a></li>
                <li><a href="#contact">Контакти</a></li>
            </ul>
        </div>
        <div class="footer-section">
            <div class="' . $buttonClass . ' notification-button">
                <span class="notification-count">' . $unreadMessagesResult . '</span>
                <img src="assets\images\icons8-notification-24.png" alt="Иконка"> <!-- Добавляем иконку -->
            </div>
            <h3>Підписатися на розсилку</h3>
            <form action="#" method="post">
                <input type="email" name="email" placeholder="Електронна пошта">
                <button type="submit">Підписатися</button>
            </form>
        </div>
    </div>
    <div class="copyright">
        <p>&copy; 2024 WorkStream Усі права захищені.</p>
    </div>
    </footer>
    <script src="/assets/js/vacancies.js"></script>
    <script src="/assets/js/vacanciesInfo.js"></script>
    <script src="/assets/js/interview.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Получаем кнопку уведомлений
            var notificationButton = document.querySelector(".notification-button");
            
            // Проверяем, что кнопка существует, чтобы избежать ошибок
            if (notificationButton) {
                // Добавляем обработчик события при нажатии на кнопку
                notificationButton.addEventListener("click", function() {
                    // Перенаправляем пользователя на страницу чата
                    window.location.href = "/Chat";
                });
            }
        });
    </script>
    ';
        }



        protected function displayBodyContent()
        {

        }

    }
}

?>