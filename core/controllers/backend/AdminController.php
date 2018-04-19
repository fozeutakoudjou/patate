<?php
namespace core\controllers\backend;

use core\controllers\Controller;

use core\Tools;
use core\Media;
use core\StringTools;
use core\FileTools;
use core\models\Model;
use core\models\Configuration;
use core\models\Language;

use core\generator\html\HtmlGenerator;
use core\constant\generator\ColumnType;
use core\constant\generator\SearchType;
use core\generator\html\interfaces\AccesChecker;
use core\generator\html\interfaces\UrlCreator;

abstract class AdminController extends Controller implements AccesChecker, UrlCreator
{

    /** @var array */
    protected $modelActions;
	
    protected $modelClassName;
	
    protected $defaultModel;
	
    protected $modelDefinition;

    /** @var string */
    protected $layout = 'layout';
    protected $header = 'header';
    protected $footer = 'footer';
    protected $defaultAction = 'list';
	protected $modals = array();
	protected $metaTitle = array();
	protected $isAdmin = true;
	protected $formLanguages;
	
	protected $toolbarTitle;
	
	protected $table;
	
	protected $columnsToExclude = array('dateAdd', 'dateUpdate', 'deleted');
	
	protected $generator;
	
	protected $orderWay;
	
	protected $orderColumn;
	
	protected $itemsPerPage;
	
	protected $itemsPerPageOptions;
	protected $currentPage;

    public function __construct()
    {
		parent::__construct();
        global $timer_start;
        $this->timer_start = $timer_start;
        $this->modelActions = array();
        $this->formLanguages = Language::getLanguages(false);
		$this->generator = new HtmlGenerator($this->l('Save'), $this->l('Cancel'), $this->formLanguages, $this->lang);
		$boolOptions = array(''=>'--', '1'=>$this->l('Yes'), '0'=>$this->l('No'));
		$this->generator->setSearchOptions(SearchType::SELECT, $boolOptions);
		$this->generator->setSearchButtonText($this->l('Search'));
		$this->generator->setResetButtonText($this->l('Reset'));
		$this->generator->setAccessChecker($this);
        /*$default_theme_name = 'default';

        if (defined('_PS_BO_DEFAULT_THEME_') && _PS_BO_DEFAULT_THEME_
            && @filemtime(_PS_BO_ALL_THEMES_DIR_._PS_BO_DEFAULT_THEME_.DIRECTORY_SEPARATOR.'template')) {
            $default_theme_name = _PS_BO_DEFAULT_THEME_;
        }

        $this->bo_theme = ((Validate::isLoadedObject($this->context->employee)
            && $this->context->employee->bo_theme) ? $this->context->employee->bo_theme : $default_theme_name);

        if (!@filemtime(_PS_BO_ALL_THEMES_DIR_.$this->bo_theme.DIRECTORY_SEPARATOR.'template')) {
            $this->bo_theme = $default_theme_name;
        }

        $this->bo_css = ((Validate::isLoadedObject($this->context->employee)
            && $this->context->employee->bo_css) ? $this->context->employee->bo_css : 'admin-theme.css');

        if (!@filemtime(_PS_BO_ALL_THEMES_DIR_.$this->bo_theme.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR.$this->bo_css)) {
            $this->bo_css = 'admin-theme.css';
        }

        $this->template->setTemplateDir(array(
            _PS_BO_ALL_THEMES_DIR_.$this->bo_theme.DIRECTORY_SEPARATOR.'template',
            _PS_OVERRIDE_DIR_.'controllers'.DIRECTORY_SEPARATOR.'admin'.DIRECTORY_SEPARATOR.'templates'
        ));

        $this->id = Tab::getIdFromClassName($this->controller_name);
        $this->token = Tools::getAdminToken($this->controller_name.(int)$this->id.(int)$this->context->employee->id);

        $token = $this->token;

        if (!$this->identifier) {
            $this->identifier = 'id_'.$this->table;
        }
        if (!$this->_defaultOrderBy) {
            $this->_defaultOrderBy = $this->identifier;
        }
        $this->tabAccess = Profile::getProfileAccess($this->context->employee->id_profile, $this->id);

        // Fix for homepage
        if ($this->controller_name == 'AdminDashboard') {
            $_POST['token'] = $this->token;
        }

        if (!Shop::isFeatureActive()) {
            $this->shopLinkType = '';
        }

        //$this->base_template_folder = _PS_BO_ALL_THEMES_DIR_.$this->bo_theme.'/template';
        $this->override_folder = Tools::toUnderscoreCase(substr($this->controller_name, 5)).'/';
        // Get the name of the folder containing the custom tpl files
        $this->tpl_folder = Tools::toUnderscoreCase(substr($this->controller_name, 5)).'/';
		$this->admin_webpath = str_ireplace(_PS_CORE_DIR_, '', _PS_ADMIN_DIR_);
        $this->admin_webpath = preg_replace('/^'.preg_quote(DIRECTORY_SEPARATOR, '/').'/', '', $this->admin_webpath);*/
		$this->initModel();
    }
	
	public function createUrl($params){
		
	}
	
	public function createSortUrl($params){
		
	}
	
	public function createPaginationUrl($params){
		
	}
	
	public function createActionUrl($params){
		$params = $this->urlParams;
		if(isset($params['params']) && is_array(isset($params['params']))){
			foreach($params['params'] as $key => $param){
				$value = '';
				if(isset($param['value'])){
					$value = $param['value'];
				}elseif(isset($param['field']) && isset($values[$param['field']])){
					$value = $values[$param['field']];
				}
				$params[$key] = $value;
			}
			unset($params['params']);
		}
		return $this->table->createLink($params);
	}
	
	protected function processList(){
		$this->createTable();
		$this->createColumns();
		$this->createTableActions();
		$data = $this->formatListData($this->getListData());
		$this->table->setTotalResult($data['total']);
		$this->table->setValue($data['list']);
		$this->processResult['content'] = $this->table->generate();
	}
	
	public function initModel()
    {
		$this->defaultModel = $this->getDAOInstance()->createModel();
		$this->modelDefinition = $this->defaultModel->getDefinition();
	}
	
	public function createTable()
    {
		$this->table = $this->generator->createTable($this->l($this->modelClassName.'s'), 'user', $this->defaultAction, $this->controllerClass, $this->moduleName);
		$identifier = is_array($this->modelDefinition['primary']) ? implode('_', $this->modelDefinition['primary']) : $this->modelDefinition['primary'];
		$this->table->setIdentifier($identifier);
		$this->table->setUrlCreator($this);
		$this->customizeTable();
	}
	
	public function customizeTable() {}
	
	public function createTableActions() {
		$addLink = $this->context->getLink()->getAdminLink(strtolower($this->controllerClass), array('action'=>'add'));
		$this->generator->createTableAction($this->table, $this->l('Add'), $addLink, 'plus', $this->l('Add'), true, 'add');
	}
	
	
	public function createColumns()
    {
		$primaries = is_array($this->modelDefinition['primary'])?$this->modelDefinition['primary'] : array($this->modelDefinition['primary']);
		foreach($primaries as $field){
			if(!isset($this->modelDefinition['fields'][$field]) && !in_array($field, $this->columnsToExclude)){
				$this->generator->createColumn($this->table, $this->l($field), $field, ColumnType::TEXT, SearchType::TEXT, true, true);
			}
		}
		
		foreach($this->modelDefinition['fields'] as $field => $fieldDefinition){
			if(!in_array($field, $this->columnsToExclude)){
				$this->generator->createColumn($this->table, $this->l($field), $field, self::getColumnType($fieldDefinition['type'], $field), self::getSearchType($fieldDefinition['type'], $field), true, true);
			}
		}
		$this->customizeColumns();
	}
	
	public function customizeColumns() {}
	
	public function getListData() {
		$fields = array();
		$data = $this->getDAOInstance()->getByFields($fields, true);
		return $data;
	}
	
	public function formatListData($data) {
		return $data;
	}
	
	public function init()
    {
        parent::init();
		/*$dao = $this->getDAOInstance('Right', false);
		//$dao->setUseOfAllLang(true);
		$fields = array(
			'id'=>1,
			'idContainer___name'=>'user'
		);
		$association = array(
			'idContainer'=>array()
		);
		$data = $dao->getByFields(array(), false, $association);
		
		var_dump($data);
		var_dump($data[3]->getAssociated('idContainer'));die();*/
		$user = $this->context->getUser();
		if (isset($_GET['logout'])) {
            $this->user->logout();
        }
		$cookie = $this->context->getCookie();
        if (isset($cookie->last_activity)) {
            if ($cookie->last_activity + 900 < time()) {
                $this->user->logout();
            } else {
                $cookie->last_activity = time();
            }
        }

        /*if ($this->controllerClass != 'Login' && (!isset($this->user) || !$this->user->isLoggedBack())) {
            if (isset($this->user)) {
                $this->user->logout();
            }

            $email = false;
            if (Tools::getValue('email') && Validate::isEmail(Tools::getValue('email'))) {
                $email = Tools::getValue('email');
            }
			$redirectParams = array();
			if(!isset($_GET['logout']) && ($this->controllerClass != 'PageNotFound') && Tools::getValue('controller')){
				$redirectParams['redirect'] = strtolower($this->controllerClass);
			}
			if($email){
				$redirectParams['email'] = $email;
			}
            Tools::redirect($this->context->getLink()->getAdminLink('login', $redirectParams));
        }*/
    }
	
	protected function getRightCode($action)
    {
		$rightCode = $action;
		if(in_array($this->action, $this->modelActions)){
			$rightCode .= $this->modelClassName;
		}
		return Tools::getRightCode($rightCode);
    }
	protected function processAction()
    {
		$action = StringTools::toCamelCase($this->action, true);
		$ajaxProcessUsed = false;
		if($this->ajax && method_exists($this, 'ajaxProcess'.$action)){
			$this->{'ajaxProcess'.$action}();
			$ajaxProcessUsed = true;
		}elseif(method_exists($this, 'process'.$action)){
			$this->{'process'.$action}();
		}else{
			$this->errors[] = 'The action you specified has does not exist';
		}
		if($this->ajax && !$ajaxProcessUsed){
			$this->ajaxProcess();
		}
    }
	public function checkUserAccess($action)
    {
        $rightCode = $this->getRightCode($action);
		$rightDao = $this->getDAOInstance('Right', false);
		$rights = $rightDao->getByField('code', $rightCode, true);
		if(!empty($rights)){
			$hasRight = $this->context->user->hasRight($rightCode);
		}else{
			$hasRight = true;
		}
		return $hasRight;
    }
	
	protected function getDAOInstance($className = '', $fromCurrent = true, $module = ''){
		$className = empty($className) ? $this->modelClassName : $className;
		return parent::getDAOInstance($className, $fromCurrent, $module);
    }
	
	
	protected function initHeader(){
		
	}

    protected function initContent(){
		
	}

    protected function initSecurityFailedPage(){
		
	} 
	
	protected function initFooter(){
		$this->template->assign(array(
            'modals' => $this->renderModal(),
        ));
	}
	protected function renderModal()
    {
        $modalRender = '';
        if (is_array($this->modals) && count($this->modals)) {
            foreach ($this->modals as $modal) {
                $this->template->assign($modal);
                $modalRender .= $this->template->render('modal.tpl');
            }
        }
        return $modalRender;
    }
	
	protected function redirect(){
		
	}
	
	protected function checkSecurityAccess(){
		return true;
	}

    protected function setMedia(){
		parent::setMedia();
		$link = $this->context->getLink();
		$librariesUri = $link->getAssetLibrariesURI();
        //$this->addjQueryPlugin(array('scrollTo', 'alerts', 'chosen', 'autosize', 'fancybox' ));
       // $this->addjQueryPlugin('growl', null, false);
		$this->addJS($librariesUri.'jquery/ui/jquery-ui.min.js', array('isLibrary' => true), false);
		/*$this->addJS($librariesUri.'bootstrap/js/bootstrap.min.js', array('isLibrary' => true), false);
		$this->addJS($librariesUri.'js/modernizr.min.js', array('isLibrary' => true), false);
		$this->addJS($librariesUri.'js/moment-with-langs.min.js', array('isLibrary' => true), false);*/
	}

    protected function display()
    {
		/*$dao = $this->getDAOInstance('Group', false);
		$dao->setUseOfAllLang(true);
		$group = $dao->getById(21);
		
		var_dump($group);*/
        $this->template->assign(array(
            'useOfHeader' => $this->useOfHeader,
            'useOfHeaderJavascript'=> $this->useOfHeaderJavascript,
            'useOfFooter' => $this->useOfFooter,
        ));

        // Use page title from metaTitle if it has been set else from the breadcrumbs array
        if (!$this->metaTitle) {
            $this->metaTitle = $this->toolbarTitle;
        }
        if (is_array($this->metaTitle)) {
            $this->metaTitle = strip_tags(implode(' '.Configuration::get('NAVIGATION_PIPE').' ', $this->metaTitle));
        }
        $this->template->assign('metaTitle', $this->metaTitle);

        $page = '';
		if(isset($this->processResult['content'])){
			$page = $this->processResult['content'];
		}
		$this->template->assign(
            array(
                'page' => $page,
                'header' => $this->renderTpl($this->header, $this->useModuleHeader),
                'footer' => $this->renderTpl($this->footer, $this->useModuleFooter),
            )
        );

        $this->outputContent($this->getTemplateFile($this->layout, $this->useModuleLayout));
    }
	
	protected function renderTpl($name, $useModule = true)
    {
		return $this->template->render($this->getTemplateFile($name, $useModule));
	}
	
	protected function getTemplateFile($name, $useModule = true)
    {
		$module = $useModule ? $this->moduleName:'';
		return FileTools::getTemplateDir($this->isAdmin, $module) . $name;
	}
	
	protected static function getColumnType($modelType, $field)
    {
		$type = ColumnType::TEXT;
		if($field=='active'){
			$type = ColumnType::ACTIVE;
		}elseif($modelType==Model::TYPE_BOOL){
			$type = ColumnType::BOOL;
		}elseif($modelType==Model::TYPE_DATE){
			$type = ColumnType::DATE;
		}
		return $type;
	}
	
	protected static function getSearchType($modelType, $field)
    {
		$type = SearchType::TEXT;
		if($modelType==Model::TYPE_BOOL){
			$type = SearchType::SELECT;
		}elseif($modelType==Model::TYPE_DATE){
			$type = SearchType::DATE;
		}
		return $type;
	}
}