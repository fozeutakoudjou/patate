<?php
namespace core\controllers\backend;

use core\constant\UserType;

class UserAdminController extends AdminController
{	
	protected $modelClassName = 'User';
	protected $baseRestrictionsData = array('type'=>UserType::FRONT_USER);
	
	public function __construct()
    {
		parent::__construct();
        $this->columnsToExclude = array_merge($this->columnsToExclude, array('type', 'preferredLang', 'additionalInfos', 'idProposer', 'lastPasswordGeneratedTime', 'lastConnectionDate', 'lastConnectionData', 'password'));
        $this->formFieldsToExclude = array_merge($this->formFieldsToExclude, array('type', 'additionalInfos', 'idProposer', 'lastPasswordGeneratedTime', 'lastConnectionDate', 'lastConnectionData'));
		$this->addDefaultValues['type'] = UserType::FRONT_USER;
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
		}else{
			$input = parent::createFieldByDefinition($fieldDefinition, $field);
		}
		return $input;
	}
}