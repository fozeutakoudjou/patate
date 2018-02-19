<?php
/**
 * Description of Sites
 *
 * @author FFOZEU
 */
namespace Library;

if( !defined('IN') ) die('Hacking Attempt');

class Site extends ApplicationComponent{
    //put your code here
    protected $managers = null;
    
    public function __construct(Application $app){
        parent::__construct($app);
        $this->managers = new Managers('PDO', DbFactory::getPdoInstance());
    }
    
    /**
     * retourne une variable de session du site
     * @param type $attr
     * @return type 
     */
    public function getAttribute($attr){
        return isset($_SESSION[$attr]) ? $_SESSION[$attr] :null;
    }
    /**
     * Initialise une variable de session du site
     * @param type $attr
     * @param type $value 
     */
    public function setAttribute($attr, $value){
        $_SESSION[$attr] = $value;
    }
    
    public function setSiteInfos(){
		
    }
}

?>
