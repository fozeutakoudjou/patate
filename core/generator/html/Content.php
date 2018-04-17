<?php
namespace core\generator\html;
use core\Context;
use core\FileTools;
class Content{
	protected $html;
	protected $name;
	protected $templateFile;
	
	protected static $languages = array();
	
	protected static $activeLang = '';
	
	protected $absoluteTemplate = false;
	public function __construct($html = '') {
		$this->setHtml($html);
	}
	
	public function setHtml($html) {
		$this->html=$html;
	}
	public function gethtml() {
		return $this->html;
	}
	public function generate() {
		$content = '';
		if(!empty($this->templateFile)){
			$context = Context::getInstance();
			$template = $context->getTemplate();
			$file = $this->absoluteTemplate ? $this->templateFile : FileTools::getTemplateDir(true) . $this->templateFile;
			$template->assign('item', $this);
			$template->assign('languages', self::$languages);
			$template->assign('activeLang', self::$activeLang);
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
	public function hasName() {
		return !empty($this->name);
	}
	
	public function setName($name){
		$this->name=$name;
	}
	
	public static function setLanguages($languages){
		self::$languages=$languages;
	}
	
	public static function setActiveLang($activeLang){
		self::$activeLang=$activeLang;
	}
}