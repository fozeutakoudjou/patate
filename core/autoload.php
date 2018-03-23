<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

function autoload($class){
	var_dump(__CLASS__);
	require_once dirname(__FILE__).'/Tools.php';
	$class = core\Tools::getClass($class);
	$file = dirname(__FILE__).'/../'.str_replace('\\', '/', $class);
	require_once $file . '.php';
}

spl_autoload_register('autoload');

require_once dirname(__FILE__).'/../config/config.php';
require_once dirname(__FILE__).'/../config/alias.php';
?>
