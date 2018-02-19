<?php
/**
* Description of LangManager
*
* @author Luc Alfred MBIDA
*/
namespace Applications\Modules\Lang\Models;

if( !defined('IN') ) die('Hacking Attempt');

use Library\Manager;

abstract class LangManager extends Manager{
	protected $name = 'Applications\Modules\Lang\Models\Lang';
	protected $nameTable = 'lang';
	// Inserer votre code ici
}
?>