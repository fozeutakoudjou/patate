<?php
use core\Context;
$currentDir = dirname(__FILE__);
require_once($currentDir.'/settings.php');
require_once($currentDir.'/defines.php');
require_once($currentDir.'/defines_uri.php');

if (!defined('_MAGIC_QUOTES_GPC_'))
	define('_MAGIC_QUOTES_GPC_',         get_magic_quotes_gpc());

define('_DAO_STRUCTURE_CONCAT_', 1);
define('_DAO_STRUCTURE_FOLDER_', 2);
define('_DAO_STRUCTURE_', _DAO_STRUCTURE_FOLDER_);
Context::getInstance();
?>

