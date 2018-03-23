<?php
namespace core;

class FileTools
{
	const VIRTUAL_CONTROLLERS_FILE = 'virtuals.xml';
    /**
     * Get list of all available controllers
     *
     * @var mixed $dirs
     * @return array
     */
    public static function getControllers($dirs, $isAdmin)
    {
        if (!is_array($dirs)) {
            $dirs = array($dirs);
        }

        $controllers = array();
        foreach ($dirs as $dir) {
            $controllers = array_merge($controllers, self::getControllersInDirectory($dir, $isAdmin));
        }
        return $controllers;
    }

    /**
     * Get list of available controllers from the specified dir
     *
     * @param string $dir Directory to scan (recursively)
     * @return array
     */
    public static function getControllersInDirectory($dir, $isAdmin)
    {
        if (!is_dir($dir)) {
            return array();
        }

        $controllers = array();
        $controller_files = file_exists($dir) ? scandir($dir) :array();
		$controllerSuffix = self::getControllerSuffix($isAdmin);
        foreach ($controller_files as $controller_filename) {
            if ($controller_filename[0] != '.') {
				$isPhpFile = strpos($controller_filename, '.php');
                if (!$isPhpFile && is_dir($dir.$controller_filename)) {
                    $controllers += self::getControllersInDirectory($dir.$controller_filename.DIRECTORY_SEPARATOR, $isAdmin);
                } elseif ($isPhpFile && ($controller_filename != 'index.php')) {
                    $key = str_replace(array(strtolower($controllerSuffix).'.php', '.php'), '', strtolower($controller_filename));
                    $controllers[$key] = $dir . basename($controller_filename, '.php');
                }
            }
        }

        return $controllers;
    }
	
	public static function getRouteFile($isAdmin, $module = '')
    {
		$subFolder = self::getSubFolder($isAdmin);;
		$overrideDir =  self::getOverrideDir($module).'routes/';
		$overrideFiles = self::getRoutesInDir($overrideDir);
		$overrideSubFiles = self::getRoutesInDir($overrideDir.$subFolder);
		
		$defaultDir = (empty($module) ? _SITE_ROUTE_DIR_ : _SITE_MODULES_DIR_ .$module.'/routes/');
		$baseFiles = self::getRoutesInDir($defaultDir, $overrideFiles, $overrideDir);
		$baseSubFiles = self::getRoutesInDir($defaultDir.$subFolder, $overrideSubFiles, $overrideDir.$subFolder);
        return array_merge($baseFiles, $baseSubFiles);
    }
	
	public static function getRoutesInDir($dir, $files = array(), $existListDir = '')
    {
		$list = file_exists($dir) ? scandir($dir) : array();
		if($list){
			foreach ($list as $file) {
				$finalFile = $dir.$file;
				if (!is_dir($finalFile) && strpos($file, '.xml') && (empty($existListDir) ||!in_array(str_replace($dir, $existListDir, $finalFile), $files))) {
					$files[] = $finalFile;
				}
			}
		}
        return $files;
    }
	
	public static function getRouteFiles($isAdmin)
    {
		$filesRoutes = self::getRouteFile($isAdmin);
        $modules = file_exists(_SITE_MODULES_DIR_) ? scandir(_SITE_MODULES_DIR_) : array();
		if($modules){
			foreach ($modules as $module) {
				if (($module[0] != '.') && is_dir(_SITE_MODULES_DIR_.$module)) {
					$files = self::getRouteFile($isAdmin, $module);
					if(!empty($files)){
						$filesRoutes = array_merge($filesRoutes, $files);
					}
				}
			}
		}
        return $filesRoutes;
    }
	
	public static function getControllerSuffix($isAdmin)
    {
		return $isAdmin ? 'AdminController' : 'Controller';
    }
	
	public static function getSubFolder($isAdmin)
    {
		return $isAdmin ? 'backend/' : 'frontend/';
    }
	
	public static function getOverrideDir($module = '')
    {
		return empty($module) ? _SITE_OVERRIDE_DIR_ : _SITE_OVERRIDE_DIR_.'modules/' .$module.'/';
    }
	
	public static function getVirtualControllers($dirs)
    {
		$controllers = array();
		$dirs = is_array($dirs) ? $dirs : array($dirs);
		foreach ($dirs as $dir) {
			$file = $dir .self::VIRTUAL_CONTROLLERS_FILE;
			if(file_exists($file)){
				$dom = new \DOMDocument;
				$dom->load($file);
				$list = $dom->getElementsByTagName('controller');
				foreach ($list as $item){
					$name = $item->getAttribute('name');
					$model = $item->getAttribute('model');
					if(empty($model)){
						$model = Tools::toCamelCase($name, true);
					}
					$controllers[$name] = $model;
				}
			}
		}
        return $controllers;
    }
}
