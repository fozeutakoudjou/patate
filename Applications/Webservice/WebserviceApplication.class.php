<?php

/**
 * Description of FrontendApplication
 *
 * @author FFOZEU
 */
namespace Applications\Webservice;

use Library\Application;
use Library\Router;

class WebserviceApplication extends Application{
    protected $app;
    public function __construct(){
        
        parent::__construct($this);
        $this->name = 'Webservice';
        $this->app = $this;
    }
    
    public function run($url=''){
        $url = $this->app->httpRequest()->requestURI();
        $module = 'Webservices';
        $action = 'Webservice';
        $classname = $module.'Controller';
        $class = '\\Applications\\Modules\\'.$module.'\\Frontend\\Controller\\'.$classname;
        $controller = new $class($this->app, $module,$action);
        $controller->execute();
        
        $this->httpResponse->setPage($controller->page());
        $this->httpResponse->send();
        
    }
}

?>
