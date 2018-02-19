<?php
namespace Library;

/**
 * Description of HttRequest
 *
 * @author FFOZEU
 */

class HttpRequest extends ApplicationComponent{
    
    /**
     * Reccupère une variable dans le tableau $_COOKIE
     * @param type $key
     * @return type 
     */
    public function cookieData($key){
        return isset($_COOKIE[$key]) ? $_COOKIE[$key] : null;
    }
    /**
     * verifie l'existance d'une variable dans le tableau $_COOKIE
     * @param type $key
     * @return type 
     */
    public function cookieExists($key){
        return isset($_COOKIE[$key]);
    }
    /**
     * Reccupère une variable dans le tableau $_GET
     * @param type $key
     * @return type 
     */
    public function getData($key){
        return isset($_GET[$key]) ? $_GET[$key] : null;
    }
    /**
     * verifie l'existance d'une variable dans le tableau $_GET
     * @param type $key
     * @return type 
     */
    public function getExists($key){
        return (isset($_GET[$key]) || isset($_POST[$key]));
    }
    
    /**
     * Reccupère une variable dans le tableau $_POST
     * @param type $key
     * @return type 
     */
    public function postData($key){
        return isset($_POST[$key]) ? $_POST[$key] : null;
    }
    
    /**
     * verifie l'existance d'une variable dans le tableau $_POST
     * @param type $key
     * @return type 
     */
    public function postExists($key){
        return isset($_POST[$key]);
    }
    
    public function requestURI(){
        $uri = $_SERVER['REQUEST_URI'];
        if(_BASE_DIR_ !=''){
            $uri = str_replace('/'._BASE_DIR_, '', $uri);
           /* $uri = explode('/', $_SERVER['REQUEST_URI']);
            if($uri[0]=='' &&_BASE_DIR_==$uri[1] ){
                array_shift ($uri);
                array_shift ($uri);
                $uri = '/'.implode('/', $uri);
            }else
                $uri = implode('/', $uri);*/
        }
        return $uri;
    }
    
    public function resquestIp(){
        return $_SERVER['REMOTE_ADDR'];
    }
    
    public function resquestPort(){
        return $_SERVER['REMOTE_PORT'];
    }
    /**
     * ajoute des variable au grand tableau GET
     * @param type $key
     * @param type $value
     * @return type 
     */
    public function addGetVar($key,$value){
        return $_GET[$key]=$value;
    }
    /**
     * ajoute des variable au grand tableau POST
     * @param type $key
     * @param type $value
     * @return type 
     */
    public function addPostVar($key,$value){
        return $_POST[$key]=$value;
    }
    /**
     * determine le type de method pour un formulaire(get ou post)
     * @param type $method
     * @return type 
     */
    public function getMethod($method = null){
        
        return (!empty($method)&& $method=='post'?$_POST:(!empty($method)&& $method=='get'?$_GET:null));
    }
    
    /**
     * methode permettant de retrouver l'url ayant rédirigé à cette page
     * @return type 
     */
    public function refferer(){
        return $_SERVER['HTTP_REFERER'];
    }
    
    /**
     * methode permttant de savoir si l'url proveint d'une requete ajax 
     * @return type 
     */
    public function isXmlHttpRequest(){
        return isset($_SERVER['HTTP_X_REQUESTED_WITH'])&& $_SERVER['HTTP_X_REQUESTED_WITH']=='XMLHttpRequest'?true:false;
    }
    /**
	* Get a value from $_POST / $_GET
	* if unavailable, take a default value
	*
	* @param string $key Value key
	* @param mixed $defaultValue (optional)
	* @return mixed Value
	*/
	public function getValue($key, $defaultValue = false)
	{
	 	if (!isset($key) OR empty($key) OR !is_string($key))
			return false;
		$ret = (isset($_POST[$key]) ? $_POST[$key] : (isset($_GET[$key]) ? $_GET[$key] : $defaultValue));

		if (is_string($ret) === true)
			$ret = stripslashes(urldecode(preg_replace('/((\%5C0+)|(\%00+))/i', '', urlencode($ret))));
		elseif (is_array($ret))
            $ret = $this->getArrayValue($ret);
		return $ret;
	}
    /**
     * Escape values contained in an array
     *
     * @param array $array Value array
     * @return mixed Value
     */
    public function getArrayValue($array)
    {
        foreach ($array as &$row)
        {
            if (is_array($row))
                $row = $this->getArrayValue($row);
            else
                $row = stripslashes(urldecode(preg_replace('/((\%5C0+)|(\%00+))/i', '', urlencode($row))));
        }
        return $array;
    }
    
    public function getSendData(array $array){
        if(is_array($array)){
            foreach ($array as $key => $value) {
                $array[$key] = $this->getValue($key);
            }
        }
        return $array;
    }
}
?>