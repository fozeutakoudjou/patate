<?php
use Applications\Frontend\FrontendApplication;
use Library\MobileDetect\MobileDetect;

//dirname(__FILE__).
//require 'Library/autoload.php';
require dirname(__FILE__).'/Library/autoload.php';

$app = new FrontendApplication;
$app->run();
?>
