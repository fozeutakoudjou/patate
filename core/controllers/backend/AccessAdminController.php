<?php
namespace core\controllers\backend;
use core\Tools;
use core\controllers\backend\partial\AssociationController;
use core\constant\generator\ColumnType;
use core\constant\generator\SearchType;
use core\constant\UrlParamType;
use core\constant\ActionCode;
use core\constant\FormPosition;
use core\constant\dao\Operator;
use core\constant\UserType;
use core\constant\GroupType;
class AccessAdminController extends AssociationController
{	
	protected $modelClassName = 'Access';
	protected $targetObject;
	protected $fieldToSet = 'idRight';
	public function __construct()
    {
		parent::__construct();
        $this->formFieldsToExclude = array_merge($this->formFieldsToExclude, array('active', 'idRight', 'idUser', 'idGroup'));
        $this->saveFieldsToExclude = array_merge($this->saveFieldsToExclude, array('active'));
		$this->addDefaultValues['active'] = 1;
    }
	protected function getFormFieldsRestriction($id=''){
		$fields = parent::getFormFieldsRestriction($id='');
		$data = empty($id) ? Tools::getValue(self::ID_PARAM_URL) : $id;
		$fields[Tools::formatForeignField('idUser', 'id')] = array('operator'=>Operator::DIFFERENT, 'value'=>$this->context->getUser()->getId());
		return $fields;
	}
	/*protected function customizeForm($update = false) {
		if($this->targetObject!=null){
			$label = $this->isExtraUserParam() ? $this->l('Add new accesses to administrator %s') : $this->l('Add new accesses to profile %s');
			$this->form->setLabel(sprintf($label, $this->targetObject->__toString()));
		}
	}*/
	protected function getAddableItems() {
		if($this->addableItems===null){
			$connectedUser = $this->context->getUser();
			$field = $this->isExtraUserParam() ? 'idUser' : 'idGroup';
			$excludes = $this->getDataToExclude();
			$isSuperAdmin = $connectedUser->isSuperAdmin();
			$rightKey = $isSuperAdmin ? 'id' : 'idRight';
			$restrictions = empty($excludes) ? array() : array($rightKey=>array('operator'=>Operator::NOT_IN_LIST, 'value'=>$excludes));
			if($isSuperAdmin){
				$this->addableItems = $this->getDAOInstance('Right', false)->getByFields($restrictions);
			}else{
				$restrictions['active'] = 1;
				$restrictions['idUser'] = $connectedUser->getId();
				$rights = $this->getDAOInstance()->getByFields($restrictions, false, null, false, false, array('idRight'=>null));
				$this->addableItems = Tools::getArrayValues($rights, true, 'idRight', true);
			}
		}
		return $this->addableItems;
	}
	protected function customizeFormFields($update = false) {
		$data = $this->getAddableItems();
		$dao = $this->getDAOInstance('Right', false);
		foreach($data as $key => $right){
			$dao->setAssociatedData($right, 'idWrapper');
			$dao->setAssociatedData($right, 'idAction');
		}
		$table = $this->generator->createTableCheckboxMultiple(array('id'=>$this->l('Id'), Tools::formatForeignField('idWrapper', 'name')=>$this->l('target'), Tools::formatForeignField('idAction', 'name')=>$this->l('action'), Tools::formatForeignField('idWrapper', 'module')=>$this->l('module')), $this->l('There are not any rights you can add'));
		$table->setValue($data);
		$table->setIdentifier($this->selectableIdentifier);
		$this->form->addChild($table);
	}
	/*protected function customizeColumns() {
		$field = Tools::formatForeignField('idWrapper', 'name');
		$this->generator->createColumn($this->table, $field, $field, ColumnType::TEXT, SearchType::TEXT, true, true);
		$this->associationList['idWrapper'] = array();
	}*/
	protected function loadTargetObject(){
		$isUser = $this->isExtraUserParam();
		$class = $isUser ? 'User' : 'Group';
		$objectType = $isUser ? UserType::ADMIN : GroupType::ADMIN;
		$restrictions = array('type'=>$objectType, 'id'=>$this->extraListParams['target']);
		$objects = $this->getDAOInstance($class, false)->getByFields($restrictions);
		$this->targetField = 'id'.$class;
		if(!empty($objects)){
			$this->targetObject = $objects[0];
			if($isUser && ($this->targetObject->getId()==$this->context->getUser()->getId())){
				$this->restrictedActions=array_merge($this->restrictedActions, array(ActionCode::ADD, ActionCode::ACTIVATE, ActionCode::DESACTIVATE, ActionCode::DELETE));
			}
		}
	}
	protected function getAssociatedTableLabel(){
		return $this->isExtraUserParam() ? $this->l('Accesses of administrator %s') : $this->l('Accesses of profile %s');
	}
	protected function getAssociatedFormLabel(){
		return $this->isExtraUserParam() ? $this->l('Add new accesses to administrator %s') : $this->l('Add new accesses to profile %s');
	}
	/*protected function customizeTable() {
		if(isset($this->extraListParams['type'])){
			if($this->targetObject!=null){
				$label = $this->isExtraUserParam() ? $this->l('Access of administrator %s') : $this->l('Access of profile %s');
				$this->table->setLabel(sprintf($label, $this->targetObject->__toString()));
			}
		}
	}*/
	
	protected function getRestrictionFromExtraListParams() {
		$restriction=parent::getRestrictionFromExtraListParams();
		if($this->isExtraUserParam()){
			$restriction['idUser'] = $this->extraListParams['target'];
		}elseif($this->isExtraGroupParam()){
			$restriction['idGroup'] = $this->extraListParams['target'];
		}
		return $restriction;
	}
}