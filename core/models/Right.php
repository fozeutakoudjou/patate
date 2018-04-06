<?php
namespace core\models;

class Right extends Model{
	private $id;
	private $idContainer;
	private $code;
	private $label;
	private $description;
	protected $definition = array(
		'table' => 'right',
		'primary' => 'id',
		'auto_increment' => true,
		'multilang' => true,
		'referenced' => true,
		'fields' => array(
			'idContainer' => array('type' => self::TYPE_INT, 'foreign' => true, 'reference' => array('class' =>'RightContainer'), 'validate' => 'isUnsignedInt'),
			'code' => array('type' => self::TYPE_STRING, 'required' => true, 'unique' => true, 'validate' => 'isGenericName'),
			'label' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName'),
			'description' => array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml')
		)
	);	

	public function getId(){
		return $this->id;
	}
	public function setId($id){
		$this->id = $id;
	}
	public function getIdContainer(){
		return $this->idContainer;
	}
	public function setIdContainer($idContainer){
		$this->idContainer = $idContainer;
	}
	public function getCode(){
		return $this->code;
	}
	public function setCode($code){
		$this->code = $code;
	}
	public function getLabel(){
		return $this->label;
	}
	public function setLabel($label){
		$this->label = $label;
	}
	public function getDescription(){
		return $this->description;
	}
	public function setDescription($description){
		$this->description = $description;
	}
}