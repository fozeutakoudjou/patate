<?php

/**
 * Description of ModuleCreatorController
 *
 * @author Le Maître Rikudou
 *
 */

    namespace Applications\Modules\ModuleCreator\Backend\Controller;

    if( !defined('IN') ) die('Hacking Attempt');

    use Helper\HelperBackController;
    use Library\HttpRequest;
    use Library\Classe\Form\Form;
    use Library\Tools;
    use Applications\Modules\ModuleCreator\Form\ModuleCreatorForm;

class ModuleCreatorController extends HelperBackController{
    protected $name = 'ModuleCreator';
    private function rightcolumn(){
        $out ='Créer des modules';
        return $this->page->addVar('right_content', $out);
    }

    public function executeFields(HttpRequest $request){
        $manager =  $this->managers->getManagerOf('ModuleCreator');
        $table = $request->getValue('table');
        $fields = $manager->getTableAttributes($table);
        $extraFields = array();
        foreach ($fields as $key => $value) {
            $fieldInfo = $manager->getTableAttributesContraintes($table, $value->COLUMN_NAME);
            if(is_array($fieldInfo) && count($fieldInfo))
                if($fieldInfo[0]->REFERENCED_TABLE_SCHEMA == _DB_NAME_){
                    $extraFields[$value->COLUMN_NAME]['ref_table']  = $fieldInfo[0]->REFERENCED_TABLE_NAME;
                    $extraFields[$value->COLUMN_NAME]['ref_column'] = $fieldInfo[0]->REFERENCED_COLUMN_NAME ;
                    $extraFields[$value->COLUMN_NAME]['ref_fields'] = $manager->getTableAttributes($fieldInfo[0]->REFERENCED_TABLE_NAME);
                }
        }
        
        $this->page->addVar('fields', $fields);
        $this->page->addVar('refFields', $extraFields);
        
    }
    
    public function tableWithoutPrefix($table){
        $table = explode(_DB_PREFIX_, $table);
        $table = $table[1];
        return $table;
    }
    public function executeCreatemodule(HttpRequest $request){
        // On ajoute une définition pour le titre
        $this->page->addVar('title', 'Nouveau Module');        
        $this->rightcolumn();
        
        $dataArray = array();
        $tab['NULL'] = 'Sélectioner une table';
        $manager =  $this->managers->getManagerOf('ModuleCreator');
        $datalist = $manager->getDBTables();
        //var_dump($datalist);
        foreach($datalist as $data){
            $tab[$data->TABLE_NAME] = $data->TABLE_NAME;
        }
        if($request->getMethod('post')){
            $dataArray = $_POST;
        }
        $dataForm = ModuleCreatorForm::getForm($dataArray, $tab);
        if($request->getMethod('post')){
                $modules = ucfirst($_POST['module']);
                $auteur = $_POST['auteur'];
                $table = $this->tableWithoutPrefix($_POST['table']);
                $modulepath = _SITE_ROOT_DIR_.'\\Applications\Modules\\'.$modules.'\\';
                $txt = '';
                //echo "module dir : ".$modulepath;
                if(!mkdir($modulepath)){
                    die('Echec lors de la création du répertoire '.$modules);
                }else{
                    if($request->getValue('models')){
                        if(!mkdir($modulepath.'Models\\')){
                            die('Echec lors de la création du répertoire Models');
                        }
                        $fic1 = fopen($modulepath.'Models\\'.$modules.'.class.php', 'a');
                        $fic2 = fopen($modulepath.'Models\\'.$modules.'Manager.class.php', 'a');
                        $fic3 = fopen($modulepath.'Models\\'.$modules.'Manager_PDO.class.php', 'a');
                        $txt = '<?php
                                    /**
                                    * Description of '.$modules.'
                                    *
                                    * @author '.$auteur.'
                                    */
                                    namespace Applications\Modules\\'.$modules.'\Models;

                                    if( !defined(\'IN\') ) die(\'Hacking Attempt\');

                                    use Library\Record;
                                    
                                    class '.$modules.' extends Record{';
                        //if($_POST['table']){
                            $variables = $manager->getTableAttributes($_POST['table']);
                            foreach($variables as $data)
                                $txt .= "\n \t\t\t\t\t protected $".$data->COLUMN_NAME.";";
                            
                        /*$txt .= "\n\n \t\t\t\t\t\t  // SETTERS";
                            
                            $setters = $manager->getTableAttributes($_POST['table']);
                            foreach($setters as $data){
                                $txt .="\n \t\t\t\t\t public function set".ucfirst($data->COLUMN_NAME)."($".$data->COLUMN_NAME."){\n \t\t\t\t\t\t";
                                $txt .= '$this->'.$data->COLUMN_NAME.' = $'.$data->COLUMN_NAME.';';
                                $txt .="\n \t\t\t\t\t}";
                            }
                         $txt .= "\n\n\t\t\t\t\t\t   // GETTERS";  
                         
                            $getters = $manager->getTableAttributes($_POST['table']);
                            foreach($getters as $data){
                                $txt .="\n \t\t\t\t\t public function get".ucfirst($data->COLUMN_NAME)."(){\n \t\t\t\t\t\t";
                                $txt .= 'return $this->'.$data->COLUMN_NAME.';';
                                $txt .="\n \t\t\t\t\t}";
                            }*/
                         
                        $txt .= ' 

                        }
                    ?>';
                        fwrite($fic1, $txt);
                        $txt = '<?php
                                    /**
                                    * Description of '.$modules.'Manager
                                    *
                                    * @author '.$auteur.'
                                    */
                                    namespace Applications\Modules\\'.$modules.'\Models;

                                    if( !defined(\'IN\') ) die(\'Hacking Attempt\');

                                    use Library\Manager;

                                    abstract class '.$modules.'Manager extends Manager{
                                        protected $name = \'Applications\Modules\\'.$modules.'\Models\\'.$modules.'\';
                                        protected $nameTable = \''.$table.'\';
                                        // Inserer votre code ici
                                    }
                                ?>';
                        fwrite($fic2, $txt);
                        $txt = '<?php
                                    /**
                                    * Description of '.$modules.'Manager_PDO
                                    *
                                    * @author '.$auteur.'
                                    */
                                    namespace Applications\Modules\\'.$modules.'\Models;

                                    if( !defined(\'IN\') ) die(\'Hacking Attempt\');

                                    class '.$modules.'Manager_PDO extends '.$modules.'Manager{
                                        // Inserer votre code ici
                                    }
                                ?>';
                        fwrite($fic3, $txt);
                        
                        fclose($fic1);
                        fclose($fic2);
                        fclose($fic3);
                        
                    }
                    //création du backend
                    if($request->getValue('backend')){
                        if(!mkdir($modulepath.'Backend\\',0 ,true)){
                            echo $modulepath.'Backend\\';
                            die('Echec lors de la création du répertoire Backend');
                        }
                        mkdir($modulepath.'Backend\\Controller\\');
                        mkdir($modulepath.'Backend\\Views\\');
                        $fic1 = fopen($modulepath.'Backend\\Controller\\'.$modules.'Controller.class.php', 'a');
                        $fic2 = fopen($modulepath.'Backend\\Views\\List.tpl.php', 'a');
                        $fic3 = fopen($modulepath.'Backend\\Views\\Create.tpl.php', 'a');
                        $txt = $this->generateBackOfficeController($modules, $auteur, $request);

                        fwrite($fic1, $txt);
                        
                        $txt = $this->generateListTemplateContent($_POST['table'], $modules);
                        fwrite($fic2, $txt);
                        
                        $txt = $this->generateCreateTemplateContent();
                        fwrite($fic3, $txt);
                        fclose($fic1);
                        fclose($fic2);
                        fclose($fic3);
                    }

                    //création du frontend
                    if($request->getValue('frontend')){
                        if(!mkdir($modulepath.'Frontend\\')){
                            die('Echec lors de la création du répertoire Frontend');
                        }
                        mkdir($modulepath.'Frontend\\Controller\\');
                        mkdir($modulepath.'Frontend\\Views\\');
                        $fic1 = fopen($modulepath.'Frontend\\Views\\'.$modules.'.tpl.php', 'a');
                        $fic2 = fopen($modulepath.'Frontend\\Controller\\'.$modules.'Controller.class.php', 'a');                        
                        $txt = '<?php
                                    // Inserer votre code ici!
                                ?>';
                        fwrite($fic1, $txt);
                        $txt = '<?php
                                /**
                                 * Description of '.$modules.'Controller
                                 *
                                 * @author '.$auteur.'
                                 *
                                 */

                                    namespace Applications\Modules\\'.$modules.'\Frontend\Controller;

                                    if( !defined(\'IN\') ) die(\'Hacking Attempt\');

                                    use Helper\HelperBackController;
                                    use Library\HttpRequest;
                                    '.($request->getValue('form')?'use Applications\Modules\\'.$modules.'\Form\\'.$modules.'Form;':'').'
                                    use Library\Tools;

                                    class '.$modules.'Controller extends HelperBackController{
                                        // Inserer votre code ici!
                                        protected $name = "'.$modules.'";
                                    }
                            ?>';
                        fwrite($fic2, $txt);
                        fclose($fic1);
                        fclose($fic2);                        
                        
                    }
                    
                    //Création du répertoire form
                    if($request->getValue('form')){
                        if(!mkdir($modulepath.'Form\\')){
                            die('Echec lors de la création du répertoire Form');
                        }
                        $fic1 = fopen($modulepath.'Form\\'.$modules.'Form.class.php', 'a');
                        $txt = $this->generateFormContent($request, $_POST['table'], $modules, $auteur);
                        fwrite($fic1, $txt);
                        fclose($fic1);
                    }
                    
                    
                    // Création du repertoire config
                    if($request->getValue('config')){
                        if(!mkdir($modulepath.'Config\\')){
                            die('Echec lors de la création du répertoire Config');
                        }
                        $fic1 = fopen($modulepath.'Config\B'.$modules.'Route.xml', 'a');
                        $fic2 = fopen($modulepath.'Config\F'.$modules.'Route.xml', 'a');
                        $txt ='<?xml version="1.0" encoding="UTF-8"?>
                            <routes>
  					               <route url="/admin/'.strtolower($modules).'-list.html" module="'.$modules.'" action="List"/>
                                   <route url="/admin/'.strtolower($modules).'-create.html" module="'.$modules.'" action="Create"/>
                                   <route url="/admin/'.strtolower($modules).'-edit-([0-9]+)\.html" module="'.$modules.'" action="Create" vars="id"/>
                                   <route url="/admin/'.strtolower($modules).'-delete-([0-9]+)\.html" module="'.$modules.'" action="Delete" vars="id"/>        
                            </routes>
                        ';
                        fwrite($fic1, $txt);
                        fwrite($fic2, $txt);
                        fclose($fic1);
                        fclose($fic2);
                    }
                    
                    // Création du répertoire web
                    if($request->getValue('web')){
                        if(!mkdir($modulepath.'web\\')){
                            die('Echec lors de la création du répertoire web');
                        }
                        
                        // Création des répertoires js et css
                        
                        mkdir($modulepath.'web\\css\\');
                        mkdir($modulepath.'web\\js\\');
                        $fic1 = fopen($modulepath.'web\js\\'.$modules.'.js', 'a');
                        $fic2 = fopen($modulepath.'web\css\\'.$modules.'.css', 'a');                        
                        fclose($fic1);
                        fclose($fic2);
                    }
                    
                    // Ajout de la route dans le fichier de configuration 
                   //$fic1 = fopen( _SITE_ROOT_DIR_.'\\Applications\Backend\Config\routes.xml', 'a');
				   $txt = '
                       <!-- import route du module '.$modules.' -->
                        <xi:include href="../../Modules/'.$modules.'/Config/B'.$modules.'Route.xml#xpointer(/routes/*)">
							<xi:fallback>
								<error>xinclude: B'.$modules.'Route.xml n\'a pas été trouvé</error>
        					</xi:fallback>
						</xi:include>
                        
					</routes>';
					$filename = _SITE_ROOT_DIR_.'\\Applications\Backend\Config\routes.xml';
					$file_array = file($filename); // une ligne dans chaque "case"
					array_pop($file_array); // on dépile le dernier élément (pas la peine de récupérer sa valeur)
					$fp = fopen($filename, 'wb'); // ouverture et écrasement
					fwrite($fp, rtrim(implode('',$file_array),"\r\n"));   // écriture					
                    fwrite($fp, $txt);
                    fclose($fp);                    
                    $this->infos ='Le Module '.$_POST['module'].' à été créé avec succès!';
                    $dataArray = $_POST;
                }
                
            }
            $this->page->addVar('infos', $this->infos);
            $this->page->addVar('errors', $this->errors);
            $this->page->addVar('dataForm', $dataForm);
        }
        
        public function generateBackOfficeController($modules, $auteur, $request){
            $manager    = $this->managers->getManagerOf('ModuleCreator');
            $txt = '<?php
                        /**
                         * Description of '.$modules.'Controller
                         *
                         * @author '.$auteur.'
                         *
                         */

                        namespace Applications\Modules\\'.$modules.'\Backend\Controller;

                        if( !defined(\'IN\') ) die(\'Hacking Attempt\');

                        use Helper\HelperBackController;
                        use Library\HttpRequest;
                        '.($request->getValue('form')?'use Applications\Modules\\'.$modules.'\Form\\'.$modules.'Form;':'').'
                        use Library\Tools;

                        class '.$modules.'Controller extends HelperBackController{
                            // Inserer votre code ici!
                            protected $name = "'.$modules.'";

                             private function leftcolumn(){
                                $out = array();
                                $out[\''.strtolower($modules).'-create.html\']   = $this->l(\'Ajouter\');
                                $out[\''.strtolower($modules).'-list.html\']     =  $this->l(\'Listing\');
                                $out[\'traduire-'.strtolower($modules).'.html\'] =  $this->l(\'Traduire ce module\');

                                return $this->page->addVar(\'left_content\', $out);
                            }
                            
                            function executeList(HttpRequest $request){
                                 $manager = $this->managers->getManagerOf(\''.$modules.'\');
                                 $this->page->addVar("title",  $this->l("Listing"));
                                 $this->leftcolumn();
                                 
                                 $data = $manager->findAll2();
                                 $this->page->addVar(\'datalist\', $data);
                                 $this->page->addVar(\'pagination\', $this->pagination);
                            }

                            function executeCreate(HttpRequest $request){
                                $this->page->addVar(\'title\',  $this->l(\'Ajout\'));
                                $this->leftcolumn();
                                $dataArray   = array();

                                $edit   = false;
                                $manager     = $this->managers->getManagerOf(\''.$modules.'\');';
                                
                                $ref_infos  = $manager->getTableContraintsParams($request->getValue('table'));
                                $params     = '';
                                
                                if(is_array($ref_infos) && count($ref_infos))
                                    foreach ($ref_infos as $value){
                                        $txt .='
                                                $'.$this->tableWithoutPrefix($value->REFERENCED_TABLE_NAME).' = array();
                                                $manager'.$this->tableWithoutPrefix($value->REFERENCED_TABLE_NAME).' = $this->managers->getManagerOf(\''.ucfirst($this->tableWithoutPrefix($value->REFERENCED_TABLE_NAME)).'\');
                                                $list'.$this->tableWithoutPrefix($value->REFERENCED_TABLE_NAME).' =   $manager'.$this->tableWithoutPrefix($value->REFERENCED_TABLE_NAME).'->findAll2();
                                                foreach($list'.$this->tableWithoutPrefix($value->REFERENCED_TABLE_NAME).' as $elt)
                                                    $'.$this->tableWithoutPrefix($value->REFERENCED_TABLE_NAME).'[$elt->get'.ucfirst($value->REFERENCED_COLUMN_NAME).'()] = $elt->get'.ucfirst($request->getValue('view_'.$value->COLUMN_NAME)).'();
                                        ';
                                        $params .= ', $'.$this->tableWithoutPrefix($value->REFERENCED_TABLE_NAME);
                                    }
                $txt .='
                                if($request->getExists(\'id\')){            
                                    $edit =true;
                                    $dataObjt = $manager->findById2(\'id\', intval($request->getValue(\'id\')));
                                    $dataArray = $dataObjt[0]->tabAttrib;

                                    $this->page->addVar(\'title\',  $this->l(\'Modification\'));
                                }else{
                                    $dataArray = $_POST;
                                }
                                $dataForm = '.$modules.'Form::getForm($dataArray, $edit'.$params.');

                                if($request->getMethod(\'post\')){
                                    if(!$request->getExists(\'id\')){
                                        if($manager->add($request->getSendData($_POST)))
                                            $this->app()->httpResponse()->redirect(\''.lcfirst($modules).'-list.html\');
                                        else
                                            $this->errors = _RECCORD_SAVE_FILED_;
                                    }else{
                                        if($manager->update($request->getSendData($_POST),\'id\')){
                                            $this->app()->httpResponse()->redirect(\''.lcfirst($modules).'-list.html\');
                                        }else{
                                            $this->errors = _RECCORD_UPDATE_FILED_;
                                        }
                                    }

                                }

                                $this->page->addVar(\'errors\', $this->errors);
                                $this->page->addVar(\'dataForm\', $dataForm);
                            }

                            public function executeDelete(HttpRequest $request){
                                $manager = $this->managers->getManagerOf(\''.$modules.'\');
                                if($request->getExists(\'id\')){
                                    $out[\'id\'] = $request->getData(\'id\');
                                    if($manager->delete($out)){
                                        $this->page->addVar(\'infos\', _RECCORD_DELETE_SUCCEFULL_); 
                                    }else{
                                        $this->page->addVar(\'errors\', _RECCORD_DELETE_FILED_);
                                    }
                                    $this->app()->httpResponse()->redirect(\''.lcfirst($modules).'-list.html\');
                                }
                                $this->app()->httpResponse()->redirect(\''.lcfirst($modules).'-list.html\');
                            }
                        }
                ?>';
            return $txt;
        }
        
        public function generateFormContent($request, $table, $modules, $auteur){
            $manager    = $this->managers->getManagerOf('ModuleCreator');
            $comlumns   = $manager->getTableAttributes($table);
            $ref_infos  = $manager->getTableContraintsParams($table);
            $params     = '';
            
            foreach ($ref_infos as $value)
                $params .= ', $'.$this->tableWithoutPrefix($value->REFERENCED_TABLE_NAME).' = array()';
            
            $txt = '<?php
                        /**
                         * Description of '.$modules.'Form
                         *
                         * @author '.$auteur.'
                         *
                         */

                        namespace Applications\Modules\\'.$modules.'\Form;

                        if( !defined(\'IN\') ) die(\'Hacking Attempt\');

                        use Library\Classe\Form\Form;                                    

                        class '.$modules.'Form extends Form{
                            // Inserer votre code ici!
                             public static function getForm($dataArray = array(),$edit = false'.$params.'){
                                $registerForm = new Form(\'create'.$modules.'\',\'post\');';
                         foreach($comlumns as $data)
                            $txt .= $this->FormInput($request, $table, $data->COLUMN_NAME);

                    $txt .= '
                              if($edit)
                                    $registerForm->add(\'Hidden\', \'id\')->value($dataArray[\'id\']);

                              $registerForm->add(\'Submit\', \'submit\')
                                            ->value(\'Soumettre\');
                              $registerForm->bound($dataArray);

                              return $registerForm;
                           }
                      }
                ?>';
                    
            return $txt;
           
        }
        
        public function generateListTemplateContent($table, $modules){
            $manager  = $this->managers->getManagerOf('ModuleCreator');
            $comlumns = $manager->getTableAttributes($table);
            
            $txt = '
                <?php if(!empty($infos)): ?>
                    <div class="infos"><img alt="ok" src="/backend_images/ok2.png" /> <?php echo $infos; ?></div>
                <?php endif; ?>
                <form name="" action="actiongroupedadv.html" method="post" id="groupaction">
                    <div class="table">
                        <img src="../Themes/backend/backend_images/bg-th-left.gif" width="8" height="7" alt="" class="left" />
                        <img src="../Themes/backend/backend_images/bg-th-right.gif" width="7" height="7" alt="" class="right" />
                        <table class="listing" cellpadding="0" cellspacing="0">
                            <thead>
                                <tr>
                                    <th></th>';
                                    foreach($comlumns as $data)
                                    $txt .='
                                    <th><?php echo $this->l(\''.$data->COLUMN_NAME.'\', \''.$modules.'\'); ?></th>';
            $txt .='                
                                    <th><?php echo $this->l(\'Actions\', \''.$modules.'\'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                        <?php foreach($datalist as $data):  ?>
                            <tr>
                                <td class="first style1"><input type="checkbox" name="eltcheck[]" class="elttocheck" value="<?php echo $data->getId(); ?>"></td>';
                                foreach($comlumns as $data)
                                    $txt .='
                                        <td><?php echo $data->get'.ucfirst($data->COLUMN_NAME).'(); ?></td>';
            $txt .='
                                <td class="last"> 
                                    <?php if($this->app->employee()->haveRightTo(\'edit\')){ ?>
                                        <a href="'.strtolower($modules).'-edit-<?php echo $data->getId() ?>.html" title="<?php echo $this->l(\'modifiier\', \''.$modules.'\'); ?>"><img src="<?php echo _THEME_BO_IMG_DIR_.\'edit-icon.gif\'; ?>" style="width:16px; height:16px;" alt="&Eacute;diter" /></a> 
                                    <?php } ?>
                                    <?php if($this->app->employee()->haveRightTo(\'delete\')){ ?>
                                        <a class="delete_elt" href="'.strtolower($modules).'-delete-<?php echo $data->getId() ?>.html" title="<?php echo $this->l(\'supprimer\', \''.$modules.'\'); ?>" onclick="return(confirm(\'<?php echo  $this->l(\'êtez vous sûre de vouloir effectuer cette action?\', \''.$modules.'\'); ?>\'));"><img src="<?php echo _THEME_BO_IMG_DIR_.\'hr.gif\'; ?>" style="width:16px; height:16px;" alt="Supprimer" /></a>
                                    <?php } ?>
                                </td>

                            </tr>
                        <?php endforeach;?>
                            </tbody>
                        </table>

                        <div class="select">
                            <?php if( isset($pagination) ) echo $pagination; ?>
                        </div>

                    </div>
                    <input type="hidden" name="actiontodo" value="./actiongrouped'.lcfirst($modules).'.html" id="actiontodo" />
                </form>
                ';
            return $txt;
        }
        
        public function generateCreateTemplateContent(){
            $txt = '
                <div class="select-bar"></div>
                <?php if(!empty($infos)): ?>
                    <div class="infos"><img alt="ok" src="<?php echo _THEME_BO_IMG_DIR_; ?>ok2.png" /> <?php echo $infos; ?></div>
                <?php endif; ?>
                <?php if(!empty($errors)): ?>
                    <div class="error"><img alt="error" src="<?php echo _THEME_BO_IMG_DIR_; ?>error2.png" /> <?php echo $errors; ?></div>
                <?php endif; ?>
                <div class="table">
                    <?php echo $dataForm ?>
                </div>';
            return $txt;
        }
        
          protected function init(){       
            
            $this->tabJS[_THEME_JS_MOD_DIR_.$this->name.'/B'.$this->name.'.js'] = 'screen'; 
            
            parent::init();
        }
        
        public function FormInput($request, $table, $columnName){
            $txt = '';
            if((int)$request->getValue('visible_'.$columnName)){
                $manager  = $this->managers->getManagerOf('ModuleCreator');
                $comlumn = $manager->getTableAttributesContraintes($table, $columnName);
                
                if(is_array($comlumn) && count($comlumn))
                    $txt .='
                        $registerForm->add(\''.ucfirst($request->getValue('type_'.$columnName)).'\', \''.$columnName.'\')
                            ->label(\''.ucfirst ($columnName).'\')
                            ->choices($'.$this->tableWithoutPrefix($comlumn[0]->REFERENCED_TABLE_NAME ).')
                            ->required('.$request->getValue('requis_'.$columnName).');';
                else
                    $txt .='
                        $registerForm->add(\''.ucfirst($request->getValue('type_'.$columnName)).'\', \''.$columnName.'\')
                            ->label(\''.ucfirst ($columnName).'\')
                            ->required('.$request->getValue('requis_'.$columnName).');';
                
            }
            return $txt;
        }
    }

?>
