<?php
namespace core\models;
use core\dao\Factory;
class Group extends Model{
	protected $id;
	protected $idParent;
	protected $type;
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
			'type' => array('type' => self::TYPE_INT, 'required' => true, 'validate' => 'isUnsignedInt'),
			'dateAdd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
			'dateUpdate' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
			'name' => array('type' => self::TYPE_STRING, 'required' => true, 'lang' => true, 'validate' => 'isGenericName', 'maxSize' => '50'),
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
	public function getType(){
		return $this->type;
	}
	public function setType($type){
		$this->type = $type;
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
	
	public function getParents($idsOnly = true, $useOfLang = false, $lang = null){
		$groups = array();
		if(!empty($this->idParent)){
			$parent = self::getDao()->getById($this->idParent, false, $lang, $useOfLang);
			if($parent!=null){
				$groups[] = $idsOnly ? $parent->id : $parent;
				$groups = array_merge($groups, $parent->getParents($idsOnly, $useOfLang, $lang));
			}
			
		}
		return $groups;
	}
	
	protected static function getDao()
    {
		return Factory::getDAOInstance('Group');
    }
}