<?php
namespace core\controllers\backend\partial;

use core\Tools;
use core\constant\ActionCode;
abstract class FormAdminController extends ListAdminController
{
	protected $form;
	protected $formFieldsToExclude = array('dateAdd', 'dateUpdate', 'deleted');
	protected $updateFieldsToExclude = array('dateAdd', 'dateUpdate', 'deleted');
	protected $defaultFormRestriction = array();
	protected function createFormAction()
    {
		return 'submitFormEdit'.$this->modelClassName;
	}
	/*protected function createAddForm()
    {
		$this->createForm(false);
	}
	protected function createEditForm()
    {
		$this->createForm(true);
	}*/
	protected function createForm($update = false)
    {
		$submitAction = $this->createFormAction();
		$params = $update ? array('action' => ActionCode::UPDATE, self::ID_PARAM_URL => ActionCode::UPDATE) : array('action' => ActionCode::ADD);
		$formAction = $this->createUrl($params);
		$this->form = $this->generator->createForm(true, true, '#', true, $this->l($this->modelClassName), '', $formAction, $submitAction);
		$this->customizeForm($update);
	}
	
	protected function customizeForm($update = false) {}
	
	
	protected function createFormFields($update = false)
    {
		foreach($this->modelDefinition['fields'] as $field => $fieldDefinition){
			if(!in_array($field, $this->formFieldsToExclude)){
				$input = $this->generator->createTextField($field, $this->l($field));
				$input->setLabelWidth('col-lg-3');
				$input->setWidth('col-lg-6');
				$this->form->addChild($input);
			}
		}
		$this->customizeFormFields($update);
	}
	
	protected function customizeFormFields($update = false) {}
	
	protected function getFormData($submitted, $update = false) {
		if($update){
			$fields = $this->getFormFieldsRestriction();
			$data = $this->getDAOInstance()->getByFields($fields, true);
			if(empty($data)){
				$this->errors[] = $this->l('Data not found');
			}else{
				$this->defaultModel = $data[0];
			}
		}
		return $this->defaultModel->isLoaded() ? $this->defaultModel->toArray() : array();
	}
	
	protected function retrieveSubmittedData($update = false) {
		$this->defaultModel->copyFromPost();
	}
	
	protected function validateFormData($update = false) {
		$errors = $this->defaultModel->validateFields();
		return $errors;
	}
	
	protected function formatFormData($data, $submitted, $update = false) {
		return $data;
	}
	
	protected function getFormFieldsRestriction(){
		$data = Tools::getValue('param1');
		$fields =  $this->defaultFormRestriction();
		if($data){
			$fields = $this->defaultModel->getPrimaryValue($data);
		}else{
			$this->errors[] = $this->l('invalid Identifier');
		}
		return $fields;
	}
}