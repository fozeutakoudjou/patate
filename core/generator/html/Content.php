<?php
namespace core\generator\html;
use core\Context;
use core\FileTools;
class Content{
	protected $html;
	protected $name;
	protected $templateFile;
	protected $action;
	protected $contentForced = false;
	protected $accessChecked = false;
	
	protected static $languages = array();
	
	protected static $activeLang = '';
	
	protected static $accessChecker = null;
	
	protected static $template = null;
	
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
		if($this->accessChecked || empty($this->action) || (self::$accessChecker==null) || self::$accessChecker->checkUserAccess($this->action)){
			$content = $this->html;
			if(!$this->contentForced && !empty($this->templateFile)){
				$template = self::$template;
				$file = $this->absoluteTemplate ? $this->templateFile : FileTools::getTemplateDir(true) . $this->templateFile;
				$template->assign('item', $this);
				$template->assign('languages', self::$languages);
				$template->assign('activeLang', self::$activeLang);
				$content = $template->render($file);
			}
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
	
	public function getAction() {
		return $this->action;
	}
	
	public function forceContent($html) {
		$this->setHtml($html);
		$this->contentForced = true;
	}
	
	public function setAction($action){
		$this->action=$action;
	}
	public function isAccessChecked() {
		return $this->accessChecked;
	}
	public function setAccessChecked($accessChecked){
		$this->accessChecked=$accessChecked;
	}
	
	public static function setLanguages($languages){
		self::$languages=$languages;
	}
	
	public static function setActiveLang($activeLang){
		self::$activeLang=$activeLang;
	}
	
	public static function setAccessChecker($accessChecker){
		self::$accessChecker=$accessChecker;
	}
	
	public static function setTemplate($template){
		self::$template=$template;
	}
}