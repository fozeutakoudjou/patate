<?php

namespace Applications\Modules\ModuleCreator\Form;

/**
 * Description of ModuleCreatorForm
 *
 * @author ffozeu
 */

use Library\Classe\Form\Form;

class ModuleCreatorForm extends Form{
    //put your code here
    public static function getForm($dataArray = array(), $tab = array()){
        
        $dataForm = new Form('DefBg');

        $dataForm->add('Text', 'module')
                ->label('Nom du module')
                ->required(true);

        $dataForm->add('Text', 'auteur')
                ->label('Auteur')
                ->required(true);
        
        $dataForm->add('Select', 'table')
                ->label('Table associée')
                ->choices($tab)
                ->required(true);

        $dataForm->add('Radio', 'backend')
                ->label('Créer le dossier Backend')
                ->choices(Array(
                            '0'=>'Non',
                            '1'=>'Oui'
                ));

        $dataForm->add('Radio', 'frontend')
                ->label('Créer le dossier Frontend')
                ->choices(Array(
                            '0'=>'Non',
                            '1'=>'Oui'
                ));
        
        $dataForm->add('Radio', 'models')
                ->label('Créer le dossier Models')
                ->choices(Array(
                            '0'=>'Non',
                            '1'=>'Oui'
                ));
        
        $dataForm->add('Radio', 'config')
                ->label('Créer le dossier Config')
                ->choices(Array(
                            '0'=>'Non',
                            '1'=>'Oui'
                ));
        
        $dataForm->add('Radio', 'form')
                ->label('Créer le dossier Form')
                ->choices(Array(
                            '0'=>'Non',
                            '1'=>'Oui'
                ));
        
        $dataForm->add('Radio', 'web')
                ->label('Créer le dossier Web')
                ->choices(Array(
                            '0'=>'Non',
                            '1'=>'Oui'
                ));
        
       
        $dataForm->closeForm(false);
        $dataForm->bound($dataArray);
        
        return $dataForm;
    }
}

?>
