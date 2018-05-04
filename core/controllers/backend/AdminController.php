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
							$this->dataUsedOnce['success'] = $this->action;
							$this->redirectLink = $this->createUrl();
							$this->redirectAfter = true;
							$this->resetAllFilters();
							$this->setCookieDataUsedOnce(true);
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
		$this->doDirectAction(function ($dao, $model){
			return $dao->activate($model);
		});
	}
	
	protected function processDesactivate(){
		$this->doDirectAction(function ($dao, $model){
			return $dao->desactivate($model);
		});
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
	
	protected function doDirectAction($callback){
		
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
			$this->dataUsedOnce['success'] = $action;
			$this->redirectLink = $this->createUrl();
			$this->redirectAfter = true;
			$this->resetAllFilters();
			$this->setCookieDataUsedOnce(true);
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