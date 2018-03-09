<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

function autoload($class){
	$file = dirname(__FILE__).'/../'.str_replace('\\', '/', $class);
	if(file_exists($file . '.php')){
		require_once $file . '.php';
	}else{
		require_once $file . '.class.php';
	}
}

spl_autoload_register('autoload');

require_once dirname(__FILE__).'/../Config/Config.php';
require_once dirname(__FILE__).'/../Config/alias.php';
?>
