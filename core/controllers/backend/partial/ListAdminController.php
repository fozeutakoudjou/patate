<?php
namespace core\controllers\backend\partial;
use core\Tools;
use core\StringTools;
use core\constant\dao\OrderWay;
use core\constant\dao\LogicalOperator;
use core\constant\dao\Operator;
use core\constant\generator\ColumnType;
use core\constant\generator\SearchType;
use core\constant\ActionCode;
use core\constant\UrlParamType;
use core\constant\Separator;
use core\constant\DataType;
use core\constant\FormPosition;
use core\generator\html\interfaces\AccesChecker;
use core\generator\html\interfaces\UrlCreator;

abstract class ListAdminController extends BaseAdminController implements AccesChecker, UrlCreator
{
	protected $table;
	protected $filterPrefix = 'filterField_';
	
	protected $associationList = array();
	protected $columnsToExclude = array('dateAdd', 'dateUpdate', 'deleted');
	protected $defaultOrderWay = OrderWay::DESC;
	protected $orderWay;
	
	protected $defaultOrderColumn;
	protected $orderColumn;
	
	protected $defaultItemsPerPage =20;
	protected $itemsPerPage;
	
	protected $itemsPerPageOptions;
	protected $currentPage = 1;
	protected $maxPageDisplayed = 5;
	
	protected $orderWayParams = array(OrderWay::ASC=>'asc', OrderWay::DESC=>'desc');
	protected $limitParams = array(0=>'all');
	protected $searchData;
	protected $baseRestrictionsData = array();
	protected $executeActionUsingAjax = true;
	protected $ajaxActivatorEnabled = true;
	protected $ajaxFormPosition = FormPosition::DIALOG;
	
	public function init()
    {
		parent::init();
		$boolOptions = array(''=>'--', '1'=>$this->l('Yes'), '0'=>$this->l('No'));
		$this->generator->setSearchOptions(SearchType::SELECT, $boolOptions);
		$this->generator->setSearchButtonText($this->l('Search'));
		$this->generator->setResetButtonText($this->l('Reset'));
		$this->generator->setSelectAllText($this->l('Select all'));
		$this->generator->setUnselectAllText($this->l('Unselect all'));
		$this->generator->setEmptyRowText($this->l('No records found'));
		$this->generator->setBulkActionText($this->l('Bulk actions'));
		
		$this->itemsPerPageOptions = array('20'=>20, '50'=>50, '100'=>100, '300'=>300, '1000'=>1000, '0'=>$this->l('All'));
		if($this->defaultModel!=null){
			$this->defaultOrderColumn =$this->defaultModel->getPrimaries()[0];
		}
    }
	
	protected function createTable()
    {
		$this->table = $this->generator->createTable($this->l($this->modelClassName.'s'), '', $this->createUrl(array('action'=>ActionCode::LISTING, 'resetAllFilters'=>1)));
		$this->table->setIdentifier($this->modelIdentifier);
		$this->table->setUrlCreator($this);
		$this->table->setSubmitAction('action');
		$this->table->setFormAction($this->createUrl());
		$this->table->setItemsPerPageOptions($this->itemsPerPageOptions);
		$this->table->setCurrentPage($this->currentPage);
		$this->table->setMaxPageDisplayed($this->maxPageDisplayed);
		$this->table->setItemsPerPage(($this->itemsPerPage === null)?$this->defaultItemsPerPage : $this->itemsPerPage);
		$this->table->setOrderColumn(empty($this->orderColumn) ? $this->defaultOrderColumn : $this->orderColumn);
		$this->table->setOrderWay(($this->orderWay === null)? $this->defaultOrderWay : $this->orderWay);
		$this->table->setFilterPrefix($this->filterPrefix);
		$this->table->setSearchData($this->searchData);
		$this->table->setAjaxEnabled($this->executeActionUsingAjax);
		$this->table->setAjaxActivatorEnabled($this->ajaxActivatorEnabled);
		$this->table->setFormPosition($this->ajaxFormPosition);
		
		$activeOptions = array(
			'1'=>array('label'=>$this->l('Yes')),
			'0'=>array('label'=>$this->l('No')),
		);
		if($this->isActionEnabled(ActionCode::ACTIVATE)){
			$activeOptions['0']['rowAction'] = $this->generator->createRowAction($this->table, '', '', 'remove', $this->l('Disabled'), false, ActionCode::ACTIVATE, ActionCode::ACTIVATE, array(), false, false, '', false, false, 'action-disabled', true);
		}
		if($this->isActionEnabled(ActionCode::DESACTIVATE)){
			$activeOptions['1']['rowAction'] = $this->generator->createRowAction($this->table, '', '', 'check', $this->l('Enabled'), false, ActionCode::DESACTIVATE, ActionCode::DESACTIVATE, array(), false, false, '', false, false, 'action-enabled', true);
		}
		/*$activeOptions = array(
			'1'=>array('rowAction'=>$this->generator->createRowAction($this->table, '', '', 'check', $this->l('Enabled'), false, ActionCode::DESACTIVATE, ActionCode::DESACTIVATE, array(), false, false, '', false, false, 'action-enabled')),
			'0'=>array('rowAction'=>$this->generator->createRowAction($this->table, '', '', 'remove', $this->l('Disabled'), false, ActionCode::ACTIVATE, ActionCode::ACTIVATE, array(), false, false, '', false, false, 'action-disabled')),
		);*/
		$this->generator->setActiveOptions($activeOptions);
		$this->customizeTable();
	}
	
	protected function customizeTable() {}
	
	protected function createTableActions() {
		if($this->isActionEnabled(ActionCode::ADD)){
			$addLink = $this->createUrl(array('action'=>ActionCode::ADD));
			$this->generator->createTableAction($this->table, $this->l('Add new'), $addLink, 'plus', $this->l('Add new'), true, ActionCode::ADD, ActionCode::ADD, true);
		}
		if($this->isActionEnabled(ActionCode::LISTING)){
			$url = $this->createUrl(array('action'=>ActionCode::LISTING));
			$this->generator->createTableAction($this->table, $this->l('Refresh'), $url, 'refresh', $this->l('Refresh'), true, ActionCode::LISTING, ActionCode::LISTING, true);
		}
	}
	
	protected function createBulkActions() {
		if($this->isActionEnabled(ActionCode::ACTIVATE)){
			$activateLink = $this->generator->createBulkAction($this->table, $this->l('Activate selection'), '#', 'power-off', $this->l('Activate selection'), false, ActionCode::ACTIVATE, ActionCode::ACTIVATE, false, '', true);
			$activateLink->getIcon()->addClass('text-success');
			if(!$this->isActionEnabled(ActionCode::DESACTIVATE)){
				$activateLink->addAdditionalData('separator', '1');
			}
		}
		if($this->isActionEnabled(ActionCode::DESACTIVATE)){
			$desactivateLink = $this->generator->createBulkAction($this->table, $this->l('Desactivate selection'), '#', 'power-off', $this->l('Desactivate selection'), false, ActionCode::DESACTIVATE, ActionCode::DESACTIVATE, false, '', true);
			$desactivateLink->getIcon()->addClass('text-danger');
			$desactivateLink->addAdditionalData('separator', '1');
		}
		if($this->isActionEnabled(ActionCode::DELETE)){
			$this->generator->createBulkAction($this->table, $this->l('Delete selection'), '#', 'trash', $this->l('Delete selection'), false, ActionCode::DELETE, ActionCode::DELETE, true, $this->createBulkConfirmText(ActionCode::DELETE), true);
		}
	}
	
	protected function createRowsActions() {
		foreach($this->availableActions as $action => $data){
			if($this->isActionEnabled($action) && isset($data['row'])){
				$label = isset($data['row']) ? $data['label'] : '';
				$icon = isset($data['icon']) ? $data['icon'] : '';
				$title = isset($data['title']) ? $data['title'] : $label;
				$default = isset($data['default']) ? $data['default'] : false;
				$useOfButtonStyle = $default ? true : false;
				$confirm = isset($data['confirm']) ? $data['confirm'] : false;
				$confirmText = $confirm ? $this->createConfirmText($action) : '';
				$this->generator->createRowAction($this->table, $label, '', $icon, $title, $useOfButtonStyle, $action, $action, array(), $default, $confirm, $confirmText, true, true, '', true);
			}
		}
	}
	protected function createConfirmText($action) {
		return sprintf($this->l('Are you sure you want to %s this item?'), $this->l($action)).'<br/>'.$this->l('Detail : %s');
	}
	
	protected function createBulkConfirmText($action) {
		return sprintf($this->l('Are you sure you want to %s these items?'), $this->l($action));
	}
	
	protected function createColumns()
    {
		$primaries = is_array($this->modelDefinition['primary'])?$this->modelDefinition['primary'] : array($this->modelDefinition['primary']);
		foreach($primaries as $field){
			if(!isset($this->modelDefinition['fields'][$field]) && !in_array($field, $this->columnsToExclude)){
				$this->generator->createColumn($this->table, $this->l($field), $field, ColumnType::TEXT, SearchType::TEXT, true, true);
			}
		}
		
		foreach($this->modelDefinition['fields'] as $field => $fieldDefinition){
			if(!in_array($field, $this->columnsToExclude)){
				$this->generator->createColumn($this->table, $this->l($field), $field, self::getColumnType($fieldDefinition['type'], $field), self::getSearchType($fieldDefinition['type'], $field), true, true);
			}
		}
		$this->customizeColumns();
	}
	
	protected function customizeColumns() {}
	protected function getListBaseRestrictionFields() {
		return $this->baseRestrictionsData;
	}
	protected function getRestrictionFromExtraListParams() {
		return array();
	}
	protected function changeColumnOptions($columnName, $dataType = null, $searchType = null, $dataOptions = null, $searchOptions = null) {
		$column = $this->table->getColumn($columnName);
		$column->setDataType(($dataType === null) ? $column->getDataType() : $dataType);
		$column->setSearchType(($searchType === null) ? $column->getSearchType() : $searchType);
		$column->setDataOptions(($dataOptions === null) ? $column->getDataOptions() : $dataOptions);
		$column->setSearchOptions(($searchOptions === null) ? $column->getSearchOptions() : $searchOptions);
		return $column;
	}
	protected function getListSearchData() {
		$fields = array();
		return $fields;
	}
	protected function getListData() {
		$restrictions = is_array($this->searchData) ? $this->searchData : array();
		$baseRestrictions = $this->getListBaseRestrictionFields();
		$restrictions = is_array($baseRestrictions) ? array_merge($restrictions, $baseRestrictions) : $restrictions;
		$extraRestrictions = $this->getRestrictionFromExtraListParams();
		$restrictions = is_array($extraRestrictions) ? array_merge($restrictions, $extraRestrictions) : $restrictions;
		$limit = (int)(($this->itemsPerPage===null) ? $this->defaultItemsPerPage : $this->itemsPerPage);
		$start = ($this->currentPage-1)*$limit;
		$orderWay = (int)(($this->orderWay===null) ? $this->defaultOrderWay : $this->orderWay);
		$orderBy = ($this->orderColumn===null) ? $this->defaultOrderColumn : $this->orderColumn;
		$data = $this->getDAOInstance()->getByFields($restrictions, true, $this->lang, true, false, $this->associationList,
			$start, $limit, $orderBy, $orderWay, LogicalOperator::AND_);
		return $data;
	}
	
	protected function formatListData($data) {
		return $data;
	}
	
	protected static function getColumnType($modelType, $field)
    {
		$type = ColumnType::TEXT;
		if($field=='active'){
			$type = ColumnType::ACTIVE;
		}elseif($modelType==DataType::TYPE_BOOL){
			$type = ColumnType::BOOL;
		}elseif($modelType==DataType::TYPE_DATE){
			$type = ColumnType::DATE;
		}
		return $type;
	}
	
	protected static function getSearchType($modelType, $field)
    {
		$type = SearchType::TEXT;
		if($modelType==DataType::TYPE_BOOL){
			$type = SearchType::SELECT;
		}elseif($modelType==DataType::TYPE_DATE){
			$type = SearchType::DATE;
		}
		return $type;
	}
	
	public function createSortUrl($column, $way){
		return $this->createListUrl(array(UrlParamType::ORDER=>$column, 'way'=>$way));
	}
	public function createLimitUrl($limit){
		return $this->createListUrl(array(UrlParamType::LIMIT=>$limit));
	}
	
	public function createPaginationUrl($page){
		return $this->createListUrl(array(UrlParamType::PAGE=>$page));
	}
	
	protected function createListUrl($data){
		$params = array('action'=>ActionCode::LISTING);
		if(isset($data[UrlParamType::ORDER])){
			$params = $this->addOrderParam($params, $data[UrlParamType::ORDER], $data['way']);
		}elseif(isset($data[UrlParamType::LIMIT])){
			$params = $this->addLimitParam($params, $data[UrlParamType::LIMIT]);
		}elseif(isset($data[UrlParamType::PAGE])){
			$params = $this->addPageParam($params, $data[UrlParamType::PAGE]);
		}
		return $this->createUrl($params);
	}
	
	protected function addOrderParam($params, $column, $way){
		$way = isset($this->orderWayParams[$way])?$this->orderWayParams[$way] : $way;
		$params['param1'] = UrlParamType::ORDER.Separator::URL_VALUE.$way.Separator::URL_VALUE.
			StringTools::toUnderscoreCase(Separator::URL_VALUE.$column,Separator::COLUMN_CAMEL_CASE);
		return $params;
	}
	
	protected function addLimitParam($params, $limit){
		$str = UrlParamType::LIMIT.Separator::URL_VALUE.(isset($this->limitParams[$limit])?$this->limitParams[$limit] : $limit);
		if(!empty($this->orderColumn)){
			$params = $this->addOrderParam($params, $this->orderColumn, $this->orderWay);
			$params['param2'] = $str;
		}else{
			$params['param1'] = $str;
		}
		return $params;
	}
	
	protected function addPageParam($params, $page){
		$str = UrlParamType::PAGE.Separator::URL_VALUE.$page;
		if($this->itemsPerPage!==null){
			$params = $this->addLimitParam($params, $this->itemsPerPage);
			$params['param3'] = $str;
		}elseif(!empty($this->orderColumn)){
			$params = $this->addOrderParam($params, $this->orderColumn, $this->orderWay);
			$params['param2'] = $str;
		}else{
			$params['param1'] = $str;
		}
		return $params;
	}
	
	public function retrieveListUrlParam(){
		$paramDef = array(
			'param1'=>array(UrlParamType::ORDER, UrlParamType::LIMIT, UrlParamType::PAGE),
			'param2'=>array(UrlParamType::LIMIT, UrlParamType::PAGE),
			'param3'=>array(UrlParamType::PAGE),
		);
		foreach($paramDef as $name => $paramTypes){
			$value = Tools::getValue($name);
			if(!empty($value)){
				$data = explode(Separator::URL_VALUE, $value, 2);
				if(in_array(UrlParamType::ORDER, $paramTypes) && ($data[0]==UrlParamType::ORDER)){
					$this->setOrderProperties($data[1]);
				}elseif(in_array(UrlParamType::LIMIT, $paramTypes) && ($data[0]==UrlParamType::LIMIT)){
					$this->setLimitProperties($data[1]);
				}elseif(in_array(UrlParamType::PAGE, $paramTypes) && ($data[0]==UrlParamType::PAGE)){
					$this->setPageProperties($data[1]);
				}
			}
		}
	}
	
	protected function setOrderProperties($params){
		$data = explode(Separator::URL_VALUE, $params, 2);
		$ways = array_flip($this->orderWayParams);
		$this->orderWay = isset($ways[$data[0]])?$ways[$data[0]] : $data[0];
		$this->orderColumn = StringTools::toCamelCase($data[1],false, Separator::COLUMN_CAMEL_CASE);
	}
	
	protected function setLimitProperties($params){
		$data = (int)$params;
		$limits = array_flip($this->limitParams);
		$this->itemsPerPage = isset($limits[$data])?$limits[$data] : $data;
	}
	
	protected function setPageProperties($params){
		$this->currentPage = (int)$params;
	}
	protected function getCookieFilterPrefix()
    {
        return strtolower($this->controllerClass).$this->filterPrefix;
    }
	protected function updateListSearchData(){
		$cookiePrefix = $this->getCookieFilterPrefix();
		$cookie = $this->context->getCookie();
		foreach($_POST as $key => $value){
			if(strpos($key, $this->filterPrefix)===0){
				$cookieField = $cookiePrefix.StringTools::strReplaceOnce($this->filterPrefix, '', $key);
				if(is_array($value)){
					$emptyValue = true;
					foreach($value as $val){
						$emptyValue = ($emptyValue && ($val===''));
					}
				}else{
					$emptyValue = ($value==='');
				}
				if($emptyValue){
					unset($cookie->$cookieField);
				}else{
					$cookie->$cookieField = is_array($value) ? serialize($value) : $value;
				}
			}
		}
		$cookie->write();
	}
	protected function resetAllFilters(){
		$cookie = $this->context->getCookie();
		$cookie->unsetFamily($this->getCookieFilterPrefix());
		$cookie->write();
	}
	protected function retrieveListSearchData(){
		$cookiePrefix = $this->getCookieFilterPrefix();
		$cookie = $this->context->getCookie();
		$this->searchData = $cookie->getFamily($cookiePrefix);
	}
	
	protected function formatListSearchData(){
		if(!empty($this->searchData)){
			$list = $this->searchData;
			$this->searchData = array();
			$cookiePrefix = $this->getCookieFilterPrefix();
			foreach($list as $key => $value){
				$field = StringTools::strReplaceOnce($cookiePrefix, '', $key);
				$this->searchData[$field] =  array('operator'=>$this->getOperator($field), 'value'=>$value);
			}
		}
	}
	
	protected function getOperator($field){
		$operator = Operator::EQUALS;
		if(isset($this->modelDefinition['fields'][$field])){
			$modelType = $this->modelDefinition['fields'][$field]['type'];
		}elseif(!in_array($field, $this->defaultModel->getPrimaries())){
			$extract = Tools::extractForeignField($field);
			if(isset($extract['externalField'])){
				$foreignDefinition = $this->getDAOInstance()->createForeignDAO($extract['field'])->createModel()->getDefinition();
				$modelType = $foreignDefinition['fields'][$extract['externalField']]['type'];
			}
		}
		if(isset($modelType)){
				if(($modelType==DataType::TYPE_STRING)||($modelType==DataType::TYPE_HTML)){
				$operator = Operator::CONTAINS;
			}
		}
		return $operator;
	}
	
	protected function setColumnAsChangeFieldValue($name) {
		$options = array(
			'1'=>array('rowAction'=>$this->generator->createRowAction($this->table, '', '', 'check', $this->l('Yes'), false, $this->getValueChangeCode($name, 1), ActionCode::CHANGE_FIELD_VALUE, array('field'=>$name, 'value'=>'0'), false, false, '', false, false, 'action-enabled')),
			'0'=>array('rowAction'=>$this->generator->createRowAction($this->table, '', '', 'remove', $this->l('No'), false, $this->getValueChangeCode($name, 0), ActionCode::CHANGE_FIELD_VALUE, array('field'=>$name, 'value'=>'1'), false, false, '', false, false, 'action-disabled')),
		);
		$this->changeColumnOptions($name, ColumnType::BOOL, null, $options, null);
	}
	
	protected function addBulkChangeFieldValue($name, $options) {
		if($this->isActionEnabled(ActionCode::UPDATE)){
			foreach($options as $value => $option){
				$icon = isset($option['icon']) ? $option['icon'] : '';
				$title = isset($option['title']) ? $option['title'] : $option['label'];
				$link = $this->generator->createBulkAction($this->table, $option['label'], '#', $icon, $title, false, $this->getValueChangeCode($name, $value), ActionCode::CHANGE_FIELD_VALUE);
				if(isset($option['iconClass'])){
					$link->getIcon()->addClass($option['iconClass']);
				}
				$link->addAttribute('data-additionals', 'field='.$name.'&value='.$value);
				$link->setAccessChecked(true);
			}
			if(isset($link)){
				$link->addAdditionalData('separator', '1');
			}
		}
	}
	
	protected function getBoolBulkOptions($labelYes, $labelNo, $icon = '') {
		return array(
			'1'=>array('label'=>$labelYes, 'icon'=>$icon, 'iconClass'=>'text-success'),
			'0'=>array('label'=>$labelNo, 'icon'=>$icon, 'iconClass'=>'text-danger')
		);
	}
	
	protected function getValueChangeCode($field, $value) {
		return ActionCode::CHANGE_FIELD_VALUE.'_'.$field.'_'.$value;
	}
}