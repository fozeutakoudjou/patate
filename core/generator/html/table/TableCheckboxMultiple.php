<?php
namespace core\generator\html\table;
class TableCheckboxMultiple extends Table{
	protected $formEnabled = false;
	protected $tableFooterEnabled = false;
	protected $rowSelectetorEnabledWhatever = true;
	protected $contentOnly = true;
	public function __construct() {
		parent::__construct(false);
	}
	public function generate(){
		$this->addClass('tableCheckboxMultiple');
		return parent::generate();
	}
	
	/*public function createRow($value){
		$row = parent::createRow($value);
		$row->addClass('tr');
		return $row;
	}*/
}