<?php
namespace core\controllers\backend\partial;

use core\controllers\Controller;

use core\Tools;
use core\Validate;
use core\Media;
use core\StringTools;
use core\FileTools;
use core\models\Model;
use core\models\Configuration;
use core\models\Language;

use core\generator\html\HtmlGenerator;
use core\generator\html\formatters\MenuItemFormatter;
use core\constant\ActionCode;
use core\constant\WrapperType;

abstract class BaseAdminController extends Controller
{
	const ID_PARAM_URL = 'param1';
	const REDIRECT_PREFIX = 'redirect_';
	protected $availableActions = array();
	
    protected $modelClassName;
	
    protected $defaultModel;
	
    protected $modelDefinition;
    protected $formErrors = array();
    protected $successLabels = array();

    /** @var string */
    protected $layout = 'layout';
    protected $header = 'header';
    protected $footer = 'footer';
	const DEFAULT_ACTION = ActionCode::LISTING;
    protected $defaultAction = self::DEFAULT_ACTION;
    
	protected $modals = array();
	protected $metaTitle = array();
	protected $isAdmin = true;
	protected $formLanguages;
	
	protected $toolbarTitle;
	
	
	protected $generator;
	
	protected $modelIdentifier;
	
	protected $wrapper;
	
	const DATA_USED_ONCE_COOKIE_PREFIX = 'dataUsedOnce';

    public function __construct()
    {
		parent::__construct();
        global $timer_start;
        $this->timer_start = $timer_start;
    }
	
	public function createUrl($params = array()){
		return $this->context->getLink()->getAdminLink($this->moduleName, strtolower($this->controllerClass), $params);
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
		if(!isset($params[self::ID_PARAM_URL])){
			$params[self::ID_PARAM_URL] = $this->getFieldValue($values, $this->modelIdentifier);
		}
		return $this->createUrl($params);
	}
	
	protected function getFieldValue($values, $field){
		$value = '';
		if(is_array($values) && isset($values[$field])){
			$value = $values[$field];
		}elseif(is_object($values)){
			$value = $values->getPropertyValue($field);
		}
		return $value;
	}
	
	protected function initModel()
    {
		$this->defaultModel = $this->getDAOInstance()->createModel();
		$this->modelDefinition = $this->defaultModel->getDefinition();
		$this->modelIdentifier = $this->defaultModel->createSinglePrimary();
		if(isset($this->modelDefinition['fields']['deleted'])){
			$this->baseRestrictionsData['deleted']=0;
		}
	}
	
	protected function initActions()
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
				'confirm' =>true,
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
	protected function initSuccessLabels()
    {
		$this->successLabels = array(
			ActionCode::ADD => $this->l('Added successfully'),
			ActionCode::UPDATE => $this->l('Updated successfully'),
			ActionCode::DELETE => $this->l('Deleted successfully'),
			ActionCode::ACTIVATE => $this->l('Activated successfully'),
			ActionCode::DESACTIVATE => $this->l('Desactivated successfully'),
		);
	}
	protected function getSuccessLabel($code)
    {
		return isset($this->successLabels[$code]) ? $this->successLabels[$code] : $this->l($code.'ed successfully');
	}
	protected function restrictAction()
    {
		foreach($this->availableActions as $key => $value){
			if(!$this->checkUserAccess($key)){
				unset($this->availableActions[$key]);
			}
		}
	}
	
	public function init()
    {
        parent::init();
		$user = $this->context->getUser();
		if (isset($_GET['logout'])) {
            $user->logout();
        }
		$cookie = $this->context->getCookie();
        if (isset($cookie->lastActivity)) {
            if ($cookie->lastActivity + 900 < time()) {
                $user->logout();
            } else {
                $cookie->lastActivity = time();
            }
        }

        if ($this->controllerClass != 'Login' && (!isset($user) || !$user->isLoggedBack())) {
            $user->logout();
            $email = false;
            if (Tools::getValue('email') && Validate::isEmail(Tools::getValue('email'))) {
                $email = Tools::getValue('email');
            }
			if(isset($_GET['email'])){
				unset($_GET['email']);
			}
			
			$redirectParams = array();
			if(!isset($_GET['logout']) && ($this->controllerClass != 'PageNotFound') && Tools::getValue('controller')){
				unset($_GET['is_module']);
				unset($_GET['controllerUri']);
				foreach($_GET as $key => $value){
					$redirectParams[self::REDIRECT_PREFIX.$key] = $value;
					unset($_GET[$key]);
				}
			}
			if($email){
				$redirectParams['email'] = $email;
			}
            Tools::redirect($this->context->getLink()->getAdminLink('', 'login', $redirectParams));
        }
		$this->formLanguages = Language::getLanguages(false);
		$this->generator = new HtmlGenerator($this->template, $this->l('Save'), $this->l('Cancel'), $this->formLanguages, $this->lang);
		$radioOptions = array('1'=>$this->l('Yes'), '0'=>$this->l('No'));
		$this->generator->setAccessChecker($this);
		$this->generator->setDefaultFormErrorText($this->l('You have some form errors. Please check below.'));
		$this->generator->setRadioOptions($radioOptions);
		if(!empty($this->modelClassName)){
			$this->initModel();
			$this->initWrapper();
			$this->initActions();
			$this->restrictAction();
			$this->initSuccessLabels();
		}
    }
	protected function initWrapper()
    {
		$dao = $this->getDAOInstance('Wrapper', false);
		$fields = array('type'=>WrapperType::CONTROLLER, 'target'=>$this->controllerClass);
		if($this->isModule){
			$fields['module'] = $this->moduleName;
		}else{
			$fields['module'] = null;
		}
		$this->wrapper = $dao->getByFields($fields);
		$this->wrapper = empty($this->wrapper) ? $dao->createModel() : $this->wrapper[0];
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
			$this->errors[] = $this->l('The action you specified does not exist');
		}
		if($this->ajax && !$ajaxProcessUsed){
			$this->ajaxProcess();
		}
    }
	public function checkUserAccess($action)
    {
        $rightCode = Tools::getRightCode($action);
		return $this->context->getUser()->hasRight($this->wrapper->getId(), $rightCode);
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
		Tools::redirect($this->redirectLink);
	}
	
	protected function checkSecurityAccess(){
		return true;
	}

    protected function setMedia(){
		parent::setMedia();
		$link = $this->context->getLink();
		$librariesUri = $link->getAssetLibrariesURI();
		$jsUri = $link->getJSURI(true, '', false);
		$cssUri = $link->getCSSURI(true, '', false);
        $this->addJS($librariesUri.'jquery/ui/jquery-ui.min.js', array('isLibrary' => true), false);
		$this->addJS($jsUri.'global.js');
		$this->addCSS($cssUri.'global.css');
	}
	protected function setCookieDataUsedOnce($write = false)
    {
		$cookie = $this->context->getCookie();
        $cookie->unsetFamily(self::DATA_USED_ONCE_COOKIE_PREFIX);
		foreach($this->dataUsedOnce as $key => $data){
			$cookie->{self::DATA_USED_ONCE_COOKIE_PREFIX.$this->controllerClass.$key} = $data;
		}
		if($write){
			$cookie->write();
		}
    }
    protected function display()
    {
		$cookie = $this->context->getCookie();
		if(isset($cookie->{self::DATA_USED_ONCE_COOKIE_PREFIX.$this->controllerClass.'success'})){
			$this->confirmations[] = $this->getSuccessLabel($cookie->{self::DATA_USED_ONCE_COOKIE_PREFIX.$this->controllerClass.'success'});
		}
		$this->setCookieDataUsedOnce(false);
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
                'menuContent' => $this->renderMenu(),
                'page' => $page,
                'header' => $this->renderTpl($this->header, $this->useModuleHeader),
                'footer' => $this->renderTpl($this->footer, $this->useModuleFooter),
            )
        );

        $this->outputContent($this->getTemplateFile($this->layout, $this->useModuleLayout));
    }
	
	protected function renderMenu()
    {
		$tree = $this->generator->createTree($this->getDAOInstance('AdminMenu', false), $this->getTemplateFile('header_menu', false),
			$this->getTemplateFile('footer_menu', false), array('active' => 1),
			array('idWrapper'=> array('useOfLang'=>false), 'idAction'=> array('useOfLang'=>false)), new MenuItemFormatter($this));
		return $tree->generateContent();
	}
	
	public function formatMenuItem($item)
    {
		$menu = $item->getValue();
		$action = empty($menu->getIdAction()) ? self::DEFAULT_ACTION : $menu->getAssociated('idAction')->getCode();
		if(($this->action == $action)&& ($menu->getIdWrapper() == $this->wrapper->getId())){
			$item->addWrapperClass('active');
			$item->addAdditionalData('active', 1);
		}
		$href = 'javascript:void(0);';
		if($this->context->getUser()->hasRight($menu->getIdWrapper(), $action)){
			if($menu->isClickable()){
				$params = array();
				if($action!=self::DEFAULT_ACTION){
					$params['action'] = $action;
				}
				$href = $this->context->getLink()->getAdminLink($this->moduleName, strtolower($menu->getAssociated('idWrapper')->getTarget()), $params);
				if($menu->isClickable()){
					$item->addAttribute('target', '_blank');
				}
			}
		}else{
			if(!$item->hasChildrens()){
				$item->setRenderingCancelled(true);
			}
		}
		
		$item->addAdditionalData('href', $href);
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
	
	protected function hasErrors()
    {
		return (!empty($this->errors) || !empty($this->formErrors));
	}
	
	protected function createOptions($modelClassName = '', $module = '', $restrictions = array(), $addEmptyOption = false, $list = null)
    {
		$list = ($list === null) ? $this->getDAOInstance($modelClassName, false, $module)->getByFields($restrictions, false, $this->lang, true, false) : $list;
		$options = array();
		if($addEmptyOption){
			$options[''] = '--';
		}
		foreach($list as $object){
			$options[$object->getSinglePrimaryValue()] = $object->__toString();
		}
		return $options;
	}
}