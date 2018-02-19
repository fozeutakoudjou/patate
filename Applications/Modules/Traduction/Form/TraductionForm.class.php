<?php
/**
* Description of TraductionForm
*
* @author zokankais
*
*/

namespace Applications\Modules\Traduction\Form;

if( !defined('IN') ) die('Hacking Attempt');

use Library\Classe\Form\Form;                                    

class TraductionForm extends Form{
    
    public static function getForm($dataArray = array(),$object, $module = '', $langs = array()){
        $dataForm = new Form('TraductionForm');
        
	$strings = array();
	$strings = $object->app()->Translate()->GetStringTotranslate($module);
	//$ligne = $string->length();
	$i = 0;
        //var_dump($strings);
        //die();
    $dataForm->add('Radio', 'lang')
                ->label('choisissez une langue de traduction')
                ->choices($langs)
                ->required(false)
                ;
	foreach ($strings as $mots){		
            foreach ($mots as $mot) {
                $dataForm->add('text','mot_'.$i)
                       ->label(ucfirst($mot))
                       ->required(true);

		$i++;
                
            }
		
	}
        
        $dataForm->add('Submit','Valider')
                 ->value('valider')->add_Class("btn green");
        $dataForm->bound($dataArray);
        
        return $dataForm;
    }
}
?>
