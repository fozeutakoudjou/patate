<?php
namespace core;

use core\models\Configuration;
use core\models\Language;
class Link
{
    /** @var bool Rewriting activation */
    protected $allow;
    protected $url;
    public static $cache = array('page' => array());

    protected $protocolLink;
    protected $protocolContent;

    protected $sslEnable;

    /**
     * Constructor (initialization only)
     */
    public function __construct($protocolLink = null, $protocolContent = null)
    {
        $this->allow = (int)Configuration::get('REWRITING_SETTINGS');
        $this->url = $_SERVER['SCRIPT_NAME'];
        $this->protocolLink = $protocolLink;
        $this->protocolContent = $protocolContent;

        $this->sslEnable = Configuration::get('SSL_ENABLED');
    }

    public function getModuleLink($module, $controller = 'default', $lang = null, $request = null, $ssl = null, $requestUrlEncode = false, $relativeProtocol = false, $isAdmin = false)
    {
        return $this->getPageLink($controller, $lang, $request, $ssl, $requestUrlEncode, $relativeProtocol, $module, $isAdmin);
    }
	
	public function getAdminModuleLink($module, $controller = 'default', $lang = null, $request = null, $ssl = null, $requestUrlEncode = false, $relativeProtocol = false)
    {
        return $this->getModuleLink($module, $controller, $lang, $request, $ssl, $requestUrlEncode, $relativeProtocol, true);
    }

    public function getAdminLink($controller, $request = null, $ssl = null, $requestUrlEncode = false, $relativeProtocol = false)
    {
		return $this->getPageLink($controller, $lang, $request, $ssl, $requestUrlEncode, $relativeProtocol, '', true);
    }

    public function getPageLink($controller, $lang = null, $request = null, $ssl = null, $requestUrlEncode = false, $relativeProtocol = false, $module = '', $isAdmin = false)
    {
        //If $controller contains '&' char, it means that $controller contains request data and must be parsed first
        $p = strpos($controller, '&');
        if ($p !== false) {
            $request = substr($controller, $p + 1);
            $requestUrlEncode = false;
            $controller = substr($controller, 0, $p);
        }

        $controller = StringTools::strReplaceFirst('.php', '', $controller);
        if (!$lang) {
            $lang = Context::getInstance()->getLang();
        }

        if (!is_array($request)) {
            // @FIXME html_entity_decode has been added due to '&amp;' => '%3B' ...
            $request = html_entity_decode($request);
            if ($requestUrlEncode) {
                $request = urlencode($request);
            }
            parse_str($request, $request);
        }

        $uriPath = Router::getInstance()->createUrl($controller, $lang, $request, false, '', $module, $isAdmin);

        return $this->getBaseLink($ssl, $relativeProtocol).$this->getLangLink($lang, null, $isAdmin).ltrim($uriPath, '/');
    }

    /**
     * Create link after language change, for the change language block
     *
     * @param int $id_lang Language ID
     * @return string link
     */
    public function getLanguageLink($id_lang, Context $context = null)
    {
        if (!$context) {
            $context = Context::getContext();
        }

        $params = $_GET;
        unset($params['isolang'], $params['controller']);

        if (!$this->allow) {
            $params['id_lang'] = $id_lang;
        } else {
            unset($params['id_lang']);
        }

        $controller = Dispatcher::getInstance()->getController();

        if (!empty($context->controller->php_self)) {
            $controller = $context->controller->php_self;
        }

        if ($controller == 'product' && isset($params['id_product'])) {
            return $this->getProductLink((int)$params['id_product'], null, null, null, (int)$id_lang);
        } elseif ($controller == 'category' && isset($params['id_category'])) {
            return $this->getCategoryLink((int)$params['id_category'], null, (int)$id_lang);
        } elseif ($controller == 'supplier' && isset($params['id_supplier'])) {
            return $this->getSupplierLink((int)$params['id_supplier'], null, (int)$id_lang);
        } elseif ($controller == 'manufacturer' && isset($params['id_manufacturer'])) {
            return $this->getManufacturerLink((int)$params['id_manufacturer'], null, (int)$id_lang);
        } elseif ($controller == 'cms' && isset($params['id_cms'])) {
            return $this->getCMSLink((int)$params['id_cms'], null, null, (int)$id_lang);
        } elseif ($controller == 'cms' && isset($params['id_cms_category'])) {
            return $this->getCMSCategoryLink((int)$params['id_cms_category'], null, (int)$id_lang);
        } elseif (isset($params['fc']) && $params['fc'] == 'module') {
            $module = Validate::isModuleName(Tools::getValue('module')) ? Tools::getValue('module') : '';
            if (!empty($module)) {
                unset($params['fc'], $params['module']);
                return $this->getModuleLink($module, $controller, $params, null, (int)$id_lang);
            }
        }

        return $this->getPageLink($controller, null, $id_lang, $params);
    }

    public function goPage($url, $p)
    {
        $url = rtrim(str_replace('?&', '?', $url), '?');
        return $url.($p == 1 ? '' : (!strstr($url, '?') ? '?' : '&').'p='.(int)$p);
    }

    /**
     * Get pagination link
     *
     * @param string $type Controller name
     * @param int $id_object
     * @param bool $nb Show nb element per page attribute
     * @param bool $sort Show sort attribute
     * @param bool $pagination Show page number attribute
     * @param bool $array If false return an url, if true return an array
     */
    public function getPaginationLink($type, $id_object, $nb = false, $sort = false, $pagination = false, $array = false)
    {
        // If no parameter $type, try to get it by using the controller name
        if (!$type && !$id_object) {
            $method_name = 'get'.Dispatcher::getInstance()->getController().'Link';
            if (method_exists($this, $method_name) && isset($_GET['id_'.Dispatcher::getInstance()->getController()])) {
                $type = Dispatcher::getInstance()->getController();
                $id_object = $_GET['id_'.$type];
            }
        }

        if ($type && $id_object) {
            $url = $this->{'get'.$type.'Link'}($id_object, null);
        } else {
            if (isset(Context::getContext()->controller->php_self)) {
                $name = Context::getContext()->controller->php_self;
            } else {
                $name = Dispatcher::getInstance()->getController();
            }
            $url = $this->getPageLink($name);
        }

        $vars = array();
        $vars_nb = array('n');
        $vars_sort = array('orderby', 'orderway');
        $vars_pagination = array('p');

        foreach ($_GET as $k => $value) {
            if ($k != 'id_'.$type && $k != 'controller') {
                if (Configuration::get('PS_REWRITING_SETTINGS') && ($k == 'isolang' || $k == 'id_lang')) {
                    continue;
                }
                $if_nb = (!$nb || ($nb && !in_array($k, $vars_nb)));
                $if_sort = (!$sort || ($sort && !in_array($k, $vars_sort)));
                $if_pagination = (!$pagination || ($pagination && !in_array($k, $vars_pagination)));
                if ($if_nb && $if_sort && $if_pagination) {
                    if (!is_array($value)) {
                        $vars[urlencode($k)] = $value;
                    } else {
                        foreach (explode('&', http_build_query(array($k => $value), '', '&')) as $key => $val) {
                            $data = explode('=', $val);
                            $vars[urldecode($data[0])] = $data[1];
                        }
                    }
                }
            }
        }

        if (!$array) {
            if (count($vars)) {
                return $url.(!strstr($url, '?') && ($this->allow == 1 || $url == $this->url) ? '?' : '&').http_build_query($vars, '', '&');
            } else {
                return $url;
            }
        }

        $vars['requestUrl'] = $url;

        if ($type && $id_object) {
            $vars['id_'.$type] = (is_object($id_object) ? (int)$id_object->id : (int)$id_object);
        }

        if (!$this->allow == 1) {
            $vars['controller'] = Dispatcher::getInstance()->getController();
        }
        return $vars;
    }

    public function addSortDetails($url, $orderby, $orderway)
    {
        return $url.(!strstr($url, '?') ? '?' : '&').'orderby='.urlencode($orderby).'&orderway='.urlencode($orderway);
    }

    protected function getLangLink($lang = null, Context $context = null, $isAdmin = false)
    {
		if (!$context) {
            $context = Context::getInstance();
        }

        if ($isAdmin || !$this->allow || !Language::isMultiLanguageActivated() || !(int)Configuration::get('REWRITING_SETTINGS')) {
            return '';
        }

        if (!$lang) {
            $lang = $context->getLang();
        }

        return $lang.'/';
    }

    public function getBaseLink($ssl = null, $relativeProtocol = false)
    {
        static $forceSsl = null;

        if ($ssl === null) {
            if ($forceSsl === null) {
                $forceSsl = (Configuration::get('SSL_ENABLED') && Configuration::get('SSL_ENABLED_EVERYWHERE'));
            }
            $ssl = $forceSsl;
        }

        if ($relativeProtocol) {
            $base = '//';
        } else {
            $base = ($ssl && $this->sslEnable) ? 'https://': 'http://';
        }

        return $base._BASE_DOMAIN_._BASE_DIR_;
    }
	
	/**
     * getHttpHost return the <b>current</b> host used, with the protocol (http or https) if $http is true
     *
     * @param bool $http
     * @param bool $entities
     * @return string host
     */
	public static function getHttpHost($http = false, $entities = false, $ignore_port = false)
    {
        $host = (isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : $_SERVER['HTTP_HOST']);
        if ($ignore_port && $pos = strpos($host, ':')) {
            $host = substr($host, 0, $pos);
        }
        if ($entities) {
            $host = htmlspecialchars($host, ENT_COMPAT, 'UTF-8');
        }
        if ($http) {
            $host = (Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://').$host;
        }
        return $host;
    }
	
	/**
    * Check if the current page use SSL connection on not
    *
    * @return bool uses SSL
    */
	public static function usingSecureMode()
    {
		//var_dump($_SERVER);die;
        if (isset($_SERVER['HTTPS'])) {
            return in_array(Tools::strtolower($_SERVER['HTTPS']), array(1, 'on'));
        }
        // $_SERVER['SSL'] exists only in some specific configuration
        if (isset($_SERVER['SSL'])) {
            return in_array(Tools::strtolower($_SERVER['SSL']), array(1, 'on'));
        }
        // $_SERVER['REDIRECT_HTTPS'] exists only in some specific configuration
        if (isset($_SERVER['REDIRECT_HTTPS'])) {
            return in_array(Tools::strtolower($_SERVER['REDIRECT_HTTPS']), array(1, 'on'));
        }
        if (isset($_SERVER['HTTP_SSL'])) {
            return in_array(Tools::strtolower($_SERVER['HTTP_SSL']), array(1, 'on'));
        }
        if (isset($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
            return Tools::strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) == 'https';
        }

        return false;
    }

    /**
    * Get the current url prefix protocol (https/http)
    *
    * @return string protocol
    */
    public static function getCurrentUrlProtocolPrefix()
    {
        if (self::usingSecureMode()) {
            return 'https://';
        } else {
            return 'http://';
        }
    }
	
	/**
     * getProtocol return the set protocol according to configuration (http[s])
     * @param bool $use_ssl true if require ssl
     * @return String (http|https)
     */
    public static function getProtocol($use_ssl = null)
    {
        return (!is_null($use_ssl) && $use_ssl ? 'https://' : 'http://');
    }
	
	public function getAssetLibrariesURI($module = '', $ssl = null, $relativeProtocol = false)
    {
       return $this->getURI(_ASSET_LIBRARIES_PATH_, null, $module, false, $ssl, $relativeProtocol);
    }
	
	public function getURI($path, $isAdmin = null, $module = '', $useOfTheme = true, $ssl = null, $relativeProtocol = false)
    {
		$base = $this->getBaseLink($ssl, $relativeProtocol).(empty($module)?_CORE_PATH_:_MODULES_PATH_.'/'.$module).'/';
        return $base.FileTools::getPath($path, $isAdmin, $module, $useOfTheme);
    }
	
	public function getURIFormDir($dir)
    {
		$fileUri = FileTools::standardizeFile($dir);
		$finalUri = str_replace(FileTools::standardizeFile(_SITE_ROOT_DIR_), '', $fileUri);
		$finalUri = str_replace('\\', '/', $finalUri);
		$finalUri = $this->getBaseLink() .$finalUri;
		return $finalUri;
	}
}
