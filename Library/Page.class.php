<?php
namespace Library;

/**
 * Description of Page
 *
 * @author FFOZEU
 */

class Page extends ApplicationComponent{
    
    protected $contentFile;
    protected $vars = array();
    protected $numberColumn = 3;
    protected $module;
    protected $layout = '';
    
    /**
	 * add variable to use in page 
	 * @param type $var
	 * @param type $value
	 * @throws \InvalidArgumentException
	 */
    public function addVar($var, $value){
        
        if (!is_string($var) || is_numeric($var) || empty($var)){
            throw new \InvalidArgumentException('Le nom de la variable doit être une chaine de caractère non nulle');
        }
        $this->vars[$var] = $value;
    }
    /**
     * générateur de page/view
     * @return type
     * @throws \RuntimeException 
     */
    public function getGeneratedPage(){
        
        if (!file_exists($this->contentFile)){
            //die($this->contentFile);
            throw new \RuntimeException(_UNKNOW_VIEWS_);
        }
        $link = $this->app->httpRequest();//new HttpRequest;
        if($this->app->name()=='Backend')
            $employee = $this->app->employee();
        else
            $user = $this->app->user();
        $site = $this->app->site();
        $cache = $this->app->cache();
        $breadcrumb = $this->app->breadcrumb();
        
        $this->addVar('numberColumn', $this->numberColumn);
		
        /*generate variable to see on page*/
        extract($this->vars);
        
        ob_start('ob_gzhandler');
        
        $curr_module = $this->module;
        require $this->contentFile;
        
        $content = ob_get_clean();
        
        ob_start('ob_gzhandler');
        //gestion du layout lorsqu'on n'est pas logguer et on souhaite accèder au BO
        if($this->app->name()=='Backend' &&(!$employee->isAdmin()))
            $this->layout = 'login';
        /**
         *afin de gerer l'ajax et dans le soucis de ne pas recharger entièrement une page web,
         * nous allons charger un tamplate partiel ne contenant pas toute la structure de la page web 
         */
        $templates = _SITE_APP_DIR.$this->app->name().'/Templates/'.$this->app->templates().'/layout'.ucfirst($this->layout).'.php';
        $customTemplates = _SITE_APP_DIR.$this->app->name().'/Templates/'.$this->app->templates().'/partial.php';
        require !$this->app->httpRequest()->isXmlHttpRequest()?$templates :$customTemplates;
        
        return ob_get_clean();
    }
    /**
     * chargement d'une vue ou d'un fichier
     * @param type $contentFile
     * @throws \InvalidArgumentException 
     */
    public function setContentFile($contentFile){
        if (!is_string($contentFile) || empty($contentFile)){
            throw new \InvalidArgumentException(_INVALID_VIEWS_);
        }
        $this->contentFile = $contentFile;
    }
    /**
     * retourne le fichier de langue
     * @param array $tabLangFile
     */
    public function getLanguageFile(array $tabLangFile){
        foreach ($tabLangFile as $langFile) {
            if(file_exists($langFile))
                require_once $langFile;
        }
    }
    /**
     * retourne les configurations générales de l'application
     * @return type
     */
    public function getConfig(){
        $filename = _SITE_CONFIG_DIR_.'appconfig.xml';
        $out = array();
        if(file_exists($filename)){
            $xml = simplexml_load_file($filename);
            $param = $xml->items;
            foreach ($param->children() as $key => $value) {
                $out[$key] = $value;
            }
        }
        return $out;
    }
    /**
     * tranlate string on your view
     * @param type $string
     * @param type $module
     * @return type
     */
    public function l($string, $module = ''){
		$config = $this->getConfig();
        if(is_array($config)&& array_key_exists('lang', $config)&& !empty($config['lang']))
            $defaultlang = $config['lang'];
        else
            $defaultlang = 'fr';
		if(!empty($module))
            return ($this->app()->Translate()->getStringTranslation($string, $this->module, $defaultlang));
        else
            return ($this->app()->Translate()->getStringTranslationApp($string, $this->app()->name(), $defaultlang));
    }
    /**
     * initialise le module courant
     * @param type $module
     */
    public function setModule($module){
        $this->module = $module;
    }
    /**
     * retourne le module courant
     * @return type
     */
    public function getModule(){
        return $this->module;
    }
    /**
     * this function iniatialse current layout to use
     * @param type $layout
     */
    public function setLayout($layout){
        $this->layout=(string)$layout;
    }
}

?>
