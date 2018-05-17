<?php
namespace core;

class TemplateTools{
	
	public function includeTpl($template, $isAbsolutePath = true, $data = array(), $useCurrentData = true, $checkPath = true){
		Template::getInstance()->includeTpl($template, $isAbsolutePath, $data, $useCurrentData, $checkPath);
    }
	
	public function jsonEncode($value){
		return Tools::jsonEncode($value);
	}
	
	public function escapeHtml($value){
		return $value;
	}
	
	public function getMedia($uri){
		return FileTools::getMediaUri($uri);
	}
	
	public function getImage($src){
		return $this->getMedia($src);
	}
	
	public function l($string){
		return $string;
	}
	
	public function getFieldName($name, $lang = ''){
		return Tools::getLangFieldKey($name, $lang);
	}
}