<?php
namespace core\generator\html;
abstract class Command extends Element{
	protected $customContent;
	protected $showHide;
	protected $targetToShow;
	protected $targetToHide;
	protected $confirmText;
	protected $confirm;
	
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
	public function getConfirmText() {
		return $this->confirmText;
	}
	public function setConfirmText($confirmText) {
		$this->confirmText=$confirmText;
	}
	public function isShowHide() {
		return $this->showHide;
	}
	public function setShowHide($showHide) {
		$this->showHide=$showHide;
	}
	public function isConfirm() {
		return $this->confirm;
	}
	public function setConfirm($confirm) {
		$this->confirm=$confirm;
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
		if($this->confirm){
			$this->addAttribute('confirm_text', $this->confirmText);
			$this->addClass('confirm_command');
		}
		return parent::generate();
	}
}