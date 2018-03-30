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
		return ($isAdmin ? _ADMIN_SUB_FOLDER_ : _FRONT_SUB_FOLDER_).'/';
    }
	
	public static function getOverrideDir($module = '')
    {
		return empty($module) ? _SITE_OVERRIDE_DIR_ : _SITE_OVERRIDE_DIR_._MODULE_FOLDER_NAME_.'/' .$module.'/';
    }
	
	public static function getCoreDir($module = '')
    {
		return empty($module) ? _SITE_CORE_DIR_ : _SITE_MODULES_DIR_ . $module.'/';
    }
	
	public static function getControllerDir($isAdmin, $module = '')
    {
		$subFolder = self::getSubFolder($isAdmin);
		return (empty($module) ? _SITE_CONTROLLER_DIR_ : self::getCoreDir($module) . 'controllers/') . $subFolder;
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
	
	public static function normalizeDirectory($directory)
    {
        return rtrim($directory, '/\\').DIRECTORY_SEPARATOR;
    }
	
	public static function getFinalFile($file, $suffix = '')
    {
		$module = self::getModuleFromFile($file);
		$overrideDir = self::getOverrideDir($module);
		if(strpos($file, $overrideDir)!==0){
			$coreDir = self::getCoreDir($module);
			$overrideFile = str_replace($coreDir, $overrideDir, $file);
			if(file_exists($overrideFile.$suffix)){
				$file = $overrideFile;
			}
		}
		return $file;
	}
	
	public static function getClass($class)
    {
		$file = self::getFileFromNamespace($class);
		$finalFile = self::getFinalFile($file, '.php');
        return self::getNamespaceFromFile($finalFile);
    }
	
	public static function getTplFile($file)
    {
        return self::getFinalFile($file);
    }
	
	public static function getMediaUri($uri)
    {
		$data = Media::getDirFromUri($uri, true);
		$finalFile = self::getFinalFile($data['fileUri']);
		return Media::getUriFormDir($finalFile, $data['urlData']);
    }
	
	public static function getFileFromNamespace($namespace)
    {
		return _SITE_ROOT_DIR_ . '/'. str_replace('\\', '/', $namespace);
	}
	
	public static function getNamespaceFromFile($file)
    {
		$namespace = str_replace(_SITE_ROOT_DIR_ . '/', '', $file);
		return str_replace('/', '\\', $namespace);
	}
	
	public static function getModuleFromNamespace($namespace)
    {
		return self::getModuleFromFile(self::getFileFromNamespace($namespace));
	}
	
	public static function getModuleFromFile($file)
    {
		$module = '';
		$file = $file;
		$moduleKey = _MODULE_FOLDER_NAME_.'/';
		$start = strpos($file, $moduleKey) ;
		if($start !== false){
			$start += strlen($moduleKey);
			$end = strpos($file, '\\', $start);
			$length = $end - $start;
			$module = substr($file, $start, $length);
		}
		return $module;
	}
}
