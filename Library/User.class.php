<?php
/**
 * Description of User
 *
 * @author FFOZEU
 */
namespace Library;

session_start();

class User extends ApplicationComponent {
    
    public function __construct(Application $app){
        parent::__construct($app);
    }
    /**
     * retourne une variable de session utilisateur
     * @param type $attr
     * @return type 
     */
    public function getAttribute($attr){
        return isset($_SESSION[$attr]) ? $_SESSION[$attr] :null;
    }
    
    public function getFlash(){
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    
    public function hasFlash(){
        return isset($_SESSION['flash']);
    }
    
    /**
     *  verifie si l'user est authentifié
     * @return type 
     */
    public function isAuthenticated(){
        return isset($_SESSION['auth']) && $_SESSION['auth'] ===true;
    }
    /**
     * determine si c'est un administrateur
     * @return type 
     */
    public function isAdmin(){
        return isset($_SESSION['admin']) && $_SESSION['admin'] ===true;
    }
    /**
     * initialise une variable de session utilisateur
     * @param type $attr
     * @param type $value 
     */
    public function setAttribute($attr, $value){
        $_SESSION[$attr] = $value;
    }
    
    /**
     * initialise l'authentification
     * @param type $authenticated
     * @throws \InvalidArgumentException 
     */
    public function setAuthenticated($authenticated = true){
        if (!is_bool($authenticated)){
            throw new \InvalidArgumentException('La valeur spécifiée à la méthode User::setAuthenticated() doit être un boolean');
        }
        $_SESSION['auth'] = $authenticated;
    }
    
    public function setFlash($value){
        $_SESSION['flash'] = $value;
    }
    
    public function getPseudo(){
        return isset($_SESSION['user'])?$_SESSION['user']['pseudo']:null;
    }    
    
    public function getRole(){
        return isset($_SESSION['user'])?$_SESSION['user']['role']:'user';
    }
}

?>