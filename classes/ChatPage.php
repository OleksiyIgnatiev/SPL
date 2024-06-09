<?php

namespace pages {

    use PDO;

    require 'page.php';
    use SQLite3;

    class ChatPage extends Page
    {
        private $host = "127.0.0.1";
        private $port = 20205;

        private function getStr()
        {
            $dbPath = 'lw1.db';
            $conn = new SQLite3($dbPath);

            $query = "SELECT creation_date, sender, text FROM message";
            $result = $conn->query($query);

            if ($result) {
                $text = "";

                while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                    $formattedText = "[{$row['creation_date']}] {$row['sender']}: {$row['text']}";

                    $text .= $formattedText . "\n";
                }

                $conn->close();

                return $text;
            } else {
                echo "Error executing query: " . $conn->lastErrorMsg();
                return "";
            }
        }

        private function getName()
        {
            $dbPath = 'lw1.db';
            $conn = new SQLite3($dbPath);

            $query = "SELECT sender
                      FROM message
                      WHERE sender != 'server'
                      ORDER BY creation_date DESC
                      LIMIT 1";

            $result = $conn->querySingle($query);

            if ($result) {
                $conn->close();
                return $result;
            } else {
                echo "Error executing query: " . $conn->lastErrorMsg();
                return "";
            }
        }

        public function displayBodyContent(): void
        {
            ?>
            <!DOCTYPE html>
            <html>

            <head>
                <title></title>
            </head>

            <body>

                <div align="center"></div>

                <form method="post" action="">
    <table>
        <tr>
            <td>
                <input type="text" name="txtSender" value="<?php echo $this->getName(); ?>">
                <label>Says that</label>
                <input type="text" name="txtMessage">
                <input type="submit" name="btnSend" value="Send">
            </td>
        </tr>
        <!-- Кнопка для пометки всех сообщений как прочитанных -->
        <tr>
            <td>
            <input type="submit" name="btnMarkAllRead" value="Mark All As Read" class="mark-read-btn">
            </td>
        </tr>

                        <?php

                        $text = $this->getStr();
                        if (isset($_POST['btnSend'])) {

                            $msg = $_REQUEST['txtSender'] . '|' . $_REQUEST['txtMessage'];
                            $sock = socket_create(AF_INET, SOCK_STREAM, 0);
                            socket_connect($sock, $this->host, $this->port);

                            socket_write($sock, $msg, strlen($msg));

                            $reply = socket_read($sock, 1924);

                            // Закрыть сокет после чтения ответа
                            socket_close($sock);

                            $text = $this->getStr();
                        }

                        // Обработчик кнопки "Mark All As Read"
                        if (isset($_POST['btnMarkAllRead'])) {
                            $dbPath = 'lw1.db';
                            $conn = new SQLite3($dbPath);

                            $query = "UPDATE message SET is_read = 1";

                            $result = $conn->exec($query);

                            if ($result) {
                                echo "All messages marked as read successfully.";
                            } else {
                                echo "Error marking messages as read: " . $conn->lastErrorMsg();
                            }

                            $conn->close();
                        }
                        ?>
                        <tr>
                            <td>
                                <textarea rows="30" cols="150"><?php echo @$text; ?></textarea>
                            </td>
                        </tr>
                    </table>
                </form>

            </body>

            </html>
            <?php
        }

    }
}
?>
