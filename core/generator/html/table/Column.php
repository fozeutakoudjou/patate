<?php
namespace core\generator\html\table;
use core\generator\html\Element;
use core\generator\html\Link;
use core\generator\html\Icon;
use core\generator\html\InputText;
use core\generator\html\Select;
use core\constant\dao\OrderWay;
use core\constant\generator\ColumnType;
use core\constant\generator\SearchType;
class Column extends Element{
	protected $cellTemplateFile = 'generator/table/cell';
	protected $searchable;
	protected $sortable;
	protected $searchFields = array();
	protected $dataType;
	protected $searchType;
	protected $sortLinks = array();
	protected $searchOptions = array();
	protected $dataOptions = array();
	protected $table;
	
	protected $dataFormatter;
	
	protected $prepared = false;
	
	public function __construct($table, $label, $name, $dataType= ColumnType::TEXT, $searchType = SearchType::TEXT, $sortable = true, $searchable = true, $searchOptions = array(), $dataOptions = array()) {
		$this->setLabel($label);
		$this->setName($name);
		$this->setDataType($dataType);
		$this->setSearchType($searchType);
		$this->setDataOptions($dataOptions);
		$this->setSearchOptions($searchOptions);
		$this->setSortable($sortable);
		$this->setSearchable($searchable);
		$this->setTable($table);
		$table->addColumn($this);
	}
	
	public function prepare(){
		if(!$this->prepared){
			if($this->sortable){
				$this->sortLinks['asc'] = new Link('ASC', $this->createSortUrl(OrderWay::ASC));
				$this->sortLinks['asc']->setIcon(new Icon('caret-up'));
				$this->sortLinks['asc']->setLabelDisabled(true);
				$this->sortLinks['desc'] = new Link('DESC', $this->createSortUrl(OrderWay::DESC));
				$this->sortLinks['desc']->setIcon(new Icon('caret-down'));
				$this->sortLinks['desc']->setLabelDisabled(true);
			}
			if($this->searchable && !$this->hasCustomSearchField()){
				$this->searchFields = array();
				if(($this->searchType == SearchType::SELECT)){
					$this->searchFields[] = new Select($this->name, '', $this->searchOptions);
				}elseif($this->searchType == SearchType::DATE){
					
				}else{
					$this->searchFields[] = new InputText($this->name, '', 'text');
				}
				foreach($this->searchFields as $field){
					$field->setFieldOnly(true);
				}
			}
			$this->prepared = true;
		}
	}
	
	public function createSortUrl($way){
		return $this->table->getUrlCreator()->createSortUrl($this->getName(), $way);
	}
	
	public function getTable() {
		return $this->table;
	}
	public function setTable($table) {
		$this->table=$table;
	}
	public function getDataOptions() {
		return $this->dataOptions;
	}
	public function setDataOptions($dataOptions) {
		$this->dataOptions=$dataOptions;
	}
	public function getSearchOptions() {
		return $this->searchOptions;
	}
	public function setSearchOptions($searchOptions) {
		$this->searchOptions=$searchOptions;
	}
	public function getDataType() {
		return $this->dataType;
	}
	public function setDataType($dataType) {
		$this->dataType=$dataType;
	}
	public function getSearchType() {
		return $this->searchType;
	}
	public function setSearchType($searchType) {
		$this->searchType=$searchType;
	}
	public function isSortable() {
		return $this->sortable;
	}
	public function setSortable($sortable) {
		$this->sortable=$sortable;
	}
	public function isSearchable() {
		return $this->searchable;
	}
	public function setSearchable($searchable) {
		$this->searchable=$searchable;
	}
	public function getSearchFields() {
		return $this->searchFields;
	}
	public function setSearchFields($searchFields) {
		$this->searchFields=$searchFields;
	}
	public function getSortLinks() {
		return $this->sortLinks;
	}
	
	public function hasCustomSearchField() {
		return !empty($this->searchFields);
	}
	
	public function getCellValue($data) {
		$value = '';
		if(is_object($data)){
			$value = $data->getPropertyValue($this->name);
		}elseif(is_array($data)){
			$value = $data[$this->name];
		} 
		return $value;
	}
	
	public function createCell($data) {
		$cell = new Cell($this->getCellValue($data), $this);
		$cell->setTemplateFile($this->cellTemplateFile, false);
		return $cell;
	}
	
	public function getDataFormatter() {
		return $this->dataFormatter;
	}
	public function setDataFormatter($dataFormatter) {
		$this->dataFormatter=$dataFormatter;
	}
}