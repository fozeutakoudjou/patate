<?php
namespace core\controllers\backend;
use core\Tools;
use core\constant\generator\ColumnType;
use core\constant\generator\SearchType;
use core\constant\dao\Operator;
class AdminMenuAdminController extends AdminController
{	
	protected $modelClassName = 'AdminMenu';
	public function __construct()
    {
		parent::__construct();
        $this->columnsToExclude = array_merge($this->columnsToExclude, array('link', 'level', 'newTab', 'title', 'linkType', 'clickable'));
        $this->formFieldsToExclude = array_merge($this->formFieldsToExclude, array('linkType', 'link', 'level'));
		/*$this->addDefaultValues['type'] = UserType::FRONT_USER;
		$this->genderOptions = array(GenderOption::MALE => $this->l('Male'), GenderOption::FEMALE => $this->l('Female'));*/
    }
	protected function customizeColumns() {
		$actionOptions = $this->createOptions('Action', '', array('dependentOnId'=>0), true);
		$this->changeColumnOptions('idAction', ColumnType::OPTION, SearchType::SELECT, $actionOptions, $actionOptions);
		$parentOptions = $this->createOptions($this->modelClassName, '', array(), true);
		$this->changeColumnOptions('idParent', ColumnType::OPTION, SearchType::SELECT, $parentOptions, $parentOptions);
		/*$field = Tools::formatForeignField('idWrapper', 'name');
		$this->generator->createColumn($this->table, $field, $field, ColumnType::TEXT, SearchType::TEXT, true, true);
		$this->associationList['idWrapper'] = array();*/
	}
	
	protected function createFieldByDefinition($fieldDefinition, $field)
    {
		if($field == 'idWrapper'){
			$input = $this->generator->createSelect($field, $this->l($field), $this->createOptions('Wrapper'));
		}elseif($field == 'idAction'){
			$input = $this->generator->createSelect($field, $this->l($field), $this->createOptions('Action', '', array('dependentOnId'=>0)));
		}elseif($field == 'idParent'){
			$restrictions = $this->defaultModel->isLoaded() ? array('id'=>array('value'=>$this->defaultModel->getId(), 'operator'=>Operator::DIFFERENT)):array();
			$input = $this->generator->createSelect($field, $this->l($field), $this->createOptions($this->modelClassName, '', $restrictions, true));
		}else{
			$input = parent::createFieldByDefinition($fieldDefinition, $field);
		}
		return $input;
	}
}