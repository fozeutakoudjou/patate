<?php
/**
 * Description of MailsFormatForm
 *
 * @author francis fozeu
 *
 */

namespace Applications\Modules\MailsFormat\Form;

if( !defined('IN') ) die('Hacking Attempt');

use Library\Classe\Form\Form;                                    

class MailsFormatForm extends Form{
    // Inserer votre code ici!
    
    public static function getForm($dataArray = array(), $edit=false){
        $registerForm = new Form('createupdate','post');
        
        $registerForm->add('Text', 'template')
                     ->label('Nom du template')
                     ->required(true);
        
        $registerForm->add('Text', 'title')
                     ->label('Titre du template')
                     ->required(true);
        
        $registerForm->add('Radio', 'active')
                    ->label('Actif')
                    ->choices(Array(
                                    '0'=>'Non',
                                    '1'=>'Oui'
                    ))
                    ->required(false);
        
        $registerForm->add('Textarea', 'content')
                     ->label('Format')
                     ->add_class('rte')
                     ->required(false);
        
        if($edit)
            $registerForm->add('Hidden', 'id')->value($dataArray['id']);

        $registerForm->add('Submit', 'submit')
                         ->value('Valider');
        $registerForm->bound($dataArray);

        return $registerForm;
    }  
}
?>