<?php
namespace views {
    require './classes/profile.php';

    use pages\ProfilePage;

    $vac = new ProfilePage();
    $vac -> displayHeader();
    $vac -> displayBody();

    $vac -> displayFooter();
}
?>
