<?php
namespace core\generator\html;
class TreeItem extends Element{
	protected $first = false;
	protected $last = false;
	protected $subObjects;
	
	public function __construct($value, $subObjects = array()) {
		$this->setValue($value);
		$this->setSubObjects($subObjects);
	}
	
	public function isFirst() {
		return $this->first;
	}
	
	public function setFirst($first) {
		$this->first = $first;
	}
	
	public function isLast() {
		return $this->last;
	}
	
	public function setLast($last) {
		$this->last = $last;
	}
	public function setSubObjects($subObjects) {
		$this->subObjects = $subObjects;
	}
	
	public function hasChildren() {
		return !empty($this->subObjects);
	}
}