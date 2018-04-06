<?php
use core\Router;

require dirname(__FILE__).'/core/autoload.php';
Router::getInstance()->dispatch();
