<?php
namespace core\models;

class Configuration extends Model{
	private $id;
	private $name;
	private $value;
	private $dateUpdate;
	private $valueLang;
	protected $definition = array(
		'table' => 'configuration',
		'primary' => 'id',
		'auto_increment' => true,
		'multilang' => true,
		'fields' => array(
			'name' => array('type' => self::TYPE_STRING, 'required' => true, 'validate' => 'isGenericName'),
			'value' => array('type' => self::TYPE_HTML, 'validate' => 'isCleanHtml'),
			'dateUpdate' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
			'valueLang' => array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml')
		)
	);	
	
	public static function get($name){
		return false;
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
	public function getValue(){
		return $this->value;
	}
	public function setValue($value){
		$this->value = $value;
	}
	public function getDateUpdate(){
		return $this->dateUpdate;
	}
	public function setDateUpdate($dateUpdate){
		$this->dateUpdate = $dateUpdate;
	}
	public function getValueLang(){
		return $this->valueLang;
	}
	public function setValueLang($valueLang){
		$this->valueLang = $valueLang;
	}
}