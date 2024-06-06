<?php
namespace views {
    require './classes/registPage.php';

    use pages\RegistPage;

    $vac = new RegistPage();
    $vac->displayHeader();
    $vac->displayBody();
    $vac->displayFooter();
}
?>