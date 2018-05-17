<?php
namespace core\controllers\backend;

use core\constant\UserType;
use core\constant\ActionCode;
use core\constant\WrapperType;
use core\constant\UrlParamType;

class AdministratorAdminController extends UserAdminController
{	
	protected $userType = UserType::ADMIN;
	
	protected function createRowsActions() {
		parent::createRowsActions();
		$wrapper = $this->getDAOInstance('Wrapper', false)->getByFields(array('type'=>WrapperType::ADMIN_CONTROLLER, 'target'=>'Access', 'module'=>null), false, null, false);
		$idWrapper = empty($wrapper) ? 0 : $wrapper[0]->getId();
		if(!empty($idWrapper) && $this->checkUserAccess(ActionCode::LISTING, $idWrapper)){
			$params = array('controller'=>'access', 'module'=>'', 'type'=>UrlParamType::USER, 'idParamKey'=>'target');
			$link = $this->generator->createRowAction($this->table, $this->l('Access'), '', '', $this->l('Access'), false, 'userAccess', ActionCode::LISTING, $params, false, false, '', true, true, 'openInDialog', true);
			$link->setAjaxEnabled(true);
		}
	}
}