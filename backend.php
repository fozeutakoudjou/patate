<?php

use Applications\Backend\BackendApplication;

require dirname(__FILE__).'/Library/autoload.php';

$app = new BackendApplication;
$app->run();
?>
