<?php
namespace views {
    require './classes/testXML.php';

    use pages\testXML;

    $vac = new testXML();
    $vac->displayHeader();
    $vac->displayBody();
    $vac->displayFooter();
}
?>