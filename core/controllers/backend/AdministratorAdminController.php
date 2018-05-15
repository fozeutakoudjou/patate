<?php
namespace core\controllers\backend;

use core\constant\UserType;

class AdministratorAdminController extends UserAdminController
{	
	protected $userType = UserType::ADMIN;
}