<?php
namespace core\generator\html;
class Label extends Element{
	protected $customContent;
	
	public function getCustomContent() {
		return $this->customContent;
	}
	public function setCustomContent($customContent) {
		$this->customContent=$customContent;
	}
	
	public function hasCustomContent(){
		return ($this->customContent!=null);
	}
}