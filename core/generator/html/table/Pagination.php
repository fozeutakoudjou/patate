<?php
namespace core\generator\html\table;
use core\generator\html\Block;
use core\generator\html\Link;
use core\generator\html\Icon;
use core\Context;
class Pagination extends Block{
	protected $templateFile = 'generator/table/pagination';
	
	protected $totalResult;
	protected $itemsPerPage;
	protected $currentPage;
	protected $maxPageDisplayed;
	protected $urlCreator;
	protected $totalPage;
	
	public function __construct($table = null, $totalResult = 0, $itemsPerPage = 20, $currentPage = 1, $maxPageDisplayed = 5, $urlCreator = null) {
		if($table!=null){
			$totalResult = $table->getTotalResult();
			$itemsPerPage = $table->getItemsPerPage();
			$currentPage = $table->getCurrentPage();
			$maxPageDisplayed = $table->getMaxPageDisplayed();
			$urlCreator = $table->getUrlCreator();
		}
		$this->setTotalResult($totalResult);
		$this->setItemsPerPage($itemsPerPage);
		$this->setCurrentPage($currentPage);
		$this->setMaxPageDisplayed($maxPageDisplayed);
		$this->setUrlCreator($urlCreator);
	}
	public function isFirstEnabled() {
		return $this->currentPage !=1;
	}
	public function isLastEnabled() {
		return $this->currentPage !=$this->totalPage;
	}
	public function isPrevEnabled() {
		return $this->currentPage !=$this->getPrevPage();
	}
	public function isNextEnabled() {
		return $this->currentPage !=$this->getNextPage();
	}
	public function getNextPage() {
		return (($this->currentPage+1)>$this->totalPage) ? $this->currentPage : $this->currentPage+1;
	}
	public function getPrevPage() {
		return (($this->currentPage-1)==0) ? $this->currentPage : $this->currentPage-1;
	}
	public function createFirstLink() {
		return $this->createLink(1, $this->isFirstEnabled(), false, new Icon('angle-double-left'));
	}
	public function createLastLink() {
		return $this->createLink($this->totalPage, $this->isLastEnabled(), false, new Icon('angle-double-right'));
	}
	public function createPrevLink() {
		return $this->createLink($this->getPrevPage(), $this->isPrevEnabled(), false, new Icon('angle-left'));
	}
	public function createNextLink() {
		return $this->createLink($this->getNextPage(), $this->isNextEnabled(), false, new Icon('angle-right'));
	}
	public function createLink($page, $enabled=true, $useLabel = true, $icon = null) {
		$label = $useLabel ? $page : '';
		$href = $enabled ? $this->urlCreator->createPaginationUrl($page) : '#';
		$link = new Link($label, $href, $icon);
		return $link;
	}
	public function getStartPage() {
		return $this->currentPage;
	}
	public function getEndPage() {
		$start = $this->getStartPage();
		$end = $start -1 + $this->maxPageDisplayed;
		return (($this->totalPage - $end)<0) ? $this->totalPage : $end;
	}
	public function prepare() {
		if(!empty($this->itemsPerPage)){
			$this->totalPage = ceil($this->totalResult / $this->itemsPerPage);
		}
	}
	public function generate() {
		$this->prepare();
		if(empty($this->itemsPerPage) || ($this->totalPage<=1)){
			$this->forceContent('');
		}
		return parent::generate();
	}
	public function drawActive($page) {
		return ($this->currentPage == $page) ? 'active' : '';
	}
	
	
	
	public function getTotalPage() {
		return $this->totalPage;
	}
	public function getMaxPageDisplayed() {
		return $this->maxPageDisplayed;
	}
	public function setMaxPageDisplayed($maxPageDisplayed) {
		$this->maxPageDisplayed=$maxPageDisplayed;
	}
	
	public function getTotalResult() {
		return $this->totalResult;
	}
	public function setTotalResult($totalResult) {
		$this->totalResult=$totalResult;
	}
	
	public function getItemsPerPage() {
		return $this->itemsPerPage;
	}
	public function setItemsPerPage($itemsPerPage) {
		$this->itemsPerPage=$itemsPerPage;
	}
	
	public function getCurrentPage() {
		return $this->currentPage;
	}
	public function setCurrentPage($currentPage) {
		$this->currentPage=$currentPage;
	}
	
	public function setUrlCreator($urlCreator) {
		$this->urlCreator=$urlCreator;
	}
	
	public function getUrlCreator() {
		return $this->urlCreator;
	}
}