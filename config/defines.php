<?php

define('_FRONT_SUB_FOLDER_', 'frontend');
define('_ADMIN_SUB_FOLDER_', 'backend');
define('_OVERRIDE_PATH_', 'override');
define('_MODULES_PATH_', 'modules');
define('_TEMPLATES_PATH_', 'templates');
define('_CONTROLLERS_PATH_', 'controllers');
define('_CORE_PATH_', 'core');
define('_ROUTES_PATH_', 'routes');
define('_LIBRARIES_PATH_', 'libraries');


define('_WEB_PATH_', 'web');
define('_ASSETS_PATH_', _WEB_PATH_.'/assets');
define('_UPLOAD_PATH_', _WEB_PATH_.'/upload');
define('_UPLOAD_TMP_PATH_',  _UPLOAD_PATH_.'/tmp');

/* JS */
define('_JS_PATH_', _ASSETS_PATH_.'/js');
define('_ASSET_LIBRARIES_PATH_', _ASSETS_PATH_.'/libraries');

/* CSS */
define('_CSS_PATH_', _ASSETS_PATH_.'/css');

/* Image URLs */
define('_IMG_PATH_', _ASSETS_PATH_.'/img');
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

define('_SITE_ROOT_DIR_',    realpath($currentDir.'/..').DIRECTORY_SEPARATOR);


define('_SITE_CACHE_DIR_',_SITE_ROOT_DIR_.'cache/');
define('_SITE_OVERRIDE_DIR_',_SITE_ROOT_DIR_._OVERRIDE_PATH_.'/');
define('_SITE_MODULES_DIR_',_SITE_ROOT_DIR_._MODULES_PATH_.'/');