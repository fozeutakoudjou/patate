<?php
namespace Library;
/**
 * Description of HttpResponse
 *
 * @author FFOZEU
 */
class HttpResponse extends ApplicationComponent{
    
    protected $page;
    
    public function __construct(Application $app){
        parent::__construct($app);
    }
    
    public function addHeader($header){
        header($header);
    }
    
    /**
     * 
     * @param type $location 
     * @param type $message
     * @param type $typeMessage 0:error
     *                          1:success
     *                          2:Info
     *                          3:Notice
     */
    public function redirect($location, $message = '', $typeMessage = 2){
        $_SESSION['message_show'] = $message;
        $_SESSION['message_type'] = $typeMessage;
        header('Location: '.$location);
        exit;
    }
    
    public function redirect404(){
        
        $this->page = new Page($this->app);
        $this->page->setContentFile(dirname(__FILE__).'/../Errors/404.html');
        $this->addHeader('HTTP/1.0 404 Not Found');
        $this->send();
    }
    
    public function send(){
        exit($this->page->getGeneratedPage());
    }
    
    public function setPage(Page $page){
        $this->page = $page;
    }
// Changement par rapport à la fonction setcookie() : ledernier argument est par défaut à true
    public function setCookie($name, $value = '', $expire = 0, $path = null, $domain = null, $secure = false, $httpOnly = true){
        setcookie($name, $value, $expire, $path, $domain,$secure, $httpOnly);
    }
}

?>