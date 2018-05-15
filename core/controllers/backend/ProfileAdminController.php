<?php
namespace core\controllers\backend;

use core\constant\GroupType;

class ProfileAdminController extends GroupAdminController
{	
	protected $groupType = GroupType::ADMIN;
}