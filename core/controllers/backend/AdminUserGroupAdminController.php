<?php
namespace core\controllers\backend;
use core\controllers\backend\partial\AssociationController;
use core\constant\ActionCode;
use core\constant\UserType;
use core\constant\GroupType;
use core\constant\generator\ColumnType;
use core\constant\generator\SearchType;
use core\constant\dao\Operator;
use core\Tools;
class AdminUserGroupAdminController extends AssociationController
{	
	protected $modelClassName = 'UserGroup';
	protected $targetRequired = true;
	public function __construct()
    {
		parent::__construct();
		$this->formFieldsToExclude = array_merge($this->formFieldsToExclude, array('idUser', 'idGroup'));
        $this->restrictedActions[] = ActionCode::VIEW;
    }
	protected function loadTargetObject(){
		$isUser = $this->isExtraUserParam();
		$class = $isUser ? 'User' : 'Group';
		$objectType = $isUser ? UserType::ADMIN : GroupType::ADMIN;
		$restrictions = array('type'=>$objectType, 'id'=>$this->extraListParams['target']);
		$objects = $this->getDAOInstance($class, false)->getByFields($restrictions);
		$this->targetField = 'id'.$class;
		$this->fieldToSet = $isUser ? 'idGroup' : 'IdUser';
		$this->associationList[$this->fieldToSet] = null;
		$this->columnsToExclude[] = $this->targetField;
		if(!empty($objects)){
			$this->targetObject = $objects[0];
			if($isUser && ($this->targetObject->getId()==$this->context->getUser()->getId())){
				$this->restrictedActions=array_merge($this->restrictedActions, array(ActionCode::ADD, ActionCode::DELETE));
			}
		}
	}
	protected function getAddableItems() {
		if($this->addableItems===null){
			$excludes = $this->getDataToExclude();
			$isUser = $this->isExtraUserParam();
			$class = $isUser ? 'Group' : 'User';
			$objectType = $isUser ? GroupType::ADMIN : UserType::ADMIN;
			$restrictions = array('type'=>$objectType);
			if(!$isUser){
				$excludes[] = $this->context->getUser()->getId();
			}
			if(!empty($excludes)){
				$restrictions['id'] = array('operator'=>Operator::NOT_IN_LIST, 'value'=>$excludes);
			}
			$this->addableItems = $this->getDAOInstance($class, false)->getByFields($restrictions);
		}
		return $this->addableItems;
	}
	
	protected function getRestrictionFromExtraListParams() {
		$restriction=parent::getRestrictionFromExtraListParams();
		if($this->isExtraUserParam()){
			$restriction['idUser'] = $this->extraListParams['target'];
		}elseif($this->isExtraGroupParam()){
			$restriction['idGroup'] = $this->extraListParams['target'];
		}
		return $restriction;
	}
	
	protected function getFormFieldsRestriction($id=''){
		$fields = parent::getFormFieldsRestriction($id='');
		$data = empty($id) ? Tools::getValue(self::ID_PARAM_URL) : $id;
		$fields[Tools::formatForeignField('idUser', 'id')] = array('operator'=>Operator::DIFFERENT, 'value'=>$this->context->getUser()->getId());
		return $fields;
	}
	
	protected function getAssociatedTableLabel(){
		return $this->isExtraUserParam() ? $this->l('Profiles of administrator %s') : $this->l('Users of profile %s');
	}
	protected function getAssociatedFormLabel(){
		return $this->isExtraUserParam() ? $this->l('Add new profiles to administrator %s') : $this->l('Add new users to profile %s');
	}
	protected function customizeFormFields($update = false) {
		$data = $this->getAddableItems();
		$emptyText = $this->isExtraUserParam() ? $this->l('There are not any profiles you can add') : $this->l('There are not any administrators you can add');
		$columns = array('id'=>$this->l('Id'), 'name'=>array('label'=>$this->l('Name'), 'dataType'=>ColumnType::TO_STRING));
		
		$table = $this->generator->createTableCheckboxMultiple($columns, $emptyText);
		$table->setValue($this->getAddableItems());
		$table->setIdentifier($this->selectableIdentifier);
		$label = $this->isExtraUserParam() ? $this->l('Profiles') : $this->l('Administrators');
		$this->form->addChild($this->generator->createInputCustomContent($table, $this->selectableIdentifier, $label, false));
	}
	
	protected function customizeColumns() {
		//$this->generator->createColumn($this->table, $label, $name, $dataType= ColumnType::TEXT, $searchType = SearchType::TEXT, $sortable = true, $searchable = true, $searchOptions = array(), $dataOptions = array());
		//$label = $this->isExtraUserParam() ? $this->l('Profiles') : $this->l('Administrators');
		$this->generator->createColumn($this->table, $this->l('Name'), $this->fieldToSet, ColumnType::TO_STRING_FOREIGN, SearchType::TEXT, false, false);
	}
}