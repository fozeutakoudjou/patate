<?php
namespace core\generator\html;
use core\Context;
use core\FileTools;
class Content{
	protected $content;
	protected $name;
	protected $templateFile = 'generator/content';
	
	protected $absoluteTemplate = false;
	public function __construct($content = '') {
		$this->content=$content;
	}
	
	public function setHtml($content) {
		$this->content=$content;
	}
	public function gethtml() {
		return $this->content;
	}
	public function generate() {
		$content = '';
		if(!empty($this->templateFile)){
			$context = Context::getInstance();
			$template = $context->getTemplate();
			$file = $this->absoluteTemplate ? $this->templateFile : FileTools::getTemplateDir(true) . $this->templateFile;
			$template->assign('item', $this);
			$content = $template->render($file);
		}
		
		return $content;
	}
	
	public function needValue() {
		return false;
	}
	public function isValueSeted() {
		return false;
	}
	
	public function setTemplateFile($templateFile, $absolutePath = true) {
		$this->templateFile=$templateFile;
		$this->absoluteTemplate=$absolutePath;
	}
	
	public function getName() {
		return $this->name;
	}
}