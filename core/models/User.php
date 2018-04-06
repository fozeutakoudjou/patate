<?php
namespace core\models;

class User extends Model{
	private $id;
	private $idProposer;
	private $lastName;
	private $firstName;
	private $gender;
	private $phone;
	private $balance;
	private $active;
	private $email;
	private $preferredLang;
	private $avatar;
	private $type;
	private $additionalInfos;
	private $dateAdd;
	private $dateUpdate;
	private $deleted;
	protected $definition = array(
		'table' => 'user',
		'primary' => 'id',
		'auto_increment' => true,
		'fields' => array(
			'idProposer' => array('type' => self::TYPE_INT, 'foreign' => true, 'validate' => 'isUnsignedInt'),
			'lastName' => array('type' => self::TYPE_STRING, 'validate' => 'isName'),
			'firstName' => array('type' => self::TYPE_STRING, 'validate' => 'isName'),
			'gender' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName'),
			'phone' => array('type' => self::TYPE_STRING, 'validate' => 'isPhoneNumber'),
			'balance' => array('type' => self::TYPE_FLOAT, 'validate' => 'isUnsignedFloat'),
			'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'default' => '1'),
			'email' => array('type' => self::TYPE_STRING, 'validate' => 'isEmail'),
			'preferredLang' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName'),
			'avatar' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName'),
			'type' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
			'additionalInfos' => array('type' => self::TYPE_HTML, 'validate' => 'isCleanHtml'),
			'dateAdd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
			'dateUpdate' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
			'deleted' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'default' => '0')
		)
	);
	
	/**
     * Check employee informations saved into cookie and return employee validity
     *
     * @return bool employee validity
     */
    public function isLoggedBack()
    {
        if (!Cache::isStored('isLoggedBack'.$this->id)) {
            /* Employee is valid only if it can be load and if cookie password is the same as database one */
            $result = (
				$this->id && Validate::isUnsignedId($this->id) && Employee::checkPassword($this->id, Context::getContext()->cookie->passwd)
				&& (!isset(Context::getContext()->cookie->remote_addr) || Context::getContext()->cookie->remote_addr == ip2long(Tools::getRemoteAddr()) || !Configuration::get('COOKIE_CHECKIP'))
			);
            Cache::store('isLoggedBack'.$this->id, $result);
            return $result;
        }
        return Cache::retrieve('isLoggedBack'.$this->id);
    }
	
	/**
     * Check if employee password is the right one
     *
     * @param string $passwd Password
     * @return bool result
     */
    public static function checkPassword($id_employee, $passwd)
    {
        if (!Validate::isUnsignedId($id_employee) || !Validate::isPasswd($passwd, 8)) {
            die(Tools::displayError());
        }

        return Db::getInstance()->getValue('
		SELECT `id_employee`
		FROM `'._DB_PREFIX_.'employee`
		WHERE `id_employee` = '.(int)$id_employee.'
		AND `passwd` = \''.pSQL($passwd).'\'
		AND `active` = 1');
    }

	public function getId(){
		return $this->id;
	}
	public function setId($id){
		$this->id = $id;
	}
	public function getIdProposer(){
		return $this->idProposer;
	}
	public function setIdProposer($idProposer){
		$this->idProposer = $idProposer;
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
	public function getBalance(){
		return $this->balance;
	}
	public function setBalance($balance){
		$this->balance = $balance;
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
	public function isDeleted(){
		return $this->deleted;
	}
	public function setDeleted($deleted){
		$this->deleted = $deleted;
	}
}