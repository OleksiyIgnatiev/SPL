<?php
namespace views {
    require './classes/TestDoc.php';

    use pages\TestDoc;

    $vac = new TestDoc();
    $vac->displayHeader();
    $vac->displayBody();
    $vac->displayFooter();
}
?>