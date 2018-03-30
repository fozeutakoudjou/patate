<?php
function autoload($class){
	require_once dirname(__FILE__).'/FileTools.php';
	$class = core\FileTools::getClass($class);
	$file = dirname(__FILE__).'/../'.str_replace('\\', '/', $class);
	require_once $file . '.php';
}

spl_autoload_register('autoload');

require_once dirname(__FILE__).'/../config/config.php';
require_once dirname(__FILE__).'/../config/alias.php';
?>
