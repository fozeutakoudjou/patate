<?php
namespace dao;
use utilities\Tools;

abstract class DbFactory {
    private static $daoClasses = array();
    private static $daoSource;
    
    protected $server;
    protected $user;
    protected $password;
    protected $database;
    protected $port;
    
    public function __construct($connect = true)
    {
        $this->server = _DB_SERVER_;
        $this->port = _DB_PORT_;
        $this->user = _DB_USER_;
        $this->password = _DB_PASSWD_;
        $this->database = _DB_NAME_;
        if ($connect) {
            $this->connect();
        }
    }
    
    
    /**
     * Get pdo
     * @return \PDO
     */
    /*public function getDbConnection() {
        if (!isset($this->dbConnection)) {
            $this->connect();
        }
        return $this->dbConnection;
    }*/
    
    /**
     * Get source
     * @return array
     */
    protected abstract function getSourceParams();
    
    abstract public function connect();
    
    /**
     * Get instance
     * @return DbFactory
     */
    public static function getInstance() {
        return self::getDAOClass('DbFactory');
    }
    
    /**
     * Get instance
     * @return DAO
     */
    public static function getDAOInstance($className, $module = '') {
        $instance = self::getInstance();
        $param = $instance->getSourceParams();
        $param['factory'] = $instance;
        return self::getDAOClass($className.'DAO', _DAO_STRUCTURE_, $module, $param);
    }
    
    private static function getDAOClass($className, $daoStructure = _DAO_STRUCTURE_FOLDER_, $module = '', $param = null) {
        $key = $module.$className;
        if (!isset(self::$daoClasses[$key])){
            $daoSource = self::getDAOSource();
            $class = '';
            $namespace = (empty($module)?'':'modules\\'.$module.'\\').'dao\\';
            $defaultNamespace = $namespace;
            $defaultClassName = $className;
            if($daoStructure==_DAO_STRUCTURE_FOLDER_){
                $namespace.=Tools::strtolower($daoSource).'\\';
                $sourceClass = $className . $daoSource;
                $fileName = _BASE_DIR_ . str_replace('\\', '/', $namespace) . $sourceClass . '.php';
                $className = (file_exists($fileName))?$sourceClass:$className;
            }else{
                $className.=$daoSource;
            }
            $fileName = _BASE_DIR_ . str_replace('\\', '/', $namespace) . $className . '.php';
            $finalClassName = (file_exists($fileName))?$namespace.$className:$defaultNamespace.$defaultClassName;
            if ($params===null) {
                self::$daoClasses[$key] = new $finalClassName();
            }else{
                self::$daoClasses[$key] = new $finalClassName($params);
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
}

?>
