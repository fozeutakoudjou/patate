<?php
namespace core\controllers\backend\partial;

use core\controllers\Controller;

use core\Tools;
use core\Media;
use core\StringTools;
use core\FileTools;
use core\models\Model;
use core\models\Configuration;
use core\models\Language;

use core\generator\html\HtmlGenerator;
use core\constant\ActionCode;
use core\generator\html\interfaces\AccesChecker;
use core\generator\html\interfaces\UrlCreator;

abstract class BaseAdminController extends Controller
{
	protected $availableActions = array();
	
    protected $modelClassName;
	
    protected $defaultModel;
	
    protected $modelDefinition;

    /** @var string */
    protected $layout = 'layout';
    protected $header = 'header';
    protected $footer = 'footer';
    protected $defaultAction = ActionCode::LISTING;
	protected $modals = array();
	protected $metaTitle = array();
	protected $isAdmin = true;
	protected $formLanguages;
	
	protected $toolbarTitle;
	
	
	protected $generator;
	
	protected $modelIdentifier;

    public function __construct()
    {
		parent::__construct();
        global $timer_start;
        $this->timer_start = $timer_start;
        $this->formLanguages = Language::getLanguages(false);
		$this->generator = new HtmlGenerator($this->l('Save'), $this->l('Cancel'), $this->formLanguages, $this->lang);
		$this->generator->setAccessChecker($this);
		$this->initModel();
		$this->initActions();
    }
	
	public function createUrl($params){
		return $this->context->getLink()->getAdminLink($this->moduleName, strtolower($this->controllerClass), $params);
	}
	
	public function createSortUrl($params){
		
	}
	
	public function createPaginationUrl($params){
		
	}
	
	public function createActionUrl($params, $values){
		if(isset($params['params']) && is_array(isset($params['params']))){
			foreach($params['params'] as $key => $param){
				$value = '';
				if(isset($param['value'])){
					$value = $param['value'];
				}elseif(isset($param['field'])){
					$value = $this->getFieldValue($values, $param['field']);
				}
				$params[$key] = $value;
			}
			unset($params['params']);
		}
		if(!isset($params['param1'])){
			$params['param1'] = $this->getFieldValue($values, $this->modelIdentifier);
		}
		return $this->createUrl($params);
	}
	
	public function getFieldValue($values, $field){
		$value = '';
		if(is_array($values) && isset($values[$field])){
			$value = $values[$field];
		}elseif(is_object($values)){
			$value = $values->getPropertyValue($field);
		}
		return $value;
	}
	
	public function initModel()
    {
		$this->defaultModel = $this->getDAOInstance()->createModel();
		$this->modelDefinition = $this->defaultModel->getDefinition();
		$this->modelIdentifier = $this->defaultModel->createSinglePrimary();
	}
	
	public function initActions()
    {
		$this->availableActions = array(
			ActionCode::ADD => array(
				'model' =>true,
			),
			ActionCode::VIEW => array(
				'icon' =>'search-plus',
				'label' =>$this->l('View'),
				'title' =>$this->l('View'),
				'row' =>true,
				'default' =>true,
				'model' =>true
			),
			ActionCode::UPDATE => array(
				'icon' =>'pencil',
				'label' =>$this->l('Edit'),
				'title' =>$this->l('Edit'),
				'row' =>true,
				'model' =>true
			),
			ActionCode::DELETE => array(
				'icon' =>'trash',
				'label' =>$this->l('Delete'),
				'title' =>$this->l('Delete'),
				'row' =>true,
				'model' =>true
			),
			ActionCode::LISTING => array(
				'action_for_right' =>ActionCode::VIEW
			),
		);
		if(isset($this->modelDefinition['fields']['active'])){
			$this->availableActions[ActionCode::ACTIVATE] = array('model' =>true);
			$this->availableActions[ActionCode::DESACTIVATE] = array('model' =>true);
		}
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
		if(isset($this->availableActions[$action]) && isset($this->availableActions[$action]['model']) && $this->availableActions[$action]['model']){
			$rightCode .= '_'.$this->modelClassName;
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
		$jsUri = $link->getJSURI(true, '', false);
        $this->addJS($librariesUri.'jquery/ui/jquery-ui.min.js', array('isLibrary' => true), false);
		$this->addJS($jsUri.'global.js');
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