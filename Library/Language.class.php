<?php

namespace Library;
/**
 * Description of Language
 *
 * @author FRANCIS FOZEU
 */
class Language extends ApplicationComponent{
    //put your code here
	protected static $_LANGUAGES;
    
    public function __construct(Application $app){
        parent::__construct($app);
        $manager = new Managers('PDO', DbFactory::getPdoInstance());
        $managerlang = $manager->getManagerOf('Lang');
        $this->langs = $managerlang->findByName('active',1);
    }
    
    public function getList(){
        return $this->langs;
    }
	
	public static function loadLanguages()
    {
        self::$_LANGUAGES = array();

        $sql = 'SELECT l.*, ls.`id_shop`
				FROM `'._DB_PREFIX_.'lang` l
				LEFT JOIN `'._DB_PREFIX_.'lang_shop` ls ON (l.id_lang = ls.id_lang)';

        $result = Db::getInstance()->executeS($sql);
        foreach ($result as $row) {
            if (!isset(self::$_LANGUAGES[(int)$row['id_lang']])) {
                self::$_LANGUAGES[(int)$row['id_lang']] = $row;
            }
        }
    }
	
	public static function getLanguages($active = true, $ids_only = false)
    {
        if (!self::$_LANGUAGES) {
            Language::loadLanguages();
        }

        $languages = array();
        foreach (self::$_LANGUAGES as $language) {
            if ($active && !$language['active'] || ($id_shop && !isset($language['shops'][(int)$id_shop]))) {
                continue;
            }

            $languages[] = $ids_only ? $language['id_lang'] : $language;
        }

        return $languages;
    }
}
