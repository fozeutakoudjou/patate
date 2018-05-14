<?php
namespace core\controllers;

use core\Context;
use core\Media;
use core\Tools;
use core\StringTools;
use core\FileTools;
use core\dao\Factory;

use core\models\Language;

use core\models\Configuration;

abstract class Controller
{
    /** @var Context */
    protected $context;
	
	protected $template;
	
	protected $templateName;

    protected $cssFiles = array();
	
    protected $cssContents = array();

    protected $jsFiles = array();
	
	protected $jsContents = array();
	
    protected $jsVariables = array();
	
	protected $additionalHeaders = array();
	
	protected $additionalFooters = array();

    protected $useOfHeader = true;

    protected $useOfHeaderJavascript = true;
	
    protected $useOfFooter = true;

    protected $contentOnly = false;

    public $ajax = false;

    protected $json = false;

    protected $redirectAfter = false;
	
    protected $redirectLink;
	
    protected $action;
	
    protected $processResult;
	
    protected $errors;
	
    protected $onlyProcess;
	
    protected $moduleName;

    abstract protected function checkSecurityAccess();
	
    abstract protected function checkUserAccess($action);
	
    abstract protected function ProcessAction();
	
	protected $useModuleLayout = false;

    protected $useModuleHeader = false;
	
    protected $useModuleFooter = false;
	
    protected $isAdmin = false;
	
    protected $isModule = false;
	
    protected $controllerClass = false;
	
    protected $user;
	
    protected $lang;
    protected $confirmations;
    protected $warnings;
    protected $informations;
	protected $defaultAction = '';
	
    /**
     * Initialize the page
     */
    protected function init()
    {
        $this->assignBaseVariables();
    }

    /**
     * Displays page view
     */
    abstract protected function display();

    /**
     * Sets default media list for this controller
     */
    protected function setMedia(){
		$link = $this->context->getLink();
		$this->addJquery();
		$this->addJSVariable('baseUrl', $link->getBaseLink(), false, Media::POSITION_FIRST);
	}

    public function __construct($action = null, $ajax = null, $onlyProcess = false, $changeContextController = true)
    {
		$this->action = ($action === null) ? Tools::getValue('action') : $action;
		$this->action = empty($this->action) ? $this->defaultAction : $this->action;
		$this->ajax = $ajax;
		$this->onlyProcess = $onlyProcess;
		$this->ajax = ($ajax === null) ? (Tools::getValue('ajax') || Tools::isSubmit('ajax')) : $ajax;
		$this->context = Context::getInstance();
		$this->user = $this->context->getUser();
        $this->template = $this->context->getTemplate();
		$this->lang = $this->context->getLang();
		if($changeContextController){
			$this->context->setController($this);
		}
		if (!headers_sent()
            && isset($_SERVER['HTTP_USER_AGENT'])
            && (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false
            || strpos($_SERVER['HTTP_USER_AGENT'], 'Trident') !== false)) {
            header('X-UA-Compatible: IE=edge,chrome=1');
        }
		$this->setModuleName();
		$this->setControllerName();
    }

    /**
     * Starts the controller process (this method should not be overridden!)
     */
    public function run()
    {
        $this->init();
        if ($this->checkSecurityAccess()) {
            // setMedia MUST be called before postProcess
            if (!$this->contentOnly) {
                $this->setMedia();
            }

            if ($this->checkUserAccess($this->action)) {
                $this->ProcessAction();
            } else {
                $this->errors[] = $this->l('Access denied.');
            }
            if(!$this->onlyProcess){
				if ($this->redirectAfter) {
					$this->redirect();
				}

				if (!$this->contentOnly && ($this->useOfHeader)) {
					$this->initHeader();
				}
				$this->initContent();

				if (!$this->contentOnly && ($this->useOfFooter )) {
					$this->initFooter();
				}
				$this->assignMedia();
			}
            // then using ajaxDisplay[action]
            if ($this->ajax) {
                $action = StringTools::toCamelCase($this->action, true);
				if (!empty($action) && method_exists($this, 'ajaxDisplay'.$action)) {
                    $this->{'ajaxDisplay'.$action}();
                } else {
                    $this->ajaxDisplay();
                }
            } else {
                $this->display();
            }
        } else {
            $this->initSecurityFailedPage();
            $this->outputContent($this->layout);
        }
    }
	
	protected function ajaxDisplay()
    {
		$output = $this->processResult;
		$output['errors'] = $this->errors;
		$output['hasError'] = count($this->errors)>0;
		$output['errors'] = $this->errors;
        die(Tools::jsonEncode($output));
    }
	
	protected function ajaxProcess()
    {
        $this->onlyProcess = true;
    }
	
	protected function l($string)
    {
		return $string;
	}

    /**
     * Assigns Smarty variables for the page header
     */
    abstract protected function initHeader();

    /**
     * Assigns Smarty variables for the page main content
     */
    abstract protected function initContent();

    /**
     * Assigns Smarty variables when access is forbidden
     */
    abstract protected function initSecurityFailedPage();

    /**
     * Assigns Smarty variables for the page footer
     */
    abstract protected function initFooter();

    /**
     * Redirects to $this->redirect_after after the process if there is no error
     */
    abstract protected function redirect();
	

    /**
     * Adds a new stylesheet(s) to the page header.
     *
     * @param string|array $css_uri Path to CSS file, or list of css files like this : array(array(uri => media_type), ...)
     * @param string $css_media_type
     * @param int|null $offset
     * @param bool $check_path
     * @return true
     */
    public function addCSS($cssUri, $params = array(), $checkPath = true)
    {
		if(!isset($params['media'])){
			$params['media'] = 'all';
		}
		$this->cssFiles = Media::addMedia($this->cssFiles, $cssUri, $params, $checkPath);
    }

    /**
     * Removes CSS stylesheet(s) from the queued stylesheet list
     *
     * @param string|array $css_uri Path to CSS file or an array like: array(array(uri => media_type), ...)
     * @param string $css_media_type
     * @param bool $check_path
     */
    public function removeCSS($cssUri, $checkPath = true)
    {
        $this->cssFiles = Media::removeMedia($this->cssFiles, $cssUri, $checkPath);
    }

    /**
     * Adds a new JavaScript file(s) to the page.
     *
     * @param string|array $js_uri Path to JS file or an array like: array(uri, ...)
     * @param bool $checkPath
     * @return void
     */
    public function addJS($jsUri, $params = array(), $checkPath = true)
    {
		if(!isset($params['displayInHead'])){
			$params['displayInHead'] = false;
		}
		$this->jsFiles = Media::addMedia($this->jsFiles, $jsUri, $params, $checkPath);
    }

    /**
     * Removes JS file(s) from the queued JS file list
     *
     * @param string|array $jsUri Path to JS file or an array like: array(uri, ...)
     * @param bool $checkPath
     */
    public function removeJS($jsUri, $checkPath = true)
    {
        $this->jsFiles = Media::removeMedia($this->jsFiles, $jsUri, $checkPath);
    }

    /**
     * Adds jQuery library file to queued JS file list
     *
     * @param string|null $version jQuery library version
     * @param string|null $folder jQuery file folder
     * @param bool $minifier If set tot true, a minified version will be included.
     */
    public function addJquery($version = null, $folder = null, $minifier = true)
    {
		$params = array('position' => Media::POSITION_FIRST, 'isLibrary' => true);
        $this->addJS(Media::getJqueryPath($version, $folder, $minifier), $params, false);
    }

    /**
     * Adds jQuery plugin(s) to queued JS file list
     *
     * @param string|array $name
     * @param string null $folder
     * @param bool $css
     */
    public function addJqueryPlugin($name, $folder = null, $css = true, $module = '')
    {
        if (!is_array($name)) {
            $name = array($name);
        }
		$params = array('isLibrary' => true);
        if (is_array($name)) {
            foreach ($name as $plugin) {
                $pluginPath = Media::getJqueryPluginPath($plugin, $folder, $css, $module);
				if (!empty($pluginPath['js'])) {
                    $this->addJS($pluginPath['js'], $params, false);
                }
                if ($css && !empty($pluginPath['css'])) {
                    $this->addCSS($pluginPath['css'], $params, false);
                }
            }
        }
    }
	
	public function addJSVariable($name, $value, $displayInHead = false, $position = MEDIA::POSITION_LAST)
    {
		$this->jsVariables[$name] = array(
			'value' => $value,
			'displayInHead' => $displayInHead,
			'position' => $position,
		);
    }
	
	public function addJSContent($content, $displayInHead = false, $position = MEDIA::POSITION_LAST)
    {
		$this->jsContents[] = array(
			'content' => $content,
			'displayInHead' => $displayInHead,
			'position' => $position,
		);
    }
	
	public function addCSSContent($content, $position = MEDIA::POSITION_LAST)
    {
		$this->cssContents[] = array(
			'content' => $content,
			'position' => $position,
		);
    }

    /**
     * Checks if the controller has been called from XmlHttpRequest (AJAX)
     *
     * @since 1.5
     * @return bool
     */
    public function isXmlHttpRequest()
    {
        return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
    }

    /**
     * Renders controller templates and generates page content
     *
     * @param array|string $content Template file(s) to be rendered
     * @throws Exception
     * @throws SmartyException
     */
    protected function outputContent($content)
    {
        $this->context->getCookie()->write();

        $html = '';
		$content = is_array($content) ? $content : array($content);
		$this->template->assign('errors', $this->errors);
		$this->template->assign('confirmations', $this->confirmations);
		$this->template->assign('informations', $this->informations);
		$this->template->assign('warnings', $this->warnings);
        foreach ($content as $tpl) {
			$html .= $this->template->render($tpl);
		}
        $html = trim($html);
		echo $html;
    }

    /**
     * Checks if a template is cached
     *
     * @param string $template
     * @param string|null $idCache Cache item ID
     * @return bool
     */
    protected function isCached($template, $idCache = '')
    {
        return false;
    }
	
	protected function setModuleName()
    {
        if($this->moduleName===null){
			$this->moduleName = FileTools::getModuleFromNamespace(get_class($this));
			$this->isModule = !empty($this->moduleName);
		}
    }
	protected function setControllerName()
    {
		$class = get_class($this);
        $bits = explode('\\', $class);
        $this->controllerClass = end($bits);
        $this->controllerClass = str_replace(FileTools::getControllerSuffix($this->isAdmin), '', $this->controllerClass);
    }
	
	protected function getDAOInstance($className, $fromCurrent = true, $module = ''){
		if($fromCurrent){
			$module = $this->moduleName;
		}
        return Factory::getDAOInstance($className, $module);
    }
	
	protected function assignBaseVariables()
    {
		$languages = Language::getLanguages();
		$language = isset($languages[$this->lang]) ? $languages[$this->lang]: new Language();
		$link = $this->context->getLink();
		$folders = array('');
		if($this->isModule){
			$folders[] = $this->moduleName;
		}
		$assetsDef = array(
			'css' => _CSS_PATH_,
			'js' => _JS_PATH_,
			'img' => _IMG_PATH_,
			'libraries' => _ASSET_LIBRARIES_PATH_
		);
		$dirDef = array(
			'tpl' => _TEMPLATES_PATH_
		);
		$otherPathsDef = array(
			'upload' => _UPLOAD_PATH_
		);
		$dataDir = array();
		$typeSuffix = $this->isAdmin ? 'Admin' : 'Front';
		$pathSuffix = 'Dir';
		$themeSuffix = 'Theme';
		$dirSuffix = $pathSuffix;
		foreach($folders as $module){
			$moduleSuffix = empty($module) ? '' : 'Module';
			foreach($assetsDef as $key => $path){
				$dataDir[$key.$moduleSuffix.$pathSuffix] = $link->getURI($path, null, $module, false);
				$dataDir[$key.$typeSuffix.$moduleSuffix.$pathSuffix] = $link->getURI($path, $this->isAdmin, $module, false);
				$dataDir[$key.$typeSuffix.$themeSuffix.$moduleSuffix.$pathSuffix] = $link->getURI($path, $this->isAdmin, $module, true);
			}
			foreach($dirDef as $key => $path){
				$dataDir[$key.$moduleSuffix.$dirSuffix] = FileTools::getDirectory($path, null, $module, false);
				$dataDir[$key.$typeSuffix.$moduleSuffix.$dirSuffix] = FileTools::getDirectory($path, $this->isAdmin, $module, false);
				$dataDir[$key.$typeSuffix.$themeSuffix.$moduleSuffix.$dirSuffix] = FileTools::getDirectory($path, $this->isAdmin, $module, true);
			}
			foreach($otherPathsDef as $key => $path){
				$dataDir[$key.$moduleSuffix.$pathSuffix] = $link->getURI($path, null, $module, false);
			}
		}
		$this->template->assign($dataDir);
        $this->template->assign(array(
			'iso' => $language->getIsoCode(),
            'version' => _VERSION_,
            'langIso' => $language->getIsoCode(),
            'fullLanguageCode' => $language->getLanguageCode(),
            'link' => $link,
            'baseUrl' => $link->getBaseLink(),
            'headKey' => Media::HEAD_KEY,
            'notHeadKey' => Media::NOT_HEAD_KEY,
            'libraryKey' => Media::LIBRARY_KEY,
            'notLibraryKey' => Media::NOT_LIBRARY_KEY
        ));
    }
	
	protected function assignMedia(){
		$this->template->assign('jsFiles', Media::formatList($this->jsFiles));
		$this->template->assign('jsVariables', Media::formatList($this->jsVariables, true, false));
		$this->template->assign('jsContents', Media::formatList($this->jsContents, true, false));
		$this->template->assign('cssFiles', Media::formatList($this->cssFiles, false));
		$this->template->assign('cssContents', Media::sortList($this->cssContents));
	}
}