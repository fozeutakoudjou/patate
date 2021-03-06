<?php
namespace core\dao\pdo;
use core\dao\Factory;
use core\Tools;

class FactoryPDO extends Factory {
    protected $dbConnection;
	
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
        parent::__construct();
    }
    public function connect(){
        try {
            $this->dbConnection = $this->_getPDO($this->server, $this->port, $this->user, $this->password, $this->database, 5);
        } catch (\PDOException $e) {
            die(sprintf(Tools::displayError('Link to database cannot be established: %s'), utf8_encode($e->getMessage())));
        }
        
        // UTF-8 support
        if ($this->dbConnection->exec('SET NAMES \'utf8\'') === false) {
            die(Tools::displayError('Fatal error: no utf-8 support. Please check your server configuration.'));
        }
        
        $this->dbConnection->exec('SET SESSION sql_mode = \'\'');
		
		$this->dbConnection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
		$this->dbConnection->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
        
        return $this->dbConnection;
    }
    
    public function _escape($str)
    {
        $search = array("\\", "\0", "\n", "\r", "\x1a", "'", '"');
        $replace = array("\\\\", "\\0", "\\n", "\\r", "\Z", "\'", '\"');
        return str_replace($search, $replace, $str);
    }
    
    /**
     * Returns a new PDO object (database link)
     *
     * @param string $host
     * @param string $port
     * @param string $user
     * @param string $password
     * @param string $dbname
     * @param int $timeout
     * @return \PDO
     */
    protected static function _getPDO($host, $port, $user, $password, $dbname, $timeout = 5)
    {
        $dsn = 'mysql:host='.$host.';port='.$port.';dbname='.$dbname;
        
        return new \PDO($dsn, $user, $password, array(\PDO::ATTR_TIMEOUT => $timeout, \PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true));
    }
    
    public function getLastInsertId(){
        return $this->dbConnection->lastInsertId();
    }
    
    protected function getSourceParams(){
        return array(
            'db' => $this->dbConnection
        );
    }
}

?>
