<?php
namespace views {
    require './classes/VacanciesXML.php';

    use pages\VacanciesXML;

    $vac = new VacanciesXML();
    $vac->displayHeader();
    $vac->displayBody();
    $vac->displayFooter();
}
?>