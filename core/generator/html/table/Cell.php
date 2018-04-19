<?php
namespace core\generator\html\table;
use core\generator\html\Block;
class Cell extends Block{
	protected $templateFile = 'generator/table/cell';
	protected $column;
	
	public function __construct($value, $column) {
		$this->setValue($value);
		$this->setColumn($column);
	}
	
	public function setColumn($column){
		$this->column = $column;
	}
	public function getColumn(){
		return $this->column;
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