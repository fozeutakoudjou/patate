<?php
namespace core\models;

class Log extends Model{
	protected $id;
	protected $idUser;
	protected $type;
	protected $data;
	protected $action;
	protected $dateAdd;
	protected $trackingData;
	protected $additionalInfos;
	protected $definition = array(
		'entity' => 'log',
		'primary' => 'id',
		'auto_increment' => true,
		'referenced' => true,
		'fields' => array(
			'idUser' => array('type' => self::TYPE_INT, 'foreign' => true, 'reference' => array('class' =>'User', 'field' =>'id'), 'validate' => 'isUnsignedInt'),
			'type' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
			'data' => array('type' => self::TYPE_HTML, 'validate' => 'isCleanHtml'),
			'action' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
			'dateAdd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
			'trackingData' => array('type' => self::TYPE_HTML, 'validate' => 'isCleanHtml'),
			'additionalInfos' => array('type' => self::TYPE_HTML, 'validate' => 'isCleanHtml')
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
	public function getTrackingData(){
		return $this->trackingData;
	}
	public function setTrackingData($trackingData){
		$this->trackingData = $trackingData;
	}
	public function getAdditionalInfos(){
		return $this->additionalInfos;
	}
	public function setAdditionalInfos($additionalInfos){
		$this->additionalInfos = $additionalInfos;
	}
}