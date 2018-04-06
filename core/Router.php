<?php
namespace core;

use core\models\Configuration;
use core\models\Language;

class Router{

    /**
     * @var Dispatcher
     */
    protected static $instance = null;
	
    protected $isAdmin = false;
    protected $isModule = false;

    /**
     * @var array List of default routes
     */
	 
	protected $frontDefaultRoutes = array(
		'module' => array(
            'controller' =>    null,
            'rule' =>        'module/{module}{/:controller}',
            'keywords' => array(
                'module' =>        array('regexp' => '[_a-zA-Z0-9_-]+', 'param' => 'module'),
                'controller' =>        array('regexp' => '[_a-zA-Z0-9_-]+', 'param' => 'controller'),
            ),
            'params' => array(
                'is_module' => '1',
            ),
        ),
		'controller_rule' => array(
            'controller' =>    null,
            'rule' =>        '{controller}',
            'keywords' => array(
                'controller' =>        array('regexp' => '[_a-zA-Z0-9_-]+', 'param' => 'controller'),
            )
        )
	);
	protected $adminDefaultRoutes = array(
		'module' => array(
            'controller' =>    null,
            'rule' =>        'module/{module}{/:controller}{/:action}{/:id}',
            'keywords' => array(
                'module' =>        array('regexp' => '[_a-zA-Z0-9_-]+', 'param' => 'module'),
                'controller' =>        array('regexp' => '[_a-zA-Z0-9_-]+', 'param' => 'controller'),
                'action' =>        array('regexp' => '[_a-zA-Z0-9_-]+', 'param' => 'action'),
                'id' =>        array('regexp' => '[_a-zA-Z0-9_-]+', 'param' => 'id'),
            ),
            'params' => array(
                'is_module' => '1',
            ),
        ),
		'controller_rule' => array(
            'controller' =>    null,
            'rule' =>        '{controller}{/:action}{/:id}',
            'keywords' => array(
                'controller' =>        array('regexp' => '[_a-zA-Z0-9_-]+', 'param' => 'controller'),
                'action' =>        array('regexp' => '[_a-zA-Z0-9_-]+', 'param' => 'action'),
                'id' =>        array('regexp' => '[_a-zA-Z0-9_-]+', 'param' => 'id'),
            )
        )
	);
	
    /**
     * @var bool If true, use routes to build URL (mod rewrite must be activated)
     */
    protected $useRoutes = false;

    protected $multilangActivated = false;

    /**
     * @var array List of loaded routes
     */
    protected $routes = array();

    /**
     * @var string Current controller name
     */
    protected $controller;
	
    protected $moduleName = '';

    /**
     * @var string Current request uri
     */
    protected $requestUri;

    /**
     * @var array Store empty route (a route with an empty rule)
     */
    protected $emptyRoute;

    /**
     * @var string Set default controller, which will be used if http parameter 'controller' is empty
     */
    protected $defaultController;
    protected $useDefaultController = false;
	
	protected $defaultRoutes = array();

    /**
     * @var string Controller to use if found controller doesn't exist
     */
    protected $controllerNotFound = 'pagenotfound';
	
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
		$this->useRoutes = (bool)Configuration::get('REWRITING_SETTINGS');
		$this->adminVirtual = (defined('_VIRTUAL_ADMIN_DIR_') && !empty(_VIRTUAL_ADMIN_DIR_)) ? _VIRTUAL_ADMIN_DIR_ : 'admin';
        $this->setRequestUri();
		
		Context::getInstance()->init($this->isAdmin);
        // Switch language if needed (only on front)
        if (!$this->isAdmin) {
            Tools::switchLanguage();
        }
		Context::getInstance()->resetFactoryLang();
		if (Language::isMultiLanguageActivated()) {
            $this->multilangActivated = true;
        }
        $this->loadRoutes();
    }

    public function useDefaultController()
    {
        $this->useDefaultController = true;
        if ($this->defaultController === null) {
            if ($this->isModule) {
                $this->defaultController = 'default';
            } else {
                $this->defaultController = 'index';
            }
        }
        return $this->defaultController;
    }

    /**
     * Find the controller and instantiate it
     */
    public function dispatch()
    {
       // Get current controller
        $this->getController();
        if (!$this->controller) {
            $this->controller = $this->useDefaultController();
        }
		$isVirtual = false;
		$subFolder = FileTools::getSubFolder($this->isAdmin);
		$this->moduleName = $this->isModule ? $this->moduleName : '';
		$directories = array(
			FileTools::getControllerDir($this->isAdmin, $this->moduleName),
			FileTools::getOverrideDir($this->moduleName)._CONTROLLERS_PATH_.'/'.$subFolder
		);
		$controllers = FileTools::getControllers($directories, $this->isAdmin);
		if($this->isAdmin && !isset($controllers[$this->controller])){
			$virtuals = FileTools::getVirtualControllers($directories);
			if(isset($virtuals[$this->controller])){
				$isVirtual = true;
			}
		}
		if(!isset($controllers[$this->controller])){
			$this->controller = $this->controllerNotFound;
		}
		if(!isset($controllers[$this->controller])){
			throw new \Exception('Controller "'.$this->controller.'" does not exist');
		}else{
			$class = FileTools::getNamespaceFromFile($controllers[$this->controller]);
			$controller = new $class();
			$controller->run();
		}
    }

    /**
     * Set request uri and iso lang
     */
    protected function setRequestUri()
    {
        // Get request uri (HTTP_X_REWRITE_URL is used by IIS)
        if (isset($_SERVER['REQUEST_URI'])) {
            $this->requestUri = $_SERVER['REQUEST_URI'];
        } elseif (isset($_SERVER['HTTP_X_REWRITE_URL'])) {
            $this->requestUri = $_SERVER['HTTP_X_REWRITE_URL'];
        }
        $this->requestUri = rawurldecode($this->requestUri);
		$this->requestUri = preg_replace('#^'.preg_quote(_BASE_DIR_, '#').'#i', '/', $this->requestUri);
		$endWithSlash = StringTools::endsWith($this->requestUri, '/');
		$this->requestUri = $endWithSlash ? $this->requestUri : $this->requestUri . '/';
		$this->requestUri = preg_replace('#^'.preg_quote('/'.$this->adminVirtual, '#').'[/?]#i', '/', $this->requestUri, 1, $count);
		$this->requestUri = $endWithSlash ? $this->requestUri : substr($this->requestUri, 0, strlen($this->requestUri)-1);
		$this->isAdmin = ($count>0);
        // If there are several languages, get language from uri
        if (!$this->isAdmin && $this->useRoutes && Language::isMultiLanguageActivated()) {
            if (preg_match('#^/([a-z]{2})(?:/.*)?$#', $this->requestUri, $m)) {
                $_GET['isolang'] = $m[1];
                $this->requestUri = substr($this->requestUri, 3);
            }
        }
    }

    /**
     * Load default routes group by languages
     */
    protected function loadRoutes()
    {
        $context = Context::getInstance();

        $languages = Language::getLanguages(true);
		$lang = $context->getLang();
		$langKeys = $this->isAdmin ? array() : array_keys($languages);
        if (!in_array($lang,$langKeys)) {
            $langKeys[] = $lang;
        }
		
		$metaRoutes = array();
		foreach ($metaRoutes as $row) {
			if ($row['url_rewrite']) {
				$this->addRoute($row['page'], $row['url_rewrite'], $row['page'], $row['lang'], array(), array());
			}
		}
		$routesFiles = FileTools::getRouteFiles($this->isAdmin);
		$dom = new \DOMDocument;
		foreach ($routesFiles as $file){
            $dom->load($file);
			$list = $dom->getElementsByTagName('route');
			foreach ($list as $item){
				$keywords = array();
				$params = array();
				$keywordsElement = $item->getElementsByTagName('keywords');
				foreach ($keywordsElement as $keywordsItem){
					$name = $keywordsItem->getAttribute('name');
					$param = $keywordsItem->getAttribute('param');
					$keywords[$name]['regexp'] =$keywordsItem->getAttribute('regexp');
					if(!empty($param)){
						$keywords[$name]['param'] =$param;
					}
				}
				$paramsElement = $item->getElementsByTagName('keywords');
				foreach ($paramsElement as $paramsItem){
					$params[$paramsItem->getAttribute('name')] =$paramsItem->getAttribute('value');
				}
				$this->addRoute($item->getAttribute('name'), $item->getAttribute('rule'), $item->getAttribute('controller'),  $langKeys, $keywords, $params);
			}
        }
		$this->defaultRoutes = $this->isAdmin ? $this->adminDefaultRoutes : $this->frontDefaultRoutes;
        // Set default routes
        foreach ($this->defaultRoutes as $id => $route) {
			$this->addRoute(
				$id,
				$route['rule'],
				$route['controller'],
				$langKeys,
				$route['keywords'],
				isset($route['params']) ? $route['params'] : array()
			);
		}
        // Load the custom routes prior the defaults to avoid infinite loops
        if ($this->useRoutes) {
           // Set default empty route if no empty route (that's weird I know)
            if (!$this->emptyRoute) {
                $this->emptyRoute = array(
                    'routeID' =>    'index',
                    'rule' =>        '',
                    'controller' =>    'index',
                );
            }
        }
    }

    /**
     *
     * @param string $idRoute Name of the route (need to be uniq, a second route with same name will override the first)
     * @param string $rule Url rule
     * @param string $controller Controller to call if request uri match the rule
     * @param string $lang
     */
    public function addRoute($idRoute, $rule, $controller, $lang = null, array $keywords = array(), array $params = array())
    {
        if ($lang === null) {
            $lang = Context::getInstance()->getLang();
        }

        $regexp = preg_quote($rule, '#');
        if ($keywords) {
            $transformKeywords = array();
            preg_match_all('#\\\{(([^{}]*)\\\:)?('.implode('|', array_keys($keywords)).')(\\\:([^{}]*))?\\\}#', $regexp, $m);
            for ($i = 0, $total = count($m[0]); $i < $total; $i++) {
                $prepend = $m[2][$i];
                $keyword = $m[3][$i];
                $append = $m[5][$i];
                $transformKeywords[$keyword] = array(
                    'required' =>    isset($keywords[$keyword]['param']),
                    'prepend' =>    stripslashes($prepend),
                    'append' =>        stripslashes($append),
                );

                $prependRegexp = $appendRegexp = '';
                if ($prepend || $append) {
                    $prependRegexp = '('.$prepend;
                    $appendRegexp = $append.')?';
                }

                if (isset($keywords[$keyword]['param'])) {
                    $regexp = str_replace($m[0][$i], $prependRegexp.'(?P<'.$keywords[$keyword]['param'].'>'.$keywords[$keyword]['regexp'].')'.$appendRegexp, $regexp);
                } else {
                    $regexp = str_replace($m[0][$i], $prependRegexp.'('.$keywords[$keyword]['regexp'].')'.$appendRegexp, $regexp);
                }
            }
            $keywords = $transformKeywords;
        }

        $regexp = '#^/'.$regexp.'$#u';
		$langKeys = is_array($lang)? $lang : array($lang);
		foreach($langKeys as $langKey){
			$this->routes[$langKey][$idRoute] = array(
				'rule' =>        $rule,
				'regexp' =>        $regexp,
				'controller' =>    $controller,
				'keywords' =>    $keywords,
				'params' =>        $params,
			);
		}
    }

    /**
     * Check if a route exists
     *
     * @param string $idRoute
     * @param string $lang
     * @return bool
     */
    public function hasRoute($idRoute, $lang = null)
    {
        if ($lang === null) {
            $lang = (int)Context::getInstance()->getLang();
        }

        return isset($this->routes[$lang]) && isset($this->routes[$lang][$idRoute]);
    }

    /**
     * Check if a keyword is written in a route rule
     *
     * @param string $idRoute
     * @param string $lang
     * @param string $keyword
     * @return bool
     */
    public function hasKeyword($idRoute, $lang, $keyword)
    {
        if (empty($this->routes)) {
            $this->loadRoutes();
        }

        if (!isset($this->routes[$lang]) || !isset($this->routes[$lang][$idRoute])) {
            return false;
        }

        return preg_match('#\{([^{}]*:)?'.preg_quote($keyword, '#').'(:[^{}]*)?\}#', $this->routes[$lang][$idRoute]['rule']);
    }

    /**
     * Check if a route rule contain all required keywords of default route definition
     *
     * @param string $idRoute
     * @param string $rule Rule to verify
     * @param array $errors List of missing keywords
     */
    public function validateRoute($idRoute, $rule, &$errors = array())
    {
        $errors = array();
        if (!isset($this->defaultRoutes[$idRoute])) {
            return false;
        }

        foreach ($this->defaultRoutes[$idRoute]['keywords'] as $keyword => $data) {
            if (isset($data['param']) && !preg_match('#\{([^{}]*:)?'.$keyword.'(:[^{}]*)?\}#', $rule)) {
                $errors[] = $keyword;
            }
        }

        return (count($errors)) ? false : true;
    }

    /**
     * Create an url from
     *
     * @param string $idRoute Name the route
     * @param string $lang
     * @param array $params
     * @param bool $forceRoutes If false, don't use to create this url
     * @param string $anchor Optional anchor to add at the end of this url
     */
    public function createUrl($idRoute, $lang = null, array $params = array(), $forceRoutes = false, $anchor = '')
    {
        if ($lang === null) {
            $lang = (int)Context::getInstance()->getLang();
        }
        if (empty($this->routes)) {
            $this->loadRoutes();
        }

        if (!isset($this->routes[$lang][$idRoute])) {
            $query = http_build_query($params, '', '&');
            $indexLink = $this->useRoutes ? '' : 'index.php';
            return ($idRoute == 'index') ? $indexLink.(($query) ? '?'.$query : '') : ((trim($idRoute) == '') ? '' : 'index.php?controller='.$idRoute).(($query) ? '&'.$query : '').$anchor;
        }
        $route = $this->routes[$lang][$idRoute];
        // Check required fields
        $queryParams = isset($route['params']) ? $route['params'] : array();
        foreach ($route['keywords'] as $key => $data) {
            if (!$data['required']) {
                continue;
            }

            if (!array_key_exists($key, $params)) {
                throw new PrestaShopException('self::createUrl() miss required parameter "'.$key.'" for route "'.$idRoute.'"');
            }
            if (isset($this->defaultRoutes[$idRoute])) {
                $queryParams[$this->defaultRoutes[$idRoute]['keywords'][$key]['param']] = $params[$key];
            }
        }

        // Build an url which match a route
        if ($this->useRoutes || $forceRoutes) {
            $url = $route['rule'];
            $addParam = array();

            foreach ($params as $key => $value) {
                if (!isset($route['keywords'][$key])) {
                    if (!isset($this->defaultRoutes[$idRoute]['keywords'][$key])) {
                        $addParam[$key] = $value;
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
            if (count($addParam)) {
                $url .= '?'.http_build_query($addParam, '', '&');
            }
        }
        // Build a classic url index.php?controller=foo&...
        else {
            $addParams = array();
            foreach ($params as $key => $value) {
                if (!isset($route['keywords'][$key]) && !isset($this->defaultRoutes[$idRoute]['keywords'][$key])) {
                    $addParams[$key] = $value;
                }
            }

            if (!empty($route['controller'])) {
                $queryParams['controller'] = $route['controller'];
            }
            $query = http_build_query(array_merge($addParams, $queryParams), '', '&');
            if ($this->multilangActivated) {
                $query .= (!empty($query) ? '&' : '').'lang='.$lang;
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
    public function getController()
    {
        if ($this->isAdmin) {
            $_GET['controllerUri'] = Tools::getvalue('controller');
        }
        if ($this->controller) {
            $_GET['controller'] = $this->controller;
            return $this->controller;
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
        if ($this->useRoutes && !$controller) {
			if (!$this->requestUri) {
                return strtolower($this->controllerNotFound);
            }
            $controller = $this->controllerNotFound;
            $testRequestUri = preg_replace('/(=http:\/\/)/', '=', $this->requestUri);

            // If the request_uri matches a static file, then there is no need to check the routes, we keep "controller_not_found" (a static file should not go through the dispatcher)
            if (!preg_match('/\.(gif|jpe?g|png|css|js|ico)$/i', parse_url($testRequestUri, PHP_URL_PATH))) {
                // Add empty route as last route to prevent this greedy regexp to match request uri before right time
				$lang = (string)Context::getInstance()->getLang();
                if ($this->emptyRoute) {
                    $this->addRoute($this->emptyRoute['routeID'], $this->emptyRoute['rule'], $this->emptyRoute['controller'], $lang, array(), array());
                }

                list($uri) = explode('?', $this->requestUri);
                if (isset($this->routes[$lang])) {
                    foreach ($this->routes[$lang] as $route) {
                        if (preg_match($route['regexp'], $uri, $m)) {
                            // Route found ! Now fill $_GET with parameters of uri
                            foreach ($m as $k => $v) {
                                if (!is_numeric($k)) {
                                    $_GET[$k] = $v;
                                }
                            }

                            $controller = $route['controller'] ? $route['controller'] : false;
                            $controller = isset($_GET['controller']) ? $_GET['controller'] : false;
                            if (!empty($route['params'])) {
                                foreach ($route['params'] as $k => $v) {
                                    $_GET[$k] = $v;
                                }
                            }

                            // A patch for module friendly urls
                            if (preg_match('#module-([a-z0-9_-]+)-([a-z0-9_]+)$#i', $controller, $m)) {
                                $_GET['module'] = $m[1];
								
                                $_GET['is_module'] = '1';
                                $controller = $m[2];
                            }
                            if (isset($_GET['is_module']) && $_GET['is_module'] == '1') {
                                $this->isModule = true;
								$this->moduleName = Tools::getvalue('module');
                            }
                            break;
                        }
                    }
                }
            }

            if ($controller == 'index' || preg_match('/^\/index.php(?:\?.*)?$/', $this->requestUri)) {
                $controller = $this->useDefaultController();
            }
        }

        $this->controller = str_replace('-', '', $controller);
        $_GET['controller'] = $this->controller;
        return $this->controller;
    }
}

?>
