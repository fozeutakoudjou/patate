<?php
namespace core\generator\html;
class Link extends Command{
	protected $templateFile = 'generator/link';
	protected $href = false;
	public function __construct($label, $href ='#', $icon = null) {
		$this->setLabel($label);
		$this->setIcon($icon);
		$this->setHref($href);
	}
	
	public function getHref(){
		return $this->href;
	}
	public function setHref($href){
		$this->href=$href;
	}
}