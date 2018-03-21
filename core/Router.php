<?php
namespace core;

use core\models\Configuration;
use core\models\Language;

class Router{
    const FC_FRONT = 1;
    const FC_ADMIN = 2;
    const FC_MODULE = 3;

    /**
     * @var Dispatcher
     */
    protected static $instance = null;
	
    protected $isAdmin = false;

    /**
     * @var array List of default routes
     */
    public $default_routes = array(
        'category_rule' => array(
            'controller' =>    'category',
            'rule' =>        '{id}-{rewrite}',
            'keywords' => array(
                'id' =>            array('regexp' => '[0-9]+', 'param' => 'id_category'),
                'rewrite' =>        array('regexp' => '[_a-zA-Z0-9\pL\pS-]*'),
                'meta_keywords' =>    array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                'meta_title' =>        array('regexp' => '[_a-zA-Z0-9-\pL]*'),
            ),
        ),
        'supplier_rule' => array(
            'controller' =>    'supplier',
            'rule' =>        '{id}__{rewrite}',
            'keywords' => array(
                'id' =>            array('regexp' => '[0-9]+', 'param' => 'id_supplier'),
                'rewrite' =>        array('regexp' => '[_a-zA-Z0-9\pL\pS-]*'),
                'meta_keywords' =>    array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                'meta_title' =>        array('regexp' => '[_a-zA-Z0-9-\pL]*'),
            ),
        ),
        'manufacturer_rule' => array(
            'controller' =>    'manufacturer',
            'rule' =>        '{id}_{rewrite}',
            'keywords' => array(
                'id' =>            array('regexp' => '[0-9]+', 'param' => 'id_manufacturer'),
                'rewrite' =>        array('regexp' => '[_a-zA-Z0-9\pL\pS-]*'),
                'meta_keywords' =>    array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                'meta_title' =>        array('regexp' => '[_a-zA-Z0-9-\pL]*'),
            ),
        ),
        'cms_rule' => array(
            'controller' =>    'cms',
            'rule' =>        'content/{id}-{rewrite}',
            'keywords' => array(
                'id' =>            array('regexp' => '[0-9]+', 'param' => 'id_cms'),
                'rewrite' =>        array('regexp' => '[_a-zA-Z0-9\pL\pS-]*'),
                'meta_keywords' =>    array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                'meta_title' =>        array('regexp' => '[_a-zA-Z0-9-\pL]*'),
            ),
        ),
        'cms_category_rule' => array(
            'controller' =>    'cms',
            'rule' =>        'content/category/{id}-{rewrite}',
            'keywords' => array(
                'id' =>            array('regexp' => '[0-9]+', 'param' => 'id_cms_category'),
                'rewrite' =>        array('regexp' => '[_a-zA-Z0-9\pL\pS-]*'),
                'meta_keywords' =>    array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                'meta_title' =>        array('regexp' => '[_a-zA-Z0-9-\pL]*'),
            ),
        ),
        'module' => array(
            'controller' =>    null,
            'rule' =>        'module/{module}{/:controller}',
            'keywords' => array(
                'module' =>        array('regexp' => '[_a-zA-Z0-9_-]+', 'param' => 'module'),
                'controller' =>        array('regexp' => '[_a-zA-Z0-9_-]+', 'param' => 'controller'),
            ),
            'params' => array(
                'fc' => 'module',
            ),
        ),
        'product_rule' => array(
            'controller' =>    'product',
            'rule' =>        '{category:/}{id}-{rewrite}{-:ean13}.html',
            'keywords' => array(
                'id' =>            array('regexp' => '[0-9]+', 'param' => 'id_product'),
                'rewrite' =>        array('regexp' => '[_a-zA-Z0-9\pL\pS-]*'),
                'ean13' =>        array('regexp' => '[0-9\pL]*'),
                'category' =>        array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                'categories' =>        array('regexp' => '[/_a-zA-Z0-9-\pL]*'),
                'reference' =>        array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                'meta_keywords' =>    array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                'meta_title' =>        array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                'manufacturer' =>    array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                'supplier' =>        array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                'price' =>            array('regexp' => '[0-9\.,]*'),
                'tags' =>            array('regexp' => '[a-zA-Z0-9-\pL]*'),
            ),
        ),
        /* Must be after the product and category rules in order to avoid conflict */
        'layered_rule' => array(
            'controller' =>    'category',
            'rule' =>        '{id}-{rewrite}{/:selected_filters}',
            'keywords' => array(
                'id' =>            array('regexp' => '[0-9]+', 'param' => 'id_category'),
                /* Selected filters is used by the module blocklayered */
                'selected_filters' =>    array('regexp' => '.*', 'param' => 'selected_filters'),
                'rewrite' =>        array('regexp' => '[_a-zA-Z0-9\pL\pS-]*'),
                'meta_keywords' =>    array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                'meta_title' =>        array('regexp' => '[_a-zA-Z0-9-\pL]*'),
            ),
        ),
    );

    /**
     * @var bool If true, use routes to build URL (mod rewrite must be activated)
     */
    protected $use_routes = false;

    protected $multilang_activated = false;

    /**
     * @var array List of loaded routes
     */
    protected $routes = array();

    /**
     * @var string Current controller name
     */
    protected $controller;

    /**
     * @var string Current request uri
     */
    protected $request_uri;

    /**
     * @var array Store empty route (a route with an empty rule)
     */
    protected $empty_route;

    /**
     * @var string Set default controller, which will be used if http parameter 'controller' is empty
     */
    protected $default_controller;
    protected $use_default_controller = false;

    /**
     * @var string Controller to use if found controller doesn't exist
     */
    protected $controller_not_found = 'pagenotfound';

    /**
     * @var string Front controller to use
     */
    protected $front_controller = self::FC_FRONT;

    /**
     * Get current instance of router (singleton)
     *
     * @return Router
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new Router();
        }
        return self::$instance;
    }
	
	protected $adminVirtual;

    /**
     * Need to be instancied from getInstance() method
     */
    protected function __construct()
    {
        $this->use_routes = (bool)Configuration::get('REWRITING_SETTINGS');
		$this->adminVirtual = (defined('_VIRTUAL_ADMIN_DIR_') && !empty(_VIRTUAL_ADMIN_DIR_)) ? _VIRTUAL_ADMIN_DIR_ : 'admin';
        // Select right front controller
        if (defined('_PS_ADMIN_DIR_')) {
            $this->front_controller = self::FC_ADMIN;
            $this->controller_not_found = 'adminnotfound';
        } elseif (Tools::getValue('fc') == 'module') {
            $this->front_controller = self::FC_MODULE;
            $this->controller_not_found = 'pagenotfound';
        } else {
            $this->front_controller = self::FC_FRONT;
            $this->controller_not_found = 'pagenotfound';
        }
		
        $this->setRequestUri();

        // Switch language if needed (only on front)
        if (in_array($this->front_controller, array(self::FC_FRONT, self::FC_MODULE))) {
            Tools::switchLanguage();
        }
		if (Language::isMultiLanguageActivated()) {
            $this->multilang_activated = true;
        }

        $this->loadRoutes();
    }

    public function useDefaultController()
    {
        $this->use_default_controller = true;
        if ($this->default_controller === null) {
            if (defined('_PS_ADMIN_DIR_')) {
                if (isset(Context::getInstance()->employee) && Validate::isLoadedObject(Context::getInstance()->employee) && isset(Context::getInstance()->employee->default_tab)) {
                    $this->default_controller = Tab::getClassNameById((int)Context::getInstance()->employee->default_tab);
                }
                if (empty($this->default_controller)) {
                    $this->default_controller = 'AdminDashboard';
                }
            } elseif (Tools::getValue('fc') == 'module') {
                $this->default_controller = 'default';
            } else {
                $this->default_controller = 'index';
            }
        }
        return $this->default_controller;
    }

    /**
     * Find the controller and instantiate it
     */
    public function dispatch()
    {
        $controller_class = '';

        // Get current controller
        $this->getController();
        if (!$this->controller) {
            $this->controller = $this->useDefaultController();
        }
        // Dispatch with right front controller
        switch ($this->front_controller) {
            // Dispatch front office controller
            case self::FC_FRONT :
                $controllers = Dispatcher::getControllers(array(_PS_FRONT_CONTROLLER_DIR_, _PS_OVERRIDE_DIR_.'controllers/front/'));
                $controllers['index'] = 'IndexController';
                if (isset($controllers['auth'])) {
                    $controllers['authentication'] = $controllers['auth'];
                }
                if (isset($controllers['compare'])) {
                    $controllers['productscomparison'] = $controllers['compare'];
                }
                if (isset($controllers['contact'])) {
                    $controllers['contactform'] = $controllers['contact'];
                }

                if (!isset($controllers[strtolower($this->controller)])) {
                    $this->controller = $this->controller_not_found;
                }
                $controller_class = $controllers[strtolower($this->controller)];
                $params_hook_action_dispatcher = array('controller_type' => self::FC_FRONT, 'controller_class' => $controller_class, 'is_module' => 0);
            break;

            // Dispatch module controller for front office
            case self::FC_MODULE :
                $module_name = Validate::isModuleName(Tools::getValue('module')) ? Tools::getValue('module') : '';
                $module = Module::getInstanceByName($module_name);
                $controller_class = 'PageNotFoundController';
                if (Validate::isLoadedObject($module) && $module->active) {
                    $controllers = Dispatcher::getControllers(_PS_MODULE_DIR_.$module_name.'/controllers/front/');
                    if (isset($controllers[strtolower($this->controller)])) {
                        include_once(_PS_MODULE_DIR_.$module_name.'/controllers/front/'.$this->controller.'.php');
                        $controller_class = $module_name.$this->controller.'ModuleFrontController';
                    }
                }
                $params_hook_action_dispatcher = array('controller_type' => self::FC_FRONT, 'controller_class' => $controller_class, 'is_module' => 1);
            break;

            // Dispatch back office controller + module back office controller
            case self::FC_ADMIN :
                if ($this->use_default_controller && !Tools::getValue('token') && Validate::isLoadedObject(Context::getInstance()->employee) && Context::getInstance()->employee->isLoggedBack()) {
                    Tools::redirectAdmin('index.php?controller='.$this->controller.'&token='.Tools::getAdminTokenLite($this->controller));
                }

                $tab = Tab::getInstanceFromClassName($this->controller, Configuration::get('PS_LANG_DEFAULT'));
                $retrocompatibility_admin_tab = null;

                if ($tab->module) {
                    if (file_exists(_PS_MODULE_DIR_.$tab->module.'/'.$tab->class_name.'.php')) {
                        $retrocompatibility_admin_tab = _PS_MODULE_DIR_.$tab->module.'/'.$tab->class_name.'.php';
                    } else {
                        $controllers = Dispatcher::getControllers(_PS_MODULE_DIR_.$tab->module.'/controllers/admin/');
                        if (!isset($controllers[strtolower($this->controller)])) {
                            $this->controller = $this->controller_not_found;
                            $controller_class = 'AdminNotFoundController';
                        } else {
                            // Controllers in modules can be named AdminXXX.php or AdminXXXController.php
                            include_once(_PS_MODULE_DIR_.$tab->module.'/controllers/admin/'.$controllers[strtolower($this->controller)].'.php');
                            $controller_class = $controllers[strtolower($this->controller)].(strpos($controllers[strtolower($this->controller)], 'Controller') ? '' : 'Controller');
                        }
                    }
                    $params_hook_action_dispatcher = array('controller_type' => self::FC_ADMIN, 'controller_class' => $controller_class, 'is_module' => 1);
                } else {
                    $controllers = Dispatcher::getControllers(array(_PS_ADMIN_DIR_.'/tabs/', _PS_ADMIN_CONTROLLER_DIR_, _PS_OVERRIDE_DIR_.'controllers/admin/'));
                    if (!isset($controllers[strtolower($this->controller)])) {
                        // If this is a parent tab, load the first child
                        if (Validate::isLoadedObject($tab) && $tab->id_parent == 0 && ($tabs = Tab::getTabs(Context::getInstance()->language->id, $tab->id)) && isset($tabs[0])) {
                            Tools::redirectAdmin(Context::getInstance()->link->getAdminLink($tabs[0]['class_name']));
                        }
                        $this->controller = $this->controller_not_found;
                    }

                    $controller_class = $controllers[strtolower($this->controller)];
                    $params_hook_action_dispatcher = array('controller_type' => self::FC_ADMIN, 'controller_class' => $controller_class, 'is_module' => 0);

                    if (file_exists(_PS_ADMIN_DIR_.'/tabs/'.$controller_class.'.php')) {
                        $retrocompatibility_admin_tab = _PS_ADMIN_DIR_.'/tabs/'.$controller_class.'.php';
                    }
                }

                // @retrocompatibility with admin/tabs/ old system
                if ($retrocompatibility_admin_tab) {
                    include_once($retrocompatibility_admin_tab);
                    include_once(_PS_ADMIN_DIR_.'/functions.php');
                    runAdminTab($this->controller, !empty($_REQUEST['ajaxMode']));
                    return;
                }
            break;

            default :
                throw new PrestaShopException('Bad front controller chosen');
        }

        // Instantiate controller
        try {
            // Loading controller
            $controller = Controller::getController($controller_class);

            // Execute hook dispatcher
            if (isset($params_hook_action_dispatcher)) {
                Hook::exec('actionDispatcher', $params_hook_action_dispatcher);
            }

            // Running controller
            $controller->run();
        } catch (PrestaShopException $e) {
            $e->displayMessage();
        }
    }

    /**
     * Set request uri and iso lang
     */
    protected function setRequestUri()
    {
        // Get request uri (HTTP_X_REWRITE_URL is used by IIS)
        if (isset($_SERVER['REQUEST_URI'])) {
            $this->request_uri = $_SERVER['REQUEST_URI'];
        } elseif (isset($_SERVER['HTTP_X_REWRITE_URL'])) {
            $this->request_uri = $_SERVER['HTTP_X_REWRITE_URL'];
        }
        $this->request_uri = rawurldecode($this->request_uri);
		$this->request_uri = preg_replace('#^'.preg_quote(_BASE_DIR_, '#').'#i', '/', $this->request_uri);
		$adminUri = '/'.$this->adminVirtual.'/';
		$endWithSlash = Tools::endsWith($this->request_uri, '/');
		$this->request_uri = $endWithSlash ? $this->request_uri : $this->request_uri . '/';
		$this->request_uri = preg_replace('#^'.preg_quote($adminUri, '#').'#i', '/', $this->request_uri, 1, $count);
		$this->request_uri = $endWithSlash ? $this->request_uri : substr($this->request_uri, 0, strlen($this->request_uri)-1);
		$this->isAdmin = ($count>0);
        // If there are several languages, get language from uri
        if (!$this->isAdmin && $this->use_routes && Language::isMultiLanguageActivated()) {
            if (preg_match('#^/([a-z]{2})(?:/.*)?$#', $this->request_uri, $m)) {
                $_GET['isolang'] = $m[1];
                $this->request_uri = substr($this->request_uri, 3);
            }
        }
    }

    /**
     * Load default routes group by languages
     */
    protected function loadRoutes()
    {
        $context = Context::getInstance();

        $languages = Language::getLanguages();
		$lang = $context->getLang();
        if (!array_key_exists($lang,$languages)) {
            $language_ids[$lang] = null;
        }
		
		$metaRoutes = array();
		foreach ($metaRoutes as $row) {
			if ($row['url_rewrite']) {
				$this->addRoute($row['page'], $row['url_rewrite'], $row['page'], $row['id_lang'], array(), array());
			}
		}

        // Set default routes
        foreach ($languages as $lang => $langObject) {
            foreach ($this->default_routes as $id => $route) {
                $this->addRoute(
                    $id,
                    $route['rule'],
                    $route['controller'],
                    $lang,
                    $route['keywords'],
                    isset($route['params']) ? $route['params'] : array()
                );
            }
        }

        // Load the custom routes prior the defaults to avoid infinite loops
        if ($this->use_routes) {
           // Set default empty route if no empty route (that's weird I know)
            if (!$this->empty_route) {
                $this->empty_route = array(
                    'routeID' =>    'index',
                    'rule' =>        '',
                    'controller' =>    'index',
                );
            }
        }
    }

    /**
     *
     * @param string $route_id Name of the route (need to be uniq, a second route with same name will override the first)
     * @param string $rule Url rule
     * @param string $controller Controller to call if request uri match the rule
     * @param int $lang
     */
    public function addRoute($route_id, $rule, $controller, $lang = null, array $keywords = array(), array $params = array())
    {
        if ($lang === null) {
            $lang = Context::getInstance()->getLang();
        }

        $regexp = preg_quote($rule, '#');
        if ($keywords) {
            $transform_keywords = array();
            preg_match_all('#\\\{(([^{}]*)\\\:)?('.implode('|', array_keys($keywords)).')(\\\:([^{}]*))?\\\}#', $regexp, $m);
            for ($i = 0, $total = count($m[0]); $i < $total; $i++) {
                $prepend = $m[2][$i];
                $keyword = $m[3][$i];
                $append = $m[5][$i];
                $transform_keywords[$keyword] = array(
                    'required' =>    isset($keywords[$keyword]['param']),
                    'prepend' =>    stripslashes($prepend),
                    'append' =>        stripslashes($append),
                );

                $prepend_regexp = $append_regexp = '';
                if ($prepend || $append) {
                    $prepend_regexp = '('.$prepend;
                    $append_regexp = $append.')?';
                }

                if (isset($keywords[$keyword]['param'])) {
                    $regexp = str_replace($m[0][$i], $prepend_regexp.'(?P<'.$keywords[$keyword]['param'].'>'.$keywords[$keyword]['regexp'].')'.$append_regexp, $regexp);
                } else {
                    $regexp = str_replace($m[0][$i], $prepend_regexp.'('.$keywords[$keyword]['regexp'].')'.$append_regexp, $regexp);
                }
            }
            $keywords = $transform_keywords;
        }

        $regexp = '#^/'.$regexp.'$#u';
        if (!isset($this->routes[$lang])) {
            $this->routes[$lang] = array();
        }

        $this->routes[$lang][$route_id] = array(
            'rule' =>        $rule,
            'regexp' =>        $regexp,
            'controller' =>    $controller,
            'keywords' =>    $keywords,
            'params' =>        $params,
        );
    }

    /**
     * Check if a route exists
     *
     * @param string $route_id
     * @param int $lang
     * @return bool
     */
    public function hasRoute($route_id, $lang = null)
    {
        if ($lang === null) {
            $lang = (int)Context::getInstance()->getLang();
        }

        return isset($this->routes[$lang]) && isset($this->routes[$lang][$route_id]);
    }

    /**
     * Check if a keyword is written in a route rule
     *
     * @param string $route_id
     * @param int $id_lang
     * @param string $keyword
     * @param int $id_shop
     * @return bool
     */
    public function hasKeyword($route_id, $id_lang, $keyword)
    {
        if (empty($this->routes)) {
            $this->loadRoutes();
        }

        if (!isset($this->routes[$id_lang]) || !isset($this->routes[$id_lang][$route_id])) {
            return false;
        }

        return preg_match('#\{([^{}]*:)?'.preg_quote($keyword, '#').'(:[^{}]*)?\}#', $this->routes[$id_lang][$route_id]['rule']);
    }

    /**
     * Check if a route rule contain all required keywords of default route definition
     *
     * @param string $route_id
     * @param string $rule Rule to verify
     * @param array $errors List of missing keywords
     */
    public function validateRoute($route_id, $rule, &$errors = array())
    {
        $errors = array();
        if (!isset($this->default_routes[$route_id])) {
            return false;
        }

        foreach ($this->default_routes[$route_id]['keywords'] as $keyword => $data) {
            if (isset($data['param']) && !preg_match('#\{([^{}]*:)?'.$keyword.'(:[^{}]*)?\}#', $rule)) {
                $errors[] = $keyword;
            }
        }

        return (count($errors)) ? false : true;
    }

    /**
     * Create an url from
     *
     * @param string $route_id Name the route
     * @param int $id_lang
     * @param array $params
     * @param bool $use_routes If false, don't use to create this url
     * @param string $anchor Optional anchor to add at the end of this url
     */
    public function createUrl($route_id, $id_lang = null, array $params = array(), $force_routes = false, $anchor = '')
    {
        if ($id_lang === null) {
            $id_lang = (int)Context::getInstance()->language->id;
        }
        if (empty($this->routes)) {
            $this->loadRoutes();
        }

        if (!isset($this->routes[$id_lang][$route_id])) {
            $query = http_build_query($params, '', '&');
            $index_link = $this->use_routes ? '' : 'index.php';
            return ($route_id == 'index') ? $index_link.(($query) ? '?'.$query : '') : ((trim($route_id) == '') ? '' : 'index.php?controller='.$route_id).(($query) ? '&'.$query : '').$anchor;
        }
        $route = $this->routes[$id_lang][$route_id];
        // Check required fields
        $query_params = isset($route['params']) ? $route['params'] : array();
        foreach ($route['keywords'] as $key => $data) {
            if (!$data['required']) {
                continue;
            }

            if (!array_key_exists($key, $params)) {
                throw new PrestaShopException('Dispatcher::createUrl() miss required parameter "'.$key.'" for route "'.$route_id.'"');
            }
            if (isset($this->default_routes[$route_id])) {
                $query_params[$this->default_routes[$route_id]['keywords'][$key]['param']] = $params[$key];
            }
        }

        // Build an url which match a route
        if ($this->use_routes || $force_routes) {
            $url = $route['rule'];
            $add_param = array();

            foreach ($params as $key => $value) {
                if (!isset($route['keywords'][$key])) {
                    if (!isset($this->default_routes[$route_id]['keywords'][$key])) {
                        $add_param[$key] = $value;
                    }
                } else {
                    if ($params[$key]) {
                        $replace = $route['keywords'][$key]['prepend'].$params[$key].$route['keywords'][$key]['append'];
                    } else {
                        $replace = '';
                    }
                    $url = preg_replace('#\{([^{}]*:)?'.$key.'(:[^{}]*)?\}#', $replace, $url);
                }
            }
            $url = preg_replace('#\{([^{}]*:)?[a-z0-9_]+?(:[^{}]*)?\}#', '', $url);
            if (count($add_param)) {
                $url .= '?'.http_build_query($add_param, '', '&');
            }
        }
        // Build a classic url index.php?controller=foo&...
        else {
            $add_params = array();
            foreach ($params as $key => $value) {
                if (!isset($route['keywords'][$key]) && !isset($this->default_routes[$route_id]['keywords'][$key])) {
                    $add_params[$key] = $value;
                }
            }

            if (!empty($route['controller'])) {
                $query_params['controller'] = $route['controller'];
            }
            $query = http_build_query(array_merge($add_params, $query_params), '', '&');
            if ($this->multilang_activated) {
                $query .= (!empty($query) ? '&' : '').'id_lang='.(int)$id_lang;
            }
            $url = 'index.php?'.$query;
        }

        return $url.$anchor;
    }

    /**
     * Retrieve the controller from url or request uri if routes are activated
     *
     * @return string
     */
    public function getController($id_shop = null)
    {
        if (defined('_PS_ADMIN_DIR_')) {
            $_GET['controllerUri'] = Tools::getvalue('controller');
        }
        if ($this->controller) {
            $_GET['controller'] = $this->controller;
            return $this->controller;
        }

        if (isset(Context::getInstance()->shop) && $id_shop === null) {
            $id_shop = (int)Context::getInstance()->shop->id;
        }

        $controller = Tools::getValue('controller');

        if (isset($controller) && is_string($controller) && preg_match('/^([0-9a-z_-]+)\?(.*)=(.*)$/Ui', $controller, $m)) {
            $controller = $m[1];
            if (isset($_GET['controller'])) {
                $_GET[$m[2]] = $m[3];
            } elseif (isset($_POST['controller'])) {
                $_POST[$m[2]] = $m[3];
            }
        }

        if (!Validate::isControllerName($controller)) {
            $controller = false;
        }

        // Use routes ? (for url rewriting)
        if ($this->use_routes && !$controller && !defined('_PS_ADMIN_DIR_')) {
            if (!$this->request_uri) {
                return strtolower($this->controller_not_found);
            }
            $controller = $this->controller_not_found;
            $test_request_uri = preg_replace('/(=http:\/\/)/', '=', $this->request_uri);

            // If the request_uri matches a static file, then there is no need to check the routes, we keep "controller_not_found" (a static file should not go through the dispatcher)
            if (!preg_match('/\.(gif|jpe?g|png|css|js|ico)$/i', parse_url($test_request_uri, PHP_URL_PATH))) {
                // Add empty route as last route to prevent this greedy regexp to match request uri before right time
                if ($this->empty_route) {
                    $this->addRoute($this->empty_route['routeID'], $this->empty_route['rule'], $this->empty_route['controller'], Context::getInstance()->language->id, array(), array(), $id_shop);
                }

                list($uri) = explode('?', $this->request_uri);

                if (isset($this->routes[$id_shop][Context::getInstance()->language->id])) {
                    foreach ($this->routes[$id_shop][Context::getInstance()->language->id] as $route) {
                        if (preg_match($route['regexp'], $uri, $m)) {
                            // Route found ! Now fill $_GET with parameters of uri
                            foreach ($m as $k => $v) {
                                if (!is_numeric($k)) {
                                    $_GET[$k] = $v;
                                }
                            }

                            $controller = $route['controller'] ? $route['controller'] : $_GET['controller'];
                            if (!empty($route['params'])) {
                                foreach ($route['params'] as $k => $v) {
                                    $_GET[$k] = $v;
                                }
                            }

                            // A patch for module friendly urls
                            if (preg_match('#module-([a-z0-9_-]+)-([a-z0-9_]+)$#i', $controller, $m)) {
                                $_GET['module'] = $m[1];
                                $_GET['fc'] = 'module';
                                $controller = $m[2];
                            }

                            if (isset($_GET['fc']) && $_GET['fc'] == 'module') {
                                $this->front_controller = self::FC_MODULE;
                            }
                            break;
                        }
                    }
                }
            }

            if ($controller == 'index' || preg_match('/^\/index.php(?:\?.*)?$/', $this->request_uri)) {
                $controller = $this->useDefaultController();
            }
        }

        $this->controller = str_replace('-', '', $controller);
        $_GET['controller'] = $this->controller;
        return $this->controller;
    }

    /**
     * Get list of all available FO controllers
     *
     * @var mixed $dirs
     * @return array
     */
    public static function getControllers($dirs)
    {
        if (!is_array($dirs)) {
            $dirs = array($dirs);
        }

        $controllers = array();
        foreach ($dirs as $dir) {
            $controllers = array_merge($controllers, Dispatcher::getControllersInDirectory($dir));
        }
        return $controllers;
    }

    /**
     * Get list of all available Module Front controllers
     *
     * @return array
     */
    public static function getModuleControllers($type = 'all', $module = null)
    {
        $modules_controllers = array();
        if (is_null($module)) {
            $modules = Module::getModulesOnDisk(true);
        } elseif (!is_array($module)) {
            $modules = array(Module::getInstanceByName($module));
        } else {
            $modules = array();
            foreach ($module as $_mod) {
                $modules[] = Module::getInstanceByName($_mod);
            }
        }

        foreach ($modules as $mod) {
            foreach (Dispatcher::getControllersInDirectory(_PS_MODULE_DIR_.$mod->name.'/controllers/') as $controller) {
                if ($type == 'admin') {
                    if (strpos($controller, 'Admin') !== false) {
                        $modules_controllers[$mod->name][] = $controller;
                    }
                } elseif ($type == 'front') {
                    if (strpos($controller, 'Admin') === false) {
                        $modules_controllers[$mod->name][] = $controller;
                    }
                } else {
                    $modules_controllers[$mod->name][] = $controller;
                }
            }
        }
        return $modules_controllers;
    }

    /**
     * Get list of available controllers from the specified dir
     *
     * @param string $dir Directory to scan (recursively)
     * @return array
     */
    public static function getControllersInDirectory($dir)
    {
        if (!is_dir($dir)) {
            return array();
        }

        $controllers = array();
        $controller_files = scandir($dir);
        foreach ($controller_files as $controller_filename) {
            if ($controller_filename[0] != '.') {
                if (!strpos($controller_filename, '.php') && is_dir($dir.$controller_filename)) {
                    $controllers += Dispatcher::getControllersInDirectory($dir.$controller_filename.DIRECTORY_SEPARATOR);
                } elseif ($controller_filename != 'index.php') {
                    $key = str_replace(array('controller.php', '.php'), '', strtolower($controller_filename));
                    $controllers[$key] = basename($controller_filename, '.php');
                }
            }
        }

        return $controllers;
    }
}

?>
