<?php
namespace core\dao;
use core\Tools;
use core\FileTools;
use core\StringTools;

abstract class Factory {
    private static $daoClasses = array();
    private static $daoSource;
    
	protected $languages;
	protected $lang;
    public function __construct($connect = true)
    {
        if ($connect) {
            $this->connect();
        }
    }
    
    /**
     * Get source
     * @return array
     */
    protected abstract function getSourceParams();
    
    abstract public function connect();
    
    /**
     * Get instance
     * @return Factory
     */
    public static function getInstance() {
        return self::getDAOClass('Factory');
    }
    
    /**
     * Get instance
     * @return DAO
     */
    public static function getDAOInstance($className, $module = '') {
        $instance = self::getInstance();
        $param = $instance->getSourceParams();
        $param['factory'] = $instance;
        $param['className'] = $className;
        $param['module'] = $module;
        $param['lang'] = $instance->getLang();
        $param['languages'] = $instance->getLanguages();
		$implementation = self::getDAOClass($className.'DAO', _DAO_STRUCTURE_, $module, $param, false);
		if($implementation === null){
			$defaultImplementation = self::getDAOClass('DAO', _DAO_STRUCTURE_, $module, $param, false, true, true, false);
			unset(self::$daoClasses[self::getCacheKey('DAO', $module)]);
			$param['implementation'] = $defaultImplementation;
			$class = self::getDAOClass($className.'DAO', _DAO_STRUCTURE_, $module, $param, true, false);
			$class = ($class === null) ? $defaultImplementation : $class;
			self::$daoClasses[self::getCacheKey($className.'DAO', $module)] = $class;
		}else{
			$class = $implementation;
		}
		return $class;
    }
    private static function getCacheKey($className, $module){
		return $module . $className;
	}
    private static function getDAOClass($className, $daoStructure = _DAO_STRUCTURE_FOLDER_, $module = '', $params = null, $useDefault = true, $useImplementation = true, $throwException = false) {
        $key = self::getCacheKey($className, $module);
        if (!isset(self::$daoClasses[$key]) || (self::$daoClasses[$key] === null)){
            $daoSource = self::getDAOSource();
            $class = '';
			$directories = array(FileTools::getOverrideDir($module). 'dao/', FileTools::getCoreDir($module). 'dao/');
			$finalClassName = '';
			foreach($directories as $directory){
				$namespace = FileTools::getNamespaceFromFile($directory);
				$defaultNamespace = $namespace;
				$defaultClassName = $className;
				if($useImplementation){
					if($daoStructure==_DAO_STRUCTURE_FOLDER_){
						$namespace.=strtolower($daoSource).'\\';
						$sourceClass = $className . $daoSource;
						$fileName = FileTools::getFileFromNamespace($namespace).$sourceClass .'.php';
						$className = (file_exists($fileName)) ? $sourceClass : $className;
					}else{
						$className .= $daoSource;
					}
					$fileName = FileTools::getFileFromNamespace($namespace) . $className . '.php';
				}
				$finalClassName = '';
				if($useImplementation && file_exists($fileName)){
					$finalClassName = $namespace.$className;
				}elseif($useDefault && file_exists(FileTools::getFileFromNamespace($defaultNamespace) . $defaultClassName . '.php')){
					$finalClassName = $defaultNamespace.$defaultClassName;
				}
				if(!empty($finalClassName)){
					break;
				}
			}
            if(empty($finalClassName)){
				if($throwException){
					throw new \Exception('DAO file "'.$className.'" does not exist');
				}else{
					self::$daoClasses[$key] = null;
				}
			}else{
				if ($params===null) {
					self::$daoClasses[$key] = new $finalClassName();
				}else{
					self::$daoClasses[$key] = new $finalClassName($params);
				}
			}
        }
        return self::$daoClasses[$key];
    }
    
    public function escape($string, $html_ok = false, $bq_sql = false)
    {
        if (_MAGIC_QUOTES_GPC_) {
            $string = stripslashes($string);
        }
        
        if (!is_numeric($string)) {
            $string = $this->_escape($string);
            
            if (!$html_ok) {
                $string = strip_tags(StringTools::nl2br($string));
            }
            
            if ($bq_sql === true) {
                $string = str_replace('`', '\`', $string);
            }
        }
        
        return $string;
    }
    
    abstract public function _escape($str);
    
    abstract public function getLastInsertId();
    
    /**
     * Returns the best child layer database class.
     *
     * @return string
     */
    public static function getDAOSource()
    {
        if(!isset(self::$daoSource)){
            self::$daoSource = '';
            if (PHP_VERSION_ID >= 50200 && extension_loaded('pdo_mysql')) {
                self::$daoSource = 'PDO';
            } elseif (extension_loaded('mysqli')) {
                self::$daoSource = 'MySQLi';
            }
            
        }
        if (empty(self::$daoSource)) {
            throw new \Exception('Cannot select any valid SQL engine.');
        }
        
        return self::$daoSource;
    }
	
	public function setLang($lang)
    {
		$this->lang = $lang;
    }
	
	public function setLanguages($languages)
    {
		$this->languages = $languages;
    }
	
	public function getLang()
    {
		return $this->lang;
    }
	
	public function getLanguages()
    {
		return $this->languages;
    }
}

?>
