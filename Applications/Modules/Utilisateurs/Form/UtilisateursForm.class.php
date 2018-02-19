<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Applications\Modules\Utilisateurs\Form;

if( !defined('IN') ) die('Hacking Attempt');
/**
 * Description of UtilisateursForm
 *
 * @author ffozeu
 */
 
use Library\Classe\Form\Form;

class UtilisateursForm extends Form{ 
    
   public static function getForm($dataArray = array(), $edit=false, $groupe=array(), $admin=false){
        $registerForm = new Form('createuser','post');
        //$registerForm->action('add-user.html');
		$required = true;

        $registerForm->add('Text', 'nom')
                     ->label('Nom')
                     ->required(true);
        
        $registerForm->add('Text', 'prenom')
                     ->label('Prenom')
                     ->required(false);
        
         $registerForm->add('Text', 'pseudo')
                     ->label('Pseudo')
                     ->required(true);
		  $registerForm->add('Text', 'email')
                     ->label('Adresse email')
                    // ->addLeftAddons(array('fa-envelope'=>''))
                     ->required(true);
        if($edit)
             $required = false;
        
		
		 
        $registerForm->add('Password', 'password')
                     ->label('Mot de passe')
                     //->addLeftAddons(array('fa-user'=>''))
                     ->required($required);
        
         $registerForm->add('Password', 'verif_mdp')
                     ->label('Vérifier le  Mot de passe')
                    // ->addLeftIcons(array('fa-user'=>''))
                     ->addHelpText('Confirmez votre mot de passe')
                     ->required($required);
         
        $registerForm->add('Text', 'pays')
                     ->label('Pays')
                     ->required(false);
        
        $registerForm->add('Text', 'ville')
                     ->label('Ville')
                     ->required(false);
        
        $registerForm->add('Text', 'adresse')
                     ->label('Adresse')
                     ->required(true);
        
        $registerForm->add('Text', 'code_postal')
                     ->label('Code Postal')
                     ->required(false);
        
        $registerForm->add('Text', 'tel1')
                     ->label('Téléphone 1')
                     ->required(true);
        
        $registerForm->add('Text', 'tel2')
                     ->label('Téléphone 2')
                     ->required(false);
					 
        if($admin){
               
                $registerForm->add('File', 'avatarFile')
                    ->label('Avatar')
                    ->required(false);

                $registerForm->add('Radio', 'is_active')
                    ->label('Activer ce compte?')
                    ->choices(Array(
                                    '0'=>'Non',
                                    '1'=>'Oui'
                    ))
                    ->required(false);
        }        
        
        $registerForm->add('Textarea', 'infos_complementaires')
                     ->label('Informations Complémentaires')
                     ->add_class('champ_text form-control')
                     ->required(false);
                    
        if($edit){
            //var_dump($dataArray);
            $registerForm->add('Hidden', 'id')->value($dataArray['id']);
			$registerForm->add('Hidden', 'avatar')->value($dataArray['avatar']);
			//if($admin){
				//$registerForm->add('Hidden', 'avatar')->value($dataArray['avatar']);
           	 	$registerForm->add('Hidden', 'passwordH')->value($dataArray['password']);
			//}
			
            if(!$admin)
                $registerForm->add('Submit', 'submit')
                         ->value('Modifier')
                         ->add_class('modifier');
        }else{
            if(!$admin){
                $registerForm->add('Submit', 'submit')
                        ->value('je m\'inscris')
                        ->add_class('envoie') ;
            }
        }
       
                     
        $registerForm->closeForm(false);
        
        $registerForm->bound($dataArray);
        
        return $registerForm;
    }  
    
     public static function getFormgroup($dataArray = array(), $edit=false){
        $dataForm = new Form('DefBg');
        
        $dataForm->add('Text', 'nom_groupe')
                ->label('Libelé')
                ->required(true);
        $dataForm->add('Text', 'technical')
                ->label('nom technique')
                ->required(true);
        
        if($edit){
             $dataForm->add('Hidden', 'id')->value($dataArray['id']);
        }
        
        $dataForm->closeForm(false);
        
        $dataForm->bound($dataArray);
        
        return $dataForm;
     }
    
}

?>
