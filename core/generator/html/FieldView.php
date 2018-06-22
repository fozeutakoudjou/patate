<?php
namespace core\generator\html;
class FieldView extends Field{
	protected $templateFile = 'generator/field_view';
	protected $dataFormatter;
	protected $dataType;
	
	function __construct($name, $label='', $dataType = null) {
		parent::__construct($name, $label);
		$this->setDataType($dataType);
	}
	
	public function getDataType() {
		return $this->dataType;
	}
	public function setDataType($dataType) {
		$this->dataType=$dataType;
	}
	
	public function getDataFormatter() {
		return $this->dataFormatter;
	}
	public function setDataFormatter($dataFormatter) {
		$this->dataFormatter=$dataFormatter;
	}
	public function generate(){
		if($this->dataFormatter!=null){
			$data = $this->dataFormatter->format($this);
		}else{
			$this->html = $this->value;
		}
		return parent::generate();
	}
}