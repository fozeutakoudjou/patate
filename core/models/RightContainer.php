<?php
namespace core\models;

class RightContainer extends Model{
	private $id;
	private $entity;
	private $name;
	protected $definition = array(
		'table' => 'right_container',
		'primary' => 'id',
		'auto_increment' => true,
		'multilang' => true,
		'fields' => array(
			'entity' => array('type' => self::TYPE_HTML, 'validate' => 'isCleanHtml'),
			'name' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName')
		)
	);	

	public function getId(){
		return $this->id;
	}
	public function setId($id){
		$this->id = $id;
	}
	public function getEntity(){
		return $this->entity;
	}
	public function setEntity($entity){
		$this->entity = $entity;
	}
	public function getName(){
		return $this->name;
	}
	public function setName($name){
		$this->name = $name;
	}
}