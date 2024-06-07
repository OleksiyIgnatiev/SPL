<?php
namespace views {
    require './classes/testXML.php';
    $path = trim( $_SERVER[ 'REQUEST_URI' ], '=' );
    $parts = explode( '=', $path );

    use pages\testXML;

    $vac = new testXML();
    $vac->displayHeader();
    $vac->displayBody();
    $vac->displayFooter();
}
?>