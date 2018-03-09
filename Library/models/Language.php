<?php
namespace Library\models;

use Library\dao\Factory;

class Language extends Model{
	private $id;
	private $name;
	private $active;
	private $isoCode;
	private $languageCode;
	private $dateFormatLite;
	private $dateFormatFull;
	private $rtl;
	
	private static $_LANGUAGES;
	protected $definition = array(
		'table' => 'language',
		'primary' => 'id',
		'auto_increment' => true,
		'fields' => array(
			'name' => array('type' => self::TYPE_STRING, 'required' => true, 'validate' => 'isGenericName'),
			'active' => array('type' => self::TYPE_BOOL, 'required' => true, 'validate' => 'isBool', 'default' => '0'),
			'isoCode' => array('type' => self::TYPE_STRING, 'required' => true, 'validate' => 'isGenericName'),
			'languageCode' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName'),
			'dateFormatLite' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'default' => 'Y-m-d'),
			'dateFormatFull' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'default' => 'Y-m-d H:i:s'),
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