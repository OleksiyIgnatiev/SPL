<?php
namespace views {
    require './classes/donat.php';

    use pages\Donat;

    $vac = new Donat();
    $vac->displayHeader();
    $vac->displayBody();
    $vac->displayFooter();
}
?>