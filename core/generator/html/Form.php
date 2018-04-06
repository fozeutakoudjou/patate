<?php
namespace form\generator;
class Form extends Block{
	protected $action="";
	protected $method="post";
	protected $headerUsed=true;
	protected $footerUsed=true;
	protected $submitUsed=true;
	protected $cancelUsed=true;
	protected $backUrl="";
	protected $cancelAsButton=false;
	protected $label="";
	protected $additionalHeaders;
	protected $additionalFooters;
	protected $submitButton;
	protected $cancelButton;
	/*protected $submitIcon="";
	protected $submitLabel="";
	protected $cancelIcon="";
	protected $cancelLabel="";*/
	public function __construct($contents = array()) {
		$this->cancelButton=new Command();
		$this->submitButton=new Command();
		$this->cancelButton->setLabel(_SAVE_TEXT_);
		$this->submitButton->setLabel(_CANCEL_TEXT_);
		parent::__construct($contents);
	}
	
	public function addAdditionalFooter($content) {
		$this->additionalFooters[] = $content;
	}
	
	public function addAdditionalHeader($content) {
		$this->additionalHeaders[] = $content;
	}
	public function getAdditionalFooter() {
		return $this->additionalFooters;
	}
	
	public function getAdditionalHeader($content) {
		return $this->additionalHeaders;
	}
	public function getAction(){
		return $this->action;
	}
	public function setAction($action){
		$this->action=$action;
	}
	public function getLabel(){
		return $this->label;
	}
	public function getMethod(){
		return $this->action;
	}
	public function setMethod($method){
		$this->method=$method;
	}
	public function getLabel(){
		return $this->label;
	}
	public function setLabel($label){
		$this->label=$label;
	}
	public function getSubmitButton(){
		return $this->submitButton;
	}
	public function getCancelButton(){
		return $this->cancelButton;
	}
	public function setBackUrl($backUrl) {
		$this->backUrl=$backUrl;
	}
	public function getBackUrl() {
		return $this->backUrl;
	}
	public function setHeaderUsed($headerUsed) {
		$this->headerUsed=$headerUsed;
	}
	public function isHeaderUsed() {
		return $this->headerUsed;
	}
	public function setFooterUsed($footerUsed) {
		$this->footerUsed=$footerUsed;
	}
	public function isFooterUsed() {
		return $this->footerUsed;
	}
	public function setSubmitUsed($submitUsed) {
		$this->submitUsed=$submitUsed;
	}
	public function isSubmitUsed() {
		return $this->submitUsed;
	}
	public function setCancelUsed($cancelUsed) {
		$this->cancelUsed=$cancelUsed;
	}
	public function isCancelUsed() {
		return $this->cancelUsed;
	}
	public function setCancelAsButton($cancelAsButton) {
		$this->cancelAsButton=$cancelAsButton;
	}
	/*public function setSubmitIcon($submitIcon) {
		$this->submitIcon=$submitIcon;
	}
	
	public function setSubmitLabel($submitLabel) {
		$this->submitLabel=$submitLabel;
	}
	
	public function setCancelIcon($cancelIcon) {
		$this->cancelIcon=$cancelIcon;
	}
	public function setCancelLabel($cancelLabel) {
		$this->cancelLabel=$cancelLabel;
	}*/
}