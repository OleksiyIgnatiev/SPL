<?php
namespace views {
    require './classes/vacanciesInfo.php';
    $path = trim( $_SERVER[ 'REQUEST_URI' ], '/' );
    $parts = explode( '/', $path );

    use pages\VacanciesInfo;

    $page = new VacanciesInfo( $parts[ 1 ] );

    $page->displayHeader();
    $page->displayBody();

    $page->displayFooter();
}
?>