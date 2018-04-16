<?php
namespace core\generator\html\list;
use core\generator\html\Element;
abstract class Table extends Element{
	protected $ajaxActive;
	protected $formPosition;
	protected $searchFields;
	protected $type;
	
	public function __construct($label, $icon = null, $name = '') {
		$this->setLabel($label);
		$this->setIcon($icon);
		$this->setName($name);
	}
	
	public function generate(){
		if($this->showHide){
			$this->addAttribute('target_to_hide', $this->targetToHide);
			$this->addAttribute('target_to_show', $this->targetToShow);
			$this->addClass('show_hide');
		}
		return parent::generate();
	}
}