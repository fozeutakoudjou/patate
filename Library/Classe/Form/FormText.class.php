<?php
/**
 * Description of FormText
 *
 * @author FFOZEU
 */
namespace Library\Classe\Form;

class FormText extends FormInput {

	protected $autocomplete;

	public function __construct($name, $form) {

		parent::__construct($name, $form);
		$this->attrs['type'] = 'text';
		$this->autocomplete = true;
	}

	public function autocomplete($bool) {

		if (false === $bool) { $this->attrs['autocomplete'] = 'off'; $this->autocomplete = false; }
		else { unset($this->attrs['autocomplete']); $this->autocomplete = true; }
		return $this;

	}

	public function get_cleaned_value($value) {

		return parent::get_cleaned_value(preg_replace('`[\x00-\x19]`i', '', $value));
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

		if (true === $this->autocomplete) {

			$value = $this->form->get_bounded_data($this->attrs['name']);
			$value = (!empty($value)) ? $value : $this->value;
			$value = (!empty($value)) ? ' value="'.htmlspecialchars($value).'"' : '';

		} else {

			$value = '';
		}
        
        // icons
        $lefticons = '';
        $righticons = '';
        if(is_array($this->icons_left) && count($this->icons_left))
            foreach ($this->icons_left as $type => $value) 
                $lefticons .= '<i class="fa '.$type.'">'.$value.'</i>';
        if(is_array($this->icons_right) && count($this->icons_right))
            foreach ($this->icons_right as $type => $value) 
                $righticons .= '<i class="fa '.$type.'">'.$value.'</i>';
        
        // addons
        $leftaddons = '';
        $rightaddons = '';
        if(is_array($this->addons_left) && count($this->addons_left))
            foreach ($this->addons_left as $type => $value) 
                $leftaddons .= '<span class = "input-group-addon"><i class="fa '.$type.'">'.$value.'</i></span>';
        if(is_array($this->icons_right) && count($this->icons_right))
            foreach ($this->icons_right as $type => $value) 
                $rightaddons .= '<span class = "input-group-addon"><i class="fa '.$type.'">'.$value.'</i></span>';
        
        // helper text
        $helpertext = '';
        if($this->help_text_block != '')
            $helpertext = '<span class="help-block">'.$this->help_text_block.'</span>';
        
        if($this->help_text_inline != '')
            $helpertext = '<span class="help-inline">'.$this->help_text_block.'</span>';
        
		$field = '<div class="col-md-4">';
        if((is_array($this->icons_left) && count($this->icons_left)) || (is_array($this->icons_right) && count($this->icons_right))){
            if((is_array($this->addons_left) && count($this->addons_left)) || (is_array($this->icons_right) && count($this->icons_right)))
                $field .= '<div class="input-icon input-group">';
            else
                $field .= '<div class="input-icon">';
        }elseif((is_array($this->addons_left) && count($this->addons_left)) || (is_array($this->icons_right) && count($this->icons_right)))
            $field .= '<div class="input-group">';
                    
        $field .= $leftaddons.$lefticons.'<input class="form-control"'.$id.$this->attrs.$value.' />'.$righticons.$rightaddons;
        
        if((is_array($this->icons_left) && count($this->icons_left)) || (is_array($this->icons_right) && count($this->icons_right))
            || (is_array($this->addons_left) && count($this->addons_left)) || (is_array($this->icons_right) && count($this->icons_right)))
                   $field .= '</div>';
        $field .= $helpertext;
        $field .= '</div>';
		return $tab.sprintf("%2\$s%1\$s%3\$s", $field, $label, $errors);
	}
}

?>
