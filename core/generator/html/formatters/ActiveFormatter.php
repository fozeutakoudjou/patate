<?php
namespace core\generator\html\formatters;
use core\generator\html\interfaces\Formatter;
class ActiveFormatter implements Formatter{
	public function format($item) {
		$options = $item->getColumn()->getDataOptions();
		$value = (int)$item->getValue();
		$html = $value;
		if(isset($options[$value])){
			if(is_array($options[$value]) && isset($options[$value]['rowAction'])){
				$link = $options[$value]['rowAction']->createNewLink($item->getRowData());
				$link->addClass('list-action-enable'); 
				$html = $link->generate(); 
			}elseif(is_array($options[$value]) && isset($options[$value]['label'])){
				$html = $options[$value]['label'];
			}elseif(!is_array($options[$value])){
				$html = $options[$value];
			}
		}
		$item->setHtml($html);
	}
}