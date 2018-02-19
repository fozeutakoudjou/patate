<?php
/**
 * Description of ConfigSMTPForm
 *
 * @author Luc Alfred MBIDA
 *
 */

namespace Applications\Modules\ConfigSMTP\Form;

if( !defined('IN') ) die('Hacking Attempt');

use Library\Classe\Form\Form;                                    

class ConfigSMTPForm extends Form{
    // Inserer votre code ici!
    public static function getForm($dataArray = array()){
        $dataForm = new Form('SMTPConfig');

         $dataForm->add('Select', 'serveurMail')
               ->label('Serveur Mail')
               ->choices(Array(
                        'phpmail'=>'PHP Mail',
                        'sendmail'=>'Sendmail',
                        'smtp'=>'SMTP')
                     )
              ->required(true);

        $dataForm->add('Text', 'emailSite')
              ->label('E-mail du site')
              ->required(FALSE);


        $dataForm->add('Text', 'nomExpediteur')
              ->label('Nom de l\'expéditeur')
              ->required(FALSE);


        $dataForm->add('Radio', 'identificationSMTP')
                ->label('Identification SMTP')
                ->choices(Array(
                        '0'=>'Non',
                        '1'=>'Oui'
            ))
            ->required(false)
            ;

         $dataForm->add('Select', 'securiteSMTP')
               ->label('Sécurité SMTP')
               ->choices(Array(
                        'aucun'=>'Aucune',
                        'ssl'=>'SSL',
                        'tls'=>'TLS')
                     )
              ->required(FALSE);

         $dataForm->add('Text', 'portSMTP')
              ->label('Port SMTP')
              ->required(TRUE);

         $dataForm->add('Text', 'utilisateurSMTP')
              ->label('Utilisateur SMTP')
              ->required(TRUE);

         $dataForm->add('Password', 'passwordSMTP')
              ->label('mot de passe SMTP')
              ->required(TRUE);

         $dataForm->add('Text', 'serveurSMTP')
              ->label('serveur SMTP')
              ->required(TRUE);

        //$dataForm->add('Hidden', 'id')->value($dataArray['id']);
        $dataForm->add('Submit', 'submit')->value('Mettre à jour')->add_Class('btn green');

        $dataForm->bound($dataArray);

        return $dataForm;
    }
}
?>