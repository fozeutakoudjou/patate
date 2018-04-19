<?php
namespace core\generator\html;
abstract class Command extends Element{
	protected $customContent;
	protected $showHide;
	protected $targetToShow;
	protected $targetToHide;
	
	public function __construct($label, $icon = null, $name = '', $action = '') {
		$this->setLabel($label);
		$this->setIcon($icon);
		$this->setName($name);
		$this->setAction($action);
	}
	
	public function getCustomContent() {
		return $this->customContent;
	}
	public function setCustomContent($customContent) {
		$this->customContent=$customContent;
	}
	public function getTargetToShow() {
		return $this->targetToShow;
	}
	public function setTargetToShow($targetToShow) {
		$this->targetToShow=$targetToShow;
	}
	public function getTargetToHide() {
		return $this->targetToHide;
	}
	public function setTargetToHide($targetToHide) {
		$this->targetToHide=$targetToHide;
	}
	public function isShowHide() {
		return $this->showHide;
	}
	public function setShowHide($showHide) {
		$this->showHide=$showHide;
	}
	
	public function hasCustomContent(){
		return ($this->customContent!=null);
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