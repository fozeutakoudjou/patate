<?php
namespace Library\models;

class Log extends Model{
	private $id;
	private $idUser;
	private $type;
	private $data;
	private $action;
	private $dateAdd;
	protected $definition = array(
		'table' => 'log',
		'primary' => 'id',
		'auto_increment' => true,
		'fields' => array(
			'idUser' => array('type' => self::TYPE_INT, 'foreign' => true, 'validate' => 'isUnsignedInt'),
			'type' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
			'data' => array('type' => self::TYPE_HTML, 'validate' => 'isCleanHtml'),
			'action' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
			'dateAdd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate')
		)
	);	

	public function getId(){
		return $this->id;
	}
	public function setId($id){
		$this->id = $id;
	}
	public function getIdUser(){
		return $this->idUser;
	}
	public function setIdUser($idUser){
		$this->idUser = $idUser;
	}
	public function getType(){
		return $this->type;
	}
	public function setType($type){
		$this->type = $type;
	}
	public function getData(){
		return $this->data;
	}
	public function setData($data){
		$this->data = $data;
	}
	public function getAction(){
		return $this->action;
	}
	public function setAction($action){
		$this->action = $action;
	}
	public function getDateAdd(){
		return $this->dateAdd;
	}
	public function setDateAdd($dateAdd){
		$this->dateAdd = $dateAdd;
	}
}