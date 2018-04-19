<?php
namespace core\generator\html\table;
namespace core\generator\html\Link;
class RowAction extends Link{
	protected $urlParams;
	protected $table;
	protected $default;
	protected $formatter;
	
	public function __construct($label, $href ='#', $icon = null, $title = '', $useOfButtonStyle false, $name = '', $action = '', $urlParams = array(), $table = null, $default = false) {
		parent::__construct($label, $href, $icon, $title, $useOfButtonStyle, $name, $action);
		$this->setUrlParams($urlParams);
		$this->setTable($table);
		$this->setTable($default);
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
	
	public function createLink($values) {
		
	}
	
	public function generate(){
		$link = new Link($this->label, $this->href, $this->icon, $this->title, $this->useOfButtonStyle, $this->name, $this->action);
		$link->setClasses($this->classes);
		$formatter = $this->column->getDataFormatter();
		if($formatter!=null){
			$data = $formatter->format($this);
		}else{
			$this->html = $this->value;
		}
		return parent::generate();
	}
}