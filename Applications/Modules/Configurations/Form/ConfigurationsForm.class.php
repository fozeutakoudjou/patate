<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Applications\Modules\Configurations\Form;
/**
 * Description of ConfigurationsForm
 *
 * @author ffozeu
 */
use Library\Classe\Form\Form;

class ConfigurationsForm extends Form{
    //put your code here
	//Warning: add_Class have been had  version:2.0.1
    public static function getForm($dataArray = array(), $langs = array()){
        $dataForm = new Form('DefBgtest');
        
        $dataForm->add('Text', 'nomSite')
				 ->add_class('form-control')
                 ->label('Nom du Site')
                 ->required(true);

         $dataForm->add('Text', 'emailSite')
                  ->label('Email du Site')
				  ->add_class('form-control  ')
                  ->required(true); 
         
         $dataForm->add('Textarea', 'metaDescription')
                  ->label('Descrition du site')
				  ->add_class('form-control ')
                  ->required(false);
         

        $dataForm->add('Text', 'metaKeyword')
                  ->label('Mots clés du site')
				  ->add_class('form-control')
                  ->required(false);
        
        $dataForm->add('Radio', 'is_activewebservice')
                ->label('Activé le webservice')
                ->choices(Array(
                            '0'=>'Non',
                            '1'=>'Oui'
                ))
                ->addHelpText(" Avant d'activer le service web, vous devez vous assurer que <ol><li>la réécriture d'URL est activée sur le serveur.</li><li>les 5 méthodes GET, POST, PUT, DELETE et HEAD sont acceptées par ce serveur.</li></ol>")
                ->required(false)
                ;
        $dataForm->add('Radio', 'is_ssl')
                ->label('Activé le https (SSL)')
                ->choices(Array(
                            '0'=>'Non',
                            '1'=>'Oui'
                ))
                ->addHelpText("Avant d'activer le ssl assurez vous d'avoir un certificat SSL installé sur votre serveur")
                ->required(false)
                ;
        
        $dataForm->add('Radio', 'is_active')
                ->label('Activé le site')
                ->choices(Array(
                            '0'=>'Non',
                            '1'=>'Oui'
                ))
                ->required(false)
                ;
        $dataForm->add('Radio', 'lang')
                ->label('  Langue par default')
                ->choices($langs)
                ->required(false)
                ;
        
       $dataForm->add('Submit', 'submit')->value('Mettre à jour')->add_Class('btn green');
        
        $dataForm->bound($dataArray);
        
        return $dataForm;
    }
}

?>
