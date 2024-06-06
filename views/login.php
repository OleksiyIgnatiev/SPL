<?php
namespace views {
    require './classes/loginPage.php';

    use pages\loginPage;

    $vac = new loginPage();
    $vac->displayHeader();
    $vac->displayBody();
    $vac->displayFooter();
}
?>