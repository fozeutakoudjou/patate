<?php
namespace core\controllers\backend;

use core\constant\UserType;
use core\constant\GenderOption;
use core\constant\generator\ColumnType;
use core\constant\generator\SearchType;

class UserAdminController extends AdminController
{	
	protected $modelClassName = 'User';
	protected $userType = UserType::FRONT_USER;
	protected $genderOptions;
	
	public function __construct()
    {
		parent::__construct();
        $this->columnsToExclude = array_merge($this->columnsToExclude, array('type', 'preferredLang', 'additionalInfos', 'lastPasswordGeneratedTime', 'lastConnectionDate', 'lastConnectionData', 'password'));
        $this->formFieldsToExclude = array_merge($this->formFieldsToExclude, array('type', 'additionalInfos', 'lastPasswordGeneratedTime', 'lastConnectionDate', 'lastConnectionData'));
        $this->saveFieldsToExclude = array_merge($this->saveFieldsToExclude, array('additionalInfos', 'lastPasswordGeneratedTime', 'lastConnectionDate', 'lastConnectionData'));
		$this->genderOptions = array(GenderOption::MALE => $this->l('Male'), GenderOption::FEMALE => $this->l('Female'));
		$this->addDefaultValues['type'] = $this->userType;
		$this->baseRestrictionsData['type'] = $this->userType;
    }
	
	protected function customizeFormFields($update = false) {
		
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
}