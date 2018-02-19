<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once dirname(__FILE__).'/../Config/Config.php';
require_once dirname(__FILE__).'/Lang/fr.php';
require_once dirname(__FILE__).'/Lang/system_fr.php';
require_once dirname(__FILE__).'/Lang/admin_fr.php';

function autoload($class){
    require_once dirname(__FILE__).'/../'.str_replace('\\', '/', $class).'.class.php';
}
spl_autoload_register('autoload');
?>
