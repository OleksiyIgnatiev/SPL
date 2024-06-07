<?php
namespace views {
    require './classes/vacancies.php';
    use pages\VacanciesPage;

    // Получение части запроса из URL
    $path = trim($_SERVER['REQUEST_URI'], '/');
    $parts = explode('/', $path);

    // Инициализация объекта VacanciesPage с передачей параметра поиска
    $searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
    $page = new VacanciesPage($searchTerm);

    // Отображение шапки, тела и подвала страницы
    $page->displayHeader();
    $page->displayBody();
    $page->displayFooter();
}
?>
