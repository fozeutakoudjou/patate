<?php
namespace core\models;

use core\constant\UserType;
use core\constant\GroupType;
use core\constant\dao\LogicalOperator;
use core\Tools;
use core\Context;
use core\Validate;
use core\dao\Factory;

class User extends Model{
	protected $id;
	protected $lastName;
	protected $firstName;
	protected $gender;
	protected $phone;
	protected $active;
	protected $email;
	protected $preferredLang;
	protected $avatar;
	protected $type;
	protected $password;
	protected $additionalInfos;
	protected $dateAdd;
	protected $dateUpdate;
	protected $lastPasswordGeneratedTime;
	protected $lastConnectionDate;
	protected $lastConnectionData;
	protected $deleted;
	protected $superAdmin;
	protected $definition = array(
		'entity' => 'user',
		'primary' => 'id',
		'uniques' => array('email_type' => array('email', 'type')),
		'auto_increment' => true,
		'fields' => array(
			'lastName' => array('type' => self::TYPE_STRING, 'required' => true, 'validate' => 'isName', 'maxSize' => '35'),
			'firstName' => array('type' => self::TYPE_STRING, 'required' => true, 'validate' => 'isName', 'maxSize' => '35'),
			'gender' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
			'phone' => array('type' => self::TYPE_STRING, 'validate' => 'isPhoneNumber', 'maxSize' => '20'),
			'active' => array('type' => self::TYPE_BOOL, 'required' => true, 'validate' => 'isBool', 'default' => '1'),
			'email' => array('type' => self::TYPE_STRING, 'required' => true, 'validate' => 'isEmail', 'maxSize' => '128'),
			'preferredLang' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'maxSize' => '2'),
			'avatar' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'maxSize' => '200'),
			'type' => array('type' => self::TYPE_INT, 'required' => true, 'validate' => 'isUnsignedInt'),
			'password' => array('type' => self::TYPE_STRING, 'required' => true, 'validate' => 'isPassword', 'maxSize' => '32'),
			'additionalInfos' => array('type' => self::TYPE_HTML, 'validate' => 'isCleanHtml'),
			'dateAdd' => array('type' => self::TYPE_DATETIME),
			'dateUpdate' => array('type' => self::TYPE_DATETIME),
			'lastPasswordGeneratedTime' => array('type' => self::TYPE_DATETIME),
			'lastConnectionDate' => array('type' => self::TYPE_DATETIME),
			'lastConnectionData' => array('type' => self::TYPE_HTML, 'validate' => 'isCleanHtml'),
			'deleted' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'default' => '0'),
			'superAdmin' => array('type' => self::TYPE_BOOL, 'required' => true, 'validate' => 'isBool', 'default' => '0')
		),
		'multipleAssociations' => array(
			'Access' => array('class'=>'Access', 'field'=>'idUser'),
			'Log' => array('class'=>'Log', 'field'=>'idUser'),
			'UserGroup' => array('class'=>'UserGroup', 'field'=>'idUser')
		)
	);	

	public function getId(){
		return $this->id;
	}
	public function setId($id){
		$this->id = $id;
	}
	public function getLastName(){
		return $this->lastName;
	}
	public function setLastName($lastName){
		$this->lastName = $lastName;
	}
	public function getFirstName(){
		return $this->firstName;
	}
	public function setFirstName($firstName){
		$this->firstName = $firstName;
	}
	public function getGender(){
		return $this->gender;
	}
	public function setGender($gender){
		$this->gender = $gender;
	}
	public function getPhone(){
		return $this->phone;
	}
	public function setPhone($phone){
		$this->phone = $phone;
	}
	public function isActive(){
		return $this->active;
	}
	public function setActive($active){
		$this->active = $active;
	}
	public function getEmail(){
		return $this->email;
	}
	public function setEmail($email){
		$this->email = $email;
	}
	public function getPreferredLang(){
		return $this->preferredLang;
	}
	public function setPreferredLang($preferredLang){
		$this->preferredLang = $preferredLang;
	}
	public function getAvatar(){
		return $this->avatar;
	}
	public function setAvatar($avatar){
		$this->avatar = $avatar;
	}
	public function getType(){
		return $this->type;
	}
	public function setType($type){
		$this->type = $type;
	}
	public function getPassword(){
		return $this->password;
	}
	public function setPassword($password){
		$this->password = $password;
	}
	public function getAdditionalInfos(){
		return $this->additionalInfos;
	}
	public function setAdditionalInfos($additionalInfos){
		$this->additionalInfos = $additionalInfos;
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
	public function getLastPasswordGeneratedTime(){
		return $this->lastPasswordGeneratedTime;
	}
	public function setLastPasswordGeneratedTime($lastPasswordGeneratedTime){
		$this->lastPasswordGeneratedTime = $lastPasswordGeneratedTime;
	}
	public function getLastConnectionDate(){
		return $this->lastConnectionDate;
	}
	public function setLastConnectionDate($lastConnectionDate){
		$this->lastConnectionDate = $lastConnectionDate;
	}
	public function getLastConnectionData(){
		return $this->lastConnectionData;
	}
	public function setLastConnectionData($lastConnectionData){
		$this->lastConnectionData = $lastConnectionData;
	}
	public function isDeleted(){
		return $this->deleted;
	}
	public function setDeleted($deleted){
		$this->deleted = $deleted;
	}
	public function isSuperAdmin(){
		return $this->superAdmin;
	}
	public function setSuperAdmin($superAdmin){
		$this->superAdmin = $superAdmin;
	}
	
	
	//
	private $accessList;
	private $accessCheckedList = array();
	
	public function __toString()
    {
		return $this->firstName. ' '. $this->lastName;
    }
	
	/**
     * Check employee informations saved into cookie and return employee validity
     *
     * @return bool employee validity
     */
    public function isLoggedBack()
    {
		
		$cookie = Context::getInstance()->getCookie();
		$result = (
			$this->id && Validate::isUnsignedId($this->id) && $this->isAdmin() && self::checkPassword($this->id, $cookie->password, true) &&
				(!isset($cookie->remoteAddress) || ($cookie->remoteAddress == Tools::getNumericRemoteAddress()) || !Configuration::get('COOKIE_CHECKIP'))
		);
		return $result;
    }
	
	public function logout()
    {
		$cookie = Context::getInstance()->getCookie();
        if ($cookie!=null){
            $cookie->logout();
            $cookie->write();
        }
        $this->id = null;
    }
	
	
	
	/**
     * Check if employee password is the right one
     *
     * @param string $password Password
     * @return bool result
     */
    public static function checkPassword($id, $password, $isAdmin = false)
    {
		$passValidate = $isAdmin ? Validate::isPasswordAdmin($password) : Validate::isPassword($password);
        if (!Validate::isUnsignedId($id) || !$passValidate) {
            die('Invalid');
        }
		$fields = array('id'=>$id);
		$fields = self::addRestrict($fields, $isAdmin, $password, true);
		$count = self::getDao()->getByFieldsCount($fields);
        return ($count>0);
    }
	
	protected static function getDao()
    {
		return Factory::getDAOInstance('User');
    }
	
	public static function getAdminByEmail($email, $password = null, $onlyActive = true)
    {
		return self::getByEmail($email, $password, $onlyActive, true);
    }
	
	public static function addRestrict($fields, $isAdmin, $password = null, $onlyActive = true)
    {
		if($isAdmin){
			$fields['type']=UserType::ADMIN; 
			/*$fields['type_group']= array(
				'group'=>true,
				'logicalOperator'=>LogicalOperator::OR_,
				'fields'=> Tools::getMultipleValuesRestriction('type', array(UserType::ADMIN, UserType::SUPER_ADMIN))
			);*/
		}
		if($onlyActive){
			$fields['active']=1; 
		}
		if($password!==null){
			$fields['password']=$password; 
		}
		return $fields;
    }
	
	public static function getByEmail($email, $password = null, $onlyActive = true, $isAdmin = false)
    {
		//$passValidate = $isAdmin ? Validate::isPasswordAdmin($password) : Validate::isPassword($password);
		$passValidate = $isAdmin ? Validate::isPasswordAdmin($password) : Validate::isPassword($password);
        if (!Validate::isEmail($email) || (($password != null) && !Validate::isPassword($password))) {
            die('Invalid');
        }
		$password = ($password===null) ? $password : Tools::encrypt($password);
		$fields = array('email'=>$email);
		$fields = self::addRestrict($fields, $isAdmin, $password, $onlyActive);
		$users = self::getDao()->getByFields($fields);
		return empty($users) ? null : $users[0];
    }
	
	public function isAdmin()
    {
		return ($this->type == UserType::ADMIN);
    }
	public function loadAccess()
    {
        if ($this->accessList === null) {
			$this->accessList = array();
			$dao = Factory::getDAOInstance('Access');
			$fields = array('idUser'=>$this->id);
			$list = $dao->getByFields($fields);
            //$list = $dao->getAll(false, null, false, false, 0, 0, '', 0, true);
			foreach ($list as $access) {
				$this->accessList[] = $access->getIdRight();
			}
        }
    }
	public function hasRight($idWrapper, $action)
    {
		$key = $idWrapper.'_'.$action;
		if(!isset($this->accessCheckedList[$key])){
			$hasRight = false;
			if($this->isSuperAdmin()){
				$hasRight = true;
			}else{
				$right = Right::get($idWrapper, $action);
				if($right==null){
					$hasRight = true;
				}else{
					$this->loadAccess();
					$hasRight = in_array($right->getId(), $this->accessList);
				}
			}
			$this->accessCheckedList[$key] = $hasRight;
		}
		return $this->accessCheckedList[$key];
    }
	
	public function getGroups($idsOnly = true, $withParents = true, $useOfLang = false, $lang = null){
		$dao = Factory::getDAOInstance('UserGroup');
		$fields = array('idUser'=>$this->id, Tools::formatForeignField('idGroup', 'type')=>($this->isAdmin() ? GroupType::ADMIN : GroupType::FRONT));
		//$associations = (!$idsOnly || $withParents) ? array('idGroup') : array();
		$groups = $dao->getByFields($fields, false, $lang, $useOfLang, false, array());
		$groups = ($idsOnly && !$withParents) ? Tools::getArrayValues($groups, true, 'idGroup', false) : Tools::getArrayValues($groups, true, 'idGroup', true);
		if($withParents){
			$parents = array();
			foreach($groups as $group){
				$parents = array_merge($parents, $group->getParents(false, $useOfLang, $lang));
			}
			$groups = array_merge($parents, $groups);
			$groups = $idsOnly ? Tools::getArrayValues($groups, true, 'id', false) : $groups;
		}
		return $groups;
	}
}