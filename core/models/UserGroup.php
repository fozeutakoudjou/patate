<?php
namespace core\models;

class UserGroup extends Model{
	private $idUser;
	private $idGroup;
	protected $definition = array(
		'table' => 'user_group',
		'primary' => array('idUser', 'idGroup'),
		'fields' => array(
			'idUser' => array('type' => self::TYPE_INT, 'required' => true, 'validate' => 'isUnsignedInt'),
			'idGroup' => array('type' => self::TYPE_INT, 'required' => true, 'validate' => 'isUnsignedInt')
		)
	);	

	public function getIdUser(){
		return $this->idUser;
	}
	public function setIdUser($idUser){
		$this->idUser = $idUser;
	}
	public function getIdGroup(){
		return $this->idGroup;
	}
	public function setIdGroup($idGroup){
		$this->idGroup = $idGroup;
	}
}