<?php
namespace core\models;

class Action extends Model{
	protected $id;
	protected $code;
	protected $dependentOnId;
	protected $name;
	protected $description;
	protected $definition = array(
		'entity' => 'action',
		'primary' => 'id',
		'auto_increment' => true,
		'multilang' => true,
		'fields' => array(
			'code' => array('type' => self::TYPE_STRING, 'required' => true, 'unique' => true, 'validate' => 'isGenericName', 'maxSize' => '50'),
			'dependentOnId' => array('type' => self::TYPE_BOOL, 'required' => true, 'validate' => 'isBool', 'default' => '1'),
			'name' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'maxSize' => '50'),
			'description' => array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml')
		)
	);	

	public function getId(){
		return $this->id;
	}
	public function setId($id){
		$this->id = $id;
	}
	public function getCode(){
		return $this->code;
	}
	public function setCode($code){
		$this->code = $code;
	}
	public function isDependentOnId(){
		return $this->dependentOnId;
	}
	public function setDependentOnId($dependentOnId){
		$this->dependentOnId = $dependentOnId;
	}
	public function getName(){
		return $this->name;
	}
	public function setName($name){
		$this->name = $name;
	}
	public function getDescription(){
		return $this->description;
	}
	public function setDescription($description){
		$this->description = $description;
	}
}