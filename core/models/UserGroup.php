<?php
namespace core\models;

class UserGroup extends Model{
	protected $idUser;
	protected $idGroup;
	protected $definition = array(
		'entity' => 'user_group',
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