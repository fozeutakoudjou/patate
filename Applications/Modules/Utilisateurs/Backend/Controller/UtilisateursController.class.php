<?php

/**
 * Description of ConnexionController
 *
 * @author FFOZEU
 */
namespace Applications\Modules\Utilisateurs\Backend\Controller;

if( !defined('IN') ) die('Hacking Attempt');

use Helper\HelperBackController;
use Library\HttpRequest;
use Applications\Modules\Utilisateurs\Form\UtilisateursForm;
use Library\Tools;

class UtilisateursController extends HelperBackController{
		
    public function executeConnect(HttpRequest $request){
		//Do be synchronize with assets I was changing login by username
        $this->page->addVar('title','username');
		//var_dump($_POST); die();
        if ($request->postExists('username')){
            $login = $request->getValue('username');
            $password = $this->cryptePassword($request->getValue('password'));
				//var_dump($_POST); die();
            $manager = $this->managers->getManagerOf('Utilisateurs');

            $user = $manager->verifLogin($login, $password);
            if(empty($user)){
                $this->app->employee()->setFlash('Le pseudo ou le mot de passe est incorrect.');
            }else{
				 
                $this->initGroupAndAcces($user);
                if($this->app->employee()->haveRightTo('admin_access'))
                    $this->logIn($user);
                
                if($this->app->employee()->haveRightTo('module_add'))
                    $this->app->employee()->setAttribute('addmodule',true); 
                
                if($this->app->employee()->isAdmin()){
					
                    $this->app->httpResponse()->redirect(_BASE_URI_.'admin/');
					//echo "toto2";var_dump($_POST); die();
                    exit;
                }  
                $this->app->employee()->setFlash('Vous n\'avez pas les droits d\'accès à  l\'administration');
                $this->app->httpResponse()->redirect(_BASE_URI_.'/admin/');
            }
        }

    }

    public function logIn(array $user){
        foreach ($user as $key => $value){
            $_SESSION['employee']['id'] = $value->getId();
            $_SESSION['employee']['pseudo'] = $value->getPseudo();
            $_SESSION['employee']['email'] = $value->getEmail();
            $_SESSION['employee']['password'] = $value->getPassword();
            $_SESSION['employee']['nom'] = $value->getNom();
            $_SESSION['employee']['prenom'] = $value->getPrenom();
            $_SESSION['employee']['Avatar'] = $value->getAvatar();
            $_SESSION['employee']['is_active'] = $value->getIs_active();
            $_SESSION['employee']['nbProspectToregister'] = (int)$value->getNbProspectToregister();
            //$_SESSION['employee']['infos_complementaires'] = $value->getInfos_complementaires();
            $_SESSION['employee']['admin-employee'] = true;
            $_SESSION['employee']['auth-employee'] = true;
        }
    }
    public function executeLogout(){
        $this->app->employee()->logOut();
        $this->app->httpResponse()->redirect('index.php');
    }
	

    public function executeUtilisateurs(HttpRequest $request){
        $this->page->addVar('title', 'Gestion des utilisateurs');
        $this->leftcolumn();
        $this->rightcolumn();
        $manager = $this->managers->getManagerOf('Utilisateurs');
        $data = $manager->getUtilisateurs();
        $this->page->addVar('datalist', $data);
        $this->page->addVar('pagination', $this->pagination);
		$this->addJS(array(
			_ASSETS_GLOBAL_PLUGINS_DIR_.'select2/select2.min.js',
			_ASSETS_GLOBAL_PLUGINS_DIR_.'data-tables/jquery.dataTables.min.js',
			_ASSETS_GLOBAL_PLUGINS_DIR_.'data-tables/DT_bootstrap.js',
			_ASSETS_ADMIN_PAGES_DIR_.'scripts/table-managed.js',
			_THEME_BO_JS_MOD_DIR_.$this->name.'/initusers.js',
		));
    }
	
	public function executeshowUser(HttpRequest $request){
		
		 if($request->getExists('id')){
			 $this->page->addVar('title','Profile Utilisat');
		$manager = $this->managers->getManagerOf('Utilisateurs');
		$data = $manager->getUtilisateursByID(intval($request->getValue('id')));
        //$data_array = $data->tabAttrib;
       $this->page->addVar('userdata',$data[0]);
		//var_dump($data[0]->getPseudo());
		 }
		
	}

    private function leftcolumn(){
        $out = array();
        $out['add-user.html']               = 'Ajouter un Utisateur';
        $out['utilisateurs.html']           = 'Liste des utilisateurs';
        $out['add-groupuser.html']          = 'Ajouter un Groupe d\'utisateur';
        $out['groupeutilisateurs.html']     = 'Listing des groupes d\'utilisateurs';
        return $this->page->addVar('left_content', $out);
    }
    private function rightcolumn(){
        $out ='Gérez vos utilisateurs, consultez leurs informations. Vous pouvez éditer ou supprimer un profil.';
        return $this->page->addVar('right_content', $out);
    }
	
    public function executeCreateuser(HttpRequest $request)
    {
        $parametres = array();
        $variable = array();
        
        $this->page->addVar('title', 'créer un compte');
        $this->leftcolumn();
        $this->rightcolumn();
        $edit =false;
        $tab = array();
        $manager = $this->managers->getManagerOf('Utilisateurs');
        $info = $manager->getGroupeUtilisateurs();
        
        
        
       //var_dump($info);
        $thisusergroup = $manager->getGroupesUtilisateur(intval($request->getValue('id')));
        foreach ($thisusergroup as $value) {
             $tab[$value->id] = $value->id;
        }
		
		//cas de l'édition
        if($request->getExists('id')){
            $edit =true;
            $data = $manager->findById(intval($request->getValue('id')));
            $data_array = $data->tabAttrib;
			$this->page->addVar('title', 'Modifier Mon compte');
        }else{
            $data_array = $request->getSendData($_POST);
        }
        //generation du formulaire
        $dataForm = UtilisateursForm::getForm($data_array, $edit, $tab, true);
        $configMail = $this->getConfig();
        //* ajout de la variable à la page
        if($request->getMethod('post')){
            $parametres["expediteur"]    = $configMail['emailSite'];
            $parametres["Nomexpediteur"] = $configMail['nomSite'];
            $parametres["address"]       = $request->getValue('email');
            $parametres["Nomaddress"]    = $request->getValue('prenom').' '.$request->getValue('nom');
            
            $variable["first_name"]      = $request->getValue('prenom');
            $variable["last_name"]       = $request->getValue('nom');
            $variable["pseudo"]          = $request->getValue('pseudo');
            $variable["passwd"]          = $request->getValue('password');
            
           if(!empty($_FILES['avatarFile']['tmp_name'])){
                $_POST['avatar'] = $this->addImage('avatarFile');
           }

            if ($request->getValue('password') == $request->getValue('verif_mdp')) {

                $pseudo   = $request->getValue('pseudo');
                $email    = $request->getValue('email');
                $password = $this->cryptePassword($request->getValue('password'));
                //var_dump($password);
                $user = $manager->verifInscription($pseudo, $email);
				
                if(!$request->getExists('id')){
                    if(!empty ($user)){
                            $this->errors ='Ce pseudo ou E-mail est déjà utilisé';
                    }else{  
                        $_POST['password'] = $password;
                        $userPost = $request->getSendData($_POST);
                        unset($userPost['uniqid']);
                        unset($userPost['verif_mdp']);
                        if(!empty($userPost['groupe']))
                            unset($userPost['groupe']);
                        //var_dump($userPost); die();
                        if($manager->add($userPost)){
                               $lastuser = $manager->getLastUtilisateurs();
                               foreach ($lastuser as $value) {
                                       $idu = $value->id;
                               }                          
                               if(isset($_POST['groupe']))
                                    foreach ($_POST['groupe'] as $gpe) 
                                        $result = $manager->defineUserGroup($idu, $gpe);
                               $parametres["subjet"]        = "Activation du compte";                

                               $ifoo =  $this->app()->mail()->send($parametres, $variable,'compte.html');
                               
                               $this->app->httpResponse()->redirect('utilisateurs.html');
                       }else{
                               $this->errors ="Echec lors de l'inscriptions";
                       }
                    }
                }else{
                    if(!empty ($user) && $request->getValue('id') !=$user[0]->getId()){
                            $this->errors ='Ce pseudo ou E-mail est déjà utilisé';

                    }else{
                        if($request->getValue('password') == "")
                                $_POST['password'] = $request->getValue('passwordH');
                        else
                             $_POST['password'] = $password;
                        
                        $userPost = $request->getSendData($_POST);
                        unset($userPost['uniqid']);
                        unset($userPost['verif_mdp']);
                        if(!empty($userPost['groupe']))
                            unset($userPost['groupe']);
                        if($manager->updateUser($request->getSendData($userPost))){
                            if(isset($_POST['groupe'])){
                                if($manager->DeleteGroupesUtilisateur($request->getValue('id'))){
                                    foreach ($_POST['groupe'] as $gpe) {
                                        $result = $manager->defineUserGroup($request->getValue('id'), $gpe);
                                    }
                                }

                            }

                            $this->app()->httpResponse()->redirect('utilisateurs.html');
                        }
                        else{
                                $this->errors ='Erreur lors de la mise à jour';
                        }
                    }
                }
            }
		}
        
		$this->page->addVar('errors', $this->errors);
		$this->page->addVar('dataForm', $dataForm);
                
        $this->page->addVar('groupeutilisateur', $info);
        $this->page->addVar('usergroup', $tab);
	}

    
    public function executeGroupeutilisateurs(HttpRequest $request){
        $this->page->addVar('title', 'Gestion des utilisateurs');
        $this->leftcolumn();
        $this->rightcolumn();
        $manager = $this->managers->getManagerOf('Utilisateurs');
        $data = $manager->getGroupeUtilisateurs();
        $this->page->addVar('datalist', $data);
        $this->page->addVar('pagination', $this->pagination);
    }

    public function executeCreategroupeuser(HttpRequest $request){             
        $this->page->addVar('title', 'Ajouter Un Nouveau Groupe d\'utilisateurs');
        $this->leftcolumn();
        $this->rightcolumn();
        $manager = $this->managers->getManagerOf('Utilisateurs');
        $edit = false;
        $dataArray    = array();
        $listdroits   = $manager->getDroit();
        $listModules  = $manager->getModules();
        $groupModules = array();
        $privgroup    = array();
        $modules_list = array();
		$i=0;
		foreach ($listModules as $module)
		{
			$link_access = $this->getRoutes($module->name);
			$modules_list[$i]['id'] = $module->id;
			$modules_list[$i]['name'] = $module->name;
			$modules_list[$i]['link_access'] = $link_access;
			$i++;
		}
		
        if($request->getExists('id')){        
            $dataObjt    = $manager->getGroupeUtilisateursById(intval($request->getValue('id')));
            
            $dataArray['id']   = $dataObjt[0]->id;
            $dataArray['nom_groupe']   = $dataObjt[0]->nom_groupe;
            $dataArray['technical']   = $dataObjt[0]->technical;
            $edit        = true;
            $listdroitsu = $manager->getDroit();
            
            foreach ($listdroitsu as $value) {
                $havpriv = $manager->VerifieGroupPrivilege($request->getValue('id'), $value->id);
                if(!empty($havpriv))
                    $privgroup[$value->id]     = $value->id;
            }
			foreach ($listModules as $module)
			{
				$havmod = $manager->VerifieGroupModule($request->getValue('id'), $module->id);
				if(!empty($havmod))
			$groupModules[$module->id] = unserialize ($havmod[0]->access);
				else
					$groupModules[$module->id] = array();
			}
        }else{
             $dataArray = $request->getSendData($_POST);
        }
        //generation du formulaire
        $dataForm = UtilisateursForm::getFormgroup($dataArray, $edit);
        
        if($request->getMethod('post')){
            if(!$request->getExists('id')){
                if($manager->addGroupUser($request->getValue('nom_groupe'),$request->getValue('technical'))){
                   $lastgroupuser = $manager->getLastGroupUtilisateurs();
                   foreach ($lastgroupuser as $value) {
                       $idG = $value->id;
                   }

                   if(isset($_POST['priv']))
                       foreach ($_POST['priv'] as $value) 
                           if(!$manager->addGroupPrivilege($idG,$value))
                               $this->errors ="Un Problème est survenu lors de l\'enregistrement";
                           
                   if(isset($_POST['modlinks']))
                       foreach ($_POST['modlinks'] as $key => $value) 
                           if(!$manager->addGroupModule($idG, $key, serialize($value)))
                               $this->errors ="Un Problème est survenu lors de l\'enregistrement du module";
				   
                   $this->app()->httpResponse()->redirect('groupeutilisateurs.html');
                }else{
                     $this->errors ="Impossible d\'ajouter le groupe";
                }

            }else{
                if($manager->updateGroupUser($request->getValue('id'), $request->getValue('nom_groupe'),$request->getValue('technical'))){
                    $m = $manager->deleteAllGroupPrivileges($request->getValue('id'));
                    $m = $manager->deleteAllGroupModules($request->getValue('id'));
                    if(isset($_POST['priv'])){
                        $r = $manager->deleteAllPrivGroup($request->getValue('id'));
                       foreach ($_POST['priv'] as $value)
                           if(!$manager->addGroupPrivilege($request->getValue('id'), $value))
                                $this->errors ="Un Problème est survenu lors de la mise à jour";
                    }

					if(isset($_POST['modlinks']))
                       foreach ($_POST['modlinks'] as $key => $value) 
                           if(!$manager->addGroupModule($request->getValue('id'), $key, serialize($value)))
                               $this->errors ="Un Problème est survenu lors de l\'enregistrement du module";
					$this->app()->user()->setFlash('Opération éffectué avec Succès');
                    $this->app()->httpResponse()->redirect('groupeutilisateurs.html');
                }else{
                     $this->errors = 'Echec lors de la mise à jour';
                }
            }     
        }

        $this->page->addVar('privilegegroup', $privgroup);
        $this->page->addVar('privileges', $listdroits);
        $this->page->addVar('modulesgroup', $groupModules);
        $this->page->addVar('modules', $modules_list);
        $this->page->addVar('errors', $this->errors);
        $this->page->addVar('dataForm', $dataForm);
     }



        public function executeDeleteuser(HttpRequest $request){

            $manager = $this->managers->getManagerOf('Utilisateurs');
            $out = array();
            if($request->getExists('id')){
                $out['id'] = $request->getValue('id');
				
               if($manager->delete($out)){
                   if($manager->deleteAllUserGroup($request->getValue('id'))){
                       $this->errors = 'suppression réussie';
                    }else{
                       $this->errors = 'Echec lors de la suppression';                   }

                }else{
                   $this->errors = 'Erreur lor de la suppression';
                }
                $this->app()->httpResponse()->redirect('utilisateurs.html');
				//var_dump($out);
            }
        }

        public function executeGroupedeleteuser(HttpRequest $request){
            $manager = $this->managers->getManagerOf('Utilisateurs');
			  $out = array();
            if($request->getExists('id')){
                $out['id'] = $request->getValue('id');
		
              if($manager->deleteGroupesUser($out)){
                   if($manager->deleteAllGroupPrivileges($request->getValue('id'))){
                      $this->errors = 'suppression réussie';
                  }else{
                        $this->errors = 'Echec lors de la suppression';
                    }
               }else{
                    $this->errors = 'Erreur lor de la suppression';
              }
                $this->app()->httpResponse()->redirect('groupeutilisateurs.html');
            }
        }
        
        private function cryptePassword($string){
            return md5(_COOKIE_KEY_.$string);
        }

    public function executeResults(HttpRequest $request) {
            $out = array();
            $manager = $this->managers->getManagerOf('Utilisateurs');             
            if($request->getValue('actionselect')!=""){
                switch ($request->getValue('actionselect')) {

                    case 'delete':
                        if(isset($_POST['eltcheck'])){
                            $result = $manager->deleteChecked($_POST['eltcheck']);
                        }
                        $data = $manager->findAll2();
                        break;

                     case 'active':
                        if(isset($_POST['eltcheck'])){
                            $result = $manager->ActiveChecked($_POST['eltcheck'],'id','is_active');
                        }
                        $data = $manager->findAll2();
                        break;
                     case 'unactive':
                        if(isset($_POST['eltcheck'])){
                            $result = $manager->UnActiveChecked($_POST['eltcheck'],'id','is_active');
                        }
                        $data = $manager->findAll2();
                        break;

                    default:
                        break;
                }
            }
            
           
            if($request->getValue('searchzone') != "" && $request->getValue('searchzone') != "recherche" ){
                $out[] = 'pseudo';
                $out[] = 'nom';
                $data = $manager->searchCriteria($out, $request->getValue('searchzone'));
            }else{
                $data = $manager->findAll2();
            }
            

            $this->page->addVar('datalist', $data); 
            $this->page->addVar('pagination', $this->pagination);
        }
        
        public function executeCsvUtilisateurs(HttpRequest $request){
            $list = array();
            $manager = $this->managers->getManagerOf('Utilisateurs');
            $dataList = $manager->getUtilisateurs();  
                
            $donnee = array(
                'id'=>iconv("UTF-8", "ISO-8859-1//TRANSLIT",'ID'),
                'nom'=>iconv("UTF-8", "ISO-8859-1//TRANSLIT",'Nom'),
                'tel'=>iconv("UTF-8", "ISO-8859-1//TRANSLIT",'Telephone'),
                'mail'=>iconv("UTF-8", "ISO-8859-1//TRANSLIT",'Email')
            );
            header('Content-Type:  text/csv' );
            //header('Content-Length: '.filesize($file));
            header('Content-Disposition: attachment; filename=utilisateurs.csv');
            $file = _SITE_UPLOAD_DIR_.'utilisateurs.csv';
            $fp=fopen($file, 'w');
            fputcsv($fp, $donnee,';','"');
            foreach($dataList as $dataa){
                $donnee['id'] = iconv("UTF-8", "ISO-8859-1//TRANSLIT",$dataa->tabAttrib['id']);
                $donnee['nom'] = iconv("UTF-8", "ISO-8859-1//TRANSLIT",$dataa->tabAttrib['prenom'].' '.$dataa->tabAttrib['nom']);
                $donnee['tel'] = iconv("UTF-8", "ISO-8859-1//TRANSLIT",$dataa->tabAttrib['tel1']);
                $donnee['mail'] = iconv("UTF-8", "ISO-8859-1//TRANSLIT",$dataa->tabAttrib['email']);
                fputcsv($fp,$donnee,';','"');
            }
            echo chr(10);
            fclose($fp);
            readfile($file);                    
            
            exit();
        }
        
		public function executecsvGroupeUtilisateur(HttpRequest $request){
			$list=array();
			$manager = $this->managers->getManagerOf('Utilisateurs');
			$list = $manager->getGroupeUtilisateurs();
			
			$donnee = array(
                'id'=>iconv("UTF-8", "ISO-8859-1//TRANSLIT",'ID'),
                'nom_groupe'=>iconv("UTF-8", "ISO-8859-1//TRANSLIT",'Nom'),
                'technical'=>iconv("UTF-8", "ISO-8859-1//TRANSLIT",'Technique'),
            );
            header('Content-Type:  text/csv' );
            //header('Content-Length: '.filesize($file));
            header('Content-Disposition: attachment; filename=GroupeUtilisateurs.csv');
            $file = _SITE_UPLOAD_DIR_.'GroupeUtilisateurs.csv';
            $fp=fopen($file, 'w');
            fputcsv($fp, $donnee,';','"');
            foreach($list as $dataa){
                $donnee['id'] = iconv("UTF-8", "ISO-8859-1//TRANSLIT",$dataa->tabAttrib['id']);
                $donnee['nom_groupe'] = iconv("UTF-8", "ISO-8859-1//TRANSLIT",$dataa->tabAttrib['nom_groupe']);
                $donnee['technical'] = iconv("UTF-8", "ISO-8859-1//TRANSLIT",$dataa->tabAttrib['technical']);
                fputcsv($fp,$donnee,';','"');
            }
            echo chr(10);
            fclose($fp);
            readfile($file);                    
            
            exit();
			
		}
		
        public function initGroupAndAcces($user){
            $manager = $this->managers->getManagerOf('Utilisateurs');
            $GrpUsers = $manager->getGroupesUtilisateur($user[0]->getId());
            $_SESSION['employee']['access'] = array();
            $_SESSION['employee']['groups'] = array();
            $_SESSION['employee']['modules'] = array();
			$_SESSION['employee']['modules_access'] = array();
            $_SESSION['employee']['technical_group'] = array();
            foreach ($GrpUsers as $value) {
                array_push($_SESSION['employee']['groups'], $value->nom_groupe);
                array_push($_SESSION['employee']['technical_group'], $value->technical);
                $privileges = $manager->getDroitGroup($value->id);
                foreach ($privileges as $privilege) 
                    array_push($_SESSION['employee']['access'], $privilege['uniq_id']);
                $modules_access = $manager->getModulesGroup($value->id);
                foreach ($modules_access as $module)
				{
					$access = unserialize($module['access']);
					if(is_array($access))
						foreach ($access as $mod_route)
							 array_push($_SESSION['employee']['modules_access'], $mod_route);
                    array_push($_SESSION['employee']['modules'], $module['name']);
				}
            }
        }
		
		function getRoutes($module)
		{
			$path = _MODULES_DIR_.$module.'\\Config\\';
			$path .='B';
			$filename = $path.$module.'Route.xml';
			$links_access = array();
			if(file_exists($filename)){
				$dom = new \DOMDocument; // L'antislashe précédant laclasse est très important ! DOMDocument est déclaré dans lenamespace global, ici on est dans le namespace Library
				$dom->load($filename);
				$dom->xinclude();
				foreach ($dom->getElementsByTagName('route') as $route){
					$links_access[] = $route->getAttribute('url');
				}
			}
			return $links_access;
		}
}

?>