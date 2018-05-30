<?php
namespace core\models;

class HookExclusion extends Model{
	protected $id;
	protected $idHook;
	protected $idTriggerWrapper;
	protected $idExcludedWrapper;
	protected $definition = array(
		'entity' => 'hook_exclusion',
		'primary' => 'id',
		'uniques' => array('idHook_idTriggerWrapper_idExcludedWrapper' => array('idHook', 'idTriggerWrapper', 'idExcludedWrapper')),
		'auto_increment' => true,
		'referenced' => true,
		'fields' => array(
			'idHook' => array('type' => self::TYPE_INT, 'required' => true, 'foreign' => true, 'reference' => array('class' =>'Hook', 'field' =>'id'), 'validate' => 'isUnsignedInt'),
			'idTriggerWrapper' => array('type' => self::TYPE_INT, 'required' => true, 'foreign' => true, 'reference' => array('class' =>'Wrapper', 'field' =>'id'), 'validate' => 'isUnsignedInt'),
			'idExcludedWrapper' => array('type' => self::TYPE_INT, 'required' => true, 'foreign' => true, 'reference' => array('class' =>'Wrapper', 'field' =>'id'), 'validate' => 'isUnsignedInt')
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
	public function getIdTriggerWrapper(){
		return $this->idTriggerWrapper;
	}
	public function setIdTriggerWrapper($idTriggerWrapper){
		$this->idTriggerWrapper = $idTriggerWrapper;
	}
	public function getIdExcludedWrapper(){
		return $this->idExcludedWrapper;
	}
	public function setIdExcludedWrapper($idExcludedWrapper){
		$this->idExcludedWrapper = $idExcludedWrapper;
	}
}