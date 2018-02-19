<?php

/**
 * Description of Application
 *
 * @author FFOZEU
 */
namespace Library;

abstract class Application {
    /******definition des attributs*/
    protected $httpRequest;
    protected $httpResponse;
    protected $name;
    protected $templates;
    protected $user;
    protected $employee;
    protected $site;
    protected $config;
    protected $cache;
    protected $mail;
    protected $pdf;
    protected $breadcrumb;
    protected $translate;
    protected $appconfig = array();

    /**
     * constructeur de l'application
     * @param type $app
     * @param type $appName
     */
    public function __construct($app, $appName){
        $this->httpRequest = new HttpRequest($app);
        $this->httpResponse = new HttpResponse($app);
        $this->user = new Customer($app);
        $this->employee = new Employe($app);
        $this->site = new Site($app);
        $this->config = new Config($app);
        $this->cache = new Cache($app);
        $this->mail = new Mail($app);
        $this->pdf = new Pdf($app);
        $this->breadcrumb = new Breadcrumb($app);
        $this->translate = new Translate($app);
        $this->name = (string)$appName;
        $this->loadAppConfig();
    }
    
    abstract public function run();
    
    public function httpRequest(){
        return $this->httpRequest;
    }
    
    public function httpResponse(){
        return $this->httpResponse;
    }
    /**
     * retun de name of the application
     * @return type
     */
    public function name(){
        return $this->name;
    }
    
    public function user(){
        return $this->user;
    }
    
    public function employee(){
        return $this->employee;
    }
    
    public function site(){
        return $this->site;
    }
    
    public function config(){
        return $this->config;
    }
    
    public function cache(){
        return $this->cache;
    }
    public function mail(){
        return $this->mail;
    }
    public function pdf(){
        return $this->pdf;
    }
    public function breadcrumb(){
        return $this->breadcrumb;
    }
    public function Translate(){
        return $this->translate;
    }
    /**
     * return de template folder this application
     * @return type
     */
    public function templates(){
        return $this->templates;
    }
    /**
     * chargement des paramètre de d'une application
     */
    protected function loadAppConfig(){
        $dom = new \DOMDocument; // L'antislashe précédant laclasse est très important ! DOMDocument est déclaré dans lenamespace global, ici on est dans le namespace Library
        $dom->load(_SITE_APP_DIR.$this->name().'/Config/app.xml');
        $dom->xinclude();
        foreach ($dom->getElementsByTagName('entry') as $elt){
            $this->appconfig[$elt->getAttribute('var')] = $elt->getAttribute('value');
        }
    }
}

?>
