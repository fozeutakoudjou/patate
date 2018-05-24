<?php
namespace core\generator\html;
use core\generator\html\table\Table;
use core\generator\html\table\TableCheckboxMultiple;
use core\generator\html\table\Column;
use core\generator\html\table\RowAction;
use core\generator\html\table\Row;
use core\constant\generator\ColumnType;
use core\constant\generator\SearchType;
use core\Context;
class HtmlGenerator{
	protected $defaultSubmitText;
	protected $defaultCancelText;
	protected $defaultFormErrorText;
	protected $defaultCancelIcon = 'close';
	protected $defaultSubmitIcon='save';
	protected $languages;
	protected $activeLang;
	protected $searchOptions = array();
	protected $radioOptions = array();
	protected $activeOptions = array();
	
	protected $searchButtonText;
	protected $resetButtonText;
	
	protected $selectAllText;
	protected $unselectAllText;
	protected $emptyRowText;
	protected $bulkActionText;
	protected $defaultAjaxActivatorLabel;
	public function __construct($template, $defaultSubmitText = '', $defaultCancelText = '', $languages = array(), $activeLang = '') {
		$this->setDefaultSubmitText($defaultSubmitText);
		$this->setDefaultCancelText($defaultCancelText);
		$this->setLanguages($languages);
		$this->setActiveLang($activeLang);
		Content::setLanguages($this->languages);
		Content::setActiveLang($this->activeLang);
		Content::setTemplate($template);
		$context = Context::getInstance();
		Content::setMediaSetter($context->getMediaSetter());
		Content::setMediaUriCreator($context->getLink());
	}
	public function setAccessChecker($accessChecker){
		Content::setAccessChecker($accessChecker);
	}
	public function setActiveOptions($activeOptions){
		$this->activeOptions=$activeOptions;
	}
	public function setDefaultAjaxActivatorLabel($defaultAjaxActivatorLabel){
		$this->defaultAjaxActivatorLabel=$defaultAjaxActivatorLabel;
	}
	public function setSelectAllText($selectAllText){
		$this->selectAllText=$selectAllText;
	}
	public function setUnselectAllText($unselectAllText){
		$this->unselectAllText=$unselectAllText;
	}
	public function setEmptyRowText($emptyRowText){
		$this->emptyRowText=$emptyRowText;
	}
	public function setBulkActionText($bulkActionText){
		$this->bulkActionText=$bulkActionText;
	}
	public function setSearchButtonText($searchButtonText){
		$this->searchButtonText=$searchButtonText;
	}
	public function setDefaultFormErrorText($defaultFormErrorText){
		$this->defaultFormErrorText=$defaultFormErrorText;
	}
	public function setRadioOptions($radioOptions){
		$this->radioOptions=$radioOptions;
	}
	public function setResetButtonText($resetButtonText){
		$this->resetButtonText=$resetButtonText;
	}
	public function setSearchOptions($type, $searchOptions){
		$this->searchOptions[$type]=$searchOptions;
	}
	public function setDefaultSubmitText($defaultSubmitText){
		$this->defaultSubmitText=$defaultSubmitText;
	}
	public function setDefaultCancelText($defaultCancelText){
		$this->defaultCancelText=$defaultCancelText;
	}
	
	public function setDefaultSubmitIcon($defaultSubmitIcon){
		$this->defaultSubmitIcon=$defaultSubmitIcon;
	}
	
	public function setDefaultCancelIcon($defaultCancelIcon){
		$this->defaultCancelIcon=$defaultCancelIcon;
	}
	
	public function setLanguages($languages){
		$this->languages=$languages;
	}
	
	public function setActiveLang($activeLang){
		$this->activeLang=$activeLang;
	}
	
	
	public function createBlock($decorated = true, $label = '', $icon = '', $class = ''){
		$icon = empty($icon) ? null : $this->createIcon($icon);
		$block =  new Block($decorated, $label, $icon);
		if(!empty($class)){
			$block->addClass($class);
		}
		return $block;
	}
	public function createForm($useSubmit = true, $useCancel = true, $cancelLink = '#', $decorated = true, $label = '', $icon = '', $formAction = '', $submitAction = '', $errorText = '',  $subLabel = '', $method = 'post'){
		$icon = empty($icon) ? null : $this->createIcon($icon);
		$errorText = empty($errorText) ? $this->defaultFormErrorText : $errorText;
		$form = new Form($decorated, $label, $icon, $formAction, $submitAction, $errorText,  $subLabel, $method);
		if($useSubmit){
			$form->setSubmit($this->createButton($this->defaultSubmitText, true, $this->defaultSubmitIcon));
		}
		if($useCancel){
			$cancel = $this->createLink($this->defaultCancelText, $cancelLink, $this->defaultCancelIcon, $this->defaultCancelText, true, '', '');
			$cancel->addClass('btnCancel');
			$form->setCancel($cancel);
		}
		$form->addClass('formEdit');
		return $form;
	}
	public function createIcon($value, $addOnIcon = true){
		return new Icon($value, false, $addOnIcon);
	}
	
	public function createTextIcon($value, $addOnIcon = true){
		return new Icon($value, true, $addOnIcon);
	}
	
	public function createContent($content){
		return new Content($content);
	}
	
	public function createButton($label, $isSubmit = false, $icon = '', $name = '', $action = ''){
		$icon = empty($icon) ? null : $this->createIcon($icon);
		return new Button($label, $isSubmit, $icon, $name, $action);
	}
	
	public function createLink($label, $href = '#', $icon = '', $title = '', $useOfButtonStyle = false, $name = '', $action = ''){
		$icon = empty($icon) ? null : $this->createIcon($icon);
		return new Link($label, $href, $icon, $title, $useOfButtonStyle, $name, $action);
	}
	
	public function setAsShowHide($item, $targetToShow, $targetToHide){
		$item->setShowHide(true);
		$item->setTargetToShow($targetToShow);
		$item->setTargetToHide($targetToHide);
		return $item;
	}
	
	public function createTextField($name, $label = ''){
		return new InputText($name, $label, 'text');
	}
	
	public function createHiddenInput($name, $value = null){
		return new InputHidden($name, $value);
	}
	
	public function createPasswordField($name, $label = ''){
		return new InputText($name, $label, 'password');
	}
	
	public function createEmailField($name, $label = ''){
		return new InputText($name, $label, 'email');
	}
	
	public function createCheckbox($name, $label = '', $checked = false, $value = null){
		return new Checkbox($name, $label, $checked, $value);
	}
	
	public function createTable($label = '', $icon = '', $resetHref = '#', $decorated = true){
		$icon = empty($icon) ? null : $this->createIcon($icon);
		$table = new Table($decorated, $label, $icon, $this->searchButtonText, $this->resetButtonText, $this->emptyRowText, $this->selectAllText, $this->unselectAllText, $this->bulkActionText, $resetHref);
		$table->setAjaxActivatorOptions($this->radioOptions);
		$table->setAjaxActivatorLabel($this->defaultAjaxActivatorLabel);
		return $table;
	}
	
	public function createColumn($table, $label, $name, $dataType= ColumnType::TEXT, $searchType = SearchType::TEXT, $sortable = true, $searchable = true, $searchOptions = array(), $dataOptions = array()){
		if(empty($searchOptions) && isset($this->searchOptions[$searchType])){
			$searchOptions = $this->searchOptions[$searchType];
		}
		if(empty($dataOptions)){
			if($dataType==ColumnType::ACTIVE){
				$dataOptions = $this->activeOptions;
			}elseif($dataType==ColumnType::BOOL){
				$dataOptions = $this->radioOptions;
			}
		}
		return new Column($table, $label, $name, $dataType, $searchType, $sortable, $searchable, $searchOptions, $dataOptions);
	}
	
	public function createTableAction($table, $label, $href = '#', $icon = '', $title = '', $useOfButtonStyle = false, $name = '', $action = '', $accessChecked = false){
		$link = $this->createLink($label, $href, $icon, $title, $useOfButtonStyle, $name, $action);
		$table->addTableAction($link);
		$link->setAccessChecked($accessChecked);
		return $link;
	}
	
	public function createBulkAction($table, $label, $href = '#', $icon = '', $title = '', $useOfButtonStyle = false, $name = '', $action = '', $confirm = false, $confirmText = '', $accessChecked = false){
		$link = $this->createLink($label, $href, $icon, $title, $useOfButtonStyle, $name, $action);
		$table->addBulkAction($link);
		$link->setConfirm($confirm);
		$link->setConfirmText($confirmText);
		$link->setAccessChecked($accessChecked);
		return $link;
	}
	
	public function createRowAction($table, $label, $href = '#', $icon = '', $title = '', $useOfButtonStyle = false, $name = '', $action = '', $urlParams = array(), $default = false, $confirm = false, $confirmText = '', $autoConfirm = false, $addToTable = true, $class = '', $accessChecked = false){
		$icon = empty($icon) ? null : $this->createIcon($icon);
		
		$link = new RowAction($table, $label, $href, $icon, $title, $useOfButtonStyle, $name, $action, $urlParams, $default);
		$link->setConfirm($confirm);
		$link->setConfirmText($confirmText);
		$link->setAutoConfirm($autoConfirm);
		$link->setAccessChecked($accessChecked);
		if(!empty($class)){
			$link->addClass($class);
		}
		if($addToTable){
			$table->addRowAction($link);
		}
		return $link;
	}
	
	public function createSelect($name, $label = '', $options = array()){
		return new Select($name, $label, $options);
	}
	
	public function createRadio($name, $label = '', $options = array(), $switch = false){
		if(empty($options)){
			$options = $this->radioOptions;
		}
		return new Radio($name, $label, $options, $switch);
	}
	public function createTree($dao, $headerTemplateFile, $footerTemplateFile, $restrictions = array(), $associations = array(), $itemFormatter = null){
		return new Tree($dao, $headerTemplateFile, $footerTemplateFile, $restrictions, $associations, $itemFormatter);
	}
	
	public function createSwitch($name, $label = '', $options = array()){
		return $this->createRadio($name, $label, $options, true);
	}
	
	public function createTableCheckboxMultiple($columns = array(), $emptyRowText = ''){
		$table = new TableCheckboxMultiple();
		foreach($columns as $name => $value){
			$label = (is_array($value) && isset($value['label'])) ? $value['label'] : $value;
			$dataType = (is_array($value) && isset($value['dataType'])) ? $value['dataType'] : ColumnType::TEXT;
			$this->createColumn($table, $label, $name, $dataType, SearchType::TEXT, false, false);
		}
		$table->setEmptyRowText($emptyRowText);
		return $table;
	}
	public function createInputCustomContent($content, $name='', $label, $fieldOnly, $width = null, $labelWidth = null){
		$input = new InputCustomContent($content, $name, $label, $fieldOnly);
		$input->setWidth(($width === null) ? 'col-lg-9' : $width);
		$input->setLabelWidth(($labelWidth === null) ? 'col-lg-3' : $labelWidth);
		return $input;
	}
}