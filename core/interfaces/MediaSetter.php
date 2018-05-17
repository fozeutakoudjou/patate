<?php
namespace core\interfaces;
use core\constant\MediaPosition;
interface MediaSetter{
	public function addJS($jsUri, $params = array(), $checkPath = true);

    public function removeJS($jsUri, $checkPath = true);
	
	public function addCSS($cssUri, $params = array(), $checkPath = true);

    public function removeCSS($cssUri, $checkPath = true);
	
    public function addJqueryPlugin($name, $folder = null, $css = true, $module = '');
	
	public function addJSVariable($name, $value, $displayInHead = false, $position = MediaPosition::LAST);
	
	public function addJSContent($content, $displayInHead = false, $position = MediaPosition::LAST);
	
	public function addCSSContent($content, $position = MediaPosition::LAST);
	
	public function addMediaGroup($alias);
	
	public function removeMediaGroup($alias);
	
	public function hasMediaGroup($alias);
}