<?php
namespace core\models;

class Access extends Model{
	protected $id;
	protected $idGroup;
	protected $idRight;
	protected $idUser;
	protected $dateAdd;
	protected $dateUpdate;
	protected $definition = array(
		'entity' => 'access',
		'primary' => 'id',
		'auto_increment' => true,
		'referenced' => true,
		'fields' => array(
			'idGroup' => array('type' => self::TYPE_INT, 'foreign' => true, 'reference' => array('class' =>'Group', 'field' =>'id'), 'validate' => 'isUnsignedInt'),
			'idRight' => array('type' => self::TYPE_INT, 'required' => true, 'foreign' => true, 'reference' => array('class' =>'Right', 'field' =>'id'), 'validate' => 'isUnsignedInt'),
			'idUser' => array('type' => self::TYPE_INT, 'foreign' => true, 'reference' => array('class' =>'User', 'field' =>'id'), 'validate' => 'isUnsignedInt'),
			'dateAdd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
			'dateUpdate' => array('type' => self::TYPE_DATE, 'validate' => 'isDate')
		)
	);	

	public function getId(){
		return $this->id;
	}
	public function setId($id){
		$this->id = $id;
	}
	public function getIdGroup(){
		return $this->idGroup;
	}
	public function setIdGroup($idGroup){
		$this->idGroup = $idGroup;
	}
	public function getIdRight(){
		return $this->idRight;
	}
	public function setIdRight($idRight){
		$this->idRight = $idRight;
	}
	public function getIdUser(){
		return $this->idUser;
	}
	public function setIdUser($idUser){
		$this->idUser = $idUser;
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
}