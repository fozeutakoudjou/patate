<?php
    /**
    * Description of AbonnementManager_PDO
    *
    * @author Luc Alfred MBIDA
    */
    namespace Applications\Modules\Menu\Models;

    if( !defined('IN') ) die('Hacking Attempt');

    class MenuManager_PDO extends MenuManager {
    	/**
    	 *Sélectionne Touts les attributs d'une table
    	 * @return type
    	 */
    	public function getTableAtt(){
    		$sql = 'SELECT *
                FROM '._DB_PREFIX_.$this->nameTable.' as t
                		ORDER BY  t.id ';
               
    		$req = $this->dao->query($sql);
    		
    		 return $this->fecthAssoc_data($req, $this->name);
    	
    	}
    	/**
    	 * 
    	 * @param unknown $array
    	 * This function is used to record at the same moment root and child.
    	 */
    	
    	public function add2($array){
    		
    		if(strcasecmp($array['parent'],"NULL")!=0 && strcasecmp($array['module'],"Autre")!=0) {
    			$cpteur=$array['nblien'];
    			for($i=1;$i<=$cpteur;$i++){
    				
    				if(isset($array["lien".$i])&&!empty($array["lien".$i])){
    					$sql='INSERT INTO '._DB_PREFIX_.$this->nameTable.'(type_link,module,titre,lien,parent,logo,position)
		      				VALUES('.$array['type_link'].',"'.$array['module'].'","'.$array["titre".$i].'","'.$array["lien".$i].'","'.$array['parent'].'","'.$array['logo'.$i].'","NULL")';
    					$req=$this->dao->prepare($sql);
		      			 $req->execute();
    				  }
    				
    			   }
    			}else{
    				  $position=null;
    				  if(strcasecmp($array['module'],"Autre")==0){
    				  		foreach ($array['position'] as $pos ):
    				  			 $position.=','.$pos;
    				  		endforeach;
	    				  	$sql='INSERT INTO '._DB_PREFIX_.$this->nameTable.'(type_link,module,titre,lien,parent,logo,position)
			      				VALUES('.$array['type_link'].',"'.$array['module'].'","'.$array['titre'].'","'.$array['lien'].'","'.$array['parent'].'","'.$array['logo'].'","'.substr($position,1).'")';
	    				  	$req=$this->dao->prepare($sql);
	    				  	//var_dump($req);die();
	    				  	$req->execute();
    				  }
    				
    			}
    		}
    		
    		
    	 public function update2($array){
    	 	//var_dump($array);die();
    	 	$position="NULL";
    	 	if(isset($array['position']) && !empty($array["position"])){
    	 		$position="";
    	 		foreach ($array['position'] as $pos ): 
    	 			$position.=','.$pos;
    	 		endforeach;
    	 		$position=substr($position,1);
    	 	}
    	 	 if(strcasecmp($array["parent"],"NULL") != 0 )$position="NULL";
    	 	$sql='UPDATE '._DB_PREFIX_.$this->nameTable.' SET  titre="'.$array["titre"].'",lien="'.$array["lien".$i].'", parent="'.$array["parent"].'", logo="'.$array["logo"].'", position="'.$position.'"
    	 	     WHERE id="'.$array["id"].'"';
    	 	 $req=$this->dao->prepare($sql);
    	 	 $req->execute();
    	 	 if(isset($array['nblien'])&& !empty($array["nblien"])){
    	 	 	for($i=1;$i<=$array["nblien"];$i++){
		    	 	 	$sql='UPDATE '._DB_PREFIX_.$this->nameTable.' SET  type_link="'.$array["type_link"].'",
		    	 			      titre="'.$array["titre".$i].'", lien="'.$array["lien".$i].'", parent="'.$array["id"].'", logo="'.$array["logo".$i].'", position="NULL"
		    	 	     WHERE id="'.$array["id".$i].'"';
		    	 	 	$req=$this->dao->prepare($sql);
		    	 	 	//var_dump($req);die();
		    	 	 	$req->execute();
    	 	 	}
    	 	 }
    	 	
    	 } 
    	
    	/**
    	 * 
    	 * @param unknown $contraint
    	 * choose attribut depend of contraint
    	 */
    	public function findWithContraint($contraint,$contraint2){
    		$sql = 'SELECT *
                FROM '._DB_PREFIX_.$this->nameTable.' as t
                		WHERE t.'.$contraint.' ="'.$contraint2.'"
                				ORDER BY t.lien and t.id';
    		//var_dump($sql);die();
    		$req = $this->dao->query($sql);
    		
    		 return $this->fecthAssoc_data($req, $this->name);
    	}
    	
    	public function findWithContraint2($contraint,$contraint2,$contraint3,$contraint4){
    		$sql = 'SELECT *
                FROM '._DB_PREFIX_.$this->nameTable.' as t
                		WHERE t.'.$contraint.' ="'.$contraint2.'"
                			AND t.'.$contraint3.'="'.$contraint4.'"';
    		 
    		$req = $this->dao->query($sql);
    	
    		return $this->fecthAssoc_data($req, $this->name);
    	}
    	
    	public function getId($link,$module,$titre,$lien,$parent){
    		$sql = 'SELECT id
                FROM '._DB_PREFIX_.$this->nameTable.' as t
                		WHERE t.type_link ='.$link.' and t.module="'.$titre.'" and t.titre="'.$module.'" and
                					t.lien="'.$lien.'" and t.parent="'.$parent.'"';
    		
    		$req = $this->dao->query($sql);
    		//var_dump($req);
    		return $this->fecthAssoc_data($req, $this->name);
    	}
    	/**
    	 * delete cascade because if you delete root you automatically delete all the children 
    	 */
    	public function deleteCascade(array $param, $jonction=' AND'){
    		$out=' ';
    		$i=0;
    	
    		foreach ($param as $key => $value) {
    			$out .=($i!=0?$jonction.' ':' ').$key.'='.$value;
    			$i++;
    		}
    		$sql ='DELETE
               FROM '._DB_PREFIX_.$this->nameTable.'
               WHERE ('.$out.' OR parent='.$value.')';
    		// var_dump($sql);die();
    		return $this->dao->query($sql);
    	}
    	
    	/**
    	 *This function is used to built information in Memory coming from LoadAutomaticMenu
    	 */
    
      public function buitl_table_in_memory($data,$link){
      	
      	foreach ($data as $data2=>$value){
      		$id="NULL";
      		$temp=0; 
      		$logo="NULL";
      
      		foreach ($value as $value1){
      	       if($temp==0){
      	       	//This is used to save Root
      	        	$temp++;
      	        	$titre="Autre";
      	        	$lien="#";
	      	        	switch($value1["module"]){
	      	        		case "Abonnement":
	      	        			$logo="fa fa-user";break;
	      	        		case "Menu":
	      	        			$logo="fa fa-puzzle-piece";break;
	      	        		case "Configuration":
	      	        			$logo="fa fa-cogs";break;
	      	        		case "ModuleCreator":
	      	        			$logo="fa fa-cogs";break;
	      	        		case "Utilisateurs":
	      	        			$logo="fa fa-user";break;
	      	        		default:
	      	        			$logo="fa fa-table";
	      	        			} 
	      	        			
      	        	$sql='INSERT INTO '._DB_PREFIX_.$this->nameTable.'(type_link,module,titre,lien,parent,logo,position)
		      				VALUES('.$link.',"'.$titre.'","'.$value1["module"].'","'.$lien.'","'.$id.'","'.$logo.'","Left")';
	      	        	$logo="NULL";
	      	        	$req=$this->dao->prepare($sql);
	      	        	$req->execute();
      	        	$result=$this->getId($link,$value1["module"],$titre,$lien,$id);
      	        	foreach ($result as $idParent){
      	        		$id=$idParent->getId();
      	        	}
      	        	
      	         }
      	         
      	         //This is used to save Child	
		      		$sql='INSERT INTO '._DB_PREFIX_.$this->nameTable.'(type_link,module,titre,lien,parent,logo,position)
		      				VALUES('.$link.',"'.$value1["module"].'","'.$value1["action"].'","'.$value1["url"].'","'.$id.'","'.$logo.'","NULL")';
		      		$req=$this->dao->prepare($sql);
		      		$req->execute();
		      		
      	        }
      	      
		    }
		      	
      }
      /**
       * Find Id Parent and children Id 
       */
          public function FindCascade($Id){
          	$sql = 'SELECT *
                FROM '._DB_PREFIX_.$this->nameTable.' as t
                		WHERE t.id ="'.$Id.'" OR
                				t.parent="'.$Id.'"
                				ORDER BY t.lien and t.id';
          	// var_dump($sql);die();
          	$req = $this->dao->query($sql);
          	
          	return $this->fecthAssoc_data($req, $this->name);
          }
          /**
           * This function is used to recorver the link of curent Module
           * @param unknown $Id
           */
          
          public function getLink($Id){
          	$sql = 'SELECT type_link
                FROM '._DB_PREFIX_.$this->nameTable.' as t
                		WHERE t.id ='.$Id ;
          	
          	$req = $this->dao->query($sql);
          	//var_dump($req);
          	return $this->fecthAssoc_data($req, $this->name);
          	
          }
    	
    } 
    
    
?>