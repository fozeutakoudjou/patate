<?php
	/**
	 * Description of MenuForm
	 *
	 *
	 */
	namespace Applications\Modules\Menu\Form;
	
	if( !defined('IN') ) die('Hacking Attempt');
	
	use Library\Classe\Form\Form;
	
	class MenuForm extends Form{ 
	    
		public static function getForm($dataArray = array(),$edit=false,$tabModule=array()){
			
			$dataForm= new Form('Ajout module');
	        
	        $dataForm->add('Radio', 'type_link')
        			->label('Front link')
	        		->choices(Array(
	        			'0'=>'Non',
	        			'1'=>'Oui'
	        		));      
	        		    
	       $dataForm->add('Select', 'module')
	        		->label('Catalogue')
	        		->choices($tabModule)
	        		->required(true);
	        
	        if($edit)
	        	$dataForm->add('Hidden', 'id')->value($dataArray['id']);
	       
	    
			$dataForm->closeForm(false);
	        
	        $dataForm->bound($dataArray);
	        
	        return $dataForm;
			
		}
	}
		
				?>