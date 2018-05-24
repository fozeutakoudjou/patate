<?php
namespace core\generator\html;
class InputCustomContent extends Field{
	protected $templateFile = 'generator/input_custom_content';
	protected $content;
	public function __construct($content, $name, $label='', $fieldOnly = false) {
		parent::__construct($name, $label);
		$this->setContent($content);
		$this->setFieldOnly($fieldOnly);
	}
	public function getContent() {
		return $this->content;
	}
	
	public function setContent($content) {
		$this->content = $content;
	}
}