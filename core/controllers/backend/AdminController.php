<?php
namespace core\controllers\backend;
use core\Tools;
use core\controllers\backend\partial\FormAdminController;
use core\constant\ActionCode;

abstract class AdminController extends FormAdminController
{
	protected $dataUsedOnce = array();
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
					$this->setAddDefaultValues(array_merge($this->formFieldsToExclude, $this->saveFieldsToExclude));
				}
				$this->validateFormData($update);
				if($this->defaultModel->isFieldsValidated() && !$this->hasErrors()){
					$continue = $this->beforeEdit($update);
					if($continue && !$this->hasErrors()){
						$result = $update ? $this->getDAOInstance()->update($this->defaultModel, $this->saveFieldsToExclude, array(), $identifiers, true, $this->formLanguages) :
							$this->getDAOInstance()->add($this->defaultModel, true, $this->formLanguages);
						$this->afterEdit($result, $update);
						if($result){
							$this->processResult['success'] = $this->action;
							$this->redirectAfter = true;
							$this->resetAllFilters();
							
						}else{
							$this->errors[] = $this->l('An error occured while saving');
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
				$this->processResult['formContentType'] = 1;
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
			$this->redirectAfter = true;
		}elseif(Tools::getValue('resetAllFilters')){
			$this->resetAllFilters();
			$this->redirectAfter = true;
		}else{
			$this->renderList();
		}
	}
	
	protected function renderList(){
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
		$this->processResult['listContentType'] = 1;
	}
	
	protected function processActivate(){
		$this->doDirectAction(function ($dao, $model){
			return $dao->activate($model);
		});
	}
	
	protected function processDesactivate(){
		$this->doDirectAction(function ($dao, $model){
			return $dao->desactivate($model);
		});
	}
	
	protected function processChangeValue(){
		if(Tools::isSubmit('submitBulkAction')){
			parse_str(Tools::getValue('bulkAdditionalData'), $data);
			$_POST = array_merge($data, $_POST);
		}
		$this->doDirectAction(function ($dao, $model){
			return $dao->changeValue($model, Tools::getValue('field'), Tools::getValue('value'));
		}, $this->getValueChangeCode(Tools::getValue('field'), Tools::getValue('value')));
	}
	
	protected function processDelete(){
		$this->doDirectAction(function ($dao, $model){
			return $dao->delete($model, (isset($this->modelDefinition['fields']['deleted']) ? true : false));
		});
	}
	
	protected function beforeDirectAction($action, $model){
		return true;
	}
	protected function afterDirectAction($action, $result, $model){
		
	}
	
	protected function doDirectAction($callback, $successCode = ''){
		
		$action = $this->action;
		$models = $this->prepareDirectActionData(false);
		if(!$this->hasErrors()){
			foreach($models as $model){
				$continue = $this->beforeDirectAction($action, $model);
				if($continue && !$this->hasErrors()){
					$result = $callback($this->getDAOInstance(), $model);
					$this->afterDirectAction($action,$result, $model);
					if(!$result){
						$this->errors[] = sprintf($this->l('An error occured while processing data "%s"'), $model->getSinglePrimaryValue());
					}
				}
			}
		}
		if($this->hasErrors()){
			unset($_POST['submitFilterData']);
			if($this->checkUserAccess(ActionCode::LISTING)){
				$this->processList();
			}
		}else{
			$this->processResult['success'] = empty($successCode) ? $action : $successCode;
			$this->redirectAfter = true;
			$this->resetAllFilters();
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
		}else{
			$this->errors[] = $this->l('You must select at least an item');
		}
		return $models;
	}
}

interface DirectActionRunnable{
	public function run($model);
}