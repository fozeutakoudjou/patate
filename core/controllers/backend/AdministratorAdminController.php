<?php
namespace core\controllers\backend;

use core\constant\UserType;
use core\constant\UrlParamType;
use core\constant\ActionCode;
use core\generator\html\interfaces\Formatter;

class AdministratorAdminController extends UserAdminController
{	
	protected $userType = UserType::ADMIN;
	protected $superAdmin = 0;
	
	protected function createRowsActions() {
		parent::createRowsActions();
		if(!$this->superAdmin){
			$this->createAssociatedRowsActions('Access', $this->l('Access'), array('type'=>UrlParamType::USER, 'idParamKey'=>'target'));
			$this->createAssociatedRowsActions('AdminUserGroup', $this->l('Profiles'), array('type'=>UrlParamType::USER, 'idParamKey'=>'target'));
		}
	}
	/*protected function createRowsActions() {
		parent::createRowsActions();
		$wrapper = $this->getDAOInstance('Wrapper', false)->getByFields(array('type'=>WrapperType::ADMIN_CONTROLLER, 'target'=>'Access', 'module'=>null), false, null, false);
		$idWrapper = empty($wrapper) ? 0 : $wrapper[0]->getId();
		if(!empty($idWrapper) && $this->checkUserAccess(ActionCode::LISTING, $idWrapper)){
			$params = array('controller'=>'access', 'module'=>'', 'type'=>UrlParamType::USER, 'idParamKey'=>'target');
			$link = $this->generator->createRowAction($this->table, $this->l('Access'), '', '', $this->l('Access'), false, 'userAccess', ActionCode::LISTING, $params, false, false, '', true, true, 'openInDialog', true);
			$link->setAjaxEnabled(true);
		}
	}*/
	
	protected function checkFormFieldAccess($update){
		parent::checkFormFieldAccess($update);
		if($update && $this->defaultModel->isLoaded() && ($this->defaultModel->getId()==$this->context->getUser()->getId())){
			$this->saveFieldsToExclude[] = 'active';
			$this->formFieldsToExclude[] = 'active';
		}
	}
	
	protected function checkFormObjectLoaded(){
		if(($this->context->getUser()->getId() != $this->defaultModel->getId()) || ($this->action == ActionCode::UPDATE) || ($this->action == ActionCode::UPDATE_PASSWORD)){
			return true;
		}else{
			$this->errors[] = $this->l('You can not perform this action on your own account');
			return false;
		}
	}
	protected function customizeTable() {
		parent::customizeTable();
		$this->table->setRowFormatter(new AdministratorRowFormatter($this->context->getUser()));
	}
}

class AdministratorRowFormatter implements Formatter
{
	protected $connectedUser;
	public function __construct($connectedUser)
    {
		$this->connectedUser = $connectedUser;
    }
	public function format($item){
		$object = $item->getValue();
		if($object->getId()==$this->connectedUser->getId()){
			$options = $item->getTable()->getColumn('active')->getSearchOptions();
			$item->setColumnDataOptions('active', $options);
			$item->addRowActionToExclude(ActionCode::DELETE);
		}
	}
}