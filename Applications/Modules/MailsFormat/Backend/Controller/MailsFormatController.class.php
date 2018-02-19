<?php
/**
 * Description of MailsFormatController
 *
 * @author francis fozeu
 *
 */

namespace Applications\Modules\MailsFormat\Backend\Controller;

if( !defined('IN') ) die('Hacking Attempt');

use Helper\HelperBackController;
use Library\HttpRequest;
use Applications\Modules\MailsFormat\Form\MailsFormatForm;
use Library\Tools;

class MailsFormatController extends HelperBackController{
    // Inserer votre code ici!
    protected $name = "MailsFormat";
    protected $title = "Format des mails";
    
    public function executeCreate(HttpRequest $request) {
        $this->page->addVar('left_content', $this->leftcolumnMenu());
        $dataArray = array();
        $manager  = $this->managers->getManagerOf($this->name);
        $edit = false;
        $form = $this->name.'Form';
        //cas de l'édition
        if($request->getExists('id')){            
            $edit =true;
            $dataObjt = $manager->findById(intval($request->getValue('id')));
            $dataArray = $dataObjt->tabAttrib;
            $this->page->addVar('title', 'Modification d\'entrée');
        }else{
               $dataArray = $_POST;
        }
        $dataForm = MailsFormatForm::getForm($dataArray, $edit);
        if($request->getMethod('post')){
            if(!$request->getExists('id')){ 
                if($manager->add($request->getSendData($_POST))){
                    $this->page->addVar('infos', _RECCORD_SAVE_SUCCEFULL_);
                    $this->app()->httpResponse()->redirect('mails-format.html');
                }else{
                    $this->errors = _RECCORD_SAVE_FILED_;
                }
            }else{
                if($manager->update($request->getSendData($_POST),'id')){
                    $this->page->addVar('infos', _RECCORD_UPDATE_SUCCEFULL_);
                    $this->app()->httpResponse()->redirect('mails-format.html');
                }else{
                    $this->errors = _RECCORD_UPDATE_FILED_;
                }
            }
        }
        
        $this->page->addVar('errors', $this->errors);
        $this->page->addVar('dataForm', $dataForm);
        
    }
}
?>