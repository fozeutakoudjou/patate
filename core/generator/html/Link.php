<?php
namespace core\generator\html;
class Link extends Command{
	protected $templateFile = 'generator/link';
	protected $href;
	protected $useOfButtonStyle = false;
	protected $title;
	public function __construct($label, $href ='#', $icon = null, $title = '', $useOfButtonStyle = false, $name = '', $action = '') {
		parent::__construct($label, $icon, $name, $action);
		$this->setHref($href);
		$this->setUseOfButtonStyle($useOfButtonStyle);
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
	
	public function isUseOfButtonStyle(){
		return $this->useOfButtonStyle;
	}
	public function setUseOfButtonStyle($useOfButtonStyle){
		$this->useOfButtonStyle=$useOfButtonStyle;
	}
}