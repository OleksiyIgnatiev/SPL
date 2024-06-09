<?php

$host = "127.0.0.1";
$port = "20205";
set_time_limit(0);

$sock = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not create socket\n");
$result = socket_bind($sock, $host, $port) or die("Could not bind to socket\n");

$result = socket_listen($sock, 3) or die("Could not setup socket listener\n");

class Chat {
    function readline() {
        return rtrim(fgets(STDIN));
    }
}

do {

    $accept = socket_accept($sock);
    if (!$accept) {
        die("Could not accept incoming connection\n");
    }

    $msg = socket_read($accept, 1024);
    if (!$msg) {
        die("Could not read input\n");
    }

    $words = explode("|", $msg);

    $sender = $words[0];
    $msgText = $words[1];

    // Безопасное подключение к базе данных и выполнение запроса
    $dbPath = 'C:\OSPanel\home\SPL\lw1.db';
    $conn = new SQLite3($dbPath);
    if (!$conn) {
        die("Connection failed: Unable to open database.");
    }

    // Защита от SQL-инъекций
    $msgText = $conn->escapeString($msgText);
    $sql = "
        INSERT INTO message (application_id, text, sender) VALUES
        (1, '$msgText', '$sender')";
    $conn->query($sql);

    // Получение ID последней вставленной записи
    $conn->lastInsertRowID();

    // Закрытие соединения с базой данных
    $conn->close();

    echo "$sender Says:\t".$msgText."\n\n";

    $line = new Chat();
    echo "Enter reply:\t";
    $reply = $line->readline();

    $conn = new SQLite3($dbPath);
    if (!$conn) {
        die("Connection failed: Unable to open database.");
    }

    // Защита от SQL-инъекций
    $msgText = $conn->escapeString($msgText);
    $sql = "
        INSERT INTO message (application_id, text, sender) VALUES
        (1, '$reply', 'server')";
    $conn->query($sql);

    // Получение ID последней вставленной записи
    $id = $conn->lastInsertRowID();

    // Закрытие соединения с базой данных
    $conn->close();

    // Отправка ответа клиенту
    socket_write($accept, $reply, strlen($reply)) or die("Could not write output\n");

    // Закрытие соединения
    socket_close($accept);
} while(true);

// Закрытие сокета (не дойдет до этой строки, так как скрипт работает в бесконечном цикле)
socket_close($sock);

?>
