<?php
/**
 * Description of MenuController
 * 
 */
 namespace Applications\Modules\Menu\Backend\Controller;
 
  if( !defined('IN') ) die('Hacking Attempt');

    use Helper\HelperBackController;
    use Library\HttpRequest;
    use Applications\Modules\Menu\Form\MenuForm;
    use Library\Tools;

 
 class MenuController extends HelperBackController {
 	
 	private function leftcolumn(){
 		$out = array();
 		$out['cree-menu.html']   = $this->l('Ajouter');
 		$out['list-menu.html']     =  $this->l('Listing');
 	
 		return $this->page->addVar('left_content', $out);
 	}
 	
 	public function executeAddMenu(HttpRequest $request){
 		// On ajoute une définition pour le titre
 		$this->page->addVar('title', 'Ajout de module et lien');
 		$this->leftcolumn();
 		$manager= $this->managers->getManagerOf('Menu');
 		$Menu = array();
 		$managerMenu = $this->managers->getManagerOf('Menu');
 		
 		//$this->rightcolumn(); 
 		//$this->getMenu("F");
 		//$this->LoadAutomaticMenu('B');
 		//$this->LoadAutomaticMenu('F'); 
 		$edit =false;
 		$dataArray = array();
 		$data["NULL"]="Selectionner un Module";
 		$data['Autre'] = 'Autre';
 		
 		$datalist=$this->getDir(_MODULES_DIR_);
 		foreach ($datalist as $key){
 			$data[$key]=$key;
 		}
 		if($request->getExists('id')){
 			$edit =true;
 			$dataObjt = $manager->findById(intval($request->getValue('id')));
 			$dataArray = $dataObjt->tabAttrib;
 			//var_dump($dataArray);die();
 			$this->page->addVar('title',  $this->l('Modification'));
 		}else{
 			$dataArray = $_POST;
 			}
 		
 		$dataForm = MenuForm::getForm($dataArray,$edit, $data);
 		
 		if($request->getMethod('post')){
 			if(!$request->getExists('id')){
 			 $manager->add2($request->getSendData($_POST));
 					$this->app()->httpResponse()->redirect('list-menu.html');
 			}else{
 				if($manager->update($request->getSendData($_POST),'id')){
 					$this->app()->httpResponse()->redirect('list-menu.html');
 				}else{
 					$this->errors = _RECCORD_UPDATE_FILED_;
 				}
 			}
 			
 			
 		}
 		$this->page->addVar('dataForm', $dataForm);
 	}
 	
 	
 	function executeListMenu(HttpRequest $request){
 		$manager = $this->managers->getManagerOf('Menu');
 		$this->page->addVar("title",  $this->l("Listing des Menu"));
 		$this->leftcolumn();
 	
 		$data = $manager->getTableAtt();
 		$this->page->addVar('datalist', $data);
 		//$this->page->addVar('pagination', $this->pagination);
 	}
 	
 	
 	public function executeLoadAction(HttpRequest $request){
 		$manager =  $this->managers->getManagerOf('Menu');
 		$modules = $request->getValue('modules');
 		$typeLink = $request->getValue('typlink');
 		$path = _MODULES_DIR_.$modules.'\\Config\\';
 		if($typeLink){
 			$path .='F';
 			$link=1;
 		}else {
 			$path .='B';
 			$link = 0;
 		}
 			
 			
 		$filename =$path.$modules.'Route.xml';
 		$fields = array();
 		$fields2=array();
 		$str="enable";
 		$inc=0;
 		$data['NULL']="NULL";
 		//Recherche des modules deja dispo dans la BD respectant la contrainte typelink
 		$datalist =$manager->findWithContraint("type_link",$link);
 		foreach ($datalist as $key){
 			$data[$key->getId()]=$key->getTitre();
 					 }
 		//var_dump($data);die();
 		$fields2['data']=$data;
 		$fields2['Autre']=$modules;
 		if(file_exists($filename)){
 			$xml = simplexml_load_file($filename);
 			$param = $xml;
 			//Ici on parcours le fichier pars� et on ne retien que les routes ayant le champ visible=enable
 			foreach ($param as $key => $value) {
 				foreach($value->attributes() as $key0 => $value0){
 					switch ($key0){
 						case "url":
 								$fields[$key0]=$value0;
 						
 						case "visible":
 								if (strcasecmp($value0,$str) == 0 && ($manager->findWithContraint2("lien",$fields['url'],"type_link",$link)==null)){
 									$fields2[$inc]=$fields;
 									$inc+=1;
 								}
 					
 					}
 				
 				}
 				unset($fields);
 				
 			}
 			
 			
 		}
 		$this->page->addVar('results', $fields2);
 		
 	}
 	
 	public function executeDeleteMenu(HttpRequest $request){
 		$manager = $this->managers->getManagerOf('Menu');
 		if($request->getExists('id')){
 			$out['id'] = $request->getData('id');
 			if($manager->deleteCascade($out)){
 				$this->page->addVar('infos', _RECCORD_DELETE_SUCCEFULL_);
 			}else{
 				$this->page->addVar('errors', _RECCORD_DELETE_FILED_);
 			}
 			$this->app()->httpResponse()->redirect('list-menu.html');
 		}
 		$this->app()->httpResponse()->redirect('list-menu.html');
 	}
 	
 	public function executeEditMenu(HttpRequest $request){
 		$manager = $this->managers->getManagerOf('Menu');
 		$this->page->addVar("title",  $this->l("Modification d'un Menu"));
 		if($request->getExists('id')){
 			$out = $request->getData('id');
 			$link=$manager->getLink($out);//recherche du type lien correspondant
 			 foreach ($link as $link2):
 			 	$lien=$link2->getType_link();
 			 endforeach;
 			 // var_dump($lien); die();
 			 $data=$manager->FindCascade($out);//recherche les module avec ID=$out et tout ses descendance
 			 $dataM['Autre'] = 'Autre';
 			 $datalist=$this->getDir(_MODULES_DIR_);//recuper tout les module disponible
 			 foreach ($datalist as $key){
 			 	$dataM[$key]=$key;
 			 }
 			 $datalist =$manager->findWithContraint("type_link",$lien);//Recherche des modules deja dispo dans la BD respectant la contrainte typelink
 			 $dataP['NULL']="NULL";
 			 foreach ($datalist as $key){
 			 	$dataP[$key->getId()]=$key->getTitre();
 			 }
 			if(!empty($data)){
 				$data['Module']=$dataM;
 				$data['Parent']=$dataP;
 				$this->page->addVar('data', $data);
 				if($request->getMethod('post')){
 				       $manager->update2($request->getSendData($_POST),'id');
 						$this->app()->httpResponse()->redirect('list-menu.html');
 				  
 				}
 			 }else 
 				$this->app()->httpResponse()->redirect('list-menu.html');
 		}
 		
 	}
 	
 	protected function init(){
 	
 		$this->tabJS[_THEME_JS_MOD_DIR_.$this->name.'/B'.$this->name.'.js'] = 'screen';
 	
 		parent::init();
 	}

 }
?>