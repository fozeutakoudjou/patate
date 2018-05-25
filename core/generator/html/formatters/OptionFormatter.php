<?php
namespace core\generator\html\formatters;
use core\generator\html\interfaces\Formatter;
class OptionFormatter implements Formatter{
	public function format($item) {
		$options = $item->getFinalOptions();
		$value = $item->getValue();
		$item->setHtml(isset($options[$value]) ? $options[$value] : $value);
	}
}