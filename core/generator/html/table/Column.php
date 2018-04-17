<?php
namespace core\generator\html\table;
use core\generator\html\Element;
abstract class Column extends Element{
	protected $searchable;
	protected $sortable;
	protected $searchFields;
	protected $type;
	protected $options;
	protected $chooser;
	protected $sortLinks = array();
	
	public function __construct($label, $name, $type, $sortable = true, $searchable = true, $options = array()) {
		$this->setLabel($label);
		$this->setName($name);
	}
	
	public function prepare(){
		
	}
	
	public function getTargetToHide() {
		return $this->targetToHide;
	}
	public function setTargetToHide($targetToHide) {
		$this->targetToHide=$targetToHide;
	}
	public function getSearchFields() {
		return $this->searchFields;
	}
	public function setSearchFields($searchFields) {
		$this->searchFields=$searchFields;
	}
	public function getSortLink() {
		return $sortLinks->sortLinks;
	}
}