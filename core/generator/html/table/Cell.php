<?php
namespace core\generator\html\table;
use core\generator\html\Block;
class Cell extends Block{
	protected $templateFile = 'generator/table/cell';
	protected $column;
	protected $rowData;
	protected $row;
	protected $dataOptions;
	
	public function __construct($value, $rowData, $column, $dataOptions/*, $row*/) {
		$this->setValue($value);
		$this->setColumn($column);
		$this->setRowData($rowData);
		$this->setDataOptions($dataOptions);
		//$this->setRow($row);
	}
	public function setDataOptions($dataOptions){
		$this->dataOptions = $dataOptions;
	}
	public function getFinalOptions(){
		return ($this->dataOptions===null) ? $this->column->getDataOptions() : $this->dataOptions;
	}
	public function setColumn($column){
		$this->column = $column;
	}
	public function getColumn(){
		return $this->column;
	}
	public function setRowData($rowData){
		$this->rowData = $rowData;
	}
	public function getRowData(){
		return $this->rowData;
	}
	public function setRow($row){
		$this->row = $row;
	}
	public function getRow(){
		return $this->row;
	}
	
	public function generate(){
		
		$formatter = $this->column->getDataFormatter();
		if($formatter!=null){
			$data = $formatter->format($this);
		}else{
			$this->html = $this->value;
		}
		return parent::generate();
	}
}