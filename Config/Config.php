<?php
use core\Context;
$currentDir = dirname(__FILE__);

if( !defined('IN') )
	define('IN', TRUE);
/* Debug only */
define('_CF_MODE_DEV_', true);
if (_CF_MODE_DEV_)
{
	@ini_set('display_errors', 'on');
	//@error_reporting(E_ALL | E_STRICT);
	define('_CF_DEBUG_SQL_', true);
	/* Compatibility warning */
	define('_CF_DISPLAY_COMPATIBILITY_WARNING_', true);
}
else
{
	@ini_set('display_errors', 'off');
	define('_CF_DEBUG_SQL_', false);
	/* Compatibility warning */
	define('_CF_DISPLAY_COMPATIBILITY_WARNING_', false);
}
/* Database*/
define('_DB_SERVER_', 'localhost');
define('_DB_TYPE_', 'mysql');
define('_DB_NAME_', 'patate');
define('_DB_USER_', 'root');
define('_DB_PASSWD_', '');
define('_DB_PORT_', '3306');
define('_DB_PREFIX_', 'c2w_');
define('_MYSQL_ENGINE_', 'InnoDB');

define('_BASE_DIR_', '/patate/');
define('_BASE_URI_', 'http://localhost'._BASE_DIR_);

define('_COOKIE_KEY_', '2f57912d7bd53cc6f8e8af813c8d738a');
define('_COOKIE_IV_', '');

define('_VERSION_', '2.1.0');
/* Constantes principales*/
define('_VIRTUAL_ADMIN_DIR_', 'admin');
define('_URL_WEB_DIR_',  _BASE_URI_.'web/');
define('_LIBRARY_DIR_', _BASE_URI_.'Library/');
define('_APP_DIR_', _BASE_URI_.'Applications/');
define('_MOD_DIR_', _APP_DIR_.'Modules/');
/* Fin Constantes principales*/

define('_BO_TEMPLATES_', 'Default');
define('_FO_TEMPLATES_', 'Default');
/* Gestion des constantes pour le theme */
define('_TEMPLATES_FO_NAME_', 'frontend');
define('_TEMPLATES_BO_NAME_', 'admin');
define('_ASSETS_DIR_',_URL_WEB_DIR_.'assets/');
define('_GLOBAL_ASSETS_DIR',_ASSETS_DIR_.'global/');
define('_THEMES_FO_DIR_',_ASSETS_DIR_._TEMPLATES_FO_NAME_.'/');
define('_THEMES_BO_DIR_',_ASSETS_DIR_._TEMPLATES_BO_NAME_.'/');

define('_UPLOAD_DIR_', _URL_WEB_DIR_.'upload/');
define('_THEME_FO_IMG_DIR_', _THEMES_FO_DIR_.'img/');
define('_THEME_BO_IMG_DIR_', _THEMES_BO_DIR_.'img/');
define('_THEME_FO_JS_DIR_', _THEMES_FO_DIR_.'scripts/');
define('_THEME_BO_JS_DIR_', _THEMES_BO_DIR_.'scripts/');
define('_THEME_BO_JS_MOD_DIR_', _THEME_BO_JS_DIR_.'modules/');
define('_THEME_FO_CSS_DIR_', _THEMES_FO_DIR_.'css/');
define('_THEME_BO_CSS_DIR_', _THEMES_BO_DIR_.'css/');
define('_THEME_FO_MOD_DIR_', _THEMES_FO_DIR_.'modules/');
define('_THEME_BO_MOD_DIR_', _THEMES_BO_DIR_.'modules/');

/* Gestion des constantes assets*/
define('_CSS_DIR_',_GLOBAL_ASSETS_DIR.'css/');
define('_IMG_DIR_',_GLOBAL_ASSETS_DIR.'img/');
define('_PLUGINS_DIR_',_GLOBAL_ASSETS_DIR.'plugins/');
define('_SCRIPTS_DIR_',_GLOBAL_ASSETS_DIR.'scripts/');
/* Fin gestion des constantes assets*/

/* Fin gestion des constantes pour le theme */

/* Directories : repertoire physique, toute constante _SITE_ doit pointer sur un repertoire physique*/
define('_SITE_ROOT_DIR_',    realpath($currentDir.'/..'));
define('_SITE_LIBRARIES_DIR_',_SITE_ROOT_DIR_.'/libraries/');
define('_SITE_CORE_DIR_',_SITE_ROOT_DIR_.'/core/');
define('_SITE_OVERRIDE_DIR_',_SITE_ROOT_DIR_.'/override/');
define('_SITE_CONTROLLER_DIR_',_SITE_CORE_DIR_.'controllers/');
define('_SITE_ROUTE_DIR_',_SITE_CORE_DIR_.'routes/');
define('_SITE_MODULES_DIR_',_SITE_ROOT_DIR_.'/modules/');
define('_SITE_CACHE_DIR_', _SITE_ROOT_DIR_.'/Cache/');
define('_SITE_MAIL_TPL_DIR_', _SITE_ROOT_DIR_.'/Mails/');
define('_SITE_LOG_DIR_', _SITE_ROOT_DIR_.'/log/');

define('_SITE_WEB_DIR_',  _SITE_ROOT_DIR_.'/web/');
define('_SITE_UPLOAD_DIR_',  _SITE_WEB_DIR_.'upload/');
define('_SITE_UPLOAD_TMP_DIR_',  _SITE_UPLOAD_DIR_.'tmp/');
define('_SITE_ASSETS_DIR_',_SITE_WEB_DIR_.'assets/');
define('_SITE_GLOBAL_DIR_', _SITE_ASSETS_DIR_.'global/');
define('_SITE_THEME_FO_DIR_',  _SITE_ASSETS_DIR_._TEMPLATES_FO_NAME_.'/');
define('_SITE_THEME_BO_DIR_',  _SITE_ASSETS_DIR_._TEMPLATES_BO_NAME_.'/');
define('_SITE_THEME_FO_JS_DIR_',  _SITE_THEME_FO_DIR_.'scripts/');
define('_SITE_THEME_BO_JS_DIR_',  _SITE_THEME_BO_DIR_.'scripts/');
define('_SITE_THEME_FO_CSS_DIR_',  _SITE_THEME_FO_DIR_.'css/');
define('_SITE_THEME_BO_CSS_DIR_',  _SITE_THEME_BO_DIR_.'css/');
define('_SITE_THEME_FO_MOD_DIR_',  _SITE_THEME_FO_DIR_.'modules/');
define('_SITE_THEME_BO_MOD_DIR_',  _SITE_THEME_BO_DIR_.'modules/');
/* Gestion des constantes repertoire assets*/
define('_SITE_CSS_DIR_',_SITE_GLOBAL_DIR_.'css/');
define('_SITE_IMG_DIR_',_SITE_GLOBAL_DIR_.'img/');
define('_SITE_PLUGINS_DIR_',_SITE_GLOBAL_DIR_.'plugins/');
define('_SITE_SCRIPTS_DIR_',_SITE_GLOBAL_DIR_.'scripts/');
/* Fin gestion des constantes repertoire assets*/
/* END Directories */

/*configuration des données du site*/
define('__NAME__', 'Crystals Framework');
define('__EMAIL__', 'contact@crystals-services.com');
define('__AUTHOR__', 'Crystals Services Sarl');
define('__AUTHOR_URL__', 'http://crystals-services.com');
define('__DEFAULT_DEVISE__', '€;');
define('__DEFAULT_ISODEVISE__', 'euro');
define('__LIMIT_PER_PAGE__', 8);
define('__CACHE_LIFETIME__', 3600);
define('__TITLE__', 'Crystals framework');
define('__LOGO__', _IMG_DIR_.'logo'._BASE_DIR_.'.png');
define('__LOGO_MAIL__', _IMG_DIR_.'logo-mail'._BASE_DIR_.'.png');
        
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
/*Variable de configuration*/
if (!defined('_MAGIC_QUOTES_GPC_'))
	define('_MAGIC_QUOTES_GPC_',         get_magic_quotes_gpc());

define('_DAO_STRUCTURE_CONCAT_', 1);
define('_DAO_STRUCTURE_FOLDER_', 2);
define('_DAO_STRUCTURE_', _DAO_STRUCTURE_FOLDER_);

Context::getInstance();
?>

