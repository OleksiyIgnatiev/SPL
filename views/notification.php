<?php
namespace views {
    require './classes/notification.php';

    use pages\notification;

    $vac = new notification();
    $vac->displayHeader();
    $vac->displayBody();
    $vac->displayFooter();
}
?>