<?php
namespace views {
    require './classes/vacancies.php';

    use pages\VacanciesPage;

    $vac = new VacanciesPage();
    $vac -> displayHeader();
    $vac -> displayBody();

    $vac -> displayFooter();
}
?>
