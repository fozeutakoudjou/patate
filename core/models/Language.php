<?php
namespace core\models;

use core\dao\Factory;

class Language extends Model{
	protected $id;
	protected $name;
	protected $active;
	protected $isoCode;
	protected $languageCode;
	protected $dateFormatLite;
	protected $dateFormatFull;
	protected $rtl;
	
	private static $_LANGUAGES;
	private static $totalActiveLanguages;
	
	protected $definition = array(
		'entity' => 'language',
		'primary' => 'id',
		'auto_increment' => true,
		'fields' => array(
			'name' => array('type' => self::TYPE_STRING, 'required' => true, 'validate' => 'isGenericName', 'size' => 32),
			'active' => array('type' => self::TYPE_BOOL, 'required' => true, 'validate' => 'isBool', 'default' => '0'),
			'isoCode' => array('type' => self::TYPE_STRING, 'required' => true, 'unique' => true, 'validate' => 'isLanguageIsoCode', 'size' => 2),
			'languageCode' => array('type' => self::TYPE_STRING, 'validate' => 'isLanguageCode', 'size' => 5),
			'dateFormatLite' => array('type' => self::TYPE_STRING, 'validate' => 'isPhpDateFormat', 'default' => 'Y-m-d', 'size' => 32),
			'dateFormatFull' => array('type' => self::TYPE_STRING, 'validate' => 'isPhpDateFormat', 'default' => 'Y-m-d H:i:s', 'size' => 32),
			'rtl' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'default' => '0')
		)
	);

	public function getKey(){
		return $this->isoCode;
	}
	
	public static function getLanguages($active = true)
    {
        if (!isset(self::$_LANGUAGES)) {
			self::$_LANGUAGES = array();
			$dao = Factory::getDAOInstance('Language');
            self::$_LANGUAGES = $dao->getAll();
        }

        $languages = array();
        foreach (self::$_LANGUAGES as $language) {
            if (!$active || $language->isActive()) {
                $languages[$language->getKey()] = $language;
            }
        }

        return $languages;
    }
	
	public static function isMultiLanguageActivated()
    {
        if (self::$totalActiveLanguages === null) {
			self::$totalActiveLanguages = count(self::getLanguages(true));
        }
        return (self::$totalActiveLanguages >0);
    }

	public function getId(){
		return $this->id;
	}
	public function setId($id){
		$this->id = $id;
	}
	public function getName(){
		return $this->name;
	}
	public function setName($name){
		$this->name = $name;
	}
	public function isActive(){
		return $this->active;
	}
	public function setActive($active){
		$this->active = $active;
	}
	public function getIsoCode(){
		return $this->isoCode;
	}
	public function setIsoCode($isoCode){
		$this->isoCode = $isoCode;
	}
	public function getLanguageCode(){
		return $this->languageCode;
	}
	public function setLanguageCode($languageCode){
		$this->languageCode = $languageCode;
	}
	public function getDateFormatLite(){
		return $this->dateFormatLite;
	}
	public function setDateFormatLite($dateFormatLite){
		$this->dateFormatLite = $dateFormatLite;
	}
	public function getDateFormatFull(){
		return $this->dateFormatFull;
	}
	public function setDateFormatFull($dateFormatFull){
		$this->dateFormatFull = $dateFormatFull;
	}
	public function isRtl(){
		return $this->rtl;
	}
	public function setRtl($rtl){
		$this->rtl = $rtl;
	}
}