<?php
namespace core\models;

class Lang extends Model{
	private $id_lang;
	private $name;
	private $active;
	private $iso_code;
	private $language_code;
	private $date_format_lite;
	private $date_format_full;
	private $is_rtl;
	protected $definition = array(
		'table' => 'lang',
		'primary' => 'id_lang',
		'fields' => array(
			'name' => array('type' => self::TYPE_STRING, 'required' => true, 'validate' => 'isGenericName'),
			'active' => array('type' => self::TYPE_BOOL, 'required' => true, 'validate' => 'isBool', 'default' => '0'),
			'iso_code' => array('type' => self::TYPE_STRING, 'required' => true, 'foreign' => true, 'validate' => 'isGenericName'),
			'language_code' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName'),
			'date_format_lite' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'default' => 'Y-m-d'),
			'date_format_full' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'default' => 'Y-m-d H:i:s'),
			'is_rtl' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'default' => '0')
		)
	);	

	public function getId_lang(){
		return $this->id_lang;
	}
	public function setId_lang($id_lang){
		$this->id_lang = id_lang;
	}
	public function getName(){
		return $this->name;
	}
	public function setName($name){
		$this->name = name;
	}
	public function isActive(){
		return $this->active;
	}
	public function setActive($active){
		$this->active = active;
	}
	public function getIso_code(){
		return $this->iso_code;
	}
	public function setIso_code($iso_code){
		$this->iso_code = iso_code;
	}
	public function getLanguage_code(){
		return $this->language_code;
	}
	public function setLanguage_code($language_code){
		$this->language_code = language_code;
	}
	public function getDate_format_lite(){
		return $this->date_format_lite;
	}
	public function setDate_format_lite($date_format_lite){
		$this->date_format_lite = date_format_lite;
	}
	public function getDate_format_full(){
		return $this->date_format_full;
	}
	public function setDate_format_full($date_format_full){
		$this->date_format_full = date_format_full;
	}
	public function isIs_rtl(){
		return $this->is_rtl;
	}
	public function setIs_rtl($is_rtl){
		$this->is_rtl = is_rtl;
	}
}