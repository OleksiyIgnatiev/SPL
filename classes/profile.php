<?php

namespace pages {

    use SQLite3;
    use Exception;
    use DateTime;
    use PDO;

    require 'page.php';

    class ProfilePage extends Page
    {

        private $database_file = 'lw1.db';
        private $conn;

        private $user;

        private $company;

        public function __construct()
        {
            $this->conn = new SQLite3($this->database_file);
       
        }



        private function getUser(): void
        {
            $stmt = $this->conn->prepare('SELECT user_id, fullname, email, password, phone_number, specialty, is_blocked, creation_date FROM user WHERE user_id = :user_id');
            $stmt->bindValue(':user_id', $_COOKIE['user_id'], SQLITE3_INTEGER);
            $result = $stmt->execute();
    
            if ($result) {
                $this->user = $result->fetchArray(SQLITE3_ASSOC);
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
            echo "<div class ='page__title'>Профіль користувача ".$this->user['fullname']."</div>";
            echo "<div class ='page__title'>Профіль користувача ".$this->user['fullname']."</div>";
        }

        private function displayBodyToCompany(): void
        {
            echo "Саша не пидор";
        }
    }
}

?>