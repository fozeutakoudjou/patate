<?php
    /**
     * Description of LangController
     *
     * @author Luc Alfred MBIDA
     *
     */

    namespace Applications\Modules\Lang\Backend\Controller;

    if( !defined('IN') ) die('Hacking Attempt');

    use Helper\HelperBackController;
    use Library\HttpRequest;
    use Applications\Modules\Lang\Form\LangForm;
    use Library\Tools;

    class LangController extends HelperBackController{
        // Inserer votre code ici!
        protected $name = "Lang";
        
        
         private function leftcolumn(){
            $out = array();
            $out['lang-create.html']    = $this->l('Ajouter une langue');
            $out['lang.html']           = $this->l('Liste des langues');
            $out['traduire-lang.html']  = $this->l('Traduite ce module');
          
            return $this->page->addVar('left_content', $out);
        }
        
        private function rightcolumn(){
            $out ='Gérez vos Langues.';
            return $this->page->addVar('right_content', $out);
        }
        
        public function executeLang(HttpRequest $request) {
            $manager = $this->managers->getManagerOf('Lang');
            $this->page->addVar('title', 'Listing des langues');
            $this->leftcolumn();
            $this->rightcolumn();
            $data = $manager->findAll2();
            $this->page->addVar('datalist', $data);
            $this->page->addVar('pagination', $this->pagination);
        }
        
        public function executeLangcreate(HttpRequest $request) {
            $this->page->addVar('title', 'Ajouter une langue');
            $this->leftcolumn();
            $this->rightcolumn();
            $dataArray   = array();
            
            $edit   = false;
            $manager     = $this->managers->getManagerOf('Lang');
            
            if($request->getExists('id_lang')){            
                $edit =true;
                $dataObjt = $manager->findById2('id_lang', intval($request->getValue('id_lang')));
                $dataArray = $dataObjt[0]->tabAttrib;
               
                $this->page->addVar('title', 'Modifier une langue');
            }else{
                $dataArray = $_POST;
            }
            $dataForm = LangForm::getForm($dataArray, $edit);

            if($request->getMethod('post')){
                if(!$request->getExists('id_lang')){
                    $obj = $manager->findByName('language_code', $request->getValue('language_code'));
                    if(is_array($obj) && count($obj))
                        $this->errors = _RECCORD_SAVE_FILED_." ce code de langue  existe déjà!";
                    else{
                        $this->errors = _RECCORD_SAVE_FILED_;
                        if($manager->add($request->getSendData($_POST))){
                            $filedata = Tools::createFileImage('drapeau', _SITE_UPLOAD_DIR_.'Lang/', 16, 11, $request->getValue('language_code'));
                            $this->app()->httpResponse()->redirect('lang.html');
                        }
                        else
                            $this->errors = _RECCORD_SAVE_FILED_;
                    }
                }else{
                    if($manager->update($request->getSendData($_POST),'id_lang')){
                        $this->app()->httpResponse()->redirect('lang.html');
                    }else{
                        $this->errors = _RECCORD_UPDATE_FILED_;
                    }
                }
               
            }
                
            $this->page->addVar('errors', $this->errors);
            $this->page->addVar('dataForm', $dataForm);
        }
        
        public function executeResults(HttpRequest $request) {
            $out = array();
            $manager = $this->managers->getManagerOf('Lang');
                  
            if($request->getValue('actionselect')!=""){
                switch ($request->getValue('actionselect')) {

                    case 'delete':
                        if(isset($_POST['eltcheck']))
                            $result = $manager->deleteChecked($_POST['eltcheck'], 'id_lang');
                        break;
                     case 'active':
                        if(isset($_POST['eltcheck']))
                            $result = $manager->ActiveChecked($_POST['eltcheck'],'id_lang','active');
                        break;
                     case 'unactive':
                        if(isset($_POST['eltcheck']))
                            $result = $manager->UnActiveChecked($_POST['eltcheck'],'id_lang','active ');
                        break;
                    default:
                        break;
                }
            }
            
           
            if($request->getValue('searchzone') != "" && $request->getValue('searchzone') != "recherche" ){
                 $out = array();
                 $out[] = 'id_lang';
                 $out[] = 'name';
                 $out[] = 'iso_code';
                 $data = $manager->searchCriteria($out, $request->getValue('searchzone'));
            }  else {
                $data = $manager->findAll2();
            }
            
            $this->page->addVar('datalist', $data); 
            $this->page->addVar('pagination', $this->pagination);
        }
        
        public function executeDelete(HttpRequest $request){
            $manager = $this->managers->getManagerOf('Lang');
            if($request->getExists('id_lang')){
                $out['id_lang'] = $request->getData('id_lang');
                if($manager->delete($out)){
                    $this->page->addVar('infos', _RECCORD_DELETE_SUCCEFULL_); 
                }else{
                    $this->page->addVar('errors', _RECCORD_DELETE_FILED_);
                }
                $this->app()->httpResponse()->redirect('lang.html');

            }
            $this->app()->httpResponse()->redirect('lang.html');
        }
    }
?>