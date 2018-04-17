<?php
namespace core\generator\html\table;
use core\generator\html\Element;
abstract class Row extends Element{
	protected $table;
	
	protected $emptyContent;
	
	public function __construct($table) {
		$this->setTable($table);
	}
	
	public function generate(){
		if($this->showHide){
			$this->addAttribute('target_to_hide', $this->targetToHide);
			$this->addAttribute('target_to_show', $this->targetToShow);
			$this->addClass('show_hide');
		}
		return parent::generate();
	}
	
	public function setTable($table){
		$this->table = $table;
	}
}