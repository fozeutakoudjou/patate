<?php
namespace core\generator\html;
class Link extends Command{
	protected $templateFile = 'generator/link';
	protected $href;
	protected $buttonStyleUsed = false;
	protected $title;
	public function __construct($label, $href ='#', $icon = null, $title = '', $buttonStyleUsed = false, $name = '', $action = '') {
		parent::__construct($label, $icon, $name, $action);
		$this->setHref($href);
		$this->setButtonStyleUsed($buttonStyleUsed);
		$this->setTitle($title);
	}
	
	public function getHref(){
		return $this->href;
	}
	public function setHref($href){
		$this->href=$href;
	}
	
	public function getTitle(){
		return $this->title;
	}
	public function setTitle($title){
		$this->title=$title;
	}
	
	public function isButtonStyleUsed(){
		return $this->buttonStyleUsed;
	}
	public function setButtonStyleUsed($buttonStyleUsed){
		$this->buttonStyleUsed=$buttonStyleUsed;
	}
}