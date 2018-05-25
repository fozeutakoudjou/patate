<?php
namespace core\controllers\backend;

use core\constant\GroupType;
use core\constant\UrlParamType;

class ProfileAdminController extends GroupAdminController
{	
	protected $groupType = GroupType::ADMIN;
	
	protected function createRowsActions() {
		parent::createRowsActions();
		$this->createAssociatedRowsActions('Access', $this->l('Access'), array('type'=>UrlParamType::GROUP, 'idParamKey'=>'target'));
		$this->createAssociatedRowsActions('AdminUserGroup', $this->l('Users'), array('type'=>UrlParamType::GROUP, 'idParamKey'=>'target'));
	}
}