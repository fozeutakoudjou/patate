<?php

/**
 * Description of FrontendApplication
 *
 * @author FFOZEU
 */
namespace Applications\Frontend;

use Library\Application;
use Library\Router;

class FrontendApplication extends Application{
    
    /**
     * contructor of the FO application
     */
    public function __construct(){
        
        parent::__construct($this, 'Frontend');
        $this->templates = _FO_TEMPLATES_;
    }
    /**
     * runtable application
     * @param type $crontab
     * @param type $url
     */
    public function run($crontab=false,$url=''){
        
        $router = new Router($this,$url);
        $controller = $router->getController();
        $controller->execute();
        if(!$crontab){
            $this->httpResponse->setPage($controller->page());
            $this->httpResponse->send();
        }
    }
}

?>
