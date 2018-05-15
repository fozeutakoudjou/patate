<?php
namespace core\controllers\backend;

use core\constant\GroupType;
use core\constant\dao\Operator;
use core\constant\generator\ColumnType;
use core\constant\generator\SearchType;

class GroupAdminController extends AdminController
{	
	protected $modelClassName = 'Group';
	protected $groupType = GroupType::FRONT;
	
	public function __construct()
    {
		parent::__construct();
        $this->columnsToExclude[] = 'type';
        $this->formFieldsToExclude[] = 'type';
		$this->addDefaultValues['type'] = $this->groupType;
		$this->baseRestrictionsData['type'] = $this->groupType;
    }
	
	protected function customizeColumns() {
		$parentOptions = $this->createOptions($this->modelClassName, '', array('type'=>$this->groupType), true);
		$this->changeColumnOptions('idParent', ColumnType::OPTION, SearchType::SELECT, $parentOptions, $parentOptions);
	}
	
	protected function createFieldByDefinition($fieldDefinition, $field)
    {
		if($field == 'idParent'){
			$restrictions = $this->defaultModel->isLoaded() ? array('id'=>array('value'=>$this->defaultModel->getId(), 'operator'=>Operator::DIFFERENT)):array();
			$restrictions['type'] =$this->groupType; 
			$input = $this->generator->createSelect($field, $this->l($field), $this->createOptions('Group', '', $restrictions, true));
		}else{
			$input = parent::createFieldByDefinition($fieldDefinition, $field);
		}
		return $input;
	}
}