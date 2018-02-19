<?php
/**
 * Description of FormRadio
 *
 * @author FFOZEU
 */
namespace Library\Classe\Form;

class FormRadio extends FormInput {

	protected $choices;

	public function __construct($name, $form) {

		parent::__construct($name, $form);
		$this->attrs['type'] = 'radio';
		$this->choices = array();
	}

	public function choices($array) {

		if (!is_array($array)) {

			$array = func_get_args();
		}
		$this->choices = $array;

		return $this;
	}

	protected function _init() {

		if (!isset(self::$error_list['incorrect_value'])) {

			self::$error_list['incorrect_value'] = "La valeur fournie est interdite.";
		}
	}

	public function is_valid($value) {

		if (parent::is_valid($value)) {

			if ($this->required && !in_array($value, $this->choices) && !array_key_exists($value, $this->choices)) {
                
				$this->_error('incorrect_value');
				return false;
			}
			return true;
		}
		return false;
	}

	public function __toString() {

		$tab = func_num_args() > 0 ? func_get_arg(0) : '';
		
		$this->_generate_class();

		$i = $this->form->auto_id();
		//$span = (!empty($this->label)) ? '<span class="radio-elt">'.$this->label.$this->form->label_suffix().'<br /></span>' : '';
        $labelr = (!empty($this->label)) ? ('<label class="control-label col-md-3">'.$this->label.$this->form->label_suffix().((isset($this->attrs['required'])&&$this->attrs['required'])?'<span class="required">*</span>':'<span class="no_required">&nbsp;&nbsp;</span>').'</label>') : '';
        
		$errors = $this->error_messages->__toString($tab);
		if (!empty($errors)) { $errors = "\n".$errors; }
		$value = $this->form->get_bounded_data($this->attrs['name']);
		$value = (!empty($value)) ? $value : $this->value;

		$j = 0;
		$fields = array();
		foreach($this->choices as $v => $c) {

			$id = '';
			$labelb = '';
            $labele = '';
			if (!empty($i)) {

				list($for, $id) = self::_generate_for_id($this->form->auto_id().'_'.(++$j), $this->attrs['name']);
				$labelb = '<label class="radio-inline">';
                $labele = '</label>';
			}
			$this->attrs['value'] = htmlspecialchars($v);
			$checked = '';
			if ($value == $v) { $checked = ' checked="checked"';  }
                 $fields[] = $labelb.'<input'.$id.$this->attrs.$checked.' /> '.$c.$labele.($this->add_br_tag()?'<br />':'');

		}
         // helper text
        $helpertext = '';
        if($this->help_text_block != '')
            $helpertext = '<span class="help-block">'.$this->help_text_block.'</span>';
        
        if($this->help_text_inline != '')
            $helpertext = '<span class="help-inline">'.$this->help_text_block.'</span>';
        
		$field = "<div class='col-md-4'><div class='radio-list'>\n$tab".implode("\n$tab", $fields).'</div>'.$helpertext.'</div>';
		return $tab.sprintf("%2\$s%3\$s%1\$s", $field, $labelr, $errors);
	}
}

?>
