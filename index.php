<?php
$request = $_SERVER['REQUEST_URI'];

switch ($request) {
    case '/':
        require __DIR__ . '/views/home.php';
        break;
    case '/vacancies':
        require __DIR__ . '/views/vacancies.php';
        break;
    case '/regist':
        require __DIR__ . '/views/regist.php';
        break;
    case '/TestDoc':
        require __DIR__ . '/views/TestDoc.php';
        break;
    case '/Chat':
        require __DIR__ . '/views/ChatPage.php';
        break;
    case '/VacanciesXML':
        require __DIR__ . '/views/VacanciesXML.php';
        break;
    case '/donat':
        require __DIR__ . '/views/donat.php';
        break;
    case '/block3task8':
        require __DIR__ . '/views/block3task8.php';
        break;
    case '/notification':
        require __DIR__ . '/views/notification.php';
        break;
    case '/TestXML':
        require __DIR__ . '/views/TestXML.php';
        break;
    case '/profile':
        require __DIR__ . '/views/profile.php';
        break;
    case '/Check':
        echo "Hello its login check\n";
        if (isset($_COOKIE['login'])) {
            $login = $_COOKIE['login'];
            echo "Залогиненный пользователь: $login";
        } else {
            echo "Нет сохраненного пользователя";
        }
        break;
    case '/login':
        require __DIR__ . '/views/login.php';
        break;
    default:
        if (preg_match('#^/vacancie/(\d+)$#', $request, $matches)) {
            $vacancieId = $matches[1];
            require __DIR__ . '/views/vacancie.php';
        } else if(preg_match('#^/profileS/(\d+)$#', $request, $matches)) {
            $applicationId = $matches[1];
            require __DIR__ . '/views/profileS.php';
        }
        else if (preg_match('#^/vacancies\?search=#', $request, $matches)) {
            require __DIR__ . '/views/vacancies.php';
        }
        else {
            require __DIR__ . '/views/home.php';
        }
        break;
}

?>

