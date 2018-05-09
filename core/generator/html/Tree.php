<?php
namespace core\generator\html;
use core\constant\dao\OrderWay;
use core\Context;
class Tree extends Block{
	protected $headerTemplateFile;
	protected $footerTemplateFile;
	
	protected $itemFormatter;
	
	protected $dao;
	
	protected $parentField;
	protected $orderBy;
	protected $orderWay;
	
	protected $restrictions;
	protected $associations;
	
	public function __construct($dao, $headerTemplateFile, $footerTemplateFile, $restrictions = array(), $associations = array(), $itemFormatter = null, $parentField = 'idParent', $orderBy = 'position', $orderWay = OrderWay::ASC) {
		$this->setHeaderTemplateFile($headerTemplateFile);
		$this->setFooterTemplateFile($footerTemplateFile);
		$this->setItemFormatter($itemFormatter);
		$this->dao = $dao;
		$this->restrictions = $restrictions;
		$this->associations = $associations;
		$this->parentField = $parentField;
		$this->orderBy = $orderBy;
		$this->orderWay = $orderWay;
	}
	
	public function setHeaderTemplateFile($headerTemplateFile) {
		$this->headerTemplateFile=$headerTemplateFile;
	}
	public function setFooterTemplateFile($footerTemplateFile) {
		$this->footerTemplateFile=$footerTemplateFile;
	}
	public function getItemFormatter() {
		return $this->itemFormatter;
	}
	public function setItemFormatter($itemFormatter) {
		$this->itemFormatter=$itemFormatter;
	}
	
	public function buildTree($idParent = null, $items = null)
    {
        $output = '';
		$restrictions = $this->restrictions;
		$restrictions[$this->parentField] = $idParent;
        if ($items === null) {
            $items = $this->dao->getByFields($restrictions, false, self::$activeLang, true, false, $this->associations, 0, 0, $this->orderBy, $this->orderWay);
		}
        if (! empty($items)) {
            $isFirst = true;
            $isLast = false;
            $itemsCount = count($items);
			for ($i = 0; $i < $itemsCount; $i ++) {
				$object = $items[$i];
				$id = $object->getSinglePrimaryValue();
				$restrictions[$this->parentField] = $id;
				$childrens = $this->dao->getByFields($restrictions, false, self::$activeLang, true, false, $this->associations, 0, 0, $this->orderBy, $this->orderWay);
                $treeItem = new TreeItem($object, $childrens);
                $treeItem->setFirst($isFirst);
				if($this->itemFormatter!=null){
					$this->itemFormatter->format($treeItem);
				}
				if(!$treeItem->isRenderingCancelled()){
					self::$template->assign('item', $treeItem);
					$output .= self::$template->render($this->headerTemplateFile) . self::buildTree($id, $childrens);
					$isFirst = false;
					$treeItem->setLast(($i == ($itemsCount - 1)));
					
					self::$template->assign('item', $treeItem);
					$output .= self::$template->render($this->footerTemplateFile);
				}
            }
        }
        return $output;
    }
	
	public function generateContent()
    {
		return $this->buildTree();
	}
}