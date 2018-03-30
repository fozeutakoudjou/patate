<?php
namespace core;

use core\models\Configuration;
class Link
{
    /** @var bool Rewriting activation */
    protected $allow;
    protected $url;
    public static $cache = array('page' => array());

    public $protocol_link;
    public $protocol_content;

    protected $ssl_enable;

    /**
     * Constructor (initialization only)
     */
    public function __construct($protocol_link = null, $protocol_content = null)
    {
        $this->allow = (int)Configuration::get('REWRITING_SETTINGS');
        $this->url = $_SERVER['SCRIPT_NAME'];
        $this->protocol_link = $protocol_link;
        $this->protocol_content = $protocol_content;

        $this->ssl_enable = Configuration::get('SSL_ENABLED');
    }

    /**
     * Create a link to a module
     *
     * @since 1.5.0
     * @param string $module Module name
     * @param string $process Action name
     * @param int $id_lang
     * @return string
     */
    public function getModuleLink($module, $controller = 'default', array $params = array(), $ssl = null, $id_lang = null, $relative_protocol = false)
    {
        if (!$id_lang) {
            $id_lang = Context::getContext()->language->id;
        }

        $url = $this->getBaseLink($ssl, $relative_protocol).$this->getLangLink($id_lang, null);

        // Set available keywords
        $params['module'] = $module;
        $params['controller'] = $controller ? $controller : 'default';

        // If the module has its own route ... just use it !
        if (Router::getInstance()->hasRoute('module-'.$module.'-'.$controller, $id_lang)) {
            return $this->getPageLink('module-'.$module.'-'.$controller, $ssl, $id_lang, $params, false);
        } else {
            $allow = $this->allow;
            return $url.Router::getInstance()->createUrl('module', $id_lang, $params, $allow, '');
        }
    }

    /**
     * Use controller name to create a link
     *
     * @param string $controller
     * @param bool $with_token include or not the token in the url
     * @return string url
     */
    public function getAdminLink($controller, $with_token = true)
    {
        $id_lang = Context::getContext()->language->id;

        $params = $with_token ? array('token' => Tools::getAdminTokenLite($controller)) : array();
        return Dispatcher::getInstance()->createUrl($controller, $id_lang, $params, false);
    }

    /**
     * Returns a link to a product image for display
     * Note: the new image filesystem stores product images in subdirectories of img/p/
     *
     * @param string $name rewrite link of the image
     * @param string $ids id part of the image filename - can be "id_product-id_image" (legacy support, recommended) or "id_image" (new)
     * @param string $type
     */
    public function getImageLink($name, $ids, $type = null)
    {
        $not_default = false;

        // Check if module is installed, enabled, customer is logged in and watermark logged option is on
        if (($type != '') && Configuration::get('WATERMARK_LOGGED') && (Module::isInstalled('watermark') && Module::isEnabled('watermark')) && isset(Context::getContext()->customer->id)) {
            $type .= '-'.Configuration::get('WATERMARK_HASH');
        }

        // legacy mode or default image
        $theme = ((Shop::isFeatureActive() && file_exists(_PS_PROD_IMG_DIR_.$ids.($type ? '-'.$type : '').'-'.(int)Context::getContext()->shop->id_theme.'.jpg')) ? '-'.Context::getContext()->shop->id_theme : '');
        if ((Configuration::get('PS_LEGACY_IMAGES')
            && (file_exists(_PS_PROD_IMG_DIR_.$ids.($type ? '-'.$type : '').$theme.'.jpg')))
            || ($not_default = strpos($ids, 'default') !== false)) {
            if ($this->allow == 1 && !$not_default) {
                $uri_path = __PS_BASE_URI__.$ids.($type ? '-'.$type : '').$theme.'/'.$name.'.jpg';
            } else {
                $uri_path = _THEME_PROD_DIR_.$ids.($type ? '-'.$type : '').$theme.'.jpg';
            }
        } else {
            // if ids if of the form id_product-id_image, we want to extract the id_image part
            $split_ids = explode('-', $ids);
            $id_image = (isset($split_ids[1]) ? $split_ids[1] : $split_ids[0]);
            $theme = ((Shop::isFeatureActive() && file_exists(_PS_PROD_IMG_DIR_.Image::getImgFolderStatic($id_image).$id_image.($type ? '-'.$type : '').'-'.(int)Context::getContext()->shop->id_theme.'.jpg')) ? '-'.Context::getContext()->shop->id_theme : '');
            if ($this->allow == 1) {
                $uri_path = __PS_BASE_URI__.$id_image.($type ? '-'.$type : '').$theme.'/'.$name.'.jpg';
            } else {
                $uri_path = _THEME_PROD_DIR_.Image::getImgFolderStatic($id_image).$id_image.($type ? '-'.$type : '').$theme.'.jpg';
            }
        }

        return $this->protocol_content.Tools::getMediaServer($uri_path).$uri_path;
    }

    public function getMediaLink($filepath)
    {
        return $this->protocol_content.Tools::getMediaServer($filepath).$filepath;
    }

    /**
     * Create a simple link
     *
     * @param string $controller
     * @param bool $ssl
     * @param int $id_lang
     * @param string|array $request
     * @param bool $request_url_encode Use URL encode
     *
     * @return string Page link
     */
    public function getPageLink($controller, $ssl = null, $id_lang = null, $request = null, $request_url_encode = false, $relative_protocol = false)
    {
        //If $controller contains '&' char, it means that $controller contains request data and must be parsed first
        $p = strpos($controller, '&');
        if ($p !== false) {
            $request = substr($controller, $p + 1);
            $request_url_encode = false;
            $controller = substr($controller, 0, $p);
        }

        $controller = Tools::strReplaceFirst('.php', '', $controller);
        if (!$id_lang) {
            $id_lang = (int)Context::getContext()->language->id;
        }

        //need to be unset because getModuleLink need those params when rewrite is enable
        if (is_array($request)) {
            if (isset($request['module'])) {
                unset($request['module']);
            }
            if (isset($request['controller'])) {
                unset($request['controller']);
            }
        } else {
            // @FIXME html_entity_decode has been added due to '&amp;' => '%3B' ...
            $request = html_entity_decode($request);
            if ($request_url_encode) {
                $request = urlencode($request);
            }
            parse_str($request, $request);
        }

        $uri_path = Dispatcher::getInstance()->createUrl($controller, $id_lang, $request, false, '');

        return $this->getBaseLink($ssl, $relative_protocol).$this->getLangLink($id_lang, null).ltrim($uri_path, '/');
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

    protected function getLangLink($lang = null, Context $context = null)
    {
        if (!$context) {
            $context = Context::getContext();
        }

        if (!$this->allow || !Language::isMultiLanguageActivated() || !(int)Configuration::get('REWRITING_SETTINGS')) {
            return '';
        }

        if (!$lang) {
            $lang = $context->getLang();
        }

        return $lang.'/';
    }

    public function getBaseLink($ssl = null, $relative_protocol = false)
    {
        static $force_ssl = null;

        if ($ssl === null) {
            if ($force_ssl === null) {
                $force_ssl = (Configuration::get('SSL_ENABLED') && Configuration::get('SSL_ENABLED_EVERYWHERE'));
            }
            $ssl = $force_ssl;
        }

        if ($relative_protocol) {
            $base = '//'.($ssl && $this->ssl_enable ? $shop->domain_ssl : $shop->domain);
        } else {
            $base = (($ssl && $this->ssl_enable) ? 'https://'.$shop->domain_ssl : 'http://'.$shop->domain);
        }

        return $base.$shop->getBaseURI();
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
}
