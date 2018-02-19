<?php
/**
 * Description of Manager
 *
 * @author FFOZEU
 */
namespace Library;

if( !defined('IN') ) die('Hacking Attempt');

abstract class Manager {
    
    protected $dao;
    protected $nameTable;
	
    protected $lang;
    protected $languages;
    protected $useOfLang = true;
    protected $useOfAllLang = false;
	protected $primary = 'id';
	private static $langPrimary = 'iso_code';
	
	private $langCanBeUsed = null;


    public function __construct($dao){
        $this->dao = $dao;
    }
    
    /**
     * formate les données dans un tableau associatif et iniatilse les variables de l'objet ne refernce
     * @param type $requete
     * @param type $refObjet
     * @return \Library\refObjet 
     */
    public function fecthAssoc_data($requete, $refObjet){
        
        $output = array();
		$ids = array();
		$i = 0;
        while ($data = $requete->fetch(\PDO::FETCH_ASSOC)){
			$id = $data[$this->primary];
			if($this->useOfAllLang && isset($ids[$id])){
				$tabLang = $output[$ids[$id]]->getTabLang();
				foreach($tabLang as $field){
					$output[$ids[$id]]->setFieldValue($field, $data[$field], true, $data[self::$langPrimary], $this->useOfAllLang);
				}
			}else{
				$lang = ($this->isLangCanBeUsed() && $this->useOfLang && isset($data[self::$langPrimary])) ? $data[self::$langPrimary] : '';
				$output[] = new $refObjet($data, true, $lang, $this->useOfAllLang);
				if($this->useOfAllLang){
					$ids[$id] = $i;
					$i++;
				}
			}
        }
        $requete->closeCursor();
        
        return $output;
    }
    
    /**
     * formate les données dans un tableau associatif et iniatilse les variables de l'objet ne refernce
     * @param type $requete
     * @param type $refObjet
     * @return \Library\refObjet 
     */
    public function fecthRow_data($requete, $refObjet){
        $output = $this->fecthAssoc_data($requete, $refObjet);
		return empty($output) ? '' : $output[0];
    }
    
    /**
     * suppression des données en fonction d'un ensemeble de paramètre
     * @param array $param
     * @param type $jonction
     * @return type 
     */
    public function delete(array $param, $jonction=' AND', $table=''){
        $out=' ';
        $i=0;
        if(trim($table) == '')
            $table = $this->nameTable;
            
        foreach ($param as $key => $value) {
            $out .=($i!=0?$jonction.' ':' ').$key.'='.$value;
            $i++;
        }
        $sql ='DELETE 
               FROM '._DB_PREFIX_.$table.' 
               WHERE '.$out;
       
        return $this->dao->query($sql);
    }
    
	
	
    /**
     * suppression des données en fonction d'un ensemeble de paramètre
     * @param array $param
     * @param type $identifiant
     * @return type 
     */
    public function deleteChecked(array $param, $identifiant = 'id', $table=''){
        if(trim($table) == '')
            $table = $this->nameTable;
        $sql ='DELETE 
               FROM '._DB_PREFIX_.$table.' 
               WHERE '.$identifiant.' IN ('.  implode(',', $param).')';
        return $this->dao->query($sql);
    }
    
     public function UnActiveChecked(array $param, $identifiant = 'id', $filter= 'is_actived'){
        
        $sql ='UPDATE '._DB_PREFIX_.$this->nameTable.'
               SET '.$filter.'=0
               WHERE '.$identifiant.' IN ('.  implode(',', $param).')';
        return $this->dao->query($sql);
    }
    
     public function ActiveChecked(array $param, $identifiant = 'id', $filter= 'is_actived'){
        
        $sql ='UPDATE '._DB_PREFIX_.$this->nameTable.'
               SET '.$filter.'=1
               WHERE '.$identifiant.' IN ('.  implode(',', $param).')';
        return $this->dao->query($sql);
    }
    
    /**
     * suppression des données en fonction d'un ensemeble de paramètre
     * @param array $param
     * @param type $identifiant
     * @return type 
     */
    public function searchCriteria(array $param, $name){
        $out='';
        $i = 0;
        foreach ($param as $value) {
            $out .= ($i!=0?' OR ':'').$value.' LIKE "%'.$name.'%"';
            $i++;
        }
        $sql ='SELECT DISTINCT t.* ' . $this->getLangSelect() .
                'FROM '._DB_PREFIX_.$this->nameTable.' t ' . $this->getLangJoin() .
               ' WHERE '.$out;
        
        $req = $this->dao->prepare($sql);
		$this->addLangParam($req);
        $req->execute();
        return $this->fecthAssoc_data($req, $this->name);
    }
    
    public function searchCriteriaexact(array $param, array $name){
        $out='';
        $i = 0;
        foreach ($param as $k => $value) {
        $out .= ($i!=0?' AND ':'').$value.'="'.$name[$k].'"';
            $i++;
        }
        $sql ='SELECT DISTINCT t.* ' . $this->getLangSelect() .
                'FROM '._DB_PREFIX_.$this->nameTable.' t ' . $this->getLangJoin() .
               ' WHERE '.$out;
        
        $req = $this->dao->prepare($sql);
		$this->addLangParam($req);
        $req->execute();
        return $this->fecthAssoc_data($req, $this->name);
    }
    
    /**
     * recherche un élement en fonction de son id
     * @param type $id
     * @return type 
     */
    public function findById($id){
        $sql = 'SELECT t.* ' . $this->getLangSelect() .
                'FROM '._DB_PREFIX_.$this->nameTable.' as t ' . $this->getLangJoin() .
                ' WHERE t.id=:id';        
        $req = $this->dao->prepare($sql);
        $req->bindValue(':id', intval($id));
		$this->addLangParam($req);
        $req->execute();
        return $this->fecthRow_data($req, $this->name);    
    }
    
    public function findById2($name, $id, $page=1, $limit = null, $filterOrder = NULL, $order = 'DESC'){
        $sql = 'SELECT t.* ' . $this->getLangSelect() .
                'FROM '._DB_PREFIX_.$this->nameTable.' as t ' . $this->getLangJoin() .
                ' WHERE t.'.$name.'=:name'.
                (isset($filterOrder)?' ORDER BY '.$filterOrder.' '.$order:' ').
                ($limit?' LIMIT '.($page-1)*$limit.', '.$limit:' ');     
        $req = $this->dao->prepare($sql);
        $req->bindValue(':name', intval($id));
		$this->addLangParam($req);
        $req->execute();
        return $this->fecthAssoc_data($req, $this->name);
    }
    
    /**
     * Mise à jour des données d'une table en fonction du champs Id
     * @param type $table
     * @param array $param
     * @param type $id
     * @param type $jonction
     * @return type 
     */
    public function updateRecord($table, array $param, $id, $jonction=','){
        $out='';
        $i=0;
        foreach ($param as $key => $value) {
            $out .=($i!=0?$jonction.' ':'').$key.'="'.(string)$value.'"';
            $i++;
        }
        $sql ='UPDATE '.$table.' as t
               SET '.$out.'
               WHERE t.id=:id';
        $req = $this->dao->prepare($sql);
        $req->bindValue(':id', intval($id));
        return $req->execute();
    }
    
    /**
     * Renvoi une value d'un champ du tableau a partir d'une condition
     * @param type $name
     * @param type $cond
     * @return type 
     */
    public function getValue($name,$cond){
        $sql = 'SELECT '.$name.' FROM '._DB_PREFIX_.$this->nameTable.' WHERE '.(string)$cond;
        $req = $this->dao->query($sql);
        $data = $req->fetch(\PDO::FETCH_OBJ);
        return $data->$name;
    }
    
    /**
     * recherche un enregistrement en fonction d'un nom et d'un champs
     * @param type $name
     * @param type $value
     * @return type 
     */
    public function findByName($name,$value, $filterOrder = NULL, $order = 'DESC'){
        $sql = 'SELECT t.* ' . $this->getLangSelect() .
                'FROM '._DB_PREFIX_.$this->nameTable.' as t ' . $this->getLangJoin() .
                ' WHERE t.'.$name.'=:name '.(isset($filterOrder)?'ORDER BY '.$filterOrder.' '.$order:'');        
        $req = $this->dao->prepare($sql);
        $req->bindValue(':name', $value);
		$this->addLangParam($req);
        $req->execute();
        return $this->fecthAssoc_data($req, $this->name);
    }
    
    public function findInfosStrictInf($name,$value, $filterOrder = NULL, $order = 'DESC',$criteria = NULL, $page=1, $limit = null, $type='date'){
        $sql = 'SELECT t.* ' . $this->getLangSelect() .
                'FROM '._DB_PREFIX_.$this->nameTable.' as t ' . $this->getLangJoin() .
                ' WHERE t.'.$name.'< :name '.
                (isset($criteria)?' AND '.$criteria:' ').(($type == 'date') ? ' AND  t.'.$name.' <> "0000-00-00 00:00:00" ' : '').
                (isset($filterOrder)?' ORDER BY '.$filterOrder.' '.$order:' ').
                ($limit?' LIMIT '.($page-1)*$limit.', '.$limit:' ');        
        $req = $this->dao->prepare($sql);
        $req->bindValue(':name', $value);
		$this->addLangParam($req);
        $req->execute();
        return $this->fecthAssoc_data($req, $this->name);
    }
    
    /**
     * selectionne toutes les entrées d'une table et retourne sous forme de tableau associatif
     * @return type 
     */
    public function findAll(){
        $sql = 'SELECT t.*' . $this->getLangSelect() .
                'FROM '._DB_PREFIX_.$this->nameTable.' as t';
        $req = $this->dao->query($sql);
        $output = array();
        while ($data = $req->fetch(\PDO::FETCH_ASSOC)){            
            $output[] = $data;            
        }
        $req->closeCursor();
        return $output;
    }
    
    /**
     * selectionne toutes les entrées d'une table et retourne sous forme de tableau d'objet
     * @return type 
     */
    public function findAll2($filterOrder = NULL, $order = 'DESC', $page=1, $limit = null){
        $sql = 'SELECT SQL_CALC_FOUND_ROWS t.*' . $this->getLangSelect() .
                'FROM '._DB_PREFIX_.$this->nameTable.' as t  ' . $this->getLangJoin() .
                (isset($filterOrder)?'ORDER BY '.$filterOrder.' '.$order:'').
                ($limit?' LIMIT '.($page-1)*$limit.', '.$limit:' ');
      
        $req = $this->dao->prepare($sql);
		$this->addLangParam($req);
        $req->execute();
        return $this->fecthAssoc_data($req, $this->name);
        
    }
	
	public function getLangJoin()
    {
		$join = ' ';
		if($this->isLangCanBeUsed() && $this->useOfLang){
			$join .= ' LEFT JOIN ' . _DB_PREFIX_ . $this->nameTable.'_lang tl ON (tl.id_' . $this->nameTable .
			' = t.' . $this->primary . ') '. ($this->useOfAllLang ? '' : ' AND (tl.'.self::$langPrimary.' = :lang) ');
		}
        return $join;
    }
	public function getLangSelect()
    {
		$sql = ' ';
		if($this->isLangCanBeUsed() && $this->useOfLang){
			$sql .= ', tl.* ';
		}
        return $sql;
    }
	public function addLangParam($req)
    {
		if($this->isLangCanBeUsed() && !$this->useOfAllLang && !empty($this->lang)){
			$req->bindValue(':lang', $this->lang);
		}
    }
	public function isLangCanBeUsed($object = null)
    {
		if($this->langCanBeUsed === null){
			$object = ($object === null) ? $this->getNewObject() : $object;
			$this->langCanBeUsed = !empty($object->getTabLang());
		}
		return $this->langCanBeUsed;
    }
	public function getNewObject($params = array())
    {
		return new $this->name($params);
    }
	
	
	public function setUseOfAllLang($useOfAllLang)
    {
		$this->useOfAllLang = $useOfAllLang;
    }
	
	public function setLang($lang)
    {
		$this->lang = $lang;
    }
	
	public function setUseOfLang($useOfLang)
    {
		$this->useOfLang = $useOfLang;
    }
	
    /**
     * ajout d'un enregistrement dans un objet reccord
     * @param array $params
     * @return type 
     */
    public function add(array $params){
    	
        if(is_array($params)){
            $objData = new $this->name($params);
            $fields = array();
           
            foreach ($params as $key => $value) {
                $methode = 'get'.ucfirst($key);
                if (is_callable(array($objData, $methode)) && property_exists($objData, $key)){
                    $fields[$key] = ':'.$key;
                    
                }
            }
            
            
            $sql='INSERT INTO '._DB_PREFIX_.$this->nameTable.' ('.implode(',', array_flip($fields)).') VALUES('.implode(',', $fields).')';
            
            $req=$this->dao->prepare($sql);        
            foreach ($params as $key => $value) {
                $methode = 'get'.ucfirst($key);
                if (is_callable(array($objData, $methode)) && property_exists($objData, $key)){
                    $req->bindParam(':'.$key,$objData->$methode());
                }            
            }
            $result = $req->execute();
			if($result){
				$result = $this->saveMultilangFields($objData, false);
			}
            return $result;
        }
    }
    /**
     * mise à jour d'une table d'un objet reccord
     * @param array $params
     * @param type $cond
     * @return type 
     */
    public function update(array $params,$cond){
        if(is_array($params)){
            $objData = new $this->name($params);            
            $fields = '';
            foreach ($params as $key => $value) {
                $methode = 'get'.ucfirst($key);
                if (is_callable(array($objData, $methode)) && property_exists($objData, $key)){
                    $fields .= $key.'=:'.$key.',';
                }
            }
            $fields =  substr($fields, 0, -1);
            
            $sql='UPDATE '._DB_PREFIX_.$this->nameTable.' SET '.$fields.' WHERE '.$cond.'=:'.$cond;
            $req=$this->dao->prepare($sql);
            
            foreach ($params as $key => $value) {
                $methode = 'get'.ucfirst($key);
                if (is_callable(array($objData, $methode)) && property_exists($objData, $key)){
                    $req->bindParam(':'.$key,$objData->$methode());
                }            
            }
            $result = $req->execute();
			if($result){
				$result = $this->saveMultilangFields($objData, true);
			}
            return $result;
        }
    }
	public function saveMultilangFields($objData, $update = false)
    {
		$result = true;
		if($this->isLangCanBeUsed()){
			$method = 'get' . ucfirst($objData->getPrimary());
			$idObject = ($update)? $objData->$method() : (int) $this->getLasId();
			if($update){
				$sqlInit = $this->getLangUpdateSqlInit($objData);
			}else{
				$addSqlInit= $this->getLangAddSqlInit($objData);
			}
			$isLangValuesFormatted =false;
			foreach ($this->languages as $lang){
				if($update && $this->isObjectSavedForLang($idObject, $lang)){
					$sql = $sqlInit;
				}else{
					if(!isset($addSqlInit)){
						$addSqlInit = $this->getLangAddSqlInit($objData);
					}
					$sql = $addSqlInit;
				}
				$req=$this->dao->prepare($sql);
				$req->bindParam(':id_' . $this->nameTable, $idObject);
				$req->bindParam(':' . self::$langPrimary, $lang->getIso_code());
				if(!$isLangValuesFormatted){
					$objData->fillMultilangEmptyFields();
				}
				foreach ($objData->getTablang() as $field){
					$method = 'get'.ucfirst($key);
					if (is_callable(array($objData, $method)) && property_exists($objData, $field)){
						$req->bindParam(':'.$field, $objData->$method()[$lang]);
					} 
				}
				$isLangValuesFormatted = true;
				$result = ($result && (bool)$req->execute());
			}
		}
		return $result;
    }
    
    public function getLangAddSqlInit($objData)
    {
    	$sqlInit='INSERT INTO ' . _DB_PREFIX_ . $this->nameTable . '_lang (' . 'id_'.$this->nameTable . ', ' . self::$langPrimary . ', ' .
      	implode(',', $objData->getTablang()) . ') VALUES(:id_' . $this->nameTable . ', :' . self::$langPrimary .
		', :' . implode(',:', $objData->getTablang()).')';
      	return $sqlInit;
    }
    
    public function getLangUpdateSqlInit($objData)
    {
    	$sqlInit= 'UPDATE '._DB_PREFIX_.$this->nameTable.'_lang SET ';
    	$first= true;
    	foreach ($objData->getTablang()as $field){
    		if(!$first){
    			$sqlInit.=', ';
    		}
    		$sqlInit.=$field.' = :'.$field;
    		$first = false;
    	}
    	$sqlInit.=' WHERE (id_'.$this->nameTable . ' = :id_' . $this->nameTable . ') AND (' . self::$langPrimary . ' = :' . self::$langPrimary . ')';
    	return $sqlInit;
    }
    
    public function isObjectSavedForLang($idObject, $lang)
    {
    	$sql = 'SELECT COUNT(*) AS number FROM '._DB_PREFIX_.$this->nameTable.
    	'_lang WHERE (id_'.$this->nameTable.' = :id_' . $this->nameTable . ') AND (' . self::$langPrimary . ' = :' . self::$langPrimary . ')';
    	$req=$this->dao->prepare($sql);
    	$codeLang = $lang->getIso_code();
    	$req->bindParam(':id_' . $this->nameTable, $idObject);
    	$req->bindParam(':' . self::$langPrimary, $codeLang);
    	$req->execute();
    	$data = $req->fetch(\PDO::FETCH_OBJ);
    	return (int)$data->number;
    }
    /**
     *Sélectionne Toutes les tables de la base de données
     * @return type 
     */
     public function getDBTables(){
        $sql = "SELECT TABLE_NAME 
                    FROM INFORMATION_SCHEMA.TABLES
                    WHERE TABLE_TYPE = 'BASE TABLE' AND TABLE_SCHEMA = '"._DB_NAME_."'";
        $req = $this->dao->query($sql);
        
        return $req->fetchAll(\PDO::FETCH_OBJ);
        
    }
    /**
     *Sélectionne Touts les attributs d'une table
     * @return type 
     */
     public function getTableAttributes($table){
        $sql = "SELECT * 
                FROM INFORMATION_SCHEMA.COLUMNS 
                WHERE TABLE_NAME='".$table."' AND TABLE_SCHEMA = '"._DB_NAME_."'";
        $req = $this->dao->query($sql);
        //var_dump($req); die();
        return $req->fetchAll(\PDO::FETCH_OBJ);
        
    }
    
   
    
    public function getTableAttributesContraintes($table, $field){
        $sql = "SELECT * 
                FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
                WHERE TABLE_NAME='".$table."' 
                    AND TABLE_SCHEMA = '"._DB_NAME_."'
                    AND COLUMN_NAME = '".$field."'
                    AND REFERENCED_TABLE_NAME IS NOT NULL";
        //echo $sql;
        $req = $this->dao->query($sql);
        
        return $req->fetchAll(\PDO::FETCH_OBJ);   
    }
    
    
    public function getTableContraintsParams($table){
        $sql = "SELECT * 
                FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
                WHERE TABLE_NAME='".$table."' 
                    AND TABLE_SCHEMA = '"._DB_NAME_."'
                    AND REFERENCED_TABLE_NAME IS NOT NULL";
        //echo $sql;
        $req = $this->dao->query($sql);
        
        return $req->fetchAll(\PDO::FETCH_OBJ);      
    }
    /**
     * return last id insert
     * @return type 
     */
    public function getLasId(){
        return $this->dao->lastInsertId();
    }
    /**
     * retourn le nombre d'elt s'il n 'y avait pas la notion limit de la précedente requete executée
     * @return type 
     */
    public function getNumberRows(){
        $sql='SELECT FOUND_ROWS() AS number';
        $req = $this->dao->query($sql);
        $data = $req->fetch(\PDO::FETCH_OBJ);
        return $data->number;
    }
    
	/**
	 * 
	 * @param type $dir
	 * @return type array of dirnamefile
	 */
	public function getDir($dir){
    	$dh  = opendir($dir);
    	while (($filename = readdir($dh)) ) {
    		if(is_dir($dir."/".$filename) && $filename!=="." && $filename!="..")
			$files[] = $filename;
    	}
    	sort($files);
    	return $files;
    	
    }
}

?>
