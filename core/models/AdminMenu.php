<?php
namespace core\models;

class AdminMenu extends Model{
	protected $id;
	protected $idWrapper;
	protected $idAction;
	protected $idParent;
	protected $clickable;
	protected $position;
	protected $linkType;
	protected $level;
	protected $newTab;
	protected $active;
	protected $name;
	protected $title;
	protected $link;
	protected $definition = array(
		'entity' => 'admin_menu',
		'primary' => 'id',
		'auto_increment' => true,
		'multilang' => true,
		'referenced' => true,
		'fields' => array(
			'idWrapper' => array('type' => self::TYPE_INT, 'foreign' => true, 'reference' => array('class' =>'Wrapper', 'field' =>'id'), 'validate' => 'isUnsignedInt'),
			'idAction' => array('type' => self::TYPE_INT, 'foreign' => true, 'reference' => array('class' =>'Action', 'field' =>'id'), 'validate' => 'isUnsignedInt'),
			'idParent' => array('type' => self::TYPE_INT, 'foreign' => true, 'reference' => array('class' =>'AdminMenu', 'field' =>'id'), 'validate' => 'isUnsignedInt'),
			'clickable' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
			'position' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
			'linkType' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
			'level' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
			'newTab' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
			'active' => array('type' => self::TYPE_BOOL, 'required' => true, 'validate' => 'isBool', 'default' => '1'),
			'name' => array('type' => self::TYPE_STRING, 'required' => true, 'lang' => true, 'validate' => 'isGenericName'),
			'title' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName'),
			'link' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName')
		)
	);	

	public function getId(){
		return $this->id;
	}
	public function setId($id){
		$this->id = $id;
	}
	public function getIdWrapper(){
		return $this->idWrapper;
	}
	public function setIdWrapper($idWrapper){
		$this->idWrapper = $idWrapper;
	}
	public function getIdAction(){
		return $this->idAction;
	}
	public function setIdAction($idAction){
		$this->idAction = $idAction;
	}
	public function getIdParent(){
		return $this->idParent;
	}
	public function setIdParent($idParent){
		$this->idParent = $idParent;
	}
	public function isClickable(){
		return $this->clickable;
	}
	public function setClickable($clickable){
		$this->clickable = $clickable;
	}
	public function getPosition(){
		return $this->position;
	}
	public function setPosition($position){
		$this->position = $position;
	}
	public function getLinkType(){
		return $this->linkType;
	}
	public function setLinkType($linkType){
		$this->linkType = $linkType;
	}
	public function getLevel(){
		return $this->level;
	}
	public function setLevel($level){
		$this->level = $level;
	}
	public function isNewTab(){
		return $this->newTab;
	}
	public function setNewTab($newTab){
		$this->newTab = $newTab;
	}
	public function isActive(){
		return $this->active;
	}
	public function setActive($active){
		$this->active = $active;
	}
	public function getName(){
		return $this->name;
	}
	public function setName($name){
		$this->name = $name;
	}
	public function getTitle(){
		return $this->title;
	}
	public function setTitle($title){
		$this->title = $title;
	}
	public function getLink(){
		return $this->link;
	}
	public function setLink($link){
		$this->link = $link;
	}
}