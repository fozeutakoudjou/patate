<?php
namespace core\models;

class MailFormat extends Model{
	protected $id;
	protected $template;
	protected $active;
	protected $dateAdd;
	protected $dateUpdate;
	protected $title;
	protected $content;
	protected $definition = array(
		'entity' => 'mail_format',
		'primary' => 'id',
		'auto_increment' => true,
		'multilang' => true,
		'fields' => array(
			'template' => array('type' => self::TYPE_STRING, 'required' => true, 'unique' => true, 'validate' => 'isGenericName', 'maxSize' => '100'),
			'active' => array('type' => self::TYPE_BOOL, 'required' => true, 'validate' => 'isBool', 'default' => '1'),
			'dateAdd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
			'dateUpdate' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
			'title' => array('type' => self::TYPE_STRING, 'required' => true, 'lang' => true, 'validate' => 'isGenericName', 'maxSize' => '255'),
			'content' => array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml')
		)
	);	

	public function getId(){
		return $this->id;
	}
	public function setId($id){
		$this->id = $id;
	}
	public function getTemplate(){
		return $this->template;
	}
	public function setTemplate($template){
		$this->template = $template;
	}
	public function isActive(){
		return $this->active;
	}
	public function setActive($active){
		$this->active = $active;
	}
	public function getDateAdd(){
		return $this->dateAdd;
	}
	public function setDateAdd($dateAdd){
		$this->dateAdd = $dateAdd;
	}
	public function getDateUpdate(){
		return $this->dateUpdate;
	}
	public function setDateUpdate($dateUpdate){
		$this->dateUpdate = $dateUpdate;
	}
	public function getTitle(){
		return $this->title;
	}
	public function setTitle($title){
		$this->title = $title;
	}
	public function getContent(){
		return $this->content;
	}
	public function setContent($content){
		$this->content = $content;
	}
}