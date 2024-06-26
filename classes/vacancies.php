<?php
namespace pages {
    use SQLite3;
    use Exception;

    require 'page.php';

    class VacanciesPage extends Page
    {
        private $database_file = 'lw1.db';
        private $conn;
        private $vacancies = [];
        private $companies = [];

    

        public function __construct($searchTerm = '')
        {
            $this->conn = new SQLite3($this->database_file);
            $this->getCompanies();
            $this->getVacancies($searchTerm);
        }

        private function getCompanies(): void
        {
            $result = $this->conn->query('SELECT company_id, name FROM company');
            while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                $this->companies[] = $row;
            }
        }

        private function getVacancies($searchTerm = ''): void
        {
            $query = 'SELECT v.*, c.name AS company_name FROM vacancy v LEFT JOIN company c ON v.company_id = c.company_id WHERE v.is_closed = 0';

            if (!empty($searchTerm)) {
                $query .= ' AND c.name LIKE :search';
                $stmt = $this->conn->prepare($query);
                $stmt->bindValue(':search', '%' . $searchTerm . '%', SQLITE3_TEXT);
                $result = $stmt->execute();
            } else {
                $result = $this->conn->query($query);
            }

            while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                $this->vacancies[] = $row;
            }
        }

        public function displayBodyContent(): void
        {
            $search = isset($_GET['search']) ? $_GET['search'] : '';
            echo "<div class='page__title'>Вакансії";
            echo "
            <form method='get' action='/vacancies' class='search-form'>
                <input type='text' name='search' placeholder='Пошук...' class = 'search_input' value='{$search}' />
                <button type='submit' class = 'search_button'>Пошук</button>
            </form>
        ";

            if (isset($_COOKIE['type']) && $_COOKIE['type'] == 'company'){
                echo "<button id='addVacancyBtn' style =  'width: 220px' class = 'search_button'>Додати вакансію</button>";
            }
            echo "</div>";
          
          
        

          
            echo "<div class='vacancies__row'>";

            foreach ($this->vacancies as $vacancy) {
                $is_company = isset($_COOKIE['type']) && $_COOKIE['type'] == 'company' &&  $_COOKIE['company_id'] == $vacancy['company_id'];
                echo "
           <div class='vacancie_wraper'>
                <a href='/vacancie/{$vacancy['vacancy_id']}' class='vacancie " . ($is_company ? "vacancie_wraper_company" : "") . "'>
                    <div class='vacancie__title'> {$vacancy['description']}</div>
                    <div class='vacancie__title'> {$vacancy['monthly_salary']} ₴</div>
                    <div class='vacancie__title'>   {$vacancy['company_name']}</div>
                    
                </a>
                ";

                if ($is_company) {
                    echo "<button class='editVacancyBtn' data-data='".json_encode($vacancy)."'>Редагувати</button>";
                    echo "<button class='deleteVacancyBtn' data-id='{$vacancy['vacancy_id']}'>Видалити</button>";
                }
              
                echo "
       
                </div>
                ";
            }

            echo '</div>';

            // Вывод модального окна для добавления вакансии
            echo "
                <div id='myModal' class='modal'>
                    <div class='modal-content'>
                        <div class='modal-title'>Додати вакасію</div>
                        <form id='vacancyForm' action='add_vacancy.php' method='post'>
     
                            <div>
                                <label for='description'>Опис:</label>
                                <input id='description' name='description' required></input>
                            </div>
                            <div>
                                <label for='is_remote'>Віддалений:</label>
                                <input type='checkbox' id='is_remote' name='is_remote'>
                            </div>
                            <div>
                                <label for='monthly_salary'>Щомісячна заробітна плата:</label>
                                <input type='number' id='monthly_salary' name='monthly_salary' step='0.01'>
                            </div>
                            <div>
                                <label for='worker_competence'>Компетентність працівників:</label>
                                <input id='worker_competence' name='worker_competence'>
                            </div>
                            <div>
                                <label for='profile_requirement'>Вимоги до профілю:</label>
                                <input id='profile_requirement' name='profile_requirement'>
                            </div>
                            <div>
                                <label for='language'>Мова:</label>
                               
                            <input type='text' id='language' name='language'>
                            </div>
                            <div>
                                <label for='location'>Місцезнаходження:</label>
                                <input id='location' name='location'>
                            </div>
                        </form>
                        <div class='buttonRow'>
                            <button type='submit' id='submitBtn' data-action = 'add'>Створити</button>
                            <button type='button' id='closeBtn'>Повернутись</button>
                        </div>
                    </div>
                </div>
                
            ";
        }

      
    }
}
?>