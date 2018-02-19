<?php

/**
 * Description of String
 *
 * @author FFOZEU
 */
namespace Library\Classe;

class String {
    
    /**
	 * Returns the length of the given string.
	 *
	 * @param string $string
	 * @return
	 */
	function strlen($string)
	{
		if (function_exists('mb_strlen') && GENERAL_ENCODING != 'iso-8859-1') {
			return mb_strlen($string);
		} else {
			return strlen($string);
		}
	}

	/**
	 * Returns the numeric position of the first occurrence of needle in the haystack  string.
	 * Unlike the strrpos() before PHP 5, this function can take a full string as the needle  parameter and the entire string will be used.
	 *
	 * @param string $haystack The string to search in
	 * @param string $needle If needle is not a string, it is converted to an integer and applied as the ordinal value of a character.
	 * @param integer $offset The optional offset parameter allows you to specify which character in haystack to start searching. The position returned is still relative to the beginning of haystack.
	 * @return
	 */
	function strpos($haystack, $needle, $offset = 0)
	{
		if (function_exists('mb_strpos')) {
			return mb_strpos($haystack, $needle, $offset);
		} else {
			return strpos($haystack, $needle, $offset);
		}
	}

	/**
	 * Returns the numeric position of the last occurrence of needle in the haystack string. Note that the needle in this case can only be a single character in PHP 4.
	 * If a string is passed as the needle, then only the first character of that string will be used.
	 *
	 * @param string $haystack The string to search in
	 * @param string $needle If needle is not a string, it is converted to an integer and applied as the ordinal value of a character.
	 * @param integer $offset May be specified to begin searching an arbitrary number of characters into the string. Negative values will stop searching at an arbitrary point prior to the end of the string.
	 * @return
	 */
	function strrpos($haystack, $needle, $offset = 0)
	{
		if ($offset > 0) {
			$offset = min($offset, String::strlen($haystack));
		} elseif ($offset < 0) {
			$offset = max($offset, - String::strlen($haystack));
		}
		if (function_exists('mb_strrpos')) {
			if (empty($offset)) {
				return mb_strrpos($haystack, $needle);
			} elseif (version_compare(PHP_VERSION, '5.2.0', '>=')) {
				// $offset pour mb_strrpos est introduit avec PHP 5.2
				return mb_strrpos($haystack, $needle, $offset);
			} elseif (version_compare(PHP_VERSION, '5.0.0', '>=')) {
				// $offset pour strrpos est introduit avec PHP 5 et non pas 5.2
				return strrpos($haystack, $needle, $offset);
			} else{
				return false;
			}
		} else {
			if (empty($offset)) {
				return strrpos($haystack, $needle);
			}else{
				return strrpos($haystack, $needle, $offset);
			}
		}
	}

	/**
	 * Returns the portion of string specified by the start and length parameters.
	 *
	 * @param string $string
	 * @param integer $start
	 * @param integer $length
	 * @return
	 */
	function substr($string, $start, $length = null)
	{
		if (function_exists('mb_substr')) {
			if ($length !== null) {
				return mb_substr($string, $start, $length);
			} else {
				return mb_substr($string, $start);
			}
		} else {
			if ($length !== null) {
				return substr($string, $start, $length);
			} else {
				return substr($string, $start);
			}
		}
	}

	/**
	 * Returns string with all alphabetic characters converted to lowercase.
	 *
	 * @param string $string The input string.
	 * @return
	 */
	function strtolower($string)
	{
		if (function_exists('mb_strtolower')) {
			return mb_strtolower($string);
		} else {
			return strtolower($string);
		}
	}

	/**
	 * Returns string with all alphabetic characters converted to uppercase.
	 *
	 * @param string $string The input string.
	 * @return
	 */
	function strtoupper($string)
	{
		if (function_exists('mb_strtoupper')) {
			return mb_strtoupper($string);
		} else {
			return strtoupper($string);
		}
	}

	/**
	 * Returns the number of times the needle substring occurs in the haystack string. Please note that needle is case sensitive.
	 * WARNING : This functions has only 2 arguments, as the mb_substr_count has less arguments than the non-multibyte function substr_count
	 *
	 * @param string $string The input string.
	 * @param integer $start
	 * @return
	 */
	function substr_count($string, $start)
	{
		if (function_exists('mb_substr_count')) {
			return mb_substr_count($string, $start);
		} else {
			return substr_count($string, $start);
		}
	}

	/**
	 * Raccourcit une chaine de caractère en insérant au milieu ou à la fin un séparateur
	 *
	 * @param string $string
	 * @param integer $length_limit
	 * @param string $middle_separator
	 * @param string $ending
	 * @return
	 */
	function str_shorten($string, $length_limit, $middle_separator = '', $ending = '...')
	{
		$length = String::strlen($string);
		if (String::strlen($middle_separator) + String::strlen($ending) >= $length_limit) {
			// Dans le cas où la largeur max est inférieur à la largeur du séparateur, on coupe simplement la chaîne à la longueur max
			return String::substr($string, 0, $length_limit);
		} elseif ($length > $length_limit) {
			// $cut_size est le nombre de caractères à retirer
			$cut_size = $length + String::strlen($middle_separator) + String::strlen($ending) - $length_limit;
			if (!empty($middle_separator)) {
				// On coupe au milieu
				$cut_start = ceil(($length - $cut_size) / 2);
			} else {
				// On coupe à la fin de la chaine
				$cut_start = $length - $cut_size;
			}
			$beginning_text = String::substr($string, 0, $cut_start);
			if (String::strpos(String::substr($beginning_text, -8), '&') !== false && String::strpos(String::substr($beginning_text, -8), ';') === false) {
				// Si le texte se termine par une entitée pas finie (ex : &#34) on la retire.
				$beginning_text = String::substr($beginning_text, 0, String::strrpos($beginning_text, '&'));
			}
			$ending_text = String::substr($string, $cut_start + $cut_size);
			if (String::strpos(String::substr($ending_text, 0, 8), '&') === false && String::strpos(String::substr($ending_text, 0, 8), ';') !== false) {
				// Si le texte de fin commence par un morceau de fin d'entitée, alors on retire ce morceau.
				$ending_text = String::substr($ending_text, String::strpos($ending_text, ';') + 1);
			}
			if (String::strpos(String::substr($ending_text, -8), '&') !== false && String::strpos(String::substr($ending_text, -8), ';') === false) {
				// Si le texte se termine par une entitée pas finie (ex : &#34) on la retire.
				$ending_text = String::substr($ending_text, 0, String::strrpos($ending_text, '&'));
			}
			return $beginning_text . $middle_separator . $ending_text . $ending;
		} else {
			return $string;
		}
	}

	/**
	 * On rajoute des espaces à l'intérieur des mots trop longs => à utiliser pour éviter de casser une mise en page
	 *
	 * @param string $texte
	 * @param integer $length_limit
	 * @param string $separator
	 * @return
	 */
	function str_shorten_words($texte, $length_limit = 100, $separator = " [...] ")
	{
		// On coupe autour de tous les mots
		$tab = explode(' ', $texte);

		foreach($tab as $k => $str) {
			// "quote=" => Compatibilité avec les enchaînements de quote dans lesquels il n'y a pas d'espace
			if (String::strpos($tab[$k], 'http') === false && String::strpos($tab[$k], 'quote=') === false && String::strpos($tab[$k], '[/quote]') === false && String::strpos($tab[$k], '&#') === false) {
				$tab[$k] = wordwrap($str, $length_limit, $separator, true);
			}
		}
		// on reconstitue la chaîne initiale
		$string = implode(' ', $tab);
		return $string;
	}

	/**
	 * convert_accents()
	 *
	 * @param string $string
	 * @param boolean $convert_umlaut
	 * @param boolean $strip_umlaut
	 * @return
	 */
	function convert_accents($string, $convert_umlaut = false, $strip_umlaut = true)
	{
		$string = str_replace(array('à', 'á', 'â', 'ã', 'å'), 'a', $string);
		$string = str_replace(array('è', 'é', 'ê', 'ë'), 'e' , $string);
		$string = str_replace(array('ì', 'í', 'î', 'ï'), 'i' , $string);
		$string = str_replace(array('ò', 'ó', 'ô', 'õ', 'ø'), 'o' , $string);
		$string = str_replace(array('ù', 'ú', 'û'), 'u' , $string);
		$string = str_replace(array('æ', 'œ', 'ý', 'ÿ', 'ç', 'ß', 'ñ'),
			array('ae', 'oe', 'y', 'y', 'c', 'ss', 'n'), $string);
		if ($convert_umlaut) {
			$string = str_replace(array('ä', 'ö', 'ü'), array('ae', 'oe', 'ue'), $string);
		} elseif ($strip_umlaut) {
			$string = str_replace(array('ä', 'ö', 'ü'), array('a', 'o', 'u'), $string);
		}
		return $string;
	}

	/**
	 * Converts the character encoding of string $string to $new_encoding from optionally $original_encoding.
	 * Examples of encodings : UTF-16, UTF-8, JIS, ISO-8859-1, ISO-8859-15
	 * If the mbstring is not defined, it return the original $string
	 *
	 * @param string $string
	 * @param string $new_encoding
	 * @param string $original_encoding
	 * @return
	 */
	function convert_encoding($string, $new_encoding, $original_encoding = null)
	{
		$new_encoding = strtolower($new_encoding);
		$original_encoding = strtolower($original_encoding);
		if (empty($original_encoding)) {
			$original_encoding = GENERAL_ENCODING;
		}
		// Le sigle euro n'est pas dans iso-8859-1, et par ailleurs iso-8859-15 n'est pas sur
		// tous les serveurs => on va donc contourner le problème.
		$euro_iso = mb_convert_encoding('€', "CP1252", 'utf-8');
		if ($new_encoding == $original_encoding) {
			return $string;
		} elseif ($new_encoding == 'iso-8859-1' && $original_encoding == 'utf-8') {
			return str_replace('-,/)[_', $euro_iso, utf8_decode(str_replace('€', '-,/)[_', $string)));
		} elseif ($new_encoding == 'utf-8' && $original_encoding == 'iso-8859-1') {
			return str_replace('-,/)[_', '€', $this->utf8_encode(str_replace($euro_iso, '-,/)[_', $string)));
		} elseif (function_exists('mb_convert_encoding') && (!function_exists('mb_list_encodings') || (in_array(String::strtoupper($new_encoding), mb_list_encodings()) && in_array(String::strtoupper($original_encoding), mb_list_encodings())))) {
			return mb_convert_encoding($string, $new_encoding, $original_encoding);
		} else {
			return $string;
		}
	}

	/**
	 * Convert all applicable characters to HTML entities
	 *
	 * @param string $string
	 * @param string $flags the optional second flags parameter lets you define what will be done with 'single' and "double" quotes
	 * @param string $charset defines character set used in conversion
	 * @return
	 */
	function htmlentities ($string, $flags = ENT_COMPAT, $charset = GENERAL_ENCODING)
	{
		return htmlentities($string, $flags, $charset);
	}

	/**
	 * Cette fonction sert si on veut afficher du contenu brut dans du HTML ou du XML : elle transforme les caractères spéciaux en entités HTML,
	 * et traite les incohérences qui pourraient créer des invalidités du contenu XHTML ou XML
	 *
	 * @param mixed $string
	 * @param boolean $suppr_endline
	 * @return
	 */
	function str_htmlentities($string, $suppr_endline = false)
	{
		if ($suppr_endline) {
			$string = str_replace(array("\r", "\n"), ' ', $string);
		}
		// On retire des caractères non SGML
		$string = str_replace(array('´'), array("'"), $string);
		$string = str_replace('&amp;#', '&#', String::htmlentities($string, ENT_QUOTES));
		$string = str_replace(array('€'), array('&euro;'), $string);
		if (String::strrpos($string, '&#', -5) !== false && String::strrpos($string, ';', -5) === false) {
			// Si le texte se termine par une entitée pas finie (ex : &#34) on la retire.
			$string = String::substr($string, 0, String::strrpos($string, '&#'));
		}
		return $string;
	}

	/**
	 * String::textEncode()
	 *
	 * @param mixed $text
	 * @param boolean $suppr_endline
	 * @param boolean $keep_html
	 * @return
	 */
	function textEncode($text, $suppr_endline = false, $keep_html = false)
	{
		if ($suppr_endline) {
			$text = str_replace(array("\r", "\n"), ' ', $text);
		}
		// On retire des caractères non SGML
		$text = str_replace(array('•', '™', '€', '’'), array('', '', 'E', "'"), $text);
		if ($keep_html) {
			// On en remplace que les & qui sont tout seuls. On ne touche pas au reste
			// ?! => assertion négative (et comme c'est une assertion, ça ne rentre pas de le résultat)
			$text = preg_replace('/&(?!#?[xX]?([0-9a-zA-Z]{1,9});)/', '&amp;', $text);
		} else {
			// On encode les entités, mais si il y en avait déjà, dans ce cas on se retrouverait avec une entité du type &amp;entité;
			// => On reconstruit donc ensuite les entités qui auraient été cassées grave au preg_replace
			// ?= => assertion positive (et comme c'est une assertion, ça ne rentre pas de le résultat)
			$text = preg_replace('/&amp;(?=#?[xX]?([0-9a-zA-Z]{1,9});)/', '&', String::htmlentities($text, ENT_QUOTES));
		}
		// Si le texte se termine par une entitée pas finie (ex : &#34) on la retire.
		// NB : comme l'entité n'est pas finie, on ne l'a pas détectée dans les preg_replace, donc elle est sous la forme &amp;#
		if (String::strpos($text, '&amp;#') !== false && String::strpos($text, '&amp;#') >= String::strlen($text)-9) {
			if (String::substr($text, String::strlen($text)-6, 6) == '&amp;#') {
				$text = String::substr($text, 0, String::strlen($text)-6);
			} elseif (String::substr($text, String::strlen($text)-7, 6) == '&amp;#') {
				$text = String::substr($text, 0, String::strlen($text)-7);
			} elseif (String::substr($text, String::strlen($text)-8, 6) == '&amp;#' && String::strpos(String::substr($text, String::strlen($text)-8), ';') === false) {
				$text = String::substr($text, 0, String::strlen($text)-8);
			} elseif (String::substr($text, String::strlen($text)-9, 6) == '&amp;#' && String::strpos(String::substr($text, String::strlen($text)-9), ';') === false) {
				$text = String::substr($text, 0, String::strlen($text)-8);
			}
		}
		return $text;
	}

	/**
	 * Encode une chaine de caractères pour affichage dans un value=""
	 *
	 * @param string $value
	 * @param mixed $flags
	 * @return
	 */
	function str_form_value($value, $flags = ENT_COMPAT)
	{
		if (function_exists('html_entity_decode') && (version_compare(PHP_VERSION, '5.0.0', '>=') || GENERAL_ENCODING == 'iso-8859-1')) {
			// Le 4è argument de htmlspecialchars appelé $double_encode n'est pas disponible avant PHP 5.2.3
			// Il faut donc appeler htmlentities_decode d'abord pour éviter le double encodage des entités HTML
			return htmlspecialchars(String::html_entity_decode($value, ENT_QUOTES, GENERAL_ENCODING), $flags, GENERAL_ENCODING);
		} else {
			// Version simplifiée si PHP < 4.3
			// ou si PHP >=4.3 et <5 car sinon pas de support de UTF-8
			return str_replace('"', '&quot;', $value);
		}
	}

	/**
	 * This function is String::htmlspecialchars_decode with php4 compatibility
	 *
	 * @param mixed $string
	 * @param mixed $style
	 * @return
	 */
	function htmlspecialchars_decode($string, $style = ENT_COMPAT)
	{
		$translation = array_flip(get_html_translation_table(HTML_SPECIALCHARS, $style));
		if ($style === ENT_QUOTES) {
			$translation['&#039;'] = '\'';
		}
		return strtr($string, $translation);
	}

	/**
	 * String::html_entity_decode()
	 *
	 * @param string $string
	 * @param mixed $quote_style
	 * @param mixed $charset
	 * @return
	 */
	function html_entity_decode($string, $quote_style = ENT_COMPAT, $charset = GENERAL_ENCODING)
	{
		if (version_compare(PHP_VERSION, '5.0.0', '>=')) {
			return html_entity_decode($string, $quote_style, $charset);
		} else {
			// Nécessaire pour éviter le bogue : "Warning: cannot yet handle MBCS in html entity decode"
			return html_entity_decode($string, $quote_style);
		}
	}

	/**
	 * String::html_entity_decode_if_needed()
	 *
	 * @param string $string
	 * @return
	 */
	function html_entity_decode_if_needed($string)
	{
		if (!empty($GLOBALS['compatibility_mode_with_htmlentities_encoding_content']) && String::strpos($string, '<') === false) {
			return String::html_entity_decode($string);
		} else {
			return $string;
		}
	}

	/**
	 * Fonction de compatibilité avec de vieilles versions de PEEL ou du contenu qui vient d'ailleurs
	 *
	 * @param mixed $string
	 * @return
	 */
	function nl2br_if_needed($string)
	{
		$has_no_br = String::strpos($string, '&lt;br') === false && String::strpos($string, '<br') === false;
		// Attention aux balises param
		$has_no_p = String::strpos($string, '&lt;p ') === false && String::strpos($string, '<p ') === false && String::strpos($string, '&lt;p&gt;') === false && String::strpos($string, '<p>') === false;
		$has_no_table = String::strpos($string, '&lt;table') === false && String::strpos($string, '<table') === false;
		$has_no_ul = String::strpos($string, '&lt;ul') === false && String::strpos($string, '<ul') === false;
		$has_no_script = String::strpos($string, '&lt;script') === false && String::strpos($string, '<script') === false;
		if ($has_no_br && $has_no_p && $has_no_table && $has_no_ul && $has_no_script) {
			$string = str_replace('<br>', '<br />', nl2br($string));
		}
		return $string;
	}

	/**
	 * Si vous avez des utilisateurs sous windows qui saisissent du contenu dans une interface qui l'insère
	 * dans une base en ISO-88591 vous risquez  d'avoir des surprises.
	 * Cette fonction permet de "nettoyer" l'encodage windows cp1552 en iso-88591 propre*
	 *
	 * @param mixed $string
	 * @return
	 */
	function utf8_encode($string)
	{
		$cp1252_map = array(
		   "\xc2\x80" => "\xe2\x82\xac", /* EURO SIGN */
		   "\xc2\x82" => "\xe2\x80\x9a", /* SINGLE LOW-9 QUOTATION MARK */
		   "\xc2\x83" => "\xc6\x92",     /* LATIN SMALL LETTER F WITH HOOK */
		   "\xc2\x84" => "\xe2\x80\x9e", /* DOUBLE LOW-9 QUOTATION MARK */
		   "\xc2\x85" => "\xe2\x80\xa6", /* HORIZONTAL ELLIPSIS */
		   "\xc2\x86" => "\xe2\x80\xa0", /* DAGGER */
		   "\xc2\x87" => "\xe2\x80\xa1", /* DOUBLE DAGGER */
		   "\xc2\x88" => "\xcb\x86",     /* MODIFIER LETTER CIRCUMFLEX ACCENT */
		   "\xc2\x89" => "\xe2\x80\xb0", /* PER MILLE SIGN */
		   "\xc2\x8a" => "\xc5\xa0",     /* LATIN CAPITAL LETTER S WITH CARON */
		   "\xc2\x8b" => "\xe2\x80\xb9", /* SINGLE LEFT-POINTING ANGLE QUOTATION */
		   "\xc2\x8c" => "\xc5\x92",     /* LATIN CAPITAL LIGATURE OE */
		   "\xc2\x8e" => "\xc5\xbd",     /* LATIN CAPITAL LETTER Z WITH CARON */
		   "\xc2\x91" => "\xe2\x80\x98", /* LEFT SINGLE QUOTATION MARK */
		   "\xc2\x92" => "\xe2\x80\x99", /* RIGHT SINGLE QUOTATION MARK */
		   "\xc2\x93" => "\xe2\x80\x9c", /* LEFT DOUBLE QUOTATION MARK */
		   "\xc2\x94" => "\xe2\x80\x9d", /* RIGHT DOUBLE QUOTATION MARK */
		   "\xc2\x95" => "\xe2\x80\xa2", /* BULLET */
		   "\xc2\x96" => "\xe2\x80\x93", /* EN DASH */
		   "\xc2\x97" => "\xe2\x80\x94", /* EM DASH */
		   "\xc2\x98" => "\xcb\x9c",     /* SMALL TILDE */
		   "\xc2\x99" => "\xe2\x84\xa2", /* TRADE MARK SIGN */
		   "\xc2\x9a" => "\xc5\xa1",     /* LATIN SMALL LETTER S WITH CARON */
		   "\xc2\x9b" => "\xe2\x80\xba", /* SINGLE RIGHT-POINTING ANGLE QUOTATION*/
		   "\xc2\x9c" => "\xc5\x93",     /* LATIN SMALL LIGATURE OE */
		   "\xc2\x9e" => "\xc5\xbe",     /* LATIN SMALL LETTER Z WITH CARON */
		   "\xc2\x9f" => "\xc5\xb8"      /* LATIN CAPITAL LETTER Y WITH DIAERESIS*/
		);
		return strtr(utf8_encode($string), $cp1252_map);
	}

	/**
	 * Fonction qui nettoie le HTML
	 *
	 * @param string $text
	 * @param integer $max_width
	 * @param boolean $allow_form
	 * @param boolean $allow_object
	 * @param boolean $allow_class
	 * @param mixed $additional_config
	 * @param boolean $safe
	 * @param mixed $additional_elements
	 * @return
	 */
	function getCleanHTML($text, $max_width = null, $allow_form = false, $allow_object = false, $allow_class = false, $additional_config = null, $safe = true, $additional_elements = null)
	{
		require_once($GLOBALS['dirroot'] . "/lib/fonctions/htmlawed.php");
		if(empty($text)) {
			return false;
		}
		$text = trim(str_replace(array('’', ' lang="EN-GB"', ' lang=EN-GB', ' mso-ansi-language: EN-GB', '<span>', '<SPAN>', '<font>', '<FONT>', '<strong></strong>', '<b></b>', '<STRONG></STRONG>', '<B></B>',
					"</td><br /><td", "<br />\n<td", "<br />\r\n<td", '<br /><td', '</td><br />', "<br />\n<tr", "<br />\r\n<tr", '<br /><tr', '</tr><br />',
					"</TD><br /><TD", "<br />\n<TD", "<br />\r\n<TD", '<br /><TD', "<br />\n<TR", "<br />\r\n<TR", '<br /><TR', '<TR><br />', '<tr><br />',
					'face=\'"arial,\'', 'sans-serif?', "<br /><LI", '<br /><TBODY>', '<br /><tbody>', '<TBODY><br />', '<tbody><br />', '<br /><COL', '<br /><col' , '<HR>', ' style=""', '<font>', '<span>', '<em><em>', '<b><b>', '<u><u>', '<i><i>',
					'    ', '   ', '  ', ' class=MsoNormal', ' class="MsoNormal"', ' style="mso-bidi-font-weight: normal"', ' style=""', ' align=""'),
				array("'", '', '', '', '', '', '', '', '', '', '', '',
					"</td><td", "\n<td", "\n<td", '<td', '</td>', "\n<tr", "\n<tr", '<tr', '</tr>',
					"</TD><TD", "\n<TD", "\n<TD", '<TD', "\n<TR", "\n<TR", '<TR', '<TR>', '<tr>',
					'face=\'arial\'', 'sans-serif', "<LI", '<TBODY>', '<tbody>', '<TBODY>', '<tbody>', '<COL', '<col' , '<hr />', '', '', '', '<em>', '<b>', '<u>', '<i>',
					' ', ' ', ' ', '', '', '', '', ''),
				$text));
		// $html_config['tidy']=1;
		// ATTENTION : clean_ms_char corrompt le UTF8, donc il ne faut pas l'appliquer (si c'était compatible on aurait mis la valeur 2)
		$html_config['clean_ms_char'] = 0;
		$html_config['schemes'] = 'href: ftp, http, https, mailto; classid:clsid; *:http, https';
		// $html_config['keep_bad']=1;
		if (!empty($safe)) {
			$html_config['safe'] = 1;
		}
		$html_config['comment'] = 1;
		// $html_config['show_setting']='settings';
		$html_config['elements'] = '*' . ($allow_object?'+object':'') . '' . ($allow_form?'':'-form') . '+embed-rb-rbc-rp-rt-rtc-ruby' . $additional_elements;
		$html_config['make_tag_strict'] = 0;
		$html_config['no_deprecated_attr'] = 0;
		if (empty($allow_class)) {
			if (empty($html_config['deny_attribute'])) {
				$html_config['deny_attribute'] = 'class';
			} else {
				$html_config['deny_attribute'] .= ',class';
			}
		}
		if (!empty($additional_config)) {
			$html_config += $additional_config;
		}

		$text_clean = htmLawed($text, $html_config);

		if (!empty($max_width)) {
			foreach(array('width="' => '"', 'width:' => 'px') as $begin_item => $end_item) {
				$text_end = String::strlen($text_clean);
				$new_text_clean = '';
				$pointer = 0;
				while (($begin_pointer = String::strpos($text_clean, $begin_item, $pointer)) !== false) {
					$end_pointer = String::strpos($text_clean, $end_item, $begin_pointer + String::strlen($begin_item));
					if ($end_pointer === false || $end_pointer < $pointer) {
						break;
					}
					$width = String::substr($text_clean, $begin_pointer + String::strlen($begin_item), $end_pointer - ($begin_pointer + String::strlen($begin_item)));
					$width = str_replace(array(' ', 'px'), array('', ''), $width);
					if (is_numeric($width) && $width > $max_width) {
						$width = $max_width;
					}
					$new_text_clean .= String::substr($text_clean, $pointer, $begin_pointer - $pointer) . $begin_item . $width;
					$pointer = $end_pointer;
				}
				$text_clean = $new_text_clean . String::substr($text_clean, $pointer, $text_end - $pointer);
			}
		}
		$text_clean = str_replace(array('td align="middle"', 'Verdana,;', '</td><', '</tr><', '<br /><', "\n\n\n\n", "\n\n\n", "\n\n", "\r\n\r\n\r\n", "\r\n\r\n", "\r\n", 'font-size: xx-large', 'font size="9"', 'font size="8"', 'font size="7"', ' style=""', ' align=""'),
			array('td align="center"', 'Verdana;', "</td>\n<", "</tr>\n<", "<br />\n<", "\n", "\n", "\n", "\n", "\n", "\n", 'font-size: x-large', 'font size="6"', 'font size="6"', 'font size="6"', '', ''), $text_clean);
		return $text_clean;
	}
	
	/**
	 * Fonction de compatibilité avec de vieilles versions de PEEL ou du contenu qui vient d'ailleurs
	 *
	 * @param mixed $string
	 * @return
	 */
	function fopen_utf8($filename, $mode) {
		$file = @fopen($filename, $mode);
		$bom = fread($file, 3);
		if ($bom != b"\xEF\xBB\xBF") {
			rewind($file);
		}
		return $file;
	}
}

?>
