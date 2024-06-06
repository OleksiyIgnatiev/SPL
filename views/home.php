<?php

namespace views;

require_once './classes/homePage.php';

use pages\HomePage;

$homePage = new HomePage();
$homePage->displayHeader();
$homePage->displayBodyContent();
$homePage->displayFooter();

?>