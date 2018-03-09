<?php
namespace Library;
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DbFactory
 *
 * @author FFOZEU
 */
class DbFactory {
    //put your code here
    private static $_instance;
    /* Constructeur : héritage public obligatoire par héritage de PDO
    */
    
    public function __construct( ) {
        
    }
    // End of PDO2::__construct() */
    /* Singleton */
    public static function getPdoInstance() {
        if (!isset(self::$_instance)) {
            try {
                self::$_instance = new \PDO(_DB_TYPE_.':host='._DB_SERVER_.';port='._DB_PORT_.';dbname='._DB_NAME_, _DB_USER_, _DB_PASSWD_);
            } catch (PDOException $e) {
                echo $e;
                echo 'Erreur : '.$e->getMessage().'<br />';
                echo 'N° : '.$e->getCode();
            }
        }
        return self::$_instance;
    }
// End of PDO2::getInstance() */
}

?>
