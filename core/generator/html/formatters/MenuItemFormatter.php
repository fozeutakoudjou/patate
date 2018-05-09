<?php
namespace core\generator\html\formatters;
use core\generator\html\interfaces\Formatter;
class MenuItemFormatter implements Formatter{
	protected $formatter;
	
	public function __construct($formatter) {
		$this->formatter = $formatter;
	}
	public function format($item) {
		$this->formatter->formatMenuItem($item);
	}
}