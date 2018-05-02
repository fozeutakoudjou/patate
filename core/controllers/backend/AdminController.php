<?php
namespace core\controllers\backend;
use core\Tools;
use core\controllers\backend\partial\FormAdminController;

abstract class AdminController extends FormAdminController
{	
	protected function processAdd(){
		$this->doEdit(false);
	}
	
	protected function processUpdate(){
		$this->doEdit(true);
	}
	
	protected function doEdit($update = false){
		$submitted = Tools::isSubmit($this->createFormAction());
		$continue = true;
		$objectLoaded = false;
		if($submitted){
			if($update){
				$continue = $this->loadFormObject();
				$objectLoaded = true;
				$identifiers = $this->defaultModel->getPrimaryValues();
			}
			if($continue){
				$this->checkFormFieldAccess($update);
				$this->retrieveSubmittedData($update);
				if(!$update){
					$this->setAddDefaultValues($this->formFieldsToExclude);
				}
				$this->validateFormData($update);
				if($this->defaultModel->isFieldsValidated() && !$this->hasErrors()){
					$continue = $this->beforeEdit($update);
					if($continue && !$this->hasErrors()){
						$result = $update ? $this->getDAOInstance()->update($this->defaultModel, $this->formFieldsToExclude, array(), $identifiers, true, $this->formLanguages) :
							$this->getDAOInstance()->add($this->defaultModel, true, $this->formLanguages);
						$this->afterEdit($result, $update);
						if($result){
							$this->redirectLink = $this->createUrl();
							$this->redirectAfter = true;
						}
					}
				}
			}
			
		}
		if($continue && !$this->redirectAfter){
			if($update && !$objectLoaded){
				$continue = $this->loadFormObject();
			}
			
			if($continue){
				$this->createForm($update);
				$this->createFormFields($update);
				$data = $this->formatFormData($this->getFormData($submitted, $update), $submitted, $update);
				$this->form->setValue($data);
				$this->form->setErrors($this->formatFormErrors());
				$this->processResult['content'] = $this->form->generate();
			}
		}
	}
	protected function beforeEdit($update = false){
		return true;
	}
	protected function afterEdit($result, $update = false){
		
	}
	
	protected function processList(){
		if(Tools::isSubmit('submitFilterData')){
			$this->updateListSearchData();
			$this->redirectLink = $this->createUrl();
			$this->redirectAfter = true;
		}elseif(Tools::getValue('resetAllFilters')){
			$this->resetAllFilters();
			$this->redirectLink = $this->createUrl();
			$this->redirectAfter = true;
		}else{
			$this->retrieveListSearchData();
			$this->formatListSearchData();
			$this->retrieveListUrlParam();
			$this->createTable();
			$this->createColumns();
			$this->createTableActions();
			$this->createBulkActions();
			$this->createRowsActions();
			$data = $this->formatListData($this->getListData());
			$this->table->setTotalResult($data['total']);
			$this->table->setValue($data['list']);
			$this->processResult['content'] = $this->table->generate();
		}
	}
	
	protected function processActivate(){
		
	}
	
	protected function beforeDirectAction($action, $model){
		return true;
	}
	protected function afterDirectAction($result, $model){
		
	}
	
	protected function onDirectAction(){
		$models = $this->prepareDirectActionData(false);
		if(!$this->hasErrors()){
			foreach($models as $model){
				$continue = $this->beforeDirectAction($update, $model);
				if($continue && !$this->hasErrors()){
					$result = $update ? $this->getDAOInstance()->changeActive($this->defaultModel, $this->formFieldsToExclude, array(), $identifiers, true, $this->formLanguages) :
						$this->getDAOInstance()->add($this->defaultModel, true, $this->formLanguages);
					$this->afterDirectAction($update, $model);
					if($result){
						$this->redirectLink = $this->createUrl();
						$this->redirectAfter = true;
					}
				}
			}
		}
	}
	
	protected function prepareDirectActionData($useLang = false){
		$models = array();
		$ids = Tools::isSubmit('submitBulkAction') ? Tools::getValue($this->modelIdentifier) : array('');
		if(is_array($ids)){
			foreach($ids as $id){
				if($this->loadFormObject($id, $useLang, false)){
					$models[] = $this->defaultModel;
				}else{
					break;
				}
			}
		}
	}
}