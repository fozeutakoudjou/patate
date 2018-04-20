<?php
namespace core\controllers\backend\partial;


abstract class FormAdminController extends ListAdminController
{
	protected $form;
	protected $formFieldsToExclude = array('dateAdd', 'dateUpdate', 'deleted');
	protected $updateFieldsToExclude = array('dateAdd', 'dateUpdate', 'deleted');
	protected function createFormAction()
    {
		return 'submitFormEdit'.$this->modelClassName;
	}
	protected function createForm()
    {
		$formAction = 'submit';
		$submitAction = '';
		$this->form = $this->generator->createForm(true, true, '#', true, $this->l($this->modelClassName), '', $formAction, $submitAction = '');
		$this->customizeForm();
	}
	
	protected function customizeForm() {}
	
	
	protected function createFormFields()
    {
		foreach($this->modelDefinition['fields'] as $field => $fieldDefinition){
			if(!in_array($field, $this->formFieldsToExclude)){
				$input = $this->generator->createTextField($field, $this->l($field));
				$input->setLabelWidth('col-lg-3');
				$input->setWidth('col-lg-6');
				$this->form->addChild($input);
			}
		}
		$this->customizeFormFields();
	}
	
	protected function customizeFormFields() {}
	
	protected function getFormData() {
		$fields = array();
		$data = $this->getDAOInstance()->getByFields($fields, true);
		return $data;
	}
	
	protected function formatFormData($data) {
		return $data;
	}
	
	protected function getFormFieldsRestriction(){
		$data = Tools::getValue('param1');
		$fields = array();
		if($data){
			$fields = $this->defaultModel->getPrimaryValue($data);
		}else{
			$this->errors[] = $this->l('invalid Identifier');
		}
		return $fields;
	}
}