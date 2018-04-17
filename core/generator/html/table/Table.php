<?php
namespace core\generator\html\table;
use core\generator\html\Block;
use core\generator\html\Checkbox;
class Table extends Block{
	protected $templateFile = 'generator/table/table';
	protected $rowTemplateFile = 'generator/table/row';
	
	protected $ajaxActive;
	protected $formPosition;
	
	protected $totalResult;
	protected $itemsPerPage;
	protected $currentPage;
	protected $searchData;
	protected $orderWay;
	protected $orderColumn;
	
	protected $defaultAction;
	protected $identifier;
	
	protected $searchButton;
	protected $cancelButton;
	
	protected $bulkActions = array();
	protected $tableActions = array();
	protected $rowActions = array();
	
	protected $columns = array();
	
	protected $rowSelectorCache;
	
	public function generate(){
		
		return parent::generate();
	}
	
	public function createRow(){
		$row = new Row();
	}
	
	public function needRowSelector(){
		return !empty($this->bulkActions);
	}
	
	public function createRowSelector($isHeader = false, $templateFile = ''){
		$key = (int)$isHeader.$templateFile;
		if(!isset($this->rowSelectorCache[$key])){
			$this->rowSelectorCache[$key] = $checkbox;
			$name = $isHeader?'':$this->identifier;
			$checkbox = new Checkbox($name);
			if(!empty($templateFile)){
				$checkbox->setTemplateFile($templateFile);
			}
		}
		return $this->rowSelectorCache[$key];
	}
	
	public function hasSearchColumn(){
		$searchable = false;
		foreach($this->columns as $column){
			if($column->isSearchable()){
				$searchable = true;
				break;
			}
		}
		return $searchable;
	}
	
	public function getColumns() {
		return $this->columns;
	}
}