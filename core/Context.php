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
    
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new Context();
            self::$instance->init();
        }

        return self::$instance;
    }
    
    public function cloneContext()
    {
        return clone($this);
    }
    
    public function init()
    {
		var_dump(Tools::getHttpHost(true));
		var_dump($_SERVER['REQUEST_URI']);
		var_dump(Tools::getValue('test'));
		var_dump(Tools::getValue('test6'));
		$this->lang = 'en';
		$isAdmin = true;
		$factory = Factory::getInstance();
		$languageDAO = Factory::getDAOInstance('Language');
		
		// GET ALL Language
		$languages = Language::getLanguages();
		$factory->setLanguages($languages);
		$factory->setLang($this->lang);
		$languageDAO->setLanguages($languages);
		$languageDAO->setLang($this->lang);
		
		
		
		/* Instantiate cookie */
		/*$cookie_lifetime = $isAdmin ? (int)Configuration::get('PS_COOKIE_LIFETIME_BO') : (int)Configuration::get('PS_COOKIE_LIFETIME_FO');
		if ($cookie_lifetime > 0) {
			$cookie_lifetime = time() + (max($cookie_lifetime, 1) * 3600);
		}

		if ($isAdmin) {
			$cookie = new Cookie('Admin', '', $cookie_lifetime);
		} else {
			$cookie = new Cookie('Front', '', $cookie_lifetime);
		}
		$this->cookie = $cookie;*/
    }
	
	public function setLang($lang)
    {
		$this->lang = $lang;
    }
	
	public function getLang()
    {
		return $this->lang;
    }
}
