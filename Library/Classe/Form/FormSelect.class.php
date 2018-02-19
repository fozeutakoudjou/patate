<?php
/**
 * Description of FormSelect
 *
 * @author FFOZEU
 */
namespace Library\Classe\Form;

class FormSelect extends FormInput {

	protected $choices;
    protected $default;

    public function __construct($name, $form) {

		parent::__construct($name, $form);
		$this->choices = array();
        $this->default = '';
		unset($this->attrs['type']);
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

		$id = '';
		$label = '';
		if (!empty($this->label)) {

			list($for, $id) = self::_generate_for_id($this->form->auto_id(), $this->attrs['name']);
			$label = '<label'.$for.' class="control-label col-md-3">'.$this->label.$this->form->label_suffix().((isset($this->attrs['required'])&&$this->attrs['required'])?'<span class="required">*</span>':'<span class="no_required">&nbsp;&nbsp;</span>').'</label>'."\n$tab";
		}
		$errors = $this->error_messages->__toString($tab);
		if (!empty($errors)) { $errors = "\n".$errors; }
		$value = $this->form->get_bounded_data($this->attrs['name']);
		$value = (!empty($value)) ? $value : $this->value;

		$j = 0;
		$fields = array();
		foreach($this->choices as $v => $c) {

			if (is_array($c)) {

				$fields[] = "$tab\t".'<optgroup label="'.htmlspecialchars($v).'">';
				foreach($c as $vv => $cc) {

					$selected = '';
					if ($value == $vv || $this->default == $vv) { $selected = ' selected="selected"';  }
					$fields[] = "$tab\t\t".'<option value="'.htmlspecialchars($vv).'"'.$selected.'> '.$cc.'</option>';
				}
				$fields[] = "$tab\t".'</optgroup>';

			} else {
				$selected = '';
				if ($value == $v || $this->default == $v) { $selected = ' selected="selected"';  }
				$fields[] = "$tab\t".'<option value="'.htmlspecialchars($v).'"'.$selected.'>'.$c.'</option>';
			}
		}
        
        // helper text
        $helpertext = '';
        if($this->help_text_block != '')
            $helpertext = '<span class="help-block">'.$this->help_text_block.'</span>';
        
        if($this->help_text_inline != '')
            $helpertext = '<span class="help-inline">'.$this->help_text_block.'</span>';
        
		$field = '<div class="col-md-4"><select class="form-control"'.$id.$this->attrs.'>'."\n".implode("\n", $fields)."\n$tab</select>$helpertext</div>";
		return $tab.sprintf("%2\$s%1\$s%3\$s", $field, $label, $errors);
	}
    
    public function defaultSelect($data){
        if (!is_string($data)) {

			$data = func_get_args();
		}
		$this->default = $data;

		return $this;
    }
}

?>
