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
use core\constant\dao\OrderWay;
use core\constant\dao\LogicalOperator;
use core\constant\UserType;
use core\constant\GroupType;
use core\constant\WrapperType;
use core\generator\html\interfaces\Formatter;
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
		$this->columnsToExclude[] = 'idRight';
    }
	protected function checkFormObjectLoaded(){
		if($this->context->getUser()->getId() != $this->defaultModel->getIdUser()){
			return true;
		}else{
			$this->errors[] = $this->l('You can not edit your own access');
			return false;
		}
	}
	/*protected function customizeForm($update = false) {
		if($this->targetObject!=null){
			$label = $this->isExtraUserParam() ? $this->l('Add new accesses to administrator %s') : $this->l('Add new accesses to profile %s');
			$this->form->setLabel(sprintf($label, $this->targetObject->__toString()));
		}
	}*/
	protected function customizeTable() {
		parent::customizeTable();
		$this->table->setRowFormatter(new AccessRowFormatter($this->context->getUser(), $this->targetObject, $this->targetField, $this->isExtraUserParam()));
	}
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
		$this->form->addChild($this->generator->createInputCustomContent($table, $this->selectableIdentifier, $this->l('Rights'), false));
	}
	protected function customizeColumns() {
		$field = Tools::formatForeignField('idRight', 'idWrapper');
		$targets = $this->createOptions('Wrapper', '', array('type'=>WrapperType::ADMIN_CONTROLLER), true);
		$this->generator->createColumn($this->table, $this->l('Target'), $field, ColumnType::OPTION, SearchType::SELECT, true, true, $targets, $targets);
		
		$actions = $this->createOptions('Action', '', array(), true);
		$this->generator->createColumn($this->table, $this->l('Action'), Tools::formatForeignField('idRight', 'idAction'), ColumnType::OPTION, SearchType::SELECT, true, true, $actions, $actions);
		$this->associationList['idRight'] = array();
		$profiles = $this->createOptions('Group', '', array('type'=>GroupType::ADMIN), true);
		$this->changeColumnOptions('idGroup', ColumnType::OPTION, SearchType::SELECT, $profiles, $profiles);
		if($this->targetObject==null){
			$admins = $this->createOptions('User', '', array('type'=>UserType::ADMIN, 'superAdmin'=>0), true);
			$this->changeColumnOptions('idUser', ColumnType::OPTION, SearchType::SELECT, $admins, $admins);
		}
	}
	protected function loadTargetObject(){
		$isUser = $this->isExtraUserParam();
		$class = $isUser ? 'User' : 'Group';
		$objectType = $isUser ? UserType::ADMIN : GroupType::ADMIN;
		$restrictions = array('type'=>$objectType, 'id'=>$this->extraListParams['target']);
		$this->targetField = 'id'.$class;
		if($isUser){
			$this->columnsToExclude[] = $this->targetField;
			$restrictions['superAdmin'] = 0;
		}
		$objects = $this->getDAOInstance($class, false)->getByFields($restrictions);
		
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
			$this->defaultOrderColumn = 'idUser';
			$restriction['idUser'] = array('group'=>true,'logicalOperator'=>LogicalOperator::OR_,'fields'=> array('idUser'=>$this->targetObject->getId()));
			$groups = $this->targetObject->getGroups(true, true);
			if(!empty($groups)){
				$restriction['idUser']['fields']['idGroup'] = array('value'=>$groups, 'operator'=>Operator::IN_LIST);
			}
		}elseif($this->isExtraGroupParam()){
			$this->defaultOrderColumn = 'idGroup';
			$restriction['idGroup'] = array('group'=>true,'logicalOperator'=>LogicalOperator::OR_,'fields'=> array('idGroup_main'=>array('field'=>'idGroup', 'value'=>$this->targetObject->getId())));
			$groups = $this->targetObject->getParents(true, false);
			if(!empty($groups)){
				$restriction['idGroup']['fields']['idGroup_children'] = array('field'=>'idGroup', 'value'=>$groups, 'operator'=>Operator::IN_LIST);
			}
		}
		return $restriction;
	}
}

class AccessRowFormatter implements Formatter
{
	protected $ownRights  = null;
	protected $targetObject;
	protected $isUserTarget;
	protected $connectedUser;
	protected $targetField;
	protected $rightsDisplayed = array();
	public function __construct($connectedUser, $targetObject, $targetField, $isUserTarget)
    {
		$this->connectedUser = $connectedUser;
		$this->targetObject = $targetObject;
		$this->isUserTarget = $isUserTarget;
		$this->targetField = $targetField;
    }
	public function format($item){
		$object = $item->getValue();
		if($this->targetObject!=null){
			$ownRights = $this->getOwnRights($item);
			$idRight = $object->getIdRight();
			if((($object->getPropertyValue($this->targetField)!=$this->targetObject->getId()) && in_array($idRight, $ownRights)) || in_array($idRight, $this->rightsDisplayed)){
				$item->forceContent('');
			}else{
				$this->rightsDisplayed[] = $idRight;
			}
		}
		if($object->getIdUser()==$this->connectedUser->getId()){
			$options = $item->getTable()->getColumn('active')->getSearchOptions();
			$item->setColumnDataOptions('active', $options);
			$item->addRowActionToExclude(ActionCode::DELETE);
		}
	}
	public function getOwnRights($item){
		if($this->ownRights===null){
			$this->ownRights = array();
			$data = $item->getTable()->getValue();
			foreach($data as $value){
				if($value->getPropertyValue($this->targetField) == $this->targetObject->getId()){
					$this->ownRights[] = $value->getIdRight();
				}
			}
		}
		return $this->ownRights;
	}
}