<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Library;
/**
 * Description of Employe
 *
 * @author ffozeu
 */
class Employe extends User{
    //put your code here
    private $_name ='employee';
    
    public function __construct(Application $app){
        parent::__construct($app);
    }
    
    
    /**
     * retourne une variable de session utilisateur
     * @param type $attr
     * @return type 
     */
    public function getAttribute($attr){
        return isset($_SESSION[$this->_name][$attr]) ? $_SESSION[$this->_name][$attr] :null;
    }
    
    public function getFlash(){
        if(isset($_SESSION[$this->_name]['flash'])){
            $flash = $_SESSION[$this->_name]['flash'];
            unset($_SESSION[$this->_name]['flash']);
        }  else {
            $flash = false;
        }
        return $flash;
    }
    
    public function hasFlash(){
        return isset($_SESSION[$this->_name]['flash']);
    }
    
    /**
     *  verifie si l'user est authentifié
     * @return type 
     */
    public function isAuthenticated(){
    return isset($_SESSION[$this->_name]['auth-employee']) && $_SESSION[$this->_name]['auth-employee'] ===true;
    }
    /**
     * determine si c'est un administrateur
     * @return type 
     */
    public function isAdmin(){
        return isset($_SESSION[$this->_name]['admin-employee']) && $_SESSION[$this->_name]['admin-employee'] ===true;
    }
    /**
     * initialise une variable de session utilisateur
     * @param type $attr
     * @param type $value 
     */
    public function setAttribute($attr, $value){
        $_SESSION[$this->_name][$attr] = $value;
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
        $_SESSION[$this->_name]['auth-employee'] = $authenticated;
    }
    
    public function setFlash($value){
        $_SESSION[$this->_name]['flash'] = $value;
    }
    
    public function getPseudo(){
        return isset($_SESSION[$this->_name])?$_SESSION[$this->_name]['pseudo']:null;
    }    
    
    public function getRole(){
        return isset($_SESSION[$this->_name])?$_SESSION[$this->_name]['role']:'user';
    }
    
    public function logOut(){
        $_SESSION[$this->_name] =array();
        //session_destroy();
    }
    
    public function haveRightTo($access){
        if(in_array($access, $_SESSION[$this->_name]['access']))
            return true;
       
         return false;
            
    }
    
    public function haveModuleAccess($module){
        if(in_array($module, $_SESSION[$this->_name]['modules']))
            return true;
       
         return false;
            
    }
	
	 public function haveModuleAccess_route($route){
        if(in_array($route, $_SESSION[$this->_name]['modules_access']))
            return true;
       
         return false;
            
    }
    
    public function isSuperAdmin(){
        if(isset($_SESSION[$this->_name])&& in_array('superadmin', $this->getAttribute('technical_group')))
            return true;
        return false;
    }
}

?>