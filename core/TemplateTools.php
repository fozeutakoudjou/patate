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
	
	public function l($string){
		return $string;
	}
	
	public function getFieldName($name, $lang = ''){
		return Tools::getLangFieldKey($name, $lang);
	}
	
	public function addJS($jsUri, $params = array(), $checkPath = true)
    {
		Context::getInstance()->getController()->addJS($jsUri, $params, $checkPath);
    }

    public function removeJS($jsUri, $checkPath = true)
    {
		Context::getInstance()->getController()->removeJS($jsUri, $checkPath);
    }
	
	public function addCSS($cssUri, $params = array(), $checkPath = true)
    {
		Context::getInstance()->getController()->addCSS($cssUri, $params, $checkPath);
    }

    public function removeCSS($cssUri, $checkPath = true)
    {
		Context::getInstance()->getController()->removeCSS($cssUri, $checkPath);
    }
	
    public function addJqueryPlugin($name, $folder = null, $css = true, $module = '')
    {
		Context::getInstance()->getController()->addJqueryPlugin($name, $folder, $css, $module);
    }
	
	public function addJSVariable($name, $value, $displayInHead = false, $position = MEDIA::POSITION_LAST)
    {
		Context::getInstance()->getController()->addJSVariable($name, $value, $displayInHead, $position);
    }
	
	public function addJSContent($content, $displayInHead = false, $position = MEDIA::POSITION_LAST)
    {
		Context::getInstance()->getController()->addJSContent($content, $displayInHead, $position);
    }
	
	public function addCSSContent($content, $position = MEDIA::POSITION_LAST)
    {
		Context::getInstance()->getController()->addCSSContent($content, $position);
    }
}