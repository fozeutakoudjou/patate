<?php

define('_FRONT_SUB_FOLDER_', 'frontend');
define('_ADMIN_SUB_FOLDER_', 'backend');
define('_OVERRIDE_FOLDER_NAME_', 'override');
define('_MODULE_FOLDER_NAME_', 'modules');
define('_CORE_FOLDER_NAME_', 'core');
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
define('_VERSION_', '1.0.0');
define('_JQUERY_VERSION_', '1.11.0');

/* Directories : repertoire physique, toute constante _SITE_ doit pointer sur un repertoire physique*/
define('_SITE_ROOT_DIR_',    realpath($currentDir.'/..'));
define('_SITE_LIBRARIES_DIR_',_SITE_ROOT_DIR_.'/libraries/');
define('_SITE_CORE_DIR_',_SITE_ROOT_DIR_.'/'._CORE_FOLDER_NAME_.'/');
define('_SITE_CACHE_DIR_',_SITE_ROOT_DIR_.'/cache/');
define('_SITE_OVERRIDE_DIR_',_SITE_ROOT_DIR_.'/'._OVERRIDE_FOLDER_NAME_.'/');
define('_SITE_CONTROLLER_DIR_',_SITE_CORE_DIR_.'controllers/');
define('_SITE_ROUTE_DIR_',_SITE_CORE_DIR_.'routes/');
define('_SITE_MODULES_DIR_',_SITE_ROOT_DIR_.'/'._MODULE_FOLDER_NAME_.'/');
define('_SITE_MAIL_TPL_DIR_', _SITE_ROOT_DIR_.'/Mails/');
define('_SITE_LOG_DIR_', _SITE_ROOT_DIR_.'/log/');

define('_SITE_WEB_DIR_',  _SITE_ROOT_DIR_.'/web/');
define('_SITE_UPLOAD_DIR_',  _SITE_WEB_DIR_.'upload/');
define('_SITE_UPLOAD_TMP_DIR_',  _SITE_UPLOAD_DIR_.'tmp/');
define('_SITE_ASSETS_DIR_',_SITE_WEB_DIR_.'assets/');

/* IMG */
define('_SITE_IMG_DIR_', _SITE_ASSETS_DIR_.'img/');
define('_SITE_FRONT_IMG_DIR_', _SITE_ASSETS_DIR_._FRONT_SUB_FOLDER_.'/');
define('_SITE_ADMIN_IMG_DIR_', _SITE_ASSETS_DIR_._ADMIN_SUB_FOLDER_.'/');

define('_SITE_TPL_DIR_',  _SITE_CORE_DIR_.'templates/');
define('_SITE_FRONT_TPL_DIR_', _SITE_TPL_DIR_._FRONT_SUB_FOLDER_.'/');
define('_SITE_FRONT_THEMES_TPL_DIR_', _SITE_FRONT_TPL_DIR_.'themes/');
define('_SITE_FRONT_THEME_TPL_DIR_', _SITE_FRONT_THEMES_TPL_DIR_._FRONT_THEME_NAME_.'/');

define('_SITE_ADMIN_TPL_DIR_', _SITE_TPL_DIR_._ADMIN_SUB_FOLDER_.'/');
define('_SITE_ADMIN_THEMES_TPL_DIR_', _SITE_ADMIN_TPL_DIR_.'themes/');
define('_SITE_ADMIN_THEME_TPL_DIR_', _SITE_ADMIN_THEMES_TPL_DIR_._ADMIN_THEME_NAME_.'/');