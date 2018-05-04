<?php
namespace core\models;
use core\dao\Factory;

class Configuration extends Model{
	protected $id;
	protected $name;
	protected $value;
	protected $dateUpdate;
	protected $valueLang;
	private static $values = array();
	private static $dao = null;
	protected $definition = array(
		'entity' => 'configuration',
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
	
	protected static function getDAO(){
		if(self::$dao === null){
			self::$dao = Factory::getDAOInstance('Configuration');
		}
		return self::$dao;
	}
	public static function get($name, $useOfAllLang = false, $lang = ''){
		if(!isset(self::$values[$name])){
			$dao = self::getDAO();
			$config = $dao->getByField('name',$name, false, false, null, true, true);
			if(empty($config)){
				$value = false;
			}else{
				$config = $config[0];
				$valueLang = $config->getValueLang();
				$value = empty($valueLang) ? $config->getValue() : $valueLang;
			}
			self::$values[$name] = $value;
		}
		$value = self::$values[$name];
		if(!$useOfAllLang && !empty($lang)){
			$value = (is_array($value) && isset($value[$lang]))?$value[$lang] : false;
		}
		return $value;
	}
	
	public static function set($name, $value, $isLangValue = false){
		$dao = Factory::getDAOInstance('Configuration');
		$config = $dao->getByField('name',$name);
		if(empty($config)){
			$config = new Configuration();
			$config->setName($name);
		}else{
			$config = $config[0];
		}
		$saveLangField = true;
		if($isLangValue){
			$config->setValueLang($value);
		}else{
			$saveLangField = false;
			$config->setValue($value);
		}
		$result = $dao->save($config, $saveLangField);
		if($result && isset(self::$values[$name])){
			unset(self::$values[$name]);
		}
		return $result;
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