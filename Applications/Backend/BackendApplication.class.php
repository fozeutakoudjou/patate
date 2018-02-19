<?php

/**
 * Description of BackendApplication
 *
 * @author FFOZEU
 */
namespace Applications\Backend;

use Library\Application;
use Library\Router;

class BackendApplication extends Application{
    
    /**
     * contructor of the BO application
     */
    public function __construct(){

        parent::__construct($this, 'Backend');
        $this->templates = _BO_TEMPLATES_;
    }
    
    /**
     *  runtable application
     */
    public function run(){
       if($this->employee->isAuthenticated()){
           //$_SESSION['admin']=true;
            $router = new Router($this);
            $controller = $router->getController();
            if(!$this->employee->isAdmin()){
                $controller = new \Applications\Modules\Utilisateurs\Backend\Controller\UtilisateursController($this, 'Utilisateurs', 'Connect');
                //$this->httpResponse()->redirect('/');
            }else{
                //$controller = new \Applications\Modules\Statistiques\Backend\Controller\StatistiquesController($this, 'Statistiques', 'GlobalView');
            }            
       }else{
            $controller = new \Applications\Modules\Utilisateurs\Backend\Controller\UtilisateursController($this, 'Utilisateurs', 'Connect');
           //Applications\Modules\Utilisateurs\Backend\Controller
       }
       //$curr_module = $controller->module;
       $controller->execute();
       $this->httpResponse->setPage($controller->page());
       $this->httpResponse->send();
    }
    
}

?>
