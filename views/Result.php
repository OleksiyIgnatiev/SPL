<?php
namespace views {
    require './classes/Result.php';

    use pages\Result;

    $vac = new Result();
    $vac->displayHeader();
    $vac->displayBody();
    $vac->displayFooter();
}
?>