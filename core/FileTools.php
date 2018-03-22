<?php
namespace core;

class FileTools
{
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
        foreach ($controller_files as $controller_filename) {
            if ($controller_filename[0] != '.') {
                if (!strpos($controller_filename, '.php') && is_dir($dir.$controller_filename)) {
                    $controllers += self::getControllersInDirectory($dir.$controller_filename.DIRECTORY_SEPARATOR, $controllerSuffix);
                } elseif ($controller_filename != 'index.php') {
                    $key = str_replace(array(strtolower($controllerSuffix).'.php', '.php'), '', strtolower($controller_filename));
                    $controllers[$key] = basename($controller_filename, '.php');
                }
            }
        }

        return $controllers;
    }
	
	protected function getRouteFile($module = '')
    {
		$subFolder = $this->isAdmin ? 'backend/' : 'frontend/';
		$overrideDir = _SITE_OVERRIDE_DIR_ .(empty($module) ? '' : '/modules/' .$module.'/').'routes/';
		$overrideFiles = $this->getRoutesInDir($overrideDir);
		$overrideSubFiles = $this->getRoutesInDir($overrideDir.$subFolder);
		
		$defaultDir = (empty($module) ? _SITE_ROUTE_DIR_ : _SITE_MODULES_DIR_ .$module.'/routes/');
		$baseFiles = $this->getRoutesInDir($defaultDir, $overrideFiles, $overrideDir);
		$baseSubFiles = $this->getRoutesInDir($defaultDir.$subFolder, $overrideSubFiles, $overrideDir.$subFolder);
        return array_merge($baseFiles, $baseSubFiles);
    }
	
	protected function getRoutesInDir($dir, $files = array(), $existListDir = '')
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
	
	protected  function getRouteFiles()
    {
		$filesRoutes = $this->getRouteFile();
        $modules = file_exists(_SITE_MODULES_DIR_) ? scandir(_SITE_MODULES_DIR_) : array();
		if($modules){
			foreach ($modules as $module) {
				if (($module[0] != '.') && is_dir(_SITE_MODULES_DIR_.$module)) {
					$files = $this->getRouteFile($module);
					if(!empty($files)){
						$filesRoutes = array_merge($filesRoutes, $files);
					}
				}
			}
		}
        return $filesRoutes;
    }
	
	protected static function getControllerSuffix($isAdmin)
    {
		return $isAdmin ? 'AdminController' : 'Controller'
    }
	
	protected static function getSubFolder($isAdmin)
    {
		return $isAdmin ? 'backend/' : 'frontend/'
    }
}
