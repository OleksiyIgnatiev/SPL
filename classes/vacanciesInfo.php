<?php

namespace pages {
    
    use SQLite3;
    use Exception;
    use DateTime;
    use PDO;

    require 'page.php';

    class VacanciesInfo extends Page {
        private $vacancyId;
        private $db;

        public function __construct( $id ) {
            $this->vacancyId = $id;
            $this->db = new PDO( 'sqlite:lw1.db' );
            $this->db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
        }

        public function displayBodyContent() {
            try {
                // Начинаем транзакцию
                $this->db->beginTransaction();

                // Получение информации о вакансии по её ID
                $vacancyId = $this->vacancyId;

                // Подготовленный запрос для получения информации о вакансии
                $stmt = $this->db->prepare( '
                    SELECT v.*, c.name AS company_name
                    FROM vacancy v
                    LEFT JOIN company c ON v.company_id = c.company_id
                    WHERE v.vacancy_id = :id
                ' );
                $stmt->bindParam( ':id', $vacancyId, PDO::PARAM_INT );
                $stmt->execute();

                // Если вакансия найдена, выводим информацию
                if ( $row = $stmt->fetch( PDO::FETCH_ASSOC ) ) {
                    ?>
                    <input type='hidden' id='vacancyId' value='<?php echo $this->vacancyId; ?>'>
                    <?php

                    echo '<div class="container-vacancies-info">';
                    echo '<h1 class="vacancy-title">' . $row[ 'description' ] . '</h1>';
                    echo '<p class="vacancy-info"><strong class="vacancy-label">Работодатель:</strong> ' . $row[ 'company_name' ] . '</p>';
                    echo '<p class="vacancy-info"><strong class="vacancy-label">Зарплата:</strong> ' . $row[ 'monthly_salary' ] . '</p>';
                    echo '<p class="vacancy-info"><strong class="vacancy-label">Часы работы:</strong> ' . $row[ 'opening_hours' ] . '</p>';
                    echo '<p class="vacancy-info"><strong class="vacancy-label">Место работы:</strong> ' . $row[ 'location' ] . '</p>';
                    echo '<p class="vacancy-info"><strong class="vacancy-label">Возможность удалённой работы:</strong> ' . ( $row[ 'is_remote' ] ? 'Да' : 'Нет' ) . '</p>';
                    echo '<p class="vacancy-info"><strong class="vacancy-label">Описание:</strong> ' . $row[ 'description' ] . '</p>';

                    // Закрываем контейнер
                    echo "<button id='openPopupBtn'>Відправити заявку</button>";
                    echo '</div>';
                    echo "
                    <div id='commentPopup' class='modal'>
                        <div class='modal-content'>
                            <div class='modal-title'>Заявка на вакансію</div>
                            <div>
                                <label for='commentInput'>Роскажіть про себе:</label>
                                <textarea id='commentInput' name='commentInput' class = 'commentInput'></textarea>
                            </div>
                            <div class='buttonRow'>
                                <button type='button' id='okBtn'>Відправити</button>
                                <button type='button' id='cancelBtn'>Відмінити</button>
                            </div>
                        </div>
                    </div>
                    ";






                    // Додавання поточного часу в кінець файлу
                    echo '<p>Поточний час: ' . date( 'Y-m-d H:i:s' ) . '</p>';
                } else {
                    echo 'Вакансия не найдена';
                }

                // Здесь можно добавить другие изменения в базу данных в рамках транзакции

                // Подтверждаем транзакцию
                $this->db->commit();

            } catch( PDOException $e ) {
                // Откатываем транзакцию в случае ошибки
                $this->db->rollback();
                echo 'Подключение к базе данных не удалось: ' . $e->getMessage();
            }
        }

        public function insertVacancy( $description, $company_id, $monthly_salary, $opening_hours, $location, $is_remote ) {
            try {
                // Початок транзакції
                $this->db->beginTransaction();

                // Підготовлений запит для вставки вакансії
                $stmt = $this->db->prepare( 'INSERT INTO vacancy (description, company_id, monthly_salary, opening_hours, location, is_remote) VALUES (:description, :company_id, :monthly_salary, :opening_hours, :location, :is_remote)' );
                $stmt->bindParam( ':description', $description );
                $stmt->bindParam( ':company_id', $company_id );
                $stmt->bindParam( ':monthly_salary', $monthly_salary );
                $stmt->bindParam( ':opening_hours', $opening_hours );
                $stmt->bindParam( ':location', $location );
                $stmt->bindParam( ':is_remote', $is_remote, PDO::PARAM_BOOL );
                // Якщо is_remote - булеве значення

                // Виконуємо запит
                $stmt->execute();

                // Підтвердження транзакції
                $this->db->commit();

                echo 'Вакансія успішно додана';

            } catch( PDOException $e ) {
                // Відкат транзакції у випадку помилки
                $this->db->rollback();
                echo 'Помилка при вставці в базу даних: ' . $e->getMessage();
            }
        }
    }
}
?>