<?php
namespace Library;

/**
 * Description of Mail
 *
 * @author ffozeu
 */
use Library\PHPMailer\PHPMailer;

class Mail extends ApplicationComponent{
    //put your code here
    
    public function send(array $param, $variable = array(), $template = "",$directory = _SITE_MAIL_TPL_DIR_){
        $mail = new PHPMailer();
       
        $mail->SMTPDebug  = 1;
        
        $mail->CharSet = "UTF-8";
        $filename = _SITE_CONFIG_DIR_.'mailconfig.xml';
        if(file_exists($filename)){
            $xml = simplexml_load_file($filename);
            $mailConfig = $xml->items;
            switch ($mailConfig->serveurMail) {        
                case 'phpmail':
                     $mail->IsMail();
                    break;

                case 'sendmail':
                     $mail->IsSMTP();
                    break;

                 case 'smtp':
                    $mail->IsSMTP();
                    break;
            }
            
            //on charge le format général de mail si le template vient de la database
            if(file_exists($directory.$template))
                $body = file_get_contents($directory.$template);
            else {
                $body = file_get_contents($directory.'mails.html');
                //on remplace le contenu de la variable content_mail par le template venant de la database
                $body = str_replace('content_mail', $template, $body);
            }
                
            // Titre et logo
            $variable["fw_name"]     = _SITE_TITLE;
            $variable["fw_logo"]     = _SITE_LOGO_MAIL_;
            $variable["site_url"]    = _BASE_URI_;

            foreach ($variable as $key => $value) {
                $body = str_replace($key, $value, $body);
            }
            //si on utilise une identification smtp
            if($mailConfig->identificationSMTP){
                $mail->SMTPAuth   = true;
                $mail->Username   = (string) $mailConfig->utilisateurSMTP;
                $mail->Password   = (string) $mailConfig->passwordSMTP;
            }
            //type de connexion
            if( $mailConfig->securiteSMTP != "aucun")
                $mail->SMTPSecure = (string) $mailConfig->securiteSMTP; 
        
            $mail->Host       = (string) $mailConfig->serveurSMTP;
            $mail->Port       = (int) $mailConfig->portSMTP;
            
            $mail->From       = $param['expediteur'];
            $mail->FromName   = $param['Nomexpediteur'];
            $mail->Subject    = $param['subjet'];

            $mail->WordWrap   = 50; // set word wrap

            $mail->MsgHTML($body);

            if($mailConfig->emailSite != "")
                $mail->AddReplyTo($mailConfig->emailSite, $mailConfig->nomExpediteur);

            if(isset($param['address']) && !empty($param['address']))
				if (is_array($param['address'])){/* cas d'un envoi multiple*/
					$nom_adress = (isset($param['Nomaddress']))?$param['Nomaddress']:'crystals';
					foreach ($param['address'] as $value)
						$mail->AddAddress($value,$nom_adress);
				}else
					$mail->AddAddress($param['address'],(isset($param['Nomaddress']))?$param['Nomaddress']:'crystals');
			else
				return  'Mailer Error: Adress is empty';

            if(!$mail->Send()) {
              return  "Mailer Error: " . $mail->ErrorInfo;
            } else {
              return "Votre message a été envoyé avec succès!!!";
            }
        }
    }
}

?>
