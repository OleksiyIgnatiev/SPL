<?php

namespace pages {

    use SQLite3;
    use Exception;
    use DateTime;
    use PDO;

    require 'page.php';

    class ProfilePage extends Page
    {
       
        private $specifications;
        private $database_file = 'lw1.db';
        private $conn;

        private $user;

        private $company;

        public function __construct()
        {
            $this->conn = new SQLite3($this->database_file);
            $this->specifications = require __DIR__ . '\constants\specifications.php';

        }



        private function getUser(): void
        {
            $stmt = $this->conn->prepare('SELECT user_id, fullname, email, phone_number, specialty, is_blocked, creation_date FROM user WHERE user_id = :user_id');
            $stmt->bindValue(':user_id', $_COOKIE['user_id'], SQLITE3_INTEGER);
            $result = $stmt->execute();
    
            if ($result) {
                $this->user = $result->fetchArray(SQLITE3_ASSOC);
            }
        }

        
        private function getCompany(): void 
        {
            $stmt = $this->conn->prepare('SELECT company_id,name, description,link,location,creation_date FROM company WHERE company_id = :company_id');
            $stmt->bindValue(':company_id', $_COOKIE['company_id'], SQLITE3_INTEGER);
            $result = $stmt->execute();
    
            if ($result) {
                $this->company = $result->fetchArray(SQLITE3_ASSOC);
            }
        }

        public function displayBodyContent(): void
        {
            //$_COOKIE['type'] !== 'user'
            if ($_COOKIE['type'] == 'user') {
                $this->displayBodyToUser();
            } else {

                $this->displayBodyToCompany();
            }
        }


        private function displayBodyToUser(): void
        {
            $this->getUser();
            echo "<div class = 'profile-page'>";
            echo "<div class ='page__title'>Профіль користувача ".$this->user['fullname']."</div>";
            echo "<div class ='text' >Електронна пошта: <span>".$this->user['email']." </span></div>";
            echo "<div class ='text' >Номер телефону: <span>".$this->user['phone_number']."</span></div>";
            echo "<div class ='text' >Номер телефону: <span>". $this-> specifications[$this->user['specialty']]."</span></div>";
            echo "<div class ='text' >Аккаунт було ствоерно: <span>". $this->user['creation_date']."</span></div>"; 
            echo '</div>';
        }

        private function displayBodyToCompany(): void
        {
            $this->getCompany();
            echo "<div class = 'profile-page'>";
            echo "<div class ='page__title'>Профіль компанії ".$this->company['name']."</div>";
            echo "<div class ='text'>Опис компанії: </br>".$this->company['description']." </div>";
            echo "<div class ='text'>Росположення компанії: <span>".$this->company['location']." </span></div>";
            echo "<a class ='text' href='".$this->company['link']."'>Сайт компанії</a>"; 
            echo '</div>';
        }
    }
}

?>