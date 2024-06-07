<?php

namespace pages {

    use SQLite3;
    use Exception;
    use DateTime;
    use PDO;

    class Page
    {

        public function displayHeader() {
            //чтобы видеть что у нас в куки
            echo '<pre>';
            print_r($_COOKIE);
            echo '</pre>';
        
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
                            <img src="assets/images/Логотип_ДСЗУ.png" alt="WorkStream Logo">
                        </a>
                    </div>
                    <nav>
                        <div class="navcontainer">
                            <a class="nav-item active" href="/">Головна</a>
                            <a class="nav-item" href="/vacancies">Вакансії</a>
                            <a class="nav-item" href="/donat">Підтримати проект</a>
                            <a class="nav-item" href="/block3task8">Завдання 8 блок 3</a>
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
        ';
        }

        protected function displayBodyContent()
        {

        }

    }
}

?>