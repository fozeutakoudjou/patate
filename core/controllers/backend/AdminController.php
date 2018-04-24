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
				/*$this->errors = ($this->errors==null)? array() : $this->errors;
				$this->errors = array_merge($this->errors, $validateErrors);*/
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