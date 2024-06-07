<?php
namespace views {
    require './classes/profileSobec.php';
    $path = trim( $_SERVER[ 'REQUEST_URI' ], '/' );
    $parts = explode( '/', $path );

    use pages\ProfileSobec;

    $page = new ProfileSobec( $parts[ 1 ] );

    $page->displayHeader();
    $page->displayBody();

    $page->displayFooter();
}
?>