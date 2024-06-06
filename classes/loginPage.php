<?php

namespace pages {

    use SQLite3;
    use Exception;
    use DateTime;
    use PDO;

    require 'page.php';

    class loginPage extends Page
    {

        #[Override]
        public function displayBodyContent(): void
        {
            // Обработка формы
            if ($_SERVER["REQUEST_METHOD"] == "POST") {


                $fullname = $this->test_input($_POST["fullname"]);
                $password = $this->test_input($_POST["password"]);

                $database_file = 'lw1.db';
                $conn = new SQLite3($database_file);
// Проверяем существует ли пользователь с введенным именем
                $stmt = $conn->prepare("SELECT password FROM user WHERE fullname = :fullname");
                $stmt->bindValue(':fullname', $fullname, SQLITE3_TEXT);
                $result = $stmt->execute();
                
                if ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                    $hash = $row["password"];

                    if(password_verify($password, $hash)) {
                        echo 'Данні введено правильно';
                        setcookie('login', $fullname, time() + (86400), "/");
                        setcookie('type', 'user', time() + (86400), "/");
                        
                        header("Location: index.php");
                    } else {
                        echo 'Данні введено неправильно';
                    }
                } 
                else{
                    
                    $stmt = $conn->prepare("SELECT password FROM company WHERE name = :fullname");
                    $stmt->bindValue(':fullname', $fullname, SQLITE3_TEXT);
                    $result = $stmt->execute();
                    if($row = $result->fetchArray(SQLITE3_ASSOC)){
                        $hash = $row["password"];

                        if(password_verify($password, $hash)) {
                            echo 'Данні введено правильно';
                            setcookie('login', $fullname, time() + (86400), "/");
                            setcookie('type', 'company', time() + (86400), "/");
                            
                            header("Location: index.php");
                        } else {
                            echo 'Данні введено неправильно';
                        }
                    }
                    else{
                    echo 'Пользователь не найден';}
                }
            }

            // Початок сторінки
            echo '<body>';
            echo '<div class="container-vacancies-info">';
            // Форма для реєстрації нового користувача
            echo '
        <section class="registration">
            <h3>Реєстрація нового користувача</h3>
            <form action="" method="post" onsubmit="return validateForm() ">
                <label for="fullname">Повне ім`я:</label><br>
                <input type="text" id="fullname" name="fullname" required><br><br>
                <label for="specialty">Пароль:</label><br>
                <input type="text" id="password" name="password"><br><br>
                <input type="submit" value="Залогінитися">
            </form>
        </section>
        ';
            // JavaScript для перевірки електронної адреси
            echo '
        <script>
        function validateForm() {
            var emailInput = document.getElementById("email");
            var email = emailInput.value;
            var emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

            if (!emailPattern.test(email)) {
                alert("Будь ласка, введіть коректну електронну адресу.");
                return false;
            }

            return true;
        }
        </script>
        ';
        }

        private function test_input($data)
        {
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data);
            return $data;
        }
    }
}
?>
