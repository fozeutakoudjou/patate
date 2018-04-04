<?php
namespace core;
class Media
{
	const POSITION_FIRST = 'first';
	const POSITION_LAST = 'last';
	
	const LIBRARY_KEY = 'libraries';
	const NOT_LIBRARY_KEY = 'others';
	const HEAD_KEY = 'head';
	const NOT_HEAD_KEY = 'foot';
	
	public static function addMedia($list, $uris, $params = array(), $checkPath = true)
    {
		return self::editMediaList($list, $uris, $params, $checkPath, false);
	}
	
	public static function removeMedia($list, $uris, $checkPath = true)
    {
		return self::editMediaList($list, $uris, array(), $checkPath, true);
	}
	
	protected static function editMediaList($list, $uris, $params = array(), $checkPath = true, $remove = false)
    {
		if (!is_array($uris)) {
            $uris = array($uris);
        }
		foreach ($uris as $uri) {
			if($checkPath){
				$uri = FileTools::getMediaUri($uri);
			}
			if($remove){
				if(isset($list[$uri])){
					unset($list[$uri]);
				}
			}else{
				if(!empty($uri)){
					if(!isset($params['position'])){
						$params['position'] = self::POSITION_LAST;
					}
					$list[$uri] = $params;
				}
			}
		}
		return $list;
	}
	
	public static function sortList($list){
		uasort($list, function($item1, $item2){
			$position1 = isset($item1['position']) ? $item1['position'] : Media::POSITION_LAST;
			$position2 = isset($item2['position']) ? $item2['position'] : Media::POSITION_LAST;
			if($position1==Media::POSITION_FIRST){
				return -1;
			}elseif($position1==Media::POSITION_LAST){
				return 1;
			}else{
				return (int)$position1 - (int)$position2;
			}
		});
		return $list;
    }
	
	public static function formatList($list, $useDisplayInHead = true, $checkLibrary = true){
		$list = self::sortList($list);
		$result = array();
		
		foreach($list as $key=>$params){
			if(isset($params['attributes']) && is_array($params['attributes'])){
				$attributes = '';
				foreach($params['attributes'] as $attributeName=>$attributeValue){
					$attributes.=' '.$attributeName.(empty($attributeValue)?'' : '="'.$attributeValue.'"');
				}
				$params['attributes'] = $attributes;
			}
			if($useDisplayInHead){
				$headKey = (isset($params['displayInHead']) && $params['displayInHead']) ? self::HEAD_KEY : self::NOT_HEAD_KEY;
				if($checkLibrary){
					$libraryKey = (isset($params['isLibrary']) && $params['isLibrary']) ? self::LIBRARY_KEY : self::NOT_LIBRARY_KEY;
					$result[$headKey][$libraryKey][$key] = $params;
				}else{
					$result[$headKey][$key] = $params;
				}
			}elseif($checkLibrary){
				$libraryKey = (isset($params['isLibrary']) && $params['isLibrary']) ? self::LIBRARY_KEY : self::NOT_LIBRARY_KEY;
				$result[$libraryKey][$key] = $params;
			}else{
				$result[$key] = $params;
			}
		}
		return $result;
    }
	
	/**
     * return jquery plugin css path if exist.
     *
     * @param mixed       $name
     * @param string|null $folder
     *
     * @return bool|string
     */
    public static function getJqueryPluginCSSPath($name, $folder = null, $module = '')
    {
        if ($folder === null) {
            $folder = Context::getInstance()->getLink()->getAssetLibrariesURI($module).'jquery/plugins/';
        } //set default folder
        $file = 'jquery.'.$name.'.css';
		$fileUri = FileTools::getDirFromUri($folder);
        if (@file_exists($fileUri.$file)) {
            return $folder.$file;
        } elseif (@file_exists($folder.$name.'/'.$file)) {
            return $folder.$name.'/'.$file;
        } else {
            return false;
        }
    }
	
	/**
     * return jquery plugin path.
     *
     * @param mixed $name
     * @param string|null  $folder
     *
     * @return bool|string
     */
    public static function getJqueryPluginPath($name, $folder = null, $css = true, $module = '')
    {
        $pluginPath = array('js' => array(), 'css' => array());
        if ($folder === null) {
            $folder = Context::getInstance()->getLink()->getAssetLibrariesURI($module).'jquery/plugins/';
        } //set default folder

        $file = 'jquery.'.$name.'.js';
		$fileUri = FileTools::getDirFromUri($folder);
        if (@file_exists($fileUri.$file)) {
            $pluginPath['js'] = $folder.$file;
        } elseif (@file_exists($fileUri.$name.'/'.$file)) {
            $pluginPath['js'] = $folder.$name.'/'.$file;
        } else {
            return false;
        }
		if($css){
			$pluginPath['css'] = self::getJqueryPluginCSSPath($name, $folder, $module);
		}
        return $pluginPath;
    }
	
	 /**
     * return jquery path.
     *
     * @param mixed $version
     *
     * @return string
     */
    public static function getJqueryPath($version = null, $folder = null, $minifier = true)
    {
		$librariesDir = Context::getInstance()->getLink()->getAssetLibrariesURI();
		$addNoConflict = false;
        if ($version === null) {
            $version = _JQUERY_VERSION_;
        } //set default version
        elseif (preg_match('/^([0-9\.]+)$/Ui', $version)) {
            $addNoConflict = true;
        } else {
            return false;
        }

        if ($folder === null) {
            $folder = $librariesDir.'jquery/';
        } //set default folder
        //check if file exist
        $file = $folder.'jquery-'.$version.($minifier ? '.min.js' : '.js');
		$fileUri = FileTools::getDirFromUri($file);
        $return = array();
        if (@filemtime($fileUri)) {
            $return[] = $file;
        } else {
            $return[] = Link::getCurrentUrlProtocolPrefix().'ajax.googleapis.com/ajax/libs/jquery/'.$version.'/jquery'.($minifier ? '.min.js' : '.js');
        }

        if ($addNoConflict) {
            $return[] = $librariesDir.'jquery/jquery.noConflict.php?version='.$version;
        }

        //added query migrate for compatibility with new version of jquery will be removed in ps 1.6
        $return[] = $librariesDir.'jquery/jquery-migrate-1.2.1.min.js';

        return $return;
    }
}
