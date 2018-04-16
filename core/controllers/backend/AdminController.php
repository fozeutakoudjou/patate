<?php
namespace core\controllers\backend;

use core\controllers\Controller;

use core\Tools;
use core\Media;
use core\StringTools;
use core\FileTools;
use core\models\Configuration;
use core\models\Language;

abstract class AdminController extends Controller
{

    /** @var array */
    protected $modelActions;
	
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

    public function __construct()
    {
		parent::__construct();
        global $timer_start;
        $this->timer_start = $timer_start;
        $this->modelActions = array();
        $this->formLanguages = Language::getLanguages(false);
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

        if ($this->controllerClass != 'Login' && (!isset($this->user) || !$this->user->isLoggedBack())) {
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
        }
    }
	
	protected function getRightCode()
    {
		$rightCode = $this->action;
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
	protected function checkUserAccess()
    {
        $rightCode = $this->getRightCode();
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
        $this->addjQueryPlugin('growl', null, false);
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
}