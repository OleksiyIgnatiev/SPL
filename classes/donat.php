<?php
namespace pages {
    use SQLite3;
    use Exception;
    use DateTime;
    use PDO;

    require 'page.php';

    class Donat extends Page {

        public function displayBodyContent(): void {
            echo '
        <style>
        /* Стили для формы */
        form {
            width: 300px;
            margin: 0 auto;
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        input[type="text"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        /* Стили для сообщений */
        .message {
            margin-top: 20px;
            padding: 10px;
            border-radius: 5px;
        }

        .success {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
        }

        .error {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }

        </style>
        ';

            if ( $_SERVER[ 'REQUEST_METHOD' ] == 'POST' ) {
                $creditCardNumber = $_POST[ 'credit_card_number' ] ?? '';
                $expiryDate = $_POST[ 'expiry_date' ] ?? '';

                $isValid = $this->checkCreditCard( $creditCardNumber, $expiryDate );

                if ( $isValid ) {
                    echo '<div class="message success">Номер кредитной карты и срок действия верные.</div>';
                } else {
                    echo '<div class="message error">Номер кредитной карты и/или срок действия неверные.</div>';
                }
            } else {
                echo '
            <h3>Проверка номера кредитной карты и срока действия</h3>
            <form method="post" id="credit-card-form">
                <label for="credit_card_number">Номер кредитной карты:</label>
                <input type="text" id="credit_card_number" name="credit_card_number" required>
                <label for="expiry_date">Срок действия (MM/YY):</label>
                <input type="text" id="expiry_date" name="expiry_date" placeholder="MM/YY" required>
                <input type="submit" value="Проверить">
            </form>
            ';

                // JavaScript для автоматической вставки слэша и пробела в поля ввода
                echo '
            <script>
            document.getElementById("credit_card_number").addEventListener("input", function(e) {
                let trimmedValue = e.target.value.replace(/\s/g, "");
                let formattedValue = "";
                for (let i = 0; i < trimmedValue.length; i++) {
                    if (i > 0 && i % 4 === 0) {
                        formattedValue += " ";
                    }
                    formattedValue += trimmedValue[i];
                }
                e.target.value = formattedValue;
            });

            document.getElementById("expiry_date").addEventListener("input", function(e) {
                let trimmedValue = e.target.value.replace(/\//g, "");
                let formattedValue = "";
                for (let i = 0; i < trimmedValue.length; i++) {
                    if (i > 0 && i % 2 === 0) {
                        formattedValue += "/";
                    }
                    formattedValue += trimmedValue[i];
                }
                e.target.value = formattedValue;
            });
            </script>
            ';
            }
        }

        private function checkCreditCard( $creditCardNumber, $expiryDate ) {
            // Удаляем пробелы из номера кредитной карты
            $creditCardNumber = str_replace( ' ', '', $creditCardNumber );

            // Проверяем формат номера кредитной карты
            if ( !preg_match( '/^\d{16}$/', $creditCardNumber ) ) {
                return false;
            }

            // Проверяем формат и актуальность срока действия
            if ( !preg_match( '/^(0[1-9]|1[0-2])\/(\d{2})$/', $expiryDate ) ) {
                return false;
            }

            // Получаем текущую дату
            $currentDate = date( 'm/y' );

            // Разделяем срок действия на месяц и год
            list( $expiryMonth, $expiryYear ) = explode( '/', $expiryDate );
            list( $currentMonth, $currentYear ) = explode( '/', $currentDate );

            // Проверяем, не истек ли срок действия карты
            if ( $expiryYear < $currentYear || ( $expiryYear == $currentYear && $expiryMonth < $currentMonth ) ) {
                return false;
            }

            // Проверяем номер кредитной карты по алгоритму Луна
            $sum = 0;
            for ( $i = 0; $i < 16; $i++ ) {
                $digit = ( int )$creditCardNumber[ 15 - $i ];
                if ( $i % 2 === 1 ) {
                    $digit *= 2;
                    if ( $digit > 9 ) {
                        $digit -= 9;
                    }
                }
                $sum += $digit;
            }
            return ( $sum % 10 === 0 );
        }
    }
}
?>
