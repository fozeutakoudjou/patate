<?php
namespace core\generator\html\interfaces;
interface UrlCreator{
	public function createUrl($params);
	
	public function createSortUrl($column, $way);
	
	public function createPaginationUrl($page);
	
	public function createActionUrl($params, $values);
	
	public function createLimitUrl($limit);
}