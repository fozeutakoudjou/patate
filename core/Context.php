<?php
namespace core;

use core\models\Language;
use core\models\Configuration;
//use Library\models\User;

use core\dao\Factory;
class Context
{
    /* @var Context */
    protected static $instance;

    /** @var Library\models\User */
    public $user;

    /** @var Cookie */
    protected $cookie;

    /** @var String */
    protected $lang;
	
    protected $link;
	
    protected $controller;
	
	protected $langSetted = false;
	
    protected $initialized = false;
	
    protected $template = false;
    
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new Context();
            self::$instance->firtInit();
        }

        return self::$instance;
    }
	
	protected function firtInit()
    {
		$factory = Factory::getInstance();
		$languages = Language::getLanguages();
		$factory->setLanguages($languages);
		$this->lang = Configuration::get('DEFAULT_LANG');
		$factory->setLang($this->lang);
		$httpsLink = (Link::usingSecureMode() && Configuration::get('SSL_ENABLED')) ? 'https://' : 'http://';
		$this->link = new Link($httpsLink, $httpsLink);
		$this->template = Template::getInstance();
    }
    
    public function init($isAdmin)
    {
		if(!$this->initialized){
			$cookieLifetime = $isAdmin ? (int)Configuration::get('COOKIE_LIFETIME_BO') : (int)Configuration::get('COOKIE_LIFETIME_FO');
			if ($cookieLifetime > 0) {
				$cookieLifetime = time() + (max($cookieLifetime, 1) * 3600);
			}
			$cookieName = $isAdmin ? 'Admin' : 'Front';
			$this->cookie = new Cookie($cookieName, '', $cookieLifetime);
			
			$daoUser = Factory::getDAOInstance('User');
			$user = $daoUser->getById($this->cookie->id_user, true);
			if($isAdmin && ($user != null) && $user->isAdmin()){
				$cookie->lang = $user->getPrefferedLang();
			}
			$activedLanguages = Language::getLanguages(true);
			$lang = (isset($this->cookie->lang) && $this->cookie->lang && isset($activedLanguages[$this->cookie->lang])) ? $this->cookie->lang : Configuration::get('DEFAULT_LANG');
			$this->cookie->lang = $lang;
			if($user==null){
				$user = $daoUser->createModel();
			}
			if(!$isAdmin){
				if (!Validate::isLoadedObject($user)) {
					$this->cookie->logout();
				} else {
					$user->setLogged(true);
				}
			}
			$this->user = $user;
			$this->initialized = true;
		}
    }
	
	public function setLang($lang)
    {
		$this->lang = $lang;
    }
	
	public function resetFactoryLang()
    {
		$factory = Factory::getInstance();
		$factory->setLang($this->lang);
		Factory::getDAOInstance('Configuration')->setDefaultLang($this->lang);
    }
	
	public function getLang()
    {
		return $this->lang;
    }
	
	public function getCookie()
    {
		return $this->cookie;
    }
	
	public function getUser()
    {
		return $this->user;
    }
	
	public function setController($controller)
    {
		$this->controller = $controller;
    }
	
	public function getTemplate()
    {
		return $this->template;
    }
	public function getLink()
    {
		return $this->link;
    }
	public function getController()
    {
		return $this->controller;
    }
}
