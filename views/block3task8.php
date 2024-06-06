<?php
namespace views {
    require './classes/block3task8.php';

    use pages\TaskPage;
    
    $vac = new TaskPage();
    $vac->displayHeader();
    $vac->displayBody();
    $vac->displayFooter();
}
?>