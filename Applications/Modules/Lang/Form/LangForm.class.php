<?php
    /**
     * Description of LangForm
     *
     * @author Luc Alfred MBIDA
     *
     */

    namespace Applications\Modules\Lang\Form;

    if( !defined('IN') ) die('Hacking Attempt');

    use Library\Classe\Form\Form;                                    

    class LangForm extends Form{
        // Inserer votre code ici!

         public static function getForm($dataArray = array(),$edit = false){
             $registerForm = new Form('createlang','post');
                    
             $registerForm->add('Text', 'name')
                         ->label('Libelle')
                         ->required(true);
            
             $registerForm->add('Text', 'iso_code')
                         ->label('Iso Code de la langue')
                         ->required(true);

             $registerForm->add('Text', 'language_code')
                         ->label('Code de la langue')
                         ->required(true);
            
             $registerForm->add('Radio', 'active')
                ->label('Activer')
                ->choices(Array(
                            '0'=>'Non',
                            '1'=>'Oui',
                            
                ))
                ->required(false)
                ;
             $registerForm->add('Radio', 'is_rtl')
                ->label('Is rtl')
                ->choices(Array(
                            '0'=>'Non',
                            '1'=>'Oui',
                ))
                ->required(false)
                ;
            
             $registerForm->add('Select', 'date_format_lite')
                ->label('Format de la date')
                ->choices(Array(
                            'm/d/Y'=>'m/d/Y',
                            'd/m/Y'=>'d/m/Y',
                            'd.m.Y'=>'d.m.Y',
                            'm.d.Y'=>'m.d.Y',
                            'm-d-Y'=>'m-d-Y',
                ))
                ->required(false);
            
             $registerForm->add('File', 'drapeau')
                         ->label('drapeau')
                         ->required(false);

             if($edit)
                $registerForm->add('Hidden', 'id_lang')->value($dataArray['id_lang']);

             $registerForm->add('Submit', 'submit')
                             ->value('Soumettre');
             $registerForm->bound($dataArray);

             return $registerForm;
         }
         
         
	
    }
?>