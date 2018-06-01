<?php
namespace core\controllers\backend;

use core\Tools;
use core\constant\UserType;
use core\constant\GenderOption;
use core\constant\ActionCode;
use core\constant\generator\ColumnType;
use core\constant\generator\SearchType;

class UserAdminController extends AdminController
{	
	protected $modelClassName = 'User';
	protected $userType = UserType::FRONT_USER;
	protected $superAdmin = 0;
	protected $genderOptions;
	
	public function __construct()
    {
		parent::__construct();
        $this->columnsToExclude = array_merge($this->columnsToExclude, array('avatar', 'type', 'preferredLang', 'additionalInfos', 'lastPasswordGeneratedTime', 'lastConnectionDate', 'lastConnectionData', 'password', 'superAdmin'));
        $this->formFieldsToExclude = array_merge($this->formFieldsToExclude, array('type', 'additionalInfos', 'lastPasswordGeneratedTime', 'lastConnectionDate', 'lastConnectionData', 'superAdmin'));
        $this->saveFieldsToExclude = array_merge($this->saveFieldsToExclude, array('additionalInfos', 'lastPasswordGeneratedTime', 'lastConnectionDate', 'lastConnectionData', 'superAdmin'));
		$this->genderOptions = array(GenderOption::MALE => $this->l('Male'), GenderOption::FEMALE => $this->l('Female'));
		$this->addDefaultValues['type'] = $this->userType;
		$this->addDefaultValues['superAdmin'] = $this->superAdmin;
		$this->baseRestrictionsData['type'] = $this->userType;
		$this->baseRestrictionsData['superAdmin'] = $this->superAdmin;
    }
	
	protected function checkFormFieldAccess($update){
		parent::checkFormFieldAccess($update);
		if($update){
			$this->saveFieldsToExclude[] = 'password';
			$this->formFieldsToExclude[] = 'password';
		}
	}
	
	protected function customizeFormFields($update = false) {
		if(!$update){
			$this->form->addChild($this->getConfirmPasswordField());
		}
	}
	
	protected function getConfirmPasswordField() {
		$input = $this->generator->createPasswordField('confirmPassword', $this->l('Confirm password'));
		return $this->formatFormField($input);
	}
	
	protected function createFieldByDefinition($fieldDefinition, $field)
    {
		if($field == 'preferredLang'){
			$options = array();
			foreach($this->formLanguages as $key => $lang){
				$options[$key] = $lang->getName();
			}
			$input = $this->generator->createSelect($field, $this->l($field), $options);
		}elseif($field == 'gender'){
			$input = $this->generator->createSelect($field, $this->l($field), $this->genderOptions);
		}else{
			$input = parent::createFieldByDefinition($fieldDefinition, $field);
		}
		return $input;
	}
	
	protected function customizeColumns() {
		$this->changeColumnOptions('gender', ColumnType::OPTION, SearchType::SELECT, $this->genderOptions, $this->genderOptions);
	}
	protected function formatFormData($data, $submitted, $update = false) {
		$data['password'] = '';
		return $data;
	}
	
	protected function initActions()
    {
		parent::initActions();
		$this->availableActions[ActionCode::UPDATE_PASSWORD] = array(
			'icon' =>'key',
			'label' =>$this->l('Update password'),
			'title' =>$this->l('Update password'),
			'row' =>true,
			'model' =>true
		);
	}
	
	protected function validateFormData($update = false, $fieldsToExclude = array(), $fieldsToValidate = array(), $identifiers = array()) {
		parent::validateFormData($update, $fieldsToExclude, $fieldsToValidate, $identifiers);
		if(!$update){
			$this->validateConfirmPassword();
		}
	}
	
	protected function validateConfirmPassword()
    {
		if($this->defaultModel->getPassword()!=Tools::getValue('confirmPassword')){
			$this->formErrors['confirmPassword'] = array('errors'=>'confirmPassword');
		}
	}
	
	protected function processUpdatePassword(){
		if($this->loadFormObject()){
			$submitAction = $this->createFormAction();
			$submitted = Tools::isSubmit($submitAction);
			if($submitted){
				$password = Tools::getValue('password');
				$this->defaultModel->setPassword($password);
				$this->validateFormData(true, array(), array('password'));
				$this->validateConfirmPassword();
				if($this->defaultModel->isFieldsValidated(array(), array('password')) && !$this->hasErrors()){
					if($this->getDAOInstance()->changeValue($this->defaultModel, 'password', $password)){
						$this->processResult['success'] = $this->action;
						$this->redirectAfter = true;
						$this->resetAllFilters();
					}else{
						$this->errors[] = $this->l('An error occured while updating password');
					}
				}
			}
			if(!$this->redirectAfter){
				$label = sprintf($this->l('Update password of %s'), $this->defaultModel->__toString());
				$formAction = $this->createUrl(array('action' => ActionCode::UPDATE_PASSWORD, self::ID_PARAM_URL => $this->defaultModel->getSinglePrimaryValue()));
				$form = $this->generator->createForm(true, true, $this->createUrl(), true, $label, '', $formAction, $submitAction);
				$form->addChild($this->formatFormField($this->createFieldByDefinition($this->modelDefinition['fields']['password'], 'password')));
				$form->addChild($this->getConfirmPasswordField());
				$form->setErrors($this->formatFormErrors());
				$this->processResult['content'] = $form->generate();
				$this->processResult['formContentType'] = 1;
			}
		}
		
	}
}