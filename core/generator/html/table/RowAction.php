<?php
namespace core\generator\html\table;
namespace core\generator\html\Link;
class RowAction extends Link{
	protected $urlParams;
	protected $table;
	
	public function __construct($label, $href ='#', $icon = null, $title = '', $useOfButtonStyle false, $name = '', $urlParams = array(), $table = null) {
		parent::__construct($label, $href, $icon, $title, $useOfButtonStyle, $name);
		$this->setUrlParams($urlParams);
		$this->setTable($table);
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
	
	public function createLink($values) {
		$params = $this->urlParams;
		if(isset($params['params']) && is_array(isset($params['params']))){
			foreach($params['params'] as $key => $param){
				$value = '';
				if(isset($param['value'])){
					$value = $param['value'];
				}elseif(isset($param['field']) && isset($values[$param['field']])){
					$value = $values[$param['field']];
				}
				$params[$key] = $value;
			}
			unset($params['params']);
		}
		return $this->table->createLink($params);
	}
}