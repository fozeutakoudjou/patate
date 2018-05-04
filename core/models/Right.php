<?php
namespace core\models;
use core\dao\Factory;

class Right extends Model{
	protected $id;
	protected $idWrapper;
	protected $idAction;
	protected $active;
	private static $rights;
	protected $definition = array(
		'entity' => 'right',
		'primary' => 'id',
		'auto_increment' => true,
		'referenced' => true,
		'fields' => array(
			'idWrapper' => array('type' => self::TYPE_INT, 'required' => true, 'foreign' => true, 'reference' => array('class' =>'Wrapper', 'field' =>'id'), 'validate' => 'isUnsignedInt'),
			'idAction' => array('type' => self::TYPE_INT, 'required' => true, 'foreign' => true, 'reference' => array('class' =>'Action', 'field' =>'id'), 'validate' => 'isUnsignedInt'),
			'active' => array('type' => self::TYPE_BOOL, 'required' => true, 'validate' => 'isBool', 'default' => '1')
		)
	);
	
	public static function createKey($idWrapper, $action)
    {
		return $idWrapper.'_'.$action;
	}
	
	public static function load()
    {
        if (self::$rights === null) {
			self::$rights = array();
			$dao = Factory::getDAOInstance('Right');
            $list = $dao->getAll(false, null, false, false, 0, 0, '', 0, true, array('idAction'=>array('useOfLang'=>false)));
			foreach ($list as $right) {
				self::$rights[self::createKey($right->getIdWrapper(), $right->getAssociated('idAction')->getCode())] = $right;
			}
        }
    }
	
	public static function get($idWrapper, $action)
    {
        self::load();
		$key = self::createKey($idWrapper, $action);
		return isset(self::$rights[$key]) ? self::$rights[$key] : null;
    }

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
	public function isActive(){
		return $this->active;
	}
	public function setActive($active){
		$this->active = $active;
	}
}