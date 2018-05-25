<?php
namespace core\controllers\backend\partial;
use core\controllers\backend\AdminController;
use core\Tools;
use core\constant\generator\ColumnType;
use core\constant\generator\SearchType;
use core\constant\UrlParamType;
use core\constant\ActionCode;
use core\constant\FormPosition;
use core\constant\dao\Operator;
use core\constant\UserType;
use core\constant\GroupType;
abstract class AssociationController extends AdminController
{	
	protected $targetObject;
	protected $targetRequired = false;
	protected $targetRequiredToAdd = true;
	protected $targetField;
	protected $fieldToSet;
	protected $addableItems = null;
	protected $selectableIdentifier = 'items';
	public function __construct()
    {
		parent::__construct();
		$this->restrictedActions[]=ActionCode::UPDATE;
    }
	protected function processAdd(){
		if($this->targetRequiredToAdd && ($this->targetObject==null)){
			$this->errors[] = $this->l('You must select a valid target');
		}else{
			$submitted = Tools::isSubmit($this->createFormAction());
			if($submitted){
				$ids = Tools::getValue($this->selectableIdentifier);
				if(is_array($ids)){
					$addables = $this->getAddableItems();
					$idKey = (isset($addables[0]) && ($addables[0]!=null)) ? $addables[0]->createSinglePrimary() : 'id';
					foreach($ids as $id){
						if(Tools::inModelArray($id, $addables, $idKey)){
							$this->defaultModel->setPropertyValue($this->fieldToSet, $id);
							$this->doEdit(false, true);
						}
					}
				}else{
					$this->errors[] = $this->l('You must select at least an item');
				}
			}
			if(!$submitted || $this->hasErrors()){
				$this->renderForm($submitted, false);
			}else{
				$this->processResult['success'] = $this->action;
				$this->redirectAfter = true;
				$this->resetAllFilters();
			}
		}
	}
	protected function retrieveSubmittedData($update = false) {
		if($this->targetObject!=null){
			$this->defaultModel->setPropertyValue($this->targetField, $this->targetObject->getSinglePrimaryValue());
		}
	}
	protected function getDataToExclude() {
		$excludes = $this->getDAOInstance()->getByFields(array($this->targetField=>$this->targetObject->getSinglePrimaryValue()));
		return Tools::getArrayValues($excludes, true, $this->fieldToSet);
	}
	abstract protected function getAddableItems();
	
	protected function retrieveExtraListParams(){
		
		$target = Tools::getValue('target');
		if(!empty($target)){
			$this->extraListParams['target'] = (int)$target;
			$this->extraListParams['type'] = Tools::getValue('type');
			$this->executeActionUsingAjax = true;
			$this->ajaxActivatorEnabled = false;
			$this->ajaxFormPosition = FormPosition::TOP;
			$this->loadTargetObject();
			if($this->targetObject==null){
				$this->processCancelled = true;
				$this->errors[] = $this->l('Invalid target');
			}
		}
		if(!$this->processCancelled && $this->targetRequired && ($this->targetObject==null)){
			$this->processCancelled = true;
			$this->errors[] = $this->l('Target is required');
		}
		if($this->targetRequiredToAdd && ($this->targetObject==null)){
			$this->restrictedActions[]=ActionCode::ADD;
		}
	}
	abstract protected function loadTargetObject();
	abstract protected function getAssociatedTableLabel();
	abstract protected function getAssociatedFormLabel();
	protected function customizeTable() {
		if($this->targetObject!=null){
			$this->table->setLabel(sprintf($this->getAssociatedTableLabel(), $this->targetObject->__toString()));
		}
	}
	protected function customizeForm($update = false) {
		if($this->targetObject!=null){
			$this->form->setLabel(sprintf($this->getAssociatedFormLabel(), $this->targetObject->__toString()));
		}
	}
	
	protected function getRestrictionFromExtraListParams() {
		$restriction=parent::getRestrictionFromExtraListParams();
		if($this->targetObject!=null){
			$restriction[$this->targetField] = $this->targetObject->getSinglePrimaryValue();
		}
		/*if($this->isExtraUserParam()){
			$restriction['idUser'] = $this->extraListParams['target'];
		}elseif($this->isExtraGroupParam()){
			$restriction['idGroup'] = $this->extraListParams['target'];
		}*/
		return $restriction;
	}
	
	protected function isExtraUserParam() {
		return (isset($this->extraListParams['type']) && ($this->extraListParams['type']==UrlParamType::USER));
	}
	protected function isExtraGroupParam() {
		return (isset($this->extraListParams['type']) && ($this->extraListParams['type']==UrlParamType::GROUP));
	}
}