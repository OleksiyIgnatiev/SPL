<?php

namespace pages {

    use SQLite3;
    use Exception;
    use DateTime;
    use PDO;

    require 'page.php';

    class VacanciesPage extends Page
    {

        private $database_file = 'lw1.db';
        private $conn;
        private $vacancies = [];
        private $companies = [];
        private $mode;
        private function getCompanies(): void
        {
            $result = $this->conn->query('SELECT company_id, name FROM company');
            while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                $this->companies[] = $row;
            }

        }

        public function __construct()
        {
            $this->conn = new SQLite3($this->database_file);

            $this->getVacancies();
            $this->getCompanies();
        }

        private function getVacancies(): void
        {
            if (isset($_COOKIE['type']) && $_COOKIE['type'] == 'company') {
                $company_id = $_COOKIE['company_id'];
            
                // Подготовка и выполнение запроса с использованием параметров
                $stmt = $this->conn->prepare('SELECT v.*, c.name AS company_name FROM vacancy v LEFT JOIN company c ON v.company_id = c.company_id WHERE v.company_id = :company_id');
                $stmt->bindValue(':company_id', $company_id, SQLITE3_INTEGER);
                $result = $stmt->execute();
            
                while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                    $this->vacancies[] = $row;
                }
            } else {
                $result = $this->conn->query('SELECT v.*, c.name AS company_name FROM vacancy v LEFT JOIN company c ON v.company_id = c.company_id WHERE v.is_closed = 0');
                while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                    $this->vacancies[] = $row;
                }
            }
            

        }



        public function displayBodyContent(): void
        {
            if (isset($_COOKIE['type']) && $_COOKIE['type'] == 'company') {
                echo "<div class='page__title'>Вакансії <button id='addVacancyBtn'>Додати вакансію</button></div>";
            } else {
                echo "<div class='page__title'>Вакансії</div>";
            }
            
            echo "<div class = 'vacancies__row'> ";
            foreach ($this->vacancies as $vacancy) {
                echo "
            <div class = 'vacancie_wraper'>
                <a href='/vacancie/{$vacancy['vacancy_id']}' class='vacancie'>
                    <div class='vacancie__title'> {$vacancy['description']}</div>
                    <div class='vacancie__title'> {$vacancy['monthly_salary']} ₴</div>
                    <div class='vacancie__title'>   {$vacancy['company_name']}</div>
                    
                </a>
                ";
                if (isset($_COOKIE['type']) && $_COOKIE['type'] == 'company') {
                    echo "<button class='deleteVacancyBtn' data-id='{$vacancy['vacancy_id']}'>Видалити</button>";
                }
                echo "
       
                </div>
                ";
            }

            echo '</div>';
            echo "
        <div id='myModal' class='modal'>
        <div class='modal-content'>
        <div class='modal-title'>Додати вакасію</div>
            <form id='vacancyForm' action='add_vacancy.php' method='post'>
            <div>
                <label for='description'>Опис:</label>
                <input id='description' name='description' required></input></div>
                <div>
                <label for='is_remote'>Віддалений:</label>
                <input type='checkbox' id='is_remote' name='is_remote'></div>
                <div>
                <label for='monthly_salary'>Щомісячна заробітна плата:</label>
                <input type='number' id='monthly_salary' name='monthly_salary' step='0.01'></div>
                <div>
                <label for='worker_competence'>Компетентність працівників:</label>
                <input id='worker_competence' name='worker_competence'></input></div>
                <div>
                <label for='profile_requirement'>Вимоги до профілю:</label>
                <input id='profile_requirement' name='profile_requirement'></input></div>
                <div>
                <label for='language'>Мова:</label>
                <input type='text' id='language' name='language'></div>
                <div>
                <label for='location'>Місцезнаходження:</label>
                <input id='location' name='location'></input></div>
            </form>
            <div class = 'buttonRow'>
            <button type='submit' id = 'submitBtn'>Створити</button>
            <button type='button' id='closeBtn'>Повернутись</button>
            </div>
        </div>
    </div>
        ";
        }
    }
}

?>