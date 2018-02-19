<?php
/**
 * Description of FormTextarea
 *
 * @author FFOZEU
 */
namespace Library\Classe\Form;

class FormTextarea extends FormField {

	public function cols($value) {

		if (ctype_digit((string)$value) && $value > 0) { $this->attrs['cols'] = $value; }
		else { unset($this->attrs['cols']); }
		return $this;
	}

	public function get_cleaned_value($value) {

		return preg_replace('`[\x00\x08-\x0b\x0c\x0e\x19]`i', '', $value);
	}

	public function rows($value) {

		if (ctype_digit((string)$value) && $value > 0) { $this->attrs['rows'] = $value; }
		else { unset($this->attrs['rows']); }
		return $this;
	}

	public function __toString() {

		$tab = func_num_args() > 0 ? func_get_arg(0) : '';
		
		$this->_generate_class();

		$id = '';
		$label = '';
		if (!empty($this->label)) {

			list($for, $id) = self::_generate_for_id($this->form->auto_id(), $this->attrs['name']);
			$label = '<label'.$for.' class="col-md-3 control-label textarea_label">'.$this->label.$this->form->label_suffix().'</label>'."\n$tab";
		}
		$errors = $this->error_messages->__toString($tab);
		if (!empty($errors)) { $errors = "\n".$errors; }
		$value = $this->form->get_bounded_data($this->attrs['name']);
		$value = (!empty($value)) ? htmlspecialchars($value) : htmlspecialchars($this->value);

         // helper text
        $helpertext = '';
        if($this->help_text_block != '')
            $helpertext = '<span class="help-block">'.$this->help_text_block.'</span>';
        
        if($this->help_text_inline != '')
            $helpertext = '<span class="help-inline">'.$this->help_text_block.'</span>';
        
		$field = '<div class="col-md-4"><textarea'.$id.$this->attrs.'>'.$value.'</textarea>'.$helpertext.'</div>';
		return $tab.sprintf("%2\$s%1\$s%3\$s", $field, $label, $errors);
	}
}

?>
