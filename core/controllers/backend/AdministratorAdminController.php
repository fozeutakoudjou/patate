<?php
namespace core\controllers\backend;

use core\constant\UserType;

class AdministratorAdminController extends UserAdminController
{	
	protected $modelClassName = 'User';
	protected $baseRestrictionsData = array('type'=>UserType::ADMIN);
	
	public function __construct()
    {
		parent::__construct();
		$this->addDefaultValues['type'] = UserType::ADMIN;
    }
}