<?php
namespace views {
    require './classes/ChatPage.php';

    use pages\ChatPage;


    $vac = new ChatPage();

    $vac->displayHeader();
    $vac->displayBody();
    $vac->displayFooter();
}
?>
