<?php

namespace pages {

    use SQLite3;
    use Exception;
    use DateTime;
    use PDO;

    require 'page.php';

    class RegistPage extends Page
    {

        #[Override]
        public function displayBodyContent(): void
        {
            // Обработка формы
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $userType = isset($_POST['type']) ? $_POST['type'] : '';

                if ($userType == "company") {
                    $name = $this->test_input($_POST["company_name"]);
                    $description = $this->test_input($_POST["company_description"]);
                    $link = $this->test_input($_POST["company_link"]);
                    $location = $this->test_input($_POST["company_location"]);
                    $password = $this->test_input($_POST["password"]);

                    $database_file = 'lw1.db';
                    $conn = new SQLite3($database_file);

                    $newpassword = password_hash($password, PASSWORD_DEFAULT);

                    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM company WHERE name = :name");
                    $stmt->bindValue(':name', $name, SQLITE3_TEXT);
                    $result = $stmt->execute();
                    $row = $result->fetchArray(SQLITE3_ASSOC);

                    if ($row['count'] > 0) {
                        echo "Компанія з таким ім'ям вже зареестрована\n\n";
                    } else {
                        $stmt = $conn->prepare("INSERT INTO company (name, description, link, location, password) VALUES (:name, :description, :link, :location, :password)");
                        $stmt->bindValue(':name', $name, SQLITE3_TEXT);
                        $stmt->bindValue(':description', $description, SQLITE3_TEXT);
                        $stmt->bindValue(':link', $link, SQLITE3_TEXT);
                        $stmt->bindValue(':location', $location, SQLITE3_TEXT);
                        $stmt->bindValue(':password', $newpassword, SQLITE3_TEXT);
                        $stmt->execute();

                        echo "Реєстрація компанії пройшла успішно.";

                        header("Location: index.php");
                    }
                    $conn->close();
                } else {
                    $fullname = $this->test_input($_POST["fullname"]);
                    $email = $this->test_input($_POST["email"]);
                    $phone_number = $this->test_input($_POST["phone_number"]);
                    $specialty = $this->test_input($_POST["specialty"]);
                    $password = $this->test_input($_POST["password"]);

                    $database_file = 'lw1.db';
                    $conn = new SQLite3($database_file);

                    $newpassword = password_hash($password, PASSWORD_DEFAULT);


                    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM user WHERE fullname = '$fullname' OR email = '$email'");
                    $result = $stmt->execute();
                    $row = $result->fetchArray(SQLITE3_ASSOC);
                    if ($row['count'] > 0) {
                        echo "Аккаунт на цю пошту, або на таке ім'я вже зареестровано\n\n";
                    } else {

                        $conn->query("INSERT INTO user (fullname, email, phone_number, specialty, password) VALUES
                    ('$fullname', '$email', '$phone_number', $specialty, '$newpassword')");
                        // Дополнительные проверки можно добавить здесь
                        // Например, проверка длины или формата других полей

                        // Если все данные корректны, можно сохранять их в базу данных или выполнять другие действия
                        echo "Реєстрація пройшла успішно.";


                        header("Location: index.php");
                    }
                    $conn->close();
                }
            }


            echo '<body>';
            echo '<div class="container-vacancies-info">';

            // Форма для регистрации нового пользователя или компании
            echo '
<section class="registration">
    <h3>Реєстрація</h3>
    <form action="" method="post" onsubmit="return validateForm()">
        <div id="userFields">
            <label for="fullname">Повне ім`я:</label><br>
            <input type="text" id="fullname" name="fullname"><br><br>
            <label for="email">Електронна пошта:</label><br>
            <input type="text" id="email" name="email"><br><br>
            <label for="phone_number">Номер телефону:</label><br>
            <input type="text" id="phone_number" name="phone_number"><br><br>
            <label for="specialty">Спеціальність:</label><br>
            <input type="text" id="specialty" name="specialty"><br><br>
        </div>
        <div id="companyFields" style="display:none;">
            <label for="company_name">Назва компанії:</label><br>
            <input type="text" id="company_name" name="company_name"><br><br>
            <label for="company_description">Опис компанії:</label><br>
            <textarea id="company_description" name="company_description"></textarea><br><br>
            <label for="company_link">Посилання на веб-сайт:</label><br>
            <input type="text" id="company_link" name="company_link"><br><br>
            <label for="company_location">Місцезнаходження компанії:</label><br>
            <input type="text" id="company_location" name="company_location"><br><br>
        </div>
        
            <label for="password">Пароль:</label><br>
            <input type="password" id="password" name="password"><br><br>

        <label for="type">Тип реєстрації:</label><br>
        <input type="radio" id="user" name="type" value="user" checked onchange="toggleFields()">
        <label for="user">Користувач</label><br>
        <input type="radio" id="company" name="type" value="company" onchange="toggleFields()">
        <label for="company">Компанія</label><br><br>
        <input type="submit" value="Зареєструватися">
    </form>
</section>
';

            // JavaScript для переключения полей в зависимости от выбранного типа регистрации
            echo '
<script>
function toggleFields() {
    var userFields = document.getElementById("userFields");
    var companyFields = document.getElementById("companyFields");
    var userType = document.querySelector(\'input[name="type"]:checked\').value;
    if (userType === "company") {
        userFields.style.display = "none";
        companyFields.style.display = "block";
    } else {
        userFields.style.display = "block";
        companyFields.style.display = "none";
    }
}
</script>
';


            // JavaScript для перевірки електронної адреси
            echo '<script>
    function validateForm() {
        var userType = document.querySelector(\'input[name="type"]:checked\').value;
        if (userType === "user") {
            var emailInput = document.getElementById("email");
            var email = emailInput.value;
            var emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

            var specialtyInput = document.getElementById("specialty");
            var specialty = specialtyInput.value;
            var specialtyPattern = /^\d+$/;

            if (!emailPattern.test(email)) {
                alert("Будь ласка, введіть коректну електронну адресу.");
                return false;
            }

            if (!specialtyPattern.test(specialty)) {
                alert("Спеціальність повинна бути числом.");
                return false;
            }

            return true;
        }
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