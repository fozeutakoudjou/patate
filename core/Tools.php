<?php
namespace core;

use core\models\Configuration;
class Tools
{
    /**
    * Random password generator
    *
    * @param int $length Desired length (optional)
    * @param string $flag Output type (NUMERIC, ALPHANUMERIC, NO_NUMERIC, RANDOM)
    * @return bool|string Password
    */
    public static function passwdGen($length = 8, $flag = 'ALPHANUMERIC')
    {
        $length = (int)$length;

        if ($length <= 0) {
            return false;
        }

        switch ($flag) {
            case 'NUMERIC':
                $str = '0123456789';
                break;
            case 'NO_NUMERIC':
                $str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                break;
            case 'RANDOM':
                $num_bytes = ceil($length * 0.75);
                $bytes = self::getBytes($num_bytes);
                return substr(rtrim(base64_encode($bytes), '='), 0, $length);
            case 'ALPHANUMERIC':
            default:
                $str = 'abcdefghijkmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                break;
        }

        $bytes = self::getBytes($length);
        $position = 0;
        $result = '';

        for ($i = 0; $i < $length; $i++) {
            $position = ($position + ord($bytes[$i])) % strlen($str);
            $result .= $str[$position];
        }

        return $result;
    }

    /**
     * Random bytes generator
     *
     * @param $length Desired length of random bytes
     * @return bool|string Random bytes
     */
    public static function getBytes($length)
    {
        $length = (int)$length;

        if ($length <= 0) {
            return false;
        }

        if (function_exists('openssl_random_pseudo_bytes')) {
            $bytes = openssl_random_pseudo_bytes($length, $crypto_strong);

            if ($crypto_strong === true) {
                return $bytes;
            }
        }

        if (function_exists('mcrypt_create_iv')) {
            $bytes = mcrypt_create_iv($length, MCRYPT_DEV_URANDOM);

            if ($bytes !== false && strlen($bytes) === $length) {
                return $bytes;
            }
        }

        // Else try to get $length bytes of entropy.
        // Thanks to Zend

        $result         = '';
        $entropy        = '';
        $msec_per_round = 400;
        $bits_per_round = 2;
        $total          = $length;
        $hash_length    = 20;

        while (strlen($result) < $length) {
            $bytes  = ($total > $hash_length) ? $hash_length : $total;
            $total -= $bytes;

            for ($i=1; $i < 3; $i++) {
                $t1 = microtime(true);
                $seed = mt_rand();

                for ($j=1; $j < 50; $j++) {
                    $seed = sha1($seed);
                }

                $t2 = microtime(true);
                $entropy .= $t1 . $t2;
            }

            $div = (int) (($t2 - $t1) * 1000000);

            if ($div <= 0) {
                $div = 400;
            }

            $rounds = (int) ($msec_per_round * 50 / $div);
            $iter = $bytes * (int) (ceil(8 / $bits_per_round));

            for ($i = 0; $i < $iter; $i ++) {
                $t1 = microtime();
                $seed = sha1(mt_rand());

                for ($j = 0; $j < $rounds; $j++) {
                    $seed = sha1($seed);
                }

                $t2 = microtime();
                $entropy .= $t1 . $t2;
            }

            $result .= sha1($entropy, true);
        }

        return substr($result, 0, $length);
    }

    /**
    * Get a value from $_POST / $_GET
    * if unavailable, take a default value
    *
    * @param string $key Value key
    * @param mixed $default_value (optional)
    * @return mixed Value
    */
    public static function getValue($key, $default_value = false)
    {
        if (!isset($key) || empty($key) || !is_string($key)) {
            return false;
        }

        $ret = (isset($_POST[$key]) ? $_POST[$key] : (isset($_GET[$key]) ? $_GET[$key] : $default_value));

        if (is_string($ret)) {
            return stripslashes(urldecode(preg_replace('/((\%5C0+)|(\%00+))/i', '', urlencode($ret))));
        }

        return $ret;
    }


    /**
     * Get all values from $_POST/$_GET
     * @return mixed
     */
    public static function getAllValues()
    {
        return $_POST + $_GET;
    }

    public static function getIsset($key)
    {
        if (!isset($key) || empty($key) || !is_string($key)) {
            return false;
        }
        return isset($_POST[$key]) ? true : (isset($_GET[$key]) ? true : false);
    }

    /**
    * Check if submit has been posted
    *
    * @param string $submit submit name
    */
    public static function isSubmit($submit)
    {
        return (
            isset($_POST[$submit]) || isset($_POST[$submit.'_x']) || isset($_POST[$submit.'_y'])
            || isset($_GET[$submit]) || isset($_GET[$submit.'_x']) || isset($_GET[$submit.'_y'])
        );
    }

    /**
    * Encrypt password
    *
    * @param string $passwd String to encrypt
    */
    public static function encrypt($passwd)
    {
        return md5(_COOKIE_KEY_.$passwd);
    }

    /**
    * Encrypt data string
    *
    * @param string $data String to encrypt
    */
    public static function encryptIV($data)
    {
        return md5(_COOKIE_IV_.$data);
    }

    /**
     * jsonDecode convert json string to php array / object
     *
     * @param string $json
     * @param bool $assoc  (since 1.4.2.4) if true, convert to associativ array
     * @return array
     */
    public static function jsonDecode($json, $assoc = false)
    {
        if (function_exists('json_decode')) {
            return json_decode($json, $assoc);
        } else {
            include_once(FileTools::getLibrariesDir().'json/json.php');
            $pear_json = new \Services_JSON(($assoc) ? SERVICES_JSON_LOOSE_TYPE : 0);
            return $pear_json->decode($json);
        }
    }

    /**
     * Convert an array to json string
     *
     * @param array $data
     * @return string json
     */
    public static function jsonEncode($data)
    {
        if (function_exists('json_encode')) {
            return json_encode($data);
        } else {
            include_once(FileTools::getLibrariesDir().'json/json.php');
            $pear_json = new \Services_JSON();
            return $pear_json->encode($data);
        }
    }

    /**
     * Delete unicode class from regular expression patterns
     * @param string $pattern
     * @return string pattern
     */
    public static function cleanNonUnicodeSupport($pattern)
    {
        if (!defined('PREG_BAD_UTF8_OFFSET')) {
            return $pattern;
        }
        return preg_replace('/\\\[px]\{[a-z]{1,2}\}|(\/[a-z]*)u([a-z]*)$/i', '$1$2', $pattern);
    }


    /**
     * Allows to display the category description without HTML tags and slashes
     * @return string
    */
    public static function getDescriptionClean($description)
    {
        return strip_tags(stripslashes($description));
    }

    public static function purifyHTML($html, $uri_unescape = null, $allow_style = false)
    {
        require_once(FileTools::getLibrariesDir().'htmlpurifier/HTMLPurifier.standalone.php');

        static $use_html_purifier = null;
        static $purifier = null;

        if ($use_html_purifier === null) {
            $use_html_purifier = (bool)Configuration::get('USE_HTMLPURIFIER');
        }

        if ($use_html_purifier) {
            if ($purifier === null) {
                $config = \HTMLPurifier_Config::createDefault();

                $config->set('Attr.EnableID', true);
                $config->set('HTML.Trusted', true);
                $config->set('Cache.SerializerPath', _SITE_CACHE_DIR_.'purifier');
                $config->set('Attr.AllowedFrameTargets', array('_blank', '_self', '_parent', '_top'));
                if (is_array($uri_unescape)) {
                    $config->set('URI.UnescapeCharacters', implode('', $uri_unescape));
                }

                if (Configuration::get('ALLOW_HTML_IFRAME')) {
                    $config->set('HTML.SafeIframe', true);
                    $config->set('HTML.SafeObject', true);
                    $config->set('URI.SafeIframeRegexp', '/.*/');
                }

                /** @var HTMLPurifier_HTMLDefinition|HTMLPurifier_HTMLModule $def */
                // http://developers.whatwg.org/the-video-element.html#the-video-element
                if ($def = $config->getHTMLDefinition(true)) {
                    $def->addElement('video', 'Block', 'Optional: (source, Flow) | (Flow, source) | Flow', 'Common', array(
                        'src' => 'URI',
                        'type' => 'Text',
                        'width' => 'Length',
                        'height' => 'Length',
                        'poster' => 'URI',
                        'preload' => 'Enum#auto,metadata,none',
                        'controls' => 'Bool',
                    ));
                    $def->addElement('source', 'Block', 'Flow', 'Common', array(
                        'src' => 'URI',
                        'type' => 'Text',
                    ));
                    if ($allow_style) {
                        $def->addElement('style', 'Block', 'Flow', 'Common', array('type' => 'Text'));
                    }
                }

                $purifier = new \HTMLPurifier($config);
            }
            if (_MAGIC_QUOTES_GPC_) {
                $html = stripslashes($html);
            }

            $html = $purifier->purify($html);

            if (_MAGIC_QUOTES_GPC_) {
                $html = addslashes($html);
            }
        }

        return $html;
    }
	
	/**
     * Set cookie lang
     */
    public static function switchLanguage(Context $context = null)
    {
        if (!$context) {
            $context = Context::getInstance();
        }

        // Install call the dispatcher and so the switchLanguage
        // Stop this method by checking the cookie
		$cookie = $context->getCookie();
        if (!isset($cookie)) {
            return;
        }
		$activedLanguages = Language::getLanguages(true);
        if (($iso = Tools::getValue('isolang')) && Validate::isLanguageIsoCode($iso) && isset($activedLanguages[$iso])) {
            $_GET['lang'] = $iso;
        }

        // update language only if new id is different from old id
        // or if default language changed
        $cookieLang = $cookie->lang;
        $configurationLang = Configuration::get('DEFAULT_LANG');
        if ((($lang = Tools::getValue('lang')) && Validate::isLanguageIsoCode($lang) && $cookieLang != $lang)
            || (($lang == $configurationLang) && Validate::isLanguageIsoCode($lang) && $lang != $cookieLang)) {
            $cookie->lang = $lang;
            $context->setLang($lang);
        }
    }
	
	public static function getLangFieldKey($field, $lang){
		$code = is_string($lang)?$lang:$lang->getKey();
		return $field.'_'.$code;
    }
	
	public static function getRightCode($code){
		return strtoupper(StringTools::toUnderscoreCase($code));
    }

    public static function redirect($url)
    {
        header('Location: '.$url);
        exit;
    }
	
	public static function getRouteId($page, $module = '')
    {
        return (empty($module)?'':'module-'.$module.'-').$page;
    }
}
