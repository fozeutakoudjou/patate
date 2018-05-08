<?php
namespace core\controllers\backend;
use core\Tools;
use core\constant\generator\ColumnType;
use core\constant\generator\SearchType;
class RightAdminController extends AdminController
{	
	protected $modelClassName = 'Right';
	
	protected function customizeColumns() {
		$field = Tools::formatForeignField('idWrapper', 'name');
		$this->generator->createColumn($this->table, $field, $field, ColumnType::TEXT, SearchType::TEXT, true, true);
		$this->associationList['idWrapper'] = array();
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