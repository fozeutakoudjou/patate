<?php
namespace core\generator\html\table;
use core\generator\html\Block;
class Row extends Block{
	protected $templateFile = 'generator/table/row';
	protected $table;
	protected $rowActionsToExclude = array();
	protected $columnsDataOptions = array();
	private $newRowActions;
	private $defaultRowAction;
	private $othersRowActions;
	
	public function __construct($table, $value) {
		$this->setTable($table);
		$this->setValue($value);
	}
	
	public function setTable($table){
		$this->table = $table;
	}
	public function getTable(){
		return $this->table;
	}
	public function generate(){
		$formatter = $this->table->getRowFormatter();
		if($formatter!=null){
			$formatter->format($this);
		}
		return parent::generate();
	}
	
	public function addRowActionToExclude($name){
		$this->rowActionsToExclude[] = $name;
	}
	public function getRowActionsToExclude(){
		return $this->rowActionsToExclude;
	}
	public function getColumnDataOptions($name){
		return $this->hasColumnDataOptions($name) ? $this->columnsDataOptions[$name] : null;
	}
	public function getColumnsDataOptions(){
		return $this->columnsDataOptions;
	}
	public function setColumnDataOptions($name, $options){
		$this->columnsDataOptions[$name] = $options;
	}
	public function hasColumnDataOptions($name){
		return isset($this->columnsDataOptions[$name]);
	}
	
	public function hasActionToExclude(){
		return !empty($this->rowActionsToExclude);
	}
	
	protected function getNewRowAction(){
		if($this->newRowActions === null){
			$this->newRowActions = $this->table->getRowActions();
			foreach($this->rowActionsToExclude as $name){
				unset($this->newRowActions[$name]);
			}
		}
		return $this->newRowActions;
	}
	public function separeActions() {
		if($this->defaultRowAction==null){
			$result = $this->table->separeActionsFromList($this->getNewRowAction());
			$this->defaultRowAction = $result['defaultAction'];
			$this->othersRowActions = $result['othersActions'];
		}
		return array('defaultAction'=>$this->defaultRowAction, 'othersActions'=>$this->othersRowActions);
	}
	public function hasRowActions(){
		return ($this->hasActionToExclude() ? !empty($this->getNewRowAction()) : $this->table->hasRowActions());
	}
	public function hasOthersRowActions(){
		return ($this->hasActionToExclude() ? !empty($this->getOthersRowActions()) : $this->table->hasOthersRowActions());
	}
	
	public function getDefaultRowAction(){
		return ($this->hasActionToExclude() ? $this->separeActions()['defaultAction'] : $this->table->getDefaultRowAction());
	}
	
	public function getOthersRowActions(){
		return ($this->hasActionToExclude() ? $this->separeActions()['othersActions'] : $this->table->getOthersRowActions());
	}
}