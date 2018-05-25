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
use core\constant\FormPosition;
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
		if($isUser){
			$restrictions['superAdmin'] = 0;
		}
		$objects = $this->getDAOInstance($class, false)->getByFields($restrictions);
		$this->targetField = 'id'.$class;
		$this->fieldToSet = $isUser ? 'idGroup' : 'idUser';
		$this->associationList[$this->fieldToSet] = null;
		$this->columnsToExclude[] = $this->targetField;
		if(!empty($objects)){
			$this->targetObject = $objects[0];
			if($isUser && ($this->targetObject->getId()==$this->context->getUser()->getId())){
				$this->restrictedActions=array_merge($this->restrictedActions, array(ActionCode::ADD, ActionCode::DELETE));
			}
			$connectedUser = $this->context->getUser();
			if(!$isUser && !$connectedUser->isSuperAdmin()){
				$groups = $connectedUser->getGroups(true, true, false);
				if(!in_array($this->targetObject->getId(), $groups)){
					$this->restrictedActions[] = ActionCode::ADD;
				}
			}
		}
		$this->ajaxFormPosition = FormPosition::LEFT;
	}
	protected function getAddableItems() {
		if($this->addableItems===null){
			$excludes = $this->getDataToExclude();
			$isUser = $this->isExtraUserParam();
			$class = $isUser ? 'Group' : 'User';
			$objectType = $isUser ? GroupType::ADMIN : UserType::ADMIN;
			if(!$isUser){
				$excludes[] = $this->context->getUser()->getId();
				$restrictions['superAdmin'] = 0;
			}
			$restrictions = array('type'=>$objectType);
			if(!empty($excludes)){
				$restrictions['id'] = array('operator'=>Operator::NOT_IN_LIST, 'value'=>$excludes);
			}
			if($isUser){
				$connectedUser = $this->context->getUser();
				if($connectedUser->isSuperAdmin()){
					$this->addableItems = $this->getDAOInstance($class, false)->getByFields($restrictions);
				}else{
					$groups = $connectedUser->getGroups(false, true, true);
					$this->addableItems = array();
					foreach($groups as $group){
						if(!in_array($group->getId(), $excludes) && !Tools::inModelArray($group->getId(), $this->addableItems, 'id')){
							$this->addableItems[] = $group;
						}
					}
					/*if(empty($excludes)){
						$this->addableItems = $groups;
					}else{
						$this->addableItems = array();
						foreach($groups as $group){
							if(!in_array($group->getId(), $excludes) && !Tools::inModelArray($group->getId(), $this->addableItems, 'id')){
								$this->addableItems[] = $group;
							}
						}
					}*/
				}
			}else{
				$this->addableItems = $this->getDAOInstance($class, false)->getByFields($restrictions);
			}
			
			
		}
		return $this->addableItems;
	}
	
	/*protected function getRestrictionFromExtraListParams() {
		$restriction=parent::getRestrictionFromExtraListParams();
		if($this->isExtraUserParam()){
			$restriction['idUser'] = $this->extraListParams['target'];
		}elseif($this->isExtraGroupParam()){
			$restriction['idGroup'] = $this->extraListParams['target'];
		}
		return $restriction;
	}*/
	protected function checkFormObjectLoaded(){
		/*if($this->context->getUser()->getId() != $this->defaultModel->getIdUser()){
			return true;
		}else{
			$this->errors[] = $this->l('You can not edit your own profile');
			return false;
		}*/
		return ($this->context->getUser()->getId() != $this->defaultModel->getIdUser());
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
		$column = $this->generator->createColumn($this->table, $this->l('Name'), $this->fieldToSet.'_name', ColumnType::TO_STRING_FOREIGN, SearchType::TEXT, false, false);
		$column->setValueKey($this->fieldToSet);
	}
}