<?php
/**
 * Description of FormError
 *
 * @author FFOZEU
 */
namespace Library\Classe;

class FormError {
    
    var $error = array();

	/**
	 * formError::bg()
	 *
	 * @param mixed $name
	 * @return
	 */
	function bg($name)
	{
		if (isset($this->error[$name])) {
			return ' class="form_item_error"';
		}
	}

	/**
	 * formError::text()
	 *
	 * @param mixed $name
	 * @return
	 */
	function text($name = null)
	{
		if (!empty($name) && isset($this->error[$name])) {
			return '<div class="global_error">' . ((!empty($this->error[$name])) ? $this->error[$name] : $GLOBALS['strEmptyField']) . '</div>';
		} elseif (empty($name) && $this->count()) {
			$output = '<div class="global_error">';
			foreach($this->error as $this_name => $this_value) {
				if (!empty($this->error[$this_name])) {
					$output .= $this->error[$this_name];
				} else {
					$output .= '[' . $this_name . ']' . BEFORE_TWO_POINTS . ': ' . $GLOBALS['strEmptyField'];
				}
				$output .= '<br />';
			}
			$output .= '</div>';
			return $output;
		} else {
			return false;
		}
	}

	/**
	 * formError::add()
	 *
	 * @param mixed $name
	 * @param integer $text
	 * @return
	 */
	function add($name, $text = 0)
	{
		$this->error[$name] = $text ? $text : '';
	}

	/**
	 * formError::has_error()
	 *
	 * @param mixed $name
	 * @return
	 */
	function has_error($name)
	{
		return isset($this->error[$name]);
	}

	/**
	 * formError::count()
	 *
	 * @return
	 */
	function count()
	{
		return count($this->error);
	}

	/**
	 * Valide les information
	 *
	 * @param array $frm Array with all fields data
	 * @param object $form_error_object
	 * @return
	 */
	function valide_form(&$frm, $empty_field_messages_array = array())
	{
		foreach($empty_field_messages_array as $this_field => $this_message) {
			if (empty($frm[$this_field])) {
				$this->add($this_field, $this_message);
			}
		}
	}
}

?>
