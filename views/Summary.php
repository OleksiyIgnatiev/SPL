<?php
namespace views {
    require './classes/Summary.php';

    use pages\Summary;

    $vac = new Summary();
    $vac->displayHeader();
    $vac->displayBody();
    $vac->displayFooter();
}
?>