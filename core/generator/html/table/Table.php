<?php
namespace core\generator\html\table;
use core\generator\html\Block;
use core\generator\html\Checkbox;
use core\generator\html\Icon;
use core\generator\html\Button;
use core\Context;
class Table extends Block{
	protected $templateFile = 'generator/table/table';
	protected $rowTemplateFile = 'generator/table/row';
	
	protected $ajaxActive;
	protected $formPosition;
	
	protected $totalResult;
	protected $itemsPerPage;
	protected $itemsPerPageOptions = array();
	protected $currentPage;
	protected $searchData;
	protected $orderWay;
	protected $orderColumn;
	
	protected $defaultAction;
	protected $identifier;
	
	protected $searchButton;
	protected $searchResetButton;
	
	protected $bulkActions = array();
	protected $tableActions = array();
	protected $rowActions = array();
	
	protected $columns = array();
	
	protected $rowSelectorCache;
	
	protected $module;
	
	protected $controller;
	
	protected $value = array();
	
	public function __construct($decorated = true, $label ='', $icon = null, $defaultAction = '', $controller = '', $module = '', $searchText = '', $resetText = '') {
		$this->setLabel($label);
		$this->setIcon($icon);
		$this->setDecorated($decorated);
		$this->setDefaultAction($defaultAction);
		$this->setController($controller);
		$this->setModule($module);
		$this->searchButton = new Button($searchText, true, new Icon('search'), 'searchButton');
		$this->searchResetButton = new Button($resetText, true, new Icon('times'), 'searchResetButton');
	}
	
	public function generate(){
		
		return parent::generate();
	}
	
	public function createRow($value){
		$row = new Row($this, $value);
		$row->setTemplateFile($this->rowTemplateFile, false);
		return $row;
	}
	
	public function hasHeader(){
		return (parent::hasHeader() || !empty($this->tableActions));
	}
	
	public function hasActionBlock(){
		return (!empty($this->tableActions) || !empty($this->headers));
	}
	
	public function createLink($params) {
		$link = Context::getInstance()->getLink();
		$controller = isset($params['controller']) ? $params['controller'] : $this->controller;
		$module = isset($params['module']) ? $params['module'] : $this->module;
		$isAdmin = isset($params['isAdmin']) ? $params['isAdmin'] : true;
		return $link->getPageLink(strtolower($controller), null, $params, null, false, false, $module, $isAdmin);
	}
	
	public function needSearchResetButton() {
		return !empty($this->searchData);
	}
	
	public function needRowSelector(){
		return !empty($this->bulkActions);
	}
	
	public function createRowSelector($isHeader = false, $templateFile = ''){
		$key = (int)$isHeader.$templateFile;
		if(!isset($this->rowSelectorCache[$key])){
			$this->rowSelectorCache[$key] = $checkbox;
			$name = $isHeader?'':$this->identifier;
			$checkbox = new Checkbox($name);
			if(!empty($templateFile)){
				$checkbox->setTemplateFile($templateFile);
			}
		}
		return $this->rowSelectorCache[$key];
	}
	
	public function hasSearchColumn(){
		$searchable = false;
		foreach($this->columns as $column){
			if($column->isSearchable()){
				$searchable = true;
				break;
			}
		}
		return $searchable;
	}
	
	public function addColumn($column) {
		$this->columns[$column->getName()] = $column;
	}
	
	public function getModule() {
		return $this->module;
	}
	public function setModule($module) {
		$this->module=$module;
	}
	
	public function getController() {
		return $this->controller;
	}
	public function setController($controller) {
		$this->controller=$controller;
	}
	
	public function getDefaultAction() {
		return $this->defaultAction;
	}
	public function setDefaultAction($defaultAction) {
		$this->defaultAction=$defaultAction;
	}
	
	public function isAjaxActive() {
		return $this->ajaxActive;
	}
	public function setAjaxActive($ajaxActive) {
		$this->ajaxActive=$ajaxActive;
	}
	
	public function getFormPosition() {
		return $this->formPosition;
	}
	public function setFormPosition($formPosition) {
		$this->formPosition=$formPosition;
	}
	
	public function getTotalResult() {
		return $this->totalResult;
	}
	public function setTotalResult($totalResult) {
		$this->totalResult=$totalResult;
	}
	
	public function addBulkAction($action) {
		$this->bulkActions[$action->getName()] = $action;
	}
	public function getBulkActions() {
		return $this->bulkActions;
	}
	public function setBulkActions($bulkActions) {
		$this->bulkActions=$bulkActions;
	}
	
	public function addTableAction($action) {
		$this->tableActions[$action->getName()] = $action;
	}
	public function getTableActions() {
		return $this->tableActions;
	}
	public function setTableActions($tableActions) {
		$this->tableActions=$tableActions;
	}
	
	public function addRowAction($action) {
		$this->rowActions[$action->getName()] = $action;
	}
	public function getRowActions() {
		return $this->rowActions;
	}
	
	public function getItemsPerPage() {
		return $this->itemsPerPage;
	}
	public function setItemsPerPage($itemsPerPage) {
		$this->itemsPerPage=$itemsPerPage;
	}
	
	
	public function getItemsPerPageOptions() {
		return $this->itemsPerPageOptions;
	}
	
	public function setItemsPerPageOptions($itemsPerPageOptions) {
		$this->itemsPerPageOptions=$itemsPerPageOptions;
	}
	
	public function getCurrentPage() {
		return $this->currentPage;
	}
	public function setCurrentPage($currentPage) {
		$this->currentPage=$currentPage;
	}
	
	public function getSearchData() {
		return $this->searchData;
	}
	public function setSearchData($searchData) {
		$this->itemsPerPage=$searchData;
	}
	
	
	public function getOrderWay() {
		return $this->orderWay;
	}
	
	public function setOrderWay($orderWay) {
		$this->orderWay=$orderWay;
	}
	
	public function getOrderColumn() {
		return $this->orderColumn;
	}
	public function setOrderColumn($orderColumn) {
		$this->orderColumn=$orderColumn;
	}
	
	public function getIdentifier() {
		return $this->identifier;
	}
	public function setIdentifier($identifier) {
		$this->identifier=$identifier;
	}
	
	
	public function getColumns() {
		return $this->columns;
	}
	
	public function getSearchButton() {
		return $this->searchButton;
	}
	
	public function getSearchResetButton() {
		return $this->searchResetButton;
	}
	
	public function needActionColumn() {
		return (!empty($this->rowActions) || $this->hasSearchColumn());
	}
}