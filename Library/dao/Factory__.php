<?php
namespace Library\dao;
use Library\Tools;

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
        $class = self::getDAOClass($className.'DAO', _DAO_STRUCTURE_, $module, $param, true, false);
		if($class === null){
			$class = self::getDAOClass('DAO', _DAO_STRUCTURE_, '', $param, false, true);
		}
		return $class;
    }
    
    private static function getDAOClass($className, $daoStructure = _DAO_STRUCTURE_FOLDER_, $module = '', $params = null, $useDefault = true, $throwException = false) {
        $key = $module.$className;
        if (!isset(self::$daoClasses[$key]) || (self::$daoClasses[$key] === null)){
            $daoSource = self::getDAOSource();
            $class = '';
			$namespace = (empty($module) ? _SITE_LIBRARY_DIR : _SITE_MOD_DIR . $module . '/') . 'dao/';
			$namespace = str_replace(_SITE_ROOT_DIR_ . '/', '', $namespace);
			$namespace = str_replace('/', '\\', $namespace);
            $defaultNamespace = $namespace;
            $defaultClassName = $className;
            if($daoStructure==_DAO_STRUCTURE_FOLDER_){
                $namespace.=Tools::strtolower($daoSource).'\\';
                $sourceClass = $className . $daoSource;
                $fileName = _SITE_ROOT_DIR_ . '/' . str_replace('\\', '/', $namespace) . $sourceClass . '.php';
                $className = (file_exists($fileName)) ? $sourceClass : $className;
            }else{
                $className .= $daoSource;
            }
            $fileName = _SITE_ROOT_DIR_ . '/' . str_replace('\\', '/', $namespace) . $className . '.php';
			$finalClassName = '';
			if(file_exists($fileName)){
				$finalClassName = $namespace.$className;
			}elseif($useDefault && file_exists(_SITE_ROOT_DIR_ . '/' . str_replace('\\', '/', $defaultNamespace) . $defaultClassName . '.php')){
				$finalClassName = $defaultNamespace.$defaultClassName;
			}
			
            if(empty($finalClassName)){
				if($throwException){
					throw new \Exception('DAO file does not exist');
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
                $string = strip_tags(Tools::nl2br($string));
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
