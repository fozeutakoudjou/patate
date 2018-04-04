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
		$this->lang = Configuration::get('DEFAULT_LANG');
		// GET ALL Language
		$languages = Language::getLanguages();
		$factory->setLanguages($languages);
		$factory->setLang($this->lang);
		$https_link = (Link::usingSecureMode() && Configuration::get('SSL_ENABLED')) ? 'https://' : 'http://';
		$this->link = new Link($https_link, $https_link);
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
			$this->cookie = new Cookie('Front', '', $cookieLifetime);
			
			$daoUser = Factory::getDAOInstance('User');
			$user = $daoUser->getById($this->cookie->id_user, true);
			if($user != null){
				$cookie->lang = $user->getPrefferedLang();
			}
			$activedLanguages = Language::getLanguages(true);
			$lang = (isset($this->cookie->lang) && $this->cookie->lang && isset($activedLanguages[$this->cookie->lang])) ? $this->cookie->lang : Configuration::get('DEFAULT_LANG');
			$this->cookie->lang = $lang;
			if (!Validate::isLoadedObject($user)) {
				$this->cookie->logout();
			} else {
				$customer->logged = true;
			}
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
		Factory::getDAOInstance('Configuration')->setLang($this->lang);
    }
	
	public function getLang()
    {
		return $this->lang;
    }
	
	public function getCookie()
    {
		return $this->cookie;
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
}
