<?php
namespace core\models;

class HookAssociation extends Model{
	protected $id;
	protected $idHook;
	protected $idWrapper;
	protected $position;
	protected $definition = array(
		'entity' => 'hook_association',
		'primary' => 'id',
		'uniques' => array('idHook_idWrapper' => array('idHook', 'idWrapper')),
		'auto_increment' => true,
		'referenced' => true,
		'fields' => array(
			'idHook' => array('type' => self::TYPE_INT, 'required' => true, 'foreign' => true, 'reference' => array('class' =>'Hook', 'field' =>'id'), 'validate' => 'isUnsignedInt'),
			'idWrapper' => array('type' => self::TYPE_INT, 'required' => true, 'foreign' => true, 'reference' => array('class' =>'Wrapper', 'field' =>'id'), 'validate' => 'isUnsignedInt'),
			'position' => array('type' => self::TYPE_INT, 'required' => true, 'validate' => 'isUnsignedInt')
		)
	);	

	public function getId(){
		return $this->id;
	}
	public function setId($id){
		$this->id = $id;
	}
	public function getIdHook(){
		return $this->idHook;
	}
	public function setIdHook($idHook){
		$this->idHook = $idHook;
	}
	public function getIdWrapper(){
		return $this->idWrapper;
	}
	public function setIdWrapper($idWrapper){
		$this->idWrapper = $idWrapper;
	}
	public function getPosition(){
		return $this->position;
	}
	public function setPosition($position){
		$this->position = $position;
	}
}