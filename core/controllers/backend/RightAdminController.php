<?php
namespace core\controllers\backend;
use core\Tools;
use core\constant\generator\ColumnType;
use core\constant\generator\SearchType;
use core\constant\WrapperType;
class RightAdminController extends AdminController
{	
	protected $modelClassName = 'Right';
	
	protected function customizeColumns() {
		$targets = $this->createOptions('Wrapper', '', array('type'=>WrapperType::ADMIN_CONTROLLER), true);
		$actions = $this->createOptions('Action', '', array(), true);
		$this->changeColumnOptions('idAction', ColumnType::OPTION, SearchType::SELECT, $actions, $actions);
		$this->changeColumnOptions('idWrapper', ColumnType::OPTION, SearchType::SELECT, $targets, $targets);
		$field = Tools::formatForeignField('idWrapper', 'module');
		$this->generator->createColumn($this->table, $this->l('Module'), $field, ColumnType::TEXT, SearchType::TEXT, true, true);
		$this->associationList['idWrapper'] = array();
		$this->associationList['idAction'] = array();
	}
	
	protected function createFieldByDefinition($fieldDefinition, $field)
    {
		if($field == 'idWrapper'){
			$input = $this->generator->createSelect($field, $this->l($field), $this->createOptions('Wrapper'));
		}elseif($field == 'idAction'){
			$input = $this->generator->createSelect($field, $this->l($field), $this->createOptions('Action'));
		}else{
			$input = parent::createFieldByDefinition($fieldDefinition, $field);
		}
		return $input;
	}
}