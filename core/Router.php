<?php
namespace Library;

/**
 * Description of Router
 *
 * @author FFOZEU
 */
class Router extends ApplicationComponent{
    
    public function getController(){
        
        $context = Context::getContext();
        $dom = new \DOMDocument; // L'antislashe précédant laclasse est très important ! DOMDocument est déclaré dans lenamespace global, ici on est dans le namespace Library
        $dom->load(_SITE_APP_DIR.$this->app->name().'/Config/routes.xml');
        $dom->xinclude();
        foreach ($dom->getElementsByTagName('route') as $route){
            //var_dump($this->app->httpRequest()->requestURI());
            if (preg_match('`^'.$route->getAttribute('url').'$`', ($this->getPath()?$this->getPath():$this->app->httpRequest()->requestURI()),$matches)){
				
				if( ($route->getAttribute('url') != '/'._VIRTUAL_ADMIN_DIR_.'/') && ($route->getAttribute('url') != '/'._VIRTUAL_ADMIN_DIR_.'/deconnexion.html') &&
						isset($_SESSION['employee']['modules_access']) &&
						!$this->app->employee()->haveModuleAccess_route($route->getAttribute('url')) &&
						!$this->app->employee()->isSuperAdmin())
					die("Vous n'avez pas accès à cette vue!");
				$module = $route->getAttribute('module');
                $action = $route->getAttribute('action');
                $classname = $module.'Controller';
                if(!file_exists(_SITE_MOD_DIR.$module.'/'.$this->app->name().'/Controller/'.$classname.'.class.php')){
                    throw new \RuntimeException(_UNKNOW_MOD_); // La raison de l'antislashe est la même que pour DOMDocument
                }
                
                $class = '\\Applications\\Modules\\'.$module.'\\'.$this->app->name().'\\Controller\\'.$classname;
                $controller = new $class($this->app, $module,$action);
                if ($route->hasAttribute('vars')){
                    $vars = explode(',', $route->getAttribute('vars'));
                    foreach ($matches as $key => $match){
                        if ($key !== 0){
                            $this->app->httpRequest()->addGetVar($vars[$key - 1], $match);
                        }
                    }
                }
                break;
            }
        }
        if (!isset($controller)){
            $module = 'Errors';
            $action = 'Errors';
            $classname = $module.'Controller';
            $class = '\\Applications\\Modules\\'.$module.'\\'.$this->app->name().'\\Controller\\'.$classname;
            $controller = new $class($this->app, $module,$action);
            //$this->app->httpResponse()->redirect404();
        }
        
        return $controller;
    }
}

?>
