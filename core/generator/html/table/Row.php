<?php
namespace core\generator\html\table;
use core\generator\html\Block;
class Row extends Block{
	protected $templateFile = 'generator/table/row';
	protected $table;
	
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
}