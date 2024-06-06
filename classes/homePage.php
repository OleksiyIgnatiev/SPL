<?php

namespace pages
 {
    require 'page.php';

    use SQLite3;
    use Exception;
    use DateTime;
    use PDO;

    class HomePage extends Page {

        private $database_file = 'lw1.db';

        private $conn;

        public function __construct() {
            try {
                $this->conn = new SQLite3( $this->database_file );
                $this->conn->exec( 'BEGIN' );
                $this->createTables();
                $this->populateTables();
                $this->conn->exec( 'COMMIT' );
            } catch ( Exception $e ) {
                $this->conn->exec( ' ROLLBACK' );
                echo 'Помилка при створенні БД: ' . $e->getMessage();
            }

            $this->normalizeVacancyFields( new PDO( 'sqlite:lw1.db' ) );
            $this->closeOldVacancies();
        }

        private function closeOldVacancies(): void {
            // Получаем текущую дату
            $currentDate = new DateTime();

            // Запрос всех вакансий
            $stmt = $this->conn->prepare( 'SELECT vacancy_id, creation_date FROM vacancy WHERE is_closed = 0' );
            $result = $stmt->execute();

            // Обработка каждой вакансии
            while ( $row = $result->fetchArray( SQLITE3_ASSOC ) ) {
                $creationDateString = $row[ 'creation_date' ];

                    // Используем регулярное выражение для извлечения даты из строки  
                $creationDate = new DateTime( $matches[ 0 ] );

                // Вычисляем разницу между текущей датой и датой создания вакансии
                $interval = $currentDate->diff( $creationDate );

                // Если разница составляет более 3 месяцев, закрываем вакансию
                if ( $interval->m >= 3 || $interval->y > 0 ) {
                    $stmtUpdate = $this->conn->prepare( 'UPDATE vacancy SET is_closed = 1 WHERE vacancy_id = :vacancy_id' );
                    $stmtUpdate->bindValue( ':vacancy_id', $row[ 'vacancy_id' ], SQLITE3_INTEGER );
                    $stmtUpdate->execute();
                }
            }
        }

        private function normalizeVacancyFields( $db ) {
            // Получение всех записей из таблицы vacancy
            $stmt = $db->query( 'SELECT vacancy_id, description, worker_competence, profile_requirement, language FROM vacancy' );
            $vacancies = $stmt->fetchAll( PDO::FETCH_ASSOC );

            // Регулярное выражение для поиска слов с более чем 4 заглавными буквами
            $pattern = '/\b[A-Z]{4,}\b/';

            // Обход всех записей
            foreach ( $vacancies as $vacancy ) {
                $updated = false;

                // Обход всех полей, которые могут содержать текст с заглавными буквами
                foreach ( [ 'description', 'worker_competence', 'profile_requirement', 'language' ] as $field ) {
                    if ( preg_match_all( $pattern, $vacancy[ $field ], $matches ) ) {
                        foreach ( $matches[ 0 ] as $word ) {
                            // Преобразование слова: первая буква заглавная, остальные строчные
                            $normalizedWord = ucfirst( strtolower( $word ) );
                            // Замена слова в поле
                            $vacancy[ $field ] = str_replace( $word, $normalizedWord, $vacancy[ $field ] );
                        }
                        $updated = true;
                    }
                }

                // Обновление записи в базе данных, если были изменения
                if ( $updated ) {
                    $updateStmt = $db->prepare( 'UPDATE vacancy SET description = :description, worker_competence = :worker_competence, profile_requirement = :profile_requirement, language = :language WHERE vacancy_id = :vacancy_id' );
                    $updateStmt->execute( [
                        ':description' => $vacancy[ 'description' ],
                        ':worker_competence' => $vacancy[ 'worker_competence' ],
                        ':profile_requirement' => $vacancy[ 'profile_requirement' ],
                        ':language' => $vacancy[ 'language' ],
                        ':vacancy_id' => $vacancy[ 'vacancy_id' ]
                    ] );
                }
            }
        }

        private function createTables(): void {
            $this->conn->exec( '
CREATE TABLE IF NOT EXISTS company (
                company_id INTEGER PRIMARY KEY AUTOINCREMENT,
                name VARCHAR(255) NOT NULL,
                password VARCHAR(255) NOT NULL,
                description TEXT,
                link VARCHAR(255),
                location VARCHAR(255),
                creation_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS vacancy (
                vacancy_id          INTEGER         PRIMARY KEY AUTOINCREMENT,
                description         TEXT            NOT NULL,
                is_remote           INTEGER         DEFAULT 0,
                monthly_salary      DECIMAL (10, 2),
                worker_competence   TEXT,
                profile_requirement TEXT,
                language            VARCHAR (255),
                is_blocked          INTEGER         DEFAULT 0,
                creation_date       DATETIME        DEFAULT CURRENT_TIMESTAMP,
                is_closed           INTEGER         DEFAULT 0,
                location            TEXT,
                company_id                          REFERENCES company (company_id) 
            );
            

            CREATE TABLE IF NOT EXISTS user (
                user_id INTEGER PRIMARY KEY AUTOINCREMENT, 
                fullname VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL,
                password VARCHAR(255) NOT NULL,
                phone_number VARCHAR(50),
                specialty INT,
                is_blocked INT DEFAULT 0,
                creation_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS application (
                application_id INT PRIMARY KEY,
                worker_id INT NOT NULL,
                vacancy_id INT NOT NULL,
                description TEXT,
                creation_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (worker_id) REFERENCES user(user_id),
                FOREIGN KEY (vacancy_id) REFERENCES vacancy(vacancy_id)
            );

            CREATE TABLE IF NOT EXISTS message (
                message_id INT PRIMARY KEY,
                application_id INT NOT NULL,
                text TEXT,
                is_read INT DEFAULT 0,
                sender VARCHAR(255),
                creation_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (application_id) REFERENCES application(application_id)
            );
        ' );
        }

        private function populateTables(): void {
            if ( $this->isTableEmpty( 'company' ) ) {
                $this->conn->exec( '
            INSERT INTO company (name, description, link, location, password) VALUES
            ("Tech Solutions", "Innovative tech company", "http://techsolutions.com", "New York, NY", "aaa"),
            ("HealthCorp", "Healthcare services provider", "http://healthcorp.com", "San Francisco, CA", "bbb"),
            ("EduLearn", "Educational platform", "http://edulearn.com", "Austin, TX", "ccc");
        ' );
            }

            if ( $this->isTableEmpty( 'vacancy' ) ) {
                $this->conn->exec( '
            INSERT INTO vacancy (company_id, description, is_remote, monthly_salary, worker_competence, profile_requirement, language) VALUES
            (1, "Software Engineer", 1, 8000.00, "JavaScript, React", "3+ years experience in frontend development", "English"),
            (2, "Data Analyst", 0, 6000.00, "SQL, Python", "2+ years experience in data analysis", "English"),
            (3, "Content Writer", 1, 3000.00, "SEO, Content Creation", "1+ year experience in content writing", "English");
        ' );
            }

            if ( $this->isTableEmpty( 'user' ) ) {
                $this->conn->exec( '
            INSERT INTO user (fullname, email, phone_number, specialty, password) VALUES
            ("John Doe", "john.doe@example.com", "123-456-7890", 1, "aaa"),
            ("Jane Smith", "jane.smith@example.com", "234-567-8901", 2, "bbb"),
            ("Emily Johnson", "emily.johnson@example.com", "345-678-9012", 3, "ccc");
        ' );
            }

            if ( $this->isTableEmpty( 'application' ) ) {
                $this->conn->exec( '
            INSERT INTO application (application_id, worker_id, vacancy_id, description) VALUES
            (1, 1, 1, "I am very interested in this software engineer position."),
            (2, 2, 2, "Looking forward to contributing my data analysis skills to your company."),
            (3, 3, 3, "I have a passion for writing and believe I would be a great fit for this role.");
        ' );
            }

            if ( $this->isTableEmpty( 'message' ) ) {
                $this->conn->exec( '
            INSERT INTO message (message_id, application_id, text, sender) VALUES
            (1, 1, "Thank you for applying. We will review your application.", "HR Manager"),
            (2, 2, "Your application is under consideration.", "HR Manager"),
            (3, 3, "We received your application. Thank you!", "HR Manager");
        ' );
            }
        }

        private function isTableEmpty( $tableName ): bool {
            $result = $this->conn->query( "SELECT COUNT(*) AS count FROM $tableName" );
            $row = $result->fetchArray( SQLITE3_ASSOC );
            return $row[ 'count' ] == 0;
        }

        #[ Override ]

        public function displayBodyContent(): void
 {
            echo '
        <body>
            <div class="container-vacancies-info">
                <section class="intro">
                    <h2>Ласкаво просимо на нашу біржу праці</h2>
                    <p>На нашій біржі праці ви можете знайти вакансії різних компаній та робочі місця, що відповідають вашим потребам та навичкам.</p>
                </section>
                <section class="services">
                    <h3>Наші послуги:</h3>
                    <ul>
                        <li>Пошук вакансій від відомих компаній</li>
                        <li>Підбір робочих місць відповідно до вашого досвіду та кваліфікації</li>
                    </ul>
                </section>
                <section class="contact">
                    <h3>Контакти:</h3>
                    <p>Якщо у вас виникли питання або ви бажаєте скористатися нашими послугами, будь ласка, зв`яжіться з нами:</p>
                    <ul>
                        <li>Телефон: +380681239070</li>
                        <li>Email: info@jobexchange.com</li>
                        <li>Адреса: вул. Науки, 14, м. Харків</li>
                    </ul>
                </section>
            </div>
        </body>
        ';
        }

    }
}
?>