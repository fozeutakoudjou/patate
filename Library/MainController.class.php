<?php
namespace Library;
use Library\dao\Factory;

use Library\constant\dao\Operator;
use Library\constant\dao\OrderWay;
use Library\constant\dao\OrderBy;
/**
 * Description of MainController
 *
 * @author FFOZEU
 */

abstract class MainController extends ApplicationComponent{
    
    protected $action = '';
    protected $module = '';
    protected $page = null;
    protected $cache = null;
    protected $view = '';
    protected $errors = array();
    protected $infos = array();
    protected $managers = null;
    protected $tabCSS = array();
    protected $tabJS = array();
    protected $tabPluginsCSS = array();
    protected $tabPluginsJS = array();
    protected $tabLang = array();
    protected $name ='';
    protected $pagination = '';


    public function __construct(Application $app, $module,$action){
		/*$dao = $this->getDAOInstance('Language');
		//$language = $dao->createModel();
		$data = array(
			'name' => 'English (English)',
			'active' => '1',
			'isoCode' => 'mm',
			'languageCode' => 'en-us',
			'dateFormatLite' => 'm/d/Y',
			'dateFormatFull' => 'm/d/Y H:i:s',
		);
		//$language->hydrate($data);
		//$dao->add($language);
		//var_dump($dao->add($language));
		$language = $dao->getById(15);
		$language->setIsoCode('fr');
		var_dump($dao->update($language));
		var_dump($dao->delete($language));
		var_dump($language);*/
		/*var_dump($language->validateFields());*/
		
		$dao = $this->getDAOInstance('Group');
		$daoC = $this->getDAOInstance('Configuration');
		$daol = $this->getDAOInstance('Language');
		$data = array(
			'name' => 'English (English)',
			'active' => '1',
			'isoCode' => 'es',
			'languageCode' => 'en-us',
			'dateFormatLite' => 'm/d/Y',
			'dateFormatFull' => 'm/d/Y H:i:s',
		);
		/*$language = $daol->createModel();
		$language->hydrate($data);*/
		
		/*$language = $daol->getByField('isoCode', 's', false, 0, 0, OrderBy::PRIMARY, OrderWay::DESC, Operator::START_WITH);
		var_dump($language);*/
		//var_dump($daol->add($language));
		//$group = $dao->createModel();
		/*$data = array(
			'name' => 'English (English)',
			'active' => '1',
			'isoCode' => 'mm',
			'languageCode' => 'en-us',
			'dateFormatLite' => 'm/d/Y',
			'dateFormatFull' => 'm/d/Y H:i:s',
		);
		//$language->hydrate($data);
		//$dao->add($language);
		//var_dump($dao->add($language));
		$language = $dao->getById(15);
		$language->setIsoCode('fr');
		var_dump($dao->update($language));
		var_dump($dao->delete($language));
		var_dump($language);*/
		$group = $dao->createModel();
		$data = array(
			'name' => 'grp 1',
			'description' => 'description gp1',
		);
		$group->hydrate($data);
		var_dump($dao->add($group));
		die(0);
		parent::__construct($app);
        $this->managers = new Managers('PDO', DbFactory::getPdoInstance());
        $this->page = new Page($app); 
        $this->page->setModule($module);
        $this->cache = new Cache($app);
        $this->name = $module;                    
        $this->setModule($module);        
        $this->setAction($action);
        $this->init();
        $this->setView($action);
        
    }
    protected function init(){
        //chargement du CSS et JS
        $this->tabLang[] = _SITE_ROOT_DIR_.'/Applications/'.$this->app->name().'/Lang/fr.php';
        $this->loadModLanguageFile();
        $this->addModJS();
        $this->addModCSS();
        $this->page->addVar('tabCSS', $this->tabCSS);
        $this->page->addVar('tabJS', $this->tabJS);
        $this->page->addVar('tabLangFile', $this->tabLang);
    }
    public function execute(){
        $method = 'execute'.ucfirst($this->action);
        if (!is_callable(array($this, $method))){
            throw new \RuntimeException('L\'action "'.$this->action.'" n\'est pas définie sur ce module');
        }
        $this->$method($this->app->httpRequest());
    }
    
    public function page(){
        return $this->page;
    }
    /**
     * this function initialize module
     * @param type $module
     * @throws \InvalidArgumentException
     */
    public function setModule($module){
        if (!is_string($module) || empty($module)){
            throw new \InvalidArgumentException('Le module doit être une chaine de caractères valide');
        }
        $this->module = $module;
    }
    /**
     * this function initialize view action of the module
     * @param type $action
     * @throws \InvalidArgumentException
     */
    public function setAction($action){
        if (!is_string($action) || empty($action)){
            throw new \InvalidArgumentException('L\'action doit être une chaine de caractères valide');
        }
        $this->action = $action;
    }
    /**
     * this function initialize view module
     * @param type $view
     * @throws \InvalidArgumentException
     */
    public function setView($view){
        if (!is_string($view) || empty($view)){
            throw new \InvalidArgumentException('La vue doit être une chaine de caractères valide');
        }
        $this->view = ucfirst($view);
        $appname = $this->app->name();
        $templates = $this->app->templates();
        // Vu de l'action defini dans le module
        $fileload = dirname(__FILE__).'/../Applications/Modules/'.$this->module.'/'.$appname.'/Views/'.$this->view.'.tpl.php';
        // vue par défaut override par vous
        $fileload2 = dirname(__FILE__).'/../Applications/'.$appname.'/Templates/'.$templates.'/Override/'.$this->view.'.tpl.php';
        // vue par défaut de l'action dans le template de l'application
        $fileload3 = dirname(__FILE__).'/../Applications/'.$appname.'/Templates/'.$templates.'/'.$this->view.'.tpl.php';
        $default = dirname(__FILE__).'/../Applications/'.$appname.'/Templates/'.$templates.'/Default.tpl.php'; // vue par défaut
        //find dexisting views
        if(!file_exists($fileload))
            if(!file_exists($fileload2))
                if(!file_exists($fileload3))
                    $fileload =$default;
                else $fileload = $fileload3;
            else $fileload = $fileload2;
        $this->page->setContentFile($fileload);
    }
    
    /**
     * this function load language file of your module
     */
    protected function loadModLanguageFile(){
        $dir = _SITE_MOD_DIR.$this->module.'/Lang/';
        $lang = $dir.'fr.php';
        if(is_dir($dir) && file_exists($lang)){
            $this->tabLang[] = $lang;
        }
    }
    /**
     *  add Js file on your project
     * @param type $pathfile
     */
    protected function addJS($pathfile){
        if(is_array($pathfile) && count($pathfile)){
			foreach ($pathfile as $value)
				if(!array_key_exists($value, $this->tabJS))
					$this->tabJS[$value] = 'all';
		}else
			if(!array_key_exists($pathfile, $this->tabJS)){
				$this->tabJS[$pathfile] = 'all';
			}
        $this->addVar(array('tabJS' => 'tabJS'));
    }
	
    /**
     * Ajout d'un fichier JS d'un module     
     * @param type $applaction
     * @param type $filename
     * @param type $mod
     */
    protected function addModJS($applaction='F',$filename='',$mod='')
	{
        $urlfile ='';
        $module = (!empty($mod)?$mod:$this->name);
        $pathfile = (!empty($filename)?$filename:$this->name.'.js');
        $name_dir = '/scripts/';
        if(file_exists(($applaction=='F'?_SITE_THEME_FO_MOD_DIR_:_SITE_THEME_BO_MOD_DIR_).$module.$name_dir.$applaction.$pathfile))
            $urlfile =($applaction=='F'?_THEME_FO_MOD_DIR_:_THEME_BO_MOD_DIR_).$module.'/web'.$name_dir.$applaction.$pathfile;
        elseif(file_exists(_SITE_MOD_DIR.$module.'/web'.$name_dir.$applaction.$pathfile))
            $urlfile =_MOD_DIR_.$module.'/web'.$name_dir.$applaction.$pathfile;
        if(!array_key_exists($urlfile, $this->tabJS)){
            $this->tabJS[$urlfile] = 'all';
            $this->addVar(array('tabJS' => 'tabJS'));
        }
    }
	/**
	 * this function add some plugin to projet
	 * @param type $plugins
	 */
	final function addJSPlugins($plugins)
	{
        
    }
    /**
     * Chargement des plugins
     * @param array $plugins
     */
    final function addMediaPlugins(array $plugins)
    {
        if (is_array($plugins))
        {
            $add = false;
            foreach ($plugins as $value) {
                if (file_exists(_SITE_PLUGINS_DIR_.$value.'.css') && !array_key_exists(_PLUGINS_DIR_.$value.'.css', $this->tabPluginsCSS)){
                    $this->tabPluginsCSS[_PLUGINS_DIR_.$value.'.css'] = 'screen';
                    $add = true;
                }if (file_exists(_SITE_PLUGINS_DIR_.$value.'.min.js') && !array_key_exists(_PLUGINS_DIR_.$value.'.min.js', $this->tabPluginsJS)){
                    $this->tabPluginsJS[_PLUGINS_DIR_.$value.'.min.js'] = 'all';
                    $add = true;
                }else if(file_exists(_SITE_PLUGINS_DIR_.$value.'.js') && !array_key_exists(_PLUGINS_DIR_.$value.'.js', $this->tabPluginsJS)){
                    $this->tabPluginsJS[_PLUGINS_DIR_.$value.'.js'] = 'all';
                    $add = true;
                }
            }
            if($add)
                $this->addVar(array('pluginsCSS' => 'tabPluginsCSS', 'pluginsJS' => 'tabPluginsJS'));
        }
    }
    /**
     * add css file on your project
     * @param type $pathfile
     * @param type $media
     */
    protected function addCSS($pathfile,$media='screen'){
        if(!array_key_exists($pathfile, $this->tabCSS)){
            $this->tabCSS[$pathfile] = (string)$media;
        }
        $this->addVar(array('tabCSS' => 'tabCSS'));
    }
    
    /**
     * 
     * @param type $applaction
     * @param type $filename
     * @param type $mod
     */
    protected function addModCSS($applaction='F',$filename='',$mod=''){
        $urlfile ='';
        $module = (!empty($mod)?$mod:$this->name);
        $pathfile = (!empty($filename)?$filename:$this->name.'.css');
        $name_dir = '/css/';
        if(file_exists(($applaction=='F'?_SITE_THEME_FO_MOD_DIR_:_SITE_THEME_BO_MOD_DIR_).$module.$name_dir.$applaction.$pathfile))
            $urlfile =($applaction=='F'?_THEME_FO_MOD_DIR_:_THEME_BO_MOD_DIR_).$module.'/web'.$name_dir.$applaction.$pathfile;
        elseif(file_exists(_SITE_MOD_DIR.$module.'/web'.$name_dir.$applaction.$pathfile))
            $urlfile =_MOD_DIR_.$module.'/'.$name_dir.$applaction.$pathfile;
        if(!array_key_exists($urlfile, $this->tabCSS)){
            $this->tabCSS[$urlfile] = 'all';
            $this->addVar(array('tabCSS' => 'tabCSS'));
        }
        
    }

    /**
     * permet de traduire une chaine
     * @param type $string
     * @return type
     */
    public function l($string){
		$config = $this->getConfig();
        if(is_array($config)&& array_key_exists('lang', $config)&& !empty($config['lang']))
            $defaultlang = $config['lang'];
        else
            $defaultlang = 'fr';
		
        return ($this->app()->Translate()->getStringTranslation($string,$this->module, $defaultlang));	
	
    }

    /**
     * renvoi le module instancié
     * @return type
     */
    public function getModule(){
        return $this->module;
    }
    
    /**
     * affiche le menu de gauche dans le backend ou front 
     * @return array
     */
    protected function leftcolumnMenu(){
        $out = array();
        return $out;
    }
    /**
     * méthode de creation et mise à jour d'une entrée
     * @param \Library\HttpRequest $request
     */
    public function executeCreate(HttpRequest $request) {
        $this->page->addVar('left_content', $this->leftcolumnMenu());
        $dataArray = array();
        $manager  = $this->managers->getManagerOf($this->name);
        $edit = false;
        $form = $this->name.'Form';
        //cas de l'édition
        if($request->getExists('id')){            
            $edit =true;
            $dataObjt = $manager->findById(intval($request->getValue('id')));
            $dataArray = $dataObjt->tabAttrib;
            $this->page->addVar('title', 'Modification d\'entrée');
        }else{
               $dataArray = $_POST;
        }
        $dataForm = $form::getForm($dataArray, $edit);
        if($request->getMethod('post')){
            if(!$request->getExists('id')){ 
                if($manager->add($request->getSendData($_POST))){
                    $this->app()->httpResponse()->redirect($request->refferer());
                }else{
                    $this->errors = _RECCORD_SAVE_FILED_;
                }
            }else{
                if($manager->update($request->getSendData($_POST),'id')){
                    $this->app()->httpResponse()->redirect($request->refferer());
                }else{
                    $this->errors = _RECCORD_UPDATE_FILED_;
                }
            }
        }
        
        $this->page->addVar('errors', $this->errors);
        $this->page->addVar('dataForm', $dataForm);
        
    }
    /**
     * méthode générique pour la suppression
     * @param \Library\HttpRequest $request
     */
    public function executeDelete(HttpRequest $request) {
        
    }
    /**
     * methode générique pour le listing 
     * @param \Library\HttpRequest $request
     */
    public function executeList(HttpRequest $request) {
        
        $this->page->addVar('left_content', $this->leftcolumnMenu());
        
        $this->page->addVar('title', 'Listing '.$this->title);

        $manager = $this->managers->getManagerOf($this->name);
        $datalist = $manager->findAll2();

        $this->page->addVar('datalist', $datalist);
        $this->page->addVar('pagination', $this->pagination);
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
                $out[$key] = $param->$key;
            }
        }
        return $out;
    }
    /**
     * retourne la configuration mail du projet
     * @return type
     */
    public function getMailConfig(){
        $filename = _SITE_CONFIG_DIR_.'mailconfig.xml';
        $out = array();
        if(file_exists($filename)){
            $xml = simplexml_load_file($filename);
            $param = $xml->items;            
            foreach ($param->children() as $key => $value) {
                $out[$key] = $param->$key;
            }
        }
        return $out;
    }
    /**
     * chargement des variables
     * @param array $tabVar
     */
    private function addVar(array $tabVar){
        if(is_array($tabVar))
            foreach ($tabVar as $key => $value) {
                $this->page->addVar($key, $this->{$value});
            }
    }
	
	private function getDAOInstance($className, $fromCurrent = true, $module = ''){
		if($fromCurrent){
			$module = '';
			$start = strpos(__NAMESPACE__, 'modules\\') ;
			if($start !== false){
				$start += strlen('modules\\');
				$end = strpos(__NAMESPACE__, '\\', $start);
				$length = $end - $start;
				$module = substr(__NAMESPACE__, $start, $length);
			}
		}
        return Factory::getDAOInstance($className, $module);
    }
}

?>
