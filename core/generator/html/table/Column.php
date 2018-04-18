<?php
namespace core\generator\html\table;
use core\generator\html\Element;
use core\generator\html\Link;
use core\generator\html\Icon;
use core\generator\html\InputText;
use core\generator\html\Select;
use core\constant\dao\OrderWay;
use core\constant\generator\ColumnType;
class Column extends Element{
	protected $searchable;
	protected $sortable;
	protected $searchFields = array();
	protected $type;
	protected $sortLinks = array();
	protected $options = array();
	protected $table;
	
	protected $prepared = false;
	
	public function __construct($table, $label, $name, $type, $sortable = true, $searchable = true, $options = array()) {
		$this->setLabel($label);
		$this->setName($name);
		$this->setType($type);
		$this->setSortable($sortable);
		$this->setSearchable($searchable);
		$this->setOptions($options);
		$this->setTable($table);
		$table->addColumn($this);
	}
	
	public function prepare(){
		if(!$this->prepared){
			if($this->sortable){
				$params = array('action'=>$this->table->getDefaultAction(), 'param1'=>'order', 'param2'=>$this->getName(), 'param3'=>OrderWay::ASC);
				$this->sortLinks['asc'] = new Link('ASC', $this->table->createLink($params));
				$this->sortLinks['asc']->setIcon(new Icon('caret-up'));
				$this->sortLinks['asc']->setLabelDisabled(true);
				$params['param3'] = OrderWay::DESC;
				$this->sortLinks['desc'] = new Link('DESC', $this->table->createLink($params));
				$this->sortLinks['desc']->setIcon(new Icon('caret-down'));
				$this->sortLinks['desc']->setLabelDisabled(true);
			}
			if($this->searchable && !$this->hasCustomSearchField()){
				$this->searchFields = array();
				if(($this->type == ColumnType::ACTIVE) || ($this->type == ColumnType::CHOOSE) || ($this->type == ColumnType::BOOL)){
					$this->searchFields[] = new Select($this->name, '', $this->options);
				}elseif($this->type == ColumnType::DATE){
					
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
	
	public function getTable() {
		return $this->table;
	}
	public function setTable($table) {
		$this->table=$table;
	}
	public function getOptions() {
		return $this->options;
	}
	public function setOptions($options) {
		$this->options=$options;
	}
	public function getType() {
		return $this->type;
	}
	public function setType($type) {
		$this->type=$type;
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
}