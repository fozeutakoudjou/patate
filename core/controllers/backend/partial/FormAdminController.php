<?php
namespace core\controllers\backend\partial;

use core\Tools;
use core\Validate;
use core\constant\ActionCode;
use core\constant\DataType;
abstract class FormAdminController extends ListAdminController
{
	const ERRORS_SEPARATOR = ', ';
	protected $form;
	protected $formFieldsToExclude = array('dateAdd', 'dateUpdate', 'deleted');
	protected $saveFieldsToExclude = array('deleted');
	protected $addDefaultValues = array();
	protected $formFieldPreffix = '';
	protected $formFieldsAccessChecked = false;
	protected $errorLabels = array();
	protected $errorLabelDefault;
	protected $loadedModel;
	
	public function init()
    {
		parent::init();
		$this->initErrorLabels();
		if(isset($this->modelDefinition['fields']['active']) && !isset($this->addDefaultValues['active'])){
			$this->addDefaultValues['active'] = 1;
		}
	}
	protected function initErrorLabels()
    {
		$this->errorLabelDefault = $this->l('This field is invalid');
		$this->errorLabels = array(
			Validate::VALIDATE_UNIQUE =>$this->l('Another record has already this value'),
			Validate::VALIDATE_REQUIRED =>$this->l('This field is required'),
			Validate::VALIDATE_MAX_SIZE =>$this->l('This field can not contain more than %s characters'),
			Validate::VALIDATE_MIN_SIZE =>$this->l('This field can not contain less than %s characters'),
			'isUnsignedInt' =>$this->l('This field must be an unsigned integer'),
			'isUnsignedFloat' =>$this->l('This field must be an unsigned float'),
			'isBool' =>$this->l('The value of this field 0 or 1'),
			'confirmPassword' =>$this->l('Both passwords must be identical'),
		);
	}
	protected function createFormAction()
    {
		return 'submitFormEdit'.$this->modelClassName;
	}
	protected function createForm($update = false)
    {
		$submitAction = $this->createFormAction();
		$params = $update ? array('action' => ActionCode::UPDATE, self::ID_PARAM_URL => $this->defaultModel->getSinglePrimaryValue()) : array('action' => ActionCode::ADD);
		$formAction = $this->createUrl($params);
		$title = ($update) ? sprintf($this->l('Update informations about %1$s %2$s'), $this->l($this->modelClassName), $this->loadedModel->__toString())
			: sprintf($this->l('Add new %s'), $this->l($this->modelClassName));
		$this->form = $this->generator->createForm(true, true, $this->createUrl(), true, $title, '', $formAction, $submitAction);
		$this->customizeForm($update);
	}
	
	protected function customizeForm($update = false) {}
	
	
	protected function createFormFields($update = false)
    {
		$this->checkFormFieldAccess($update);
		foreach($this->modelDefinition['fields'] as $field => $fieldDefinition){
			if(!in_array($field, $this->formFieldsToExclude)){
				$this->form->addChild($this->formatFormField($this->createFieldByDefinition($fieldDefinition, $field)));
			}
		}
		$this->customizeFormFields($update);
	}
	
	protected function formatFormField($input) {
		$input->setLabelWidth('col-lg-3');
		$input->setWidth('col-lg-9');
		return $input;
	}
	
	protected function customizeFormFields($update = false) {}
	
	protected function getFormData($submitted, $update = false) {
		$data = ($submitted || $this->defaultModel->isLoaded()) ? $this->defaultModel->toArray($this->formFieldPreffix, false, false) : array();
		if(!$update && !$submitted){
			foreach($this->addDefaultValues as $field => $value){
				if(!in_array($field, $this->formFieldsToExclude)){
					$data[$field] = $value;
				}
			}
		}
		return $data;
	}
	
	protected function loadFormObject($id='', $useLang = true, $useAllLang = true) {
		$fields = $this->getFormFieldsRestriction($id);
		$data = $this->getDAOInstance()->getByFields($fields, false, $this->lang, $useLang, $useAllLang);
		if($this->hasErrors() || empty($data)){
			$this->errors[] = $this->l('Data not found');
			$return = false;
		}else{
			$this->defaultModel = $data[0];
			$this->loadedModel = clone $this->defaultModel;
			$return = $this->checkFormObjectLoaded();
		}
		return $return;
	}
	
	protected function checkFormObjectLoaded() {
		return true;
	}
	
	protected function setAddDefaultValues($fields) {
		foreach($fields as $field){
			if(isset($this->addDefaultValues[$field])){
				$this->defaultModel->setPropertyValue($field, $this->addDefaultValues[$field]);
			}
		}
	}
	
	protected function retrieveSubmittedData($update = false) {
		$this->defaultModel->copyFromPost($this->formLanguages, $this->formFieldPreffix, $this->formFieldsToExclude);
	}
	
	protected function validateFormData($update = false, $fieldsToExclude = array(), $fieldsToValidate = array(), $identifiers = array()) {
		$this->formErrors = $this->defaultModel->validateFields($fieldsToExclude, $fieldsToValidate, $this->getDAOInstance(), $update, $identifiers);
	}
	
	protected function formatFormErrors() {
		$formatteds = array();
		foreach($this->formErrors as $field => $error){
			$label = $this->errorLabelDefault;
			if(is_array($error)){
				$label = $this->getErrorLabel($error);
			}else{
				$label = $error;
			}
			$formatteds[$field] = $label;
		}
		return $formatteds;
	}
	
	protected function getErrorLabel($validate) {
		if(is_array($validate) && isset($validate['errorCode'])){
			$validates = array($validate['errorCode']=>$validate);
		}else{
			$validates = is_array($validate) ? $validate : array($validate);
		}
		
		$first = true;
		$label = array();
		$noLangCode = 'noLang';
		foreach($validates as $key => $validate){
			if(!$first){
				$label.=self::ERRORS_SEPARATOR;
			}
			$validateCode = (empty($validate) || is_array($validate)) ? $key : $validate;
			$value = isset($this->errorLabels[$validateCode]) ? $this->errorLabels[$validateCode] : $this->errorLabelDefault;
			$value = (is_array($validate) && isset($validate['param'])) ? sprintf($value, $validate['param']) : $value;
			$langs = (is_array($validate) && isset($validate['langs'])) ? $validate['langs'] : array($noLangCode);
			foreach($langs as $lang){
				$label[$lang] = (isset($label[$lang]) ?$label[$lang] : '').$value;
			}
			
			$first = false;
		}
		return isset($label[$noLangCode]) ? $label[$noLangCode] : $label;
	}
	
	protected function formatFormData($data, $submitted, $update = false) {
		return $data;
	}
	
	protected function getFormFieldsRestriction($id=''){
		$data = empty($id) ? Tools::getValue(self::ID_PARAM_URL) : $id;
		$fields =  $this->baseRestrictionsData;
		if($data){
			$fields = array_merge($this->defaultModel->getPrimaryValuesFromString($data), $fields);
		}else{
			$this->errors[] = $this->l('invalid Identifier');
		}
		return $fields;
	}
	
	protected function checkFormFieldAccess($update){
		if(!$this->formFieldsAccessChecked){
			if(isset($this->modelDefinition['fields']['active']) && (!$this->checkUserAccess(ActionCode::ACTIVATE) || !$this->checkUserAccess(ActionCode::DESACTIVATE))){
				$this->saveFieldsToExclude[] = 'active';
				$this->formFieldsToExclude[] = 'active';
			}
			$this->formFieldsAccessChecked = true;
		}
	}
	
	protected function createFieldByDefinition($fieldDefinition, $field)
    {
		if($fieldDefinition['type']==DataType::TYPE_BOOL){
			$input = $this->generator->createSwitch($field, $this->l($field));
			//$input = $this->generator->createRadio($field, $this->l($field));
		}elseif($field=='email'){
			$input = $this->generator->createEmailField($field, $this->l($field));
			//$input->setTranslatable(true);
		}elseif($field=='password'){
			$input = $this->generator->createPasswordField($field, $this->l($field));
		}elseif($fieldDefinition['type']==DataType::TYPE_DATE){
			//$type = ColumnType::DATE;
		}else{
			$input = $this->generator->createTextField($field, $this->l($field));
		}
		if($this->defaultModel->isLangField($field)){
			$input->setTranslatable(true);
		}
		return $input;
	}
}