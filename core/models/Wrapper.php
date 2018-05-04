<?php
namespace core\models;

class Wrapper extends Model{
	protected $id;
	protected $type;
	protected $module;
	protected $target;
	protected $name;
	protected $description;
	protected $definition = array(
		'entity' => 'wrapper',
		'primary' => 'id',
		'auto_increment' => true,
		'multilang' => true,
		'fields' => array(
			'type' => array('type' => self::TYPE_INT, 'required' => true, 'validate' => 'isUnsignedInt'),
			'module' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName'),
			'target' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName'),
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
	public function getType(){
		return $this->type;
	}
	public function setType($type){
		$this->type = $type;
	}
	public function getModule(){
		return $this->module;
	}
	public function setModule($module){
		$this->module = $module;
	}
	public function getTarget(){
		return $this->target;
	}
	public function setTarget($target){
		$this->target = $target;
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