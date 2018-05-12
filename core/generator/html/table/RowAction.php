<?php
namespace core\generator\html\table;
namespace core\generator\html\table;
use core\generator\html\Link;
class RowAction extends Link{
	protected $urlParams;
	protected $table;
	protected $default;
	protected $formatter;
	
	public function __construct($table, $label, $href ='#', $icon = null, $title = '', $buttonStyleUsed = false, $name = '', $action = '', $urlParams = array(), $default = false) {
		parent::__construct($label, $href, $icon, $title, $buttonStyleUsed, $name, $action);
		$this->setUrlParams($urlParams);
		$this->setTable($table);
		$this->setDefault($default);
	}
	
	public function getUrlParams(){
		return $this->urlParams;
	}
	public function setUrlParams($urlParams){
		$this->urlParams=$urlParams;
	}
	
	public function getTable() {
		return $this->table;
	}
	public function setTable($table) {
		$this->table=$table;
	}
	
	public function isDefault() {
		return $this->default;
	}
	public function setDefault($default) {
		$this->default=$default;
	}
	
	public function createNewLink($values) {
		/*$link = new Link($this->label, $this->href, $this->icon, $this->title, $this->buttonStyleUsed, $this->name, $this->action);
		$link->setClasses($this->classes);
		$link->setConfirm($this->confirm);
		$link->setAttributes($this->attributes);*/
		$link = clone $this;
		if($this->confirm){
			$description = is_array($values) ? $values[$this->table->getIdentifier()] : $values->__toString();
			$link->setConfirmText(sprintf($this->confirmText, $description));
			$link->setAutoConfirm($this->autoConfirm);
		}
		if($this->formatter!=null){
			$data = $this->formatter->format($this);
		}
		if(empty($link->href)){
			$params = $this->urlParams;
			if(!isset($params['action'])){
				$params['action'] = $this->action;
			}
			$link->href = $this->table->getUrlCreator()->createActionUrl($params, $values);
		}
		return $link;
	}
}