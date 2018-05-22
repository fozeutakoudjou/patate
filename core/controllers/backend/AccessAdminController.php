<?php
namespace core\controllers\backend;
use core\Tools;
use core\constant\generator\ColumnType;
use core\constant\generator\SearchType;
use core\constant\UrlParamType;
use core\constant\ActionCode;
use core\constant\FormPosition;
class AccessAdminController extends AdminController
{	
	protected $modelClassName = 'Access';
	protected function restrictAction()
    {
		parent::restrictAction();
		$this->restrictedActions[]=ActionCode::UPDATE;
	}
	/*protected function beforeEdit($update = false){
		if(!$update && isset($this->extraListParams['parent'])){
			$this->defaultModel->setIdParent($this->extraListParams['parent']);
		}
		$idParent = $this->defaultModel->getIdParent();
		$return = true;
		if(empty($idParent)){
			$idParent = null;
			$this->defaultModel->setLevel(1);
		}else{
			$parent = $this->getDAOInstance($this->modelClassName, false)->getById($idParent, false, null, false, false);
			if($parent==null){
				$return = false;
				$this->formErrors['idParent'] = $this->l('This menu doest not exist');
			}else{
				$this->defaultModel->setLevel((int)$parent->getLevel()+1);
			}
		}
		if(!$update){
			$lastBrother = $this->getDAOInstance($this->modelClassName, false)->getByFields(array('idParent' => $idParent), false, null, false, false, array(), 0, 1, 'position', OrderWay::DESC);
			$position = empty($lastBrother) ? 1 : (int)$lastBrother[0]->getPosition() + 1;
			$this->defaultModel->setPosition($position);
		}
		return $return;
	}*/
	
	protected function createFormFields($update = false)
    {
		if(!$update){
			if(isset($this->extraListParams['type'])){
				$this->formFieldsToExclude[] = 'idUser';
				$this->formFieldsToExclude[] = 'idGroup';
			}
		}
		parent::createFormFields($update);
	}
	/*protected function customizeColumns() {
		$field = Tools::formatForeignField('idWrapper', 'name');
		$this->generator->createColumn($this->table, $field, $field, ColumnType::TEXT, SearchType::TEXT, true, true);
		$this->associationList['idWrapper'] = array();
	}
	
	protected function createFieldByDefinition($fieldDefinition, $field)
    {
		if($field == 'idWrapper'){
			$input = $this->generator->createSelect($field, $this->l($field), $this->createOptions('Wrapper'));
		}elseif($field == 'idAction'){
			$input = $this->generator->createSelect($field, $this->l($field), $this->createOptions('Action'));
		}else{
			$input = parent::createFieldByDefinition($fieldDefinition, $field);
		}
		return $input;
	}*/
	
	protected function retrieveExtraListParams(){
		
		$type = Tools::getValue('type');
		if(!empty($type)){
			$this->extraListParams['type'] = $type;
			$this->extraListParams['target'] = (int)Tools::getValue('target');
			$this->executeActionUsingAjax = true;
			$this->ajaxActivatorEnabled = false;
			$this->ajaxFormPosition = FormPosition::TOP;
		}
	}
	
	protected function customizeTable() {
		if(isset($this->extraListParams['type'])){
			$class = $this->isExtraUserParam() ? 'User' : 'Group';
			$object = $this->getDAOInstance($class, false)->getById($this->extraListParams['target']);
			if($object!=null){
				$label = $this->isExtraUserParam() ? $this->l('Access of administrator %s') : $this->l('Access of profile %s');
				$this->table->setLabel(sprintf($label, $object->__toString()));
			}
		}
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
	
	protected function isExtraUserParam() {
		return (isset($this->extraListParams['type']) && ($this->extraListParams['type']==UrlParamType::USER));
	}
	protected function isExtraGroupParam() {
		return (isset($this->extraListParams['type']) && ($this->extraListParams['type']==UrlParamType::GROUP));
	}
}