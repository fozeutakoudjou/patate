<?php
namespace core\models;

class Action extends Model{
	protected $id;
	protected $code;
	protected $name;
	protected $description;
	protected $definition = array(
		'entity' => 'action',
		'primary' => 'id',
		'auto_increment' => true,
		'multilang' => true,
		'fields' => array(
			'code' => array('type' => self::TYPE_STRING, 'required' => true, 'unique' => true, 'validate' => 'isGenericName'),
			'name' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName'),
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