<?php
namespace core\models;

class Group extends Model{
	protected $id;
	protected $idParent;
	protected $dateAdd;
	protected $dateUpdate;
	protected $name;
	protected $description;
	protected $definition = array(
		'entity' => 'group',
		'primary' => 'id',
		'auto_increment' => true,
		'multilang' => true,
		'referenced' => true,
		'fields' => array(
			'idParent' => array('type' => self::TYPE_INT, 'foreign' => true, 'reference' => array('class' =>'Group', 'field' =>'id'), 'validate' => 'isUnsignedInt'),
			'dateAdd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
			'dateUpdate' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
			'name' => array('type' => self::TYPE_STRING, 'required' => true, 'lang' => true, 'validate' => 'isGenericName'),
			'description' => array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml')
		)
	);	

	public function getId(){
		return $this->id;
	}
	public function setId($id){
		$this->id = $id;
	}
	public function getIdParent(){
		return $this->idParent;
	}
	public function setIdParent($idParent){
		$this->idParent = $idParent;
	}
	public function getDateAdd(){
		return $this->dateAdd;
	}
	public function setDateAdd($dateAdd){
		$this->dateAdd = $dateAdd;
	}
	public function getDateUpdate(){
		return $this->dateUpdate;
	}
	public function setDateUpdate($dateUpdate){
		$this->dateUpdate = $dateUpdate;
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