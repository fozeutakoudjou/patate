<?php
namespace core\generator\html\interfaces;
interface UrlCreator{
	public function createUrl($params);
	
	public function createSortUrl($params);
	
	public function createPaginationUrl($params);
	
	public function createActionUrl($params);
}