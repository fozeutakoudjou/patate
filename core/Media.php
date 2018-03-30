<?php
namespace core;
class Media
{
	public static function addMedia($list, $uris, $params = array(), $checkPath = true)
    {
		return self::editMediaList($list, $uris, array(), $checkPath, false);
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
					$list[$uri] = $params;
				}
			}
		}
		return $list;
	}
	
	/**
     * return jquery plugin css path if exist.
     *
     * @param mixed       $name
     * @param string|null $folder
     *
     * @return bool|string
     */
    public static function getJqueryPluginCSSPath($name, $folder = null)
    {
        if ($folder === null) {
            $folder = _LIBRARIES_JS_DIR_.'jquery/plugins/';
        } //set default folder
        $file = 'jquery.'.$name.'.css';
		$fileUri = self::getDirFromUri($folder);
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
    public static function getJqueryPluginPath($name, $folder = null)
    {
        $pluginPath = array('js' => array(), 'css' => array());
        if ($folder === null) {
            $folder = _LIBRARIES_JS_DIR_.'jquery/plugins/';
        } //set default folder

        $file = 'jquery.'.$name.'.js';
		$fileUri = self::getDirFromUri($folder);
        if (@file_exists($fileUri.$file)) {
            $pluginPath['js'] = $folder.$file;
        } elseif (@file_exists($fileUri.$name.'/'.$file)) {
            $pluginPath['js'] = $folder.$name.'/'.$file;
        } else {
            return false;
        }
        $pluginPath['css'] = self::getJqueryPluginCSSPath($name, $folder);

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
            $folder = _LIBRARIES_JS_DIR_.'jquery/';
        } //set default folder
        //check if file exist
        $file = $folder.'jquery-'.$version.($minifier ? '.min.js' : '.js');
        $fileUri = self::getDirFromUri($file);
        $return = array();
        if (@filemtime($fileUri)) {
            $return[] = $file;
        } else {
            $return[] = Link::getCurrentUrlProtocolPrefix().'ajax.googleapis.com/ajax/libs/jquery/'.$version.'/jquery'.($minifier ? '.min.js' : '.js');
        }

        if ($addNoConflict) {
            $return[] = _LIBRARIES_JS_DIR_.'jquery/jquery.noConflict.php?version='.$version;
        }

        //added query migrate for compatibility with new version of jquery will be removed in ps 1.6
        $return[] = _LIBRARIES_JS_DIR_.'jquery/jquery-migrate-1.2.1.min.js';

        return $return;
    }
	
	public static function getDirFromUri($uri, $asArray = false)
    {
		$urlData = parse_url($uri);
        $fileUri = _SITE_ROOT_DIR_.StringTools::strReplaceOnce(_BASE_DIR_, DIRECTORY_SEPARATOR, $urlData['path']);
		$result = ($asArray ? array('urlData' => $urlData, 'fileUri' => $fileUri) : $fileUri);
		return $result;
	}
	
	public static function getUriFormDir($dir, $urlData = array())
    {
		$finalUri = str_replace(_SITE_ROOT_DIR_.DIRECTORY_SEPARATOR, '', $dir);
		$finalUri = str_replace('\\', '', $finalUri);
		$finalUri = _BASE_URI_ .$finalUri;
		return $finalUri;
	}
}
