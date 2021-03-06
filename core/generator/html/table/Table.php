<?php
namespace core\generator\html\table;
use core\generator\html\Block;
use core\generator\html\Form;
use core\generator\html\Checkbox;
use core\generator\html\Icon;
use core\generator\html\Button;
use core\generator\html\Link;
use core\generator\html\InputHidden;
use core\generator\html\Radio;
use core\constant\FormPosition;
class Table extends Form{
	protected $templateFile = 'generator/table/table';
	protected $rowTemplateFile = 'generator/table/row';
	
	protected $ajaxEnabled = false;
	protected $ajaxActivatorEnabled = false;
	protected $ajaxActivatorOptions;
	protected $ajaxActivatorLabel;
	protected $formPosition = FormPosition::DIALOG;
	protected $formEnabled = true;
	protected $tableFooterEnabled = true;
	protected $rowSelectetorEnabledWhatever = false;
	
	protected $totalResult;
	protected $itemsPerPage;
	protected $itemsPerPageOptions = array();
	protected $currentPage;
	protected $searchData;
	protected $orderWay;
	protected $orderColumn;
	
	protected $defaultAction;
	protected $identifier = 'id';
	
	protected $searchButton;
	protected $searchResetButton;
	
	protected $bulkActions = array();
	protected $tableActions = array();
	protected $rowActions = array();
	
	protected $columns = array();
	
	protected $rowSelectorCache;
	
	protected $urlCreator;
	
	protected $value = array();
	
	protected $rowFormatter;
	protected $bulkActionContent;
	
	protected $defaultRowAction;
	protected $othersRowActions;
	protected $bulkActionText;
	protected $emptyRowText;
	protected $selectAll;
	protected $unselectAll;
	protected $maxPageDisplayed = 5;
	protected $formWidth = 'col-lg-5';
	protected $listWidth = 'col-lg-7';
	
	protected $filterPrefix;
	
	private $uniqueSuffix;
	
	public function __construct($decorated = true, $label ='', $icon = null, $searchText = '', $resetText = '', $emptyRowText = '', $selectAllText = '', $unselectAllText = '', $bulkActionText = '', $resetHref = '') {
		$this->setLabel($label);
		$this->setIcon($icon);
		$this->setDecorated($decorated);
		$this->searchButton = new Button($searchText, true, new Icon('search'), 'searchButton');
		$this->searchResetButton = new Link($resetText, $resetHref, new Icon('times'), $resetText, true, 'searchResetButton');
		$this->selectAll = new Link($selectAllText, '#', new Icon('check-square'), $selectAllText);
		$this->unselectAll = new Link($unselectAllText, '#', new Icon('square-o'), $unselectAllText);
		$this->setEmptyRowText($emptyRowText);
		$this->bulkActionText = $bulkActionText;
		$this->searchButton->addClass('table_search_btn');
		if(!self::$mediaSetter->hasMediaGroup('tableManager')){
			self::$mediaSetter->addJS(self::$mediaUriCreator->getJSURI(true, '', false).'TableManager.js');
			self::$mediaSetter->addMediaGroup('tableManager');
		}
		$this->uniqueSuffix = strtotime(date("Y-m-d H:i:s"));
	}
	public function setEmptyRowText($emptyRowText){
		$this->emptyRowText = $emptyRowText;
	}
	public function generate(){
		$this->addWrapperClass('listWrapper');
		$topWrapperClasses = 'listWrapperTopParent';
		
		$this->addAdditionalData('formClasses', 'formList');
		$openMode = ($this->formPosition==FormPosition::DIALOG) ? 'dialog' : '';
		if(($this->formPosition==FormPosition::LEFT)||($this->formPosition==FormPosition::RIGHT)){
			$openMode= 'side';
			$this->addWrapperAttribute('data-list_width', $this->listWidth);
		}
		$this->addWrapperAttribute('data-form_open_mode', $openMode);
		$this->addAdditionalData('topWrapperClasses', 'listWrapperTopParent');
		$this->searchResetButton->addClass('listCommand');
		if($this->ajaxEnabled){
			$this->addWrapperClass('ajaxList');
			$topWrapperClasses.=' ajaxParent';
		}
		$this->addAdditionalData('topWrapperClasses', $topWrapperClasses);
		/*self::$template->assign('FormPosition', new FormPosition());*/
		return parent::generate();
	}
	public function createTopParentId(){
		return 'listWrapperTopParent'.'_'. $this->uniqueSuffix;
	}
	public function canDrawEditionFormAtTop(){
		return (($this->formPosition==FormPosition::TOP)||($this->formPosition==FormPosition::LEFT));
	}
	public function canDrawEditionFormAtBottom(){
		return (($this->formPosition==FormPosition::BOTTOM)||($this->formPosition==FormPosition::RIGHT));
	}
	public function createFormBlock(){
		$block = new Block(false);
		$block->setVisible(false);
		$block->addClass('listEditionFormBlock');
		if(($this->formPosition==FormPosition::LEFT)||($this->formPosition==FormPosition::RIGHT)){
			$block->setWidth($this->formWidth);
		}
		return $block;
	}
	public function generateContent() {
		$this->addChild(new InputHidden($this->submitAction));
		$this->addChild(new InputHidden('submitFilterData', 1));
		$this->addChild(new InputHidden('submitBulkAction', 1));
		$this->addChild(new InputHidden('bulkAdditionalData', null, 'bulkAdditionalData'));
		return parent::generateContent();
	}
	public function getColumn($name) {
		return isset($this->columns[$name]) ? $this->columns[$name] : null;
	}
	public function createRow($value){
		$row = new Row($this, $value);
		$row->setTemplateFile($this->rowTemplateFile, false);
		$row->addClass('trData');
		return $row;
	}
	public function createItemPerPageLink($itemsPerPage, $label) {
		$link = new Link($label, $this->urlCreator->createLimitUrl($itemsPerPage));
		$link->addClass('listCommand');
		return $link;
	}
	public function isActiveItemPerPage($value){
		return $this->itemsPerPage == $value;
	}
	
	public function canDisplayItemsPerPageOptions(){
		$result = !$this->isEmpty();
		if($result && !empty($this->itemsPerPageOptions)){
			$optionCount = 0;
			$upperOptionCount = 0;
			foreach($this->itemsPerPageOptions as $value => $option){
				if($value > 0){
					$optionCount++;
					if($value >= $this->totalResult){
						$upperOptionCount++;
					}
				}
			}
			$result = ($upperOptionCount < $optionCount);
		}
		return $result;
	}
	public function drawPagination($templateFile = '', $absolutePath = true){
		$content = '';
		if(!$this->isEmpty()){
			$pagination = new Pagination($this);
			if(!empty($templateFile)){
				$pagination->setTemplateFile($this->rowTemplateFile, $absolutePath);
			}
			$pagination->setLinkClasses('listCommand');
			$content = $pagination->generate();
		}
		
		return $content;
	}

	public function getItemsPerPageLabel(){
		return isset($this->itemsPerPageOptions[$this->itemsPerPage]) ? $this->itemsPerPageOptions[$this->itemsPerPage] : $this->itemsPerPage;
	}
	public function hasHeader(){
		return (parent::hasHeader() || !empty($this->tableActions));
	}
	
	public function hasActionBlock(){
		return (!empty($this->tableActions) || !empty($this->headers));
	}
	
	public function createUrl($params) {
		return $this->urlCreator->createUrl($params);
	}
	
	public function needSearchResetButton() {
		return !empty($this->searchData);
	}
	
	public function needRowSelector(){
		return ($this->rowSelectetorEnabledWhatever || $this->hasBulkActions());
	}
	
	public function hasBulkActions(){
		return !empty($this->bulkActions);
	}
	
	public function isEmpty(){
		return empty($this->value);
	}
	
	public function drawBulkActions($templateFile = ''){
		$key = $templateFile;
		if(!isset($this->bulkActionContent[$key])){
			if($this->isEmpty() || !$this->hasBulkActions()){
				$this->bulkActionContent[$key] = '';
			}else{
				$templateFile = empty($templateFile) ? 'generator/bulk_action' : $templateFile;
				$block = new Block(true, $this->bulkActionText);
				$block->setTemplateFile($templateFile, false);
				$checkAttribute = '.check_all_item' . $this->uniqueSuffix;
				if($this->selectAll!=null){
					$this->selectAll->addClass('all_checker');
					$this->selectAll->addAttribute('target_item', $checkAttribute);
					$block->addChild($this->selectAll);
				}
				if($this->unselectAll!=null){
					$this->unselectAll->addClass('all_unchecker');
					$this->unselectAll->addAttribute('target_item', $checkAttribute);
					$this->unselectAll->addAdditionalData('separator', '1');
					$block->addChild($this->unselectAll);
				}
				foreach($this->bulkActions as $action){
					$action->addClass("bulk_action");
					$action->addAttribute('data-action', $action->getAction());
					$block->addChild($action);
				}
				$this->bulkActionContent[$key] = $block->generate();
			}
			
		}
		return $this->bulkActionContent[$key];
	}
	
	public function getColumnSearchValue($column) {
		$key = $column->getName();
		$value = isset($this->searchData[$key])?$this->searchData[$key] : '';
		return (is_array($value) && isset($value['value']))? $value['value'] : $value;
	}
	
	public function createRowSelector($isHeader = false, $value = null, $templateFile = ''){
		$key = (int)$isHeader.$templateFile;
		if(!isset($this->rowSelectorCache[$key])){
			$name = $isHeader?'':$this->identifier.'[]';
			$checkbox = new Checkbox($name);
			$itemClass = 'check_all_item' . $this->uniqueSuffix;
			$checkbox->addClass($itemClass);
			if($isHeader){
				$checkbox->addClass('check_all_switcher');
				$checkbox->addClass('check_all_switcher');
				$checkbox->addAttribute('target_item', '.'.$itemClass);
			}
			if(!empty($templateFile)){
				$checkbox->setTemplateFile($templateFile);
			}
			$this->rowSelectorCache[$key] = $checkbox;
		}
		if(!$isHeader){
			$id = '';
			if(is_array($value) && isset($value[$this->identifier])){
				
			}elseif(is_object($value)){
				$id = $value->getSinglePrimaryValue();
			}
			$this->rowSelectorCache[$key]->setValue($id);
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
	
	public function createAjaxActivator() {
		$item = new Radio('ajaxActivator', $this->ajaxActivatorLabel, $this->ajaxActivatorOptions, true);
		$item->setWrapperWidth('col-lg-5');
		$item->setValue($this->ajaxEnabled);
		$item->addClass('ajaxActivator');
		return $item;
	}
	
	public function isAjaxEnabled() {
		return $this->ajaxEnabled;
	}
	public function setAjaxEnabled($ajaxEnabled) {
		$this->ajaxEnabled=$ajaxEnabled;
	}
	public function isFormEnabled() {
		return $this->formEnabled;
	}
	public function setFormEnabled($formEnabled) {
		$this->formEnabled=$formEnabled;
	}
	public function isTableFooterEnabled() {
		return $this->tableFooterEnabled;
	}
	public function setTableFooterEnabled($tableFooterEnabled) {
		$this->tableFooterEnabled=$tableFooterEnabled;
	}
	
	public function isAjaxActivatorEnabled() {
		return $this->ajaxActivatorEnabled;
	}
	public function setAjaxActivatorEnabled($ajaxActivatorEnabled) {
		$this->ajaxActivatorEnabled=$ajaxActivatorEnabled;
	}
	public function setAjaxActivatorOptions($ajaxActivatorOptions) {
		$this->ajaxActivatorOptions=$ajaxActivatorOptions;
	}
	public function setAjaxActivatorLabel($ajaxActivatorLabel) {
		$this->ajaxActivatorLabel=$ajaxActivatorLabel;
	}
	
	public function getEmptyRowText() {
		return $this->emptyRowText;
	}
	public function getFormPosition() {
		return $this->formPosition;
	}
	public function setFormPosition($formPosition) {
		$this->formPosition=$formPosition;
	}
	public function getFilterPrefix() {
		return $this->filterPrefix;
	}
	public function setFilterPrefix($filterPrefix) {
		$this->filterPrefix=$filterPrefix;
	}
	public function getMaxPageDisplayed() {
		return $this->maxPageDisplayed;
	}
	public function setMaxPageDisplayed($maxPageDisplayed) {
		$this->maxPageDisplayed=$maxPageDisplayed;
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
		$action->addClass('listCommand');
		$action->addClass('tableAction');
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
		$this->searchData=$searchData;
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
	public function setUrlCreator($urlCreator) {
		$this->urlCreator=$urlCreator;
	}
	
	public function getUrlCreator() {
		return $this->urlCreator;
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
	
	public function getRowFormatter() {
		return $this->rowFormatter;
	}
	public function setRowFormatter($rowFormatter) {
		$this->rowFormatter=$rowFormatter;
	}
	public function separeActionsFromList($list) {
		$result = array('defaultAction'=>null, 'othersActions'=>array());
		$first = true;
		$firstKey = '';
		foreach($list as $key => $action){
			if($action->isDefault() && ($result['defaultAction']==null)){
				$result['defaultAction'] = $action;
			}else{
				$result['othersActions'][$key] = $action;
			}
			if($first){
				$first = false;
				$firstKey = $key;
			}
		}
		if(($result['defaultAction']==null) && isset($result['othersActions'][$firstKey])){
			$result['defaultAction'] = $result['othersActions'][$firstKey];
			unset($result['othersActions'][$firstKey]);
		}
		$result['othersActions'] = ($result['othersActions']==null) ? array() : $result['othersActions'];
		return $result;
	}
	public function separeRowActions() {
		if($this->defaultRowAction==null){
			$result = $this->separeActionsFromList($this->rowActions);
			$this->defaultRowAction = $result['defaultAction'];
			$this->othersRowActions = $result['othersActions'];
		}
	}
	/*public function separeRowActions() {
		if($this->defaultRowAction==null){
			$first = true;
			$firstKey = '';
			foreach($this->rowActions as $key => $action){
				if($action->isDefault() && ($this->defaultRowAction==null)){
					$this->defaultRowAction = $action;
				}else{
					$this->othersRowActions[$key] = $action;
				}
				if($first){
					$first = false;
					$firstKey = $key;
				}
			}
			if(($this->defaultRowAction==null) && isset($this->othersRowActions[$firstKey])){
				$this->defaultRowAction = $this->othersRowActions[$firstKey];
				unset($this->othersRowActions[$firstKey]);
			}
			$this->othersRowActions = ($this->othersRowActions==null) ? array() : $this->othersRowActions;
		}
	}*/
	public function getDefaultRowAction() {
		$this->separeRowActions();
		return $this->defaultRowAction;
	}
	
	public function getOthersRowActions() {
		$this->separeRowActions();
		return $this->othersRowActions;
	}
	
	public function hasOthersRowActions() {
		$this->othersRowActions = $this->getOthersRowActions();
		return !empty($this->othersRowActions);
	}
	
	public function hasRowActions() {
		return !empty($this->rowActions);
	}
}