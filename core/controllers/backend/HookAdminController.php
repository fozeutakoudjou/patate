<?php
namespace core\controllers\backend;

use core\constant\GroupType;
use core\constant\UrlParamType;

class HookAdminController extends AdminController
{	
	protected $modelClassName = 'Hook';
	
	protected function createRowsActions() {
		parent::createRowsActions();
		$this->createAssociatedRowsActions('HookAssociation', $this->l('Associations'), array('idParamKey'=>'target'));
		$this->createAssociatedRowsActions('HookExclusion', $this->l('Exclusions'), array('idParamKey'=>'target'));
	}
}