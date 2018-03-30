<?php
namespace core\controllers;

use core\Context;
use core\Media;
use core\Tools;
use core\StringTools;
use core\FileTools;
use core\dao\Factory;

abstract class Controller
{
    /** @var Context */
    protected $context;
	
	protected $template;
	
	protected $templateName;

    protected $cssFiles = array();

    protected $jsFiles = array();
	
	protected $additionalHeaders = array();

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
	
    abstract protected function checkUserAccess();
	
    abstract protected function ProcessAction();
	
    /**
     * Initialize the page
     */
    protected function init()
    {
        
    }

    /**
     * Displays page view
     */
    abstract protected function display();

    /**
     * Sets default media list for this controller
     */
    abstract protected function setMedia();

    public function __construct($action = null, $ajax = null, $onlyProcess = false, $changeContextController = true)
    {
		$this->action = ($action === null) ? Tools::getValue('action') : $action;
		$this->action = empty($this->action) ? $this->defaultAction : $this->action;
		$this->ajax = $ajax;
		$this->onlyProcess = $onlyProcess;
		$this->ajax = ($ajax === null) ? (Tools::getValue('ajax') || Tools::isSubmit('ajax')) : $ajax;
		$this->context = Context::getInstance();
        $this->template = $this->context->getTemplate();
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
    }

    /**
     * Starts the controller process (this method should not be overridden!)
     */
    public function run()
    {
        $this->init();
        if ($this->checkSecurityAccess()) {
            // setMedia MUST be called before postProcess
            if (!$this->contentOnly && $this->useOfHeader) {
                $this->setMedia();
            }

            if ($this->checkUserAccess()) {
                $this->ProcessAction();
            } else {
                $this->errors[] = Tools::displayError('Access denied.');
            }
            if(!$this->onlyProcess){
				if (!empty($this->redirectAfter)) {
					$this->redirect();
				}

				if (!$this->contentOnly && ($this->useOfHeader)) {
					$this->initHeader();
				}
				$this->initContent();

				if (!$this->contentOnly && ($this->useOfFooter )) {
					$this->initFooter();
				}
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
        $this->addJS(Media::getJqueryPath($version, $folder, $minifier), false);
    }

    /**
     * Adds jQuery plugin(s) to queued JS file list
     *
     * @param string|array $name
     * @param string null $folder
     * @param bool $css
     */
    public function addJqueryPlugin($name, $folder = null, $css = true)
    {
        if (!is_array($name)) {
            $name = array($name);
        }
		$params = array();
        if (is_array($name)) {
            foreach ($name as $plugin) {
                $pluginPath = Media::getJqueryPluginPath($plugin, $folder);
				if (!empty($pluginPath['js'])) {
                    $this->addJS($pluginPath['js'], $params, false);
                }
                if ($css && !empty($pluginPath['css'])) {
                    $this->addCSS($pluginPath['css'], $params, false);
                }
            }
        }
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
    protected function OutputContent($content)
    {
        $this->context->getCookie()->write();

        $html = '';
		$content = is_array($content) ? $content : array($content);
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
		}
    }
	
	protected function getDAOInstance($className, $fromCurrent = true, $module = ''){
		if($fromCurrent){
			$module = $this->moduleName;
		}
        return Factory::getDAOInstance($className, $module);
    }
}