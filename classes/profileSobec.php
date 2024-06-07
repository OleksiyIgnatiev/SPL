<?php

namespace pages {

    use SQLite3;
    use Exception;
    use DateTime;
    use PDO;

    require 'page.php';

    class ProfileSobec extends Page
    {
       
        private $specifications;
        private $database_file = 'lw1.db';
        private $conn;
        private $user;
        private $applicationId;

        public function __construct($Id)
        {
            $this->applicationId = $Id;
            $this->conn = new SQLite3($this->database_file);
            $this->specifications = require __DIR__ . '\constants\specifications.php';
        }

        private function getUser(): void
        {
            $stmt = $this->conn->prepare('
                SELECT 
                    u.user_id, 
                    u.fullname, 
                    u.email, 
                    u.password, 
                    u.phone_number, 
                    u.specialty, 
                    u.is_blocked, 
                    u.creation_date 
                FROM 
                    "user" u
                JOIN 
                    "application" a ON u.user_id = a.worker_id
                WHERE 
                    a.application_id = :application_id
            ');
            $stmt->bindValue(':application_id', $this->applicationId, SQLITE3_INTEGER);
            $result = $stmt->execute();

            if ($result) {
                $this->user = $result->fetchArray(SQLITE3_ASSOC);
            }
        }

        public function displayBodyContent(): void
        {
            ?>
                <input type='hidden' id='applicationId' value='<?php echo $this->applicationId; ?>'>
            <?php

            $this->getUser();
            if ($this->user) {
                echo "<div class='profile-page'>";
                echo "<div class='page__title'>Профіль користувача " . htmlspecialchars($this->user['fullname']) . "</div>";
                echo "<div class='text'>Електронна пошта: <span>" . htmlspecialchars($this->user['email']) . "</span></div>";
                echo "<div class='text'>Номер телефону: <span>" . htmlspecialchars($this->user['phone_number']) . "</span></div>";
                echo "<div class='text'>Спеціальність: <span>" . htmlspecialchars($this->specifications[$this->user['specialty']]) . "</span></div>";
                echo "<div class='text'>Аккаунт було створено: <span>" . htmlspecialchars($this->user['creation_date']) . "</span></div>"; 
                if ($_COOKIE['type'] == 'company') {
                    echo "<button id='inviteBtn'>Відправити запрошення</button>";
                    echo '</div>';
                    echo "
                    <div id='commentPopup' class='modal'>
                        <div class='modal-content'>
                            <div class='modal-title'>Запрошення на співбесіду</div>
                            <div>
                                <label for='commentInput'>Додайте коментар:</label>
                                <textarea id='commentInput' name='commentInput' class = 'commentInput'></textarea>
                            </div>
                            <div class='buttonRow'>
                                <button type='button' id='okInviteBtn'>Відправити</button>
                                <button type='button' id='cancelInviteBtn'>Відмінити</button>
                            </div>
                        </div>
                    </div>
                    ";
                }
                else{
                    echo '</div>';
                }
            } else {
                echo "<div class='profile-page'>Користувач не знайдений.</div>";
            }
        }
    }
}

?>
