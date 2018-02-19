<?php

/**
 * Description of ConnexionManager
 *
 * @author FFOZEU
 */
namespace Applications\Modules\Utilisateurs\Models;

use Library\Manager;

abstract class UtilisateursManager extends Manager{

    protected $name ='Applications\Modules\Utilisateurs\Models\Utilisateurs';
    protected $nameTable ='user';
    protected $nameTableGroupe ='group';
    protected $nameTableDroit ='right';
    protected $nameTableGroupRight ='group_right';
    protected $nameTableUserGroup ='user_group';
    protected $nameTableGroupModuleAccess ='group_module_access';
    /*
     * recupère les informations d'un utilisateur
     */
    abstract public function getInformations();

    /*
     * vérification des paramètres de connexion de l'Utilisateurs
     */
    abstract public function verifLogin($param1, $param2);
    abstract public function verifEmail($email);

    /*
     * vérification des paramètres lors de l'inscription de l'Utilisateurs
     */
    abstract public function verifInscription($param1, $param2);

    abstract public function addUser($params);
    abstract public function addGroupUser($nameG,$params2);
    abstract public function addGroupPrivilege($idGroup, $idPriv);

    abstract public function VerifieGroupPrivilege($idGroup, $idPriv);

    abstract public function findUserById($id);

    abstract public function updateUser($params);
    abstract public function updateGroupUser($id, $nameG,$params2);

    abstract public function deleteUser(array $id);
    abstract public function deleteAllUserGroup($id);
    abstract public function deleteAllGroupPrivileges($id);
    // supprime les groupes de l'itulisateur
    abstract public function deleteGroupesUser(array $id);

    //supprime un privilège pour le group
    abstract public function deletePvilegeForGroup($idGroup, $idPriv);

    abstract public function defineUserGroup($idUser, $idGroup);

    abstract public function getUtilisateurs();
    abstract public function getUtilisateursByID($id);
    abstract public function getGroupeUtilisateurs();
    abstract public function getGroupeUtilisateursById($id);
    abstract public function getDroit();
    abstract public function getDroitGroup($idG);
    abstract public function getGroupesUtilisateur($idUser);
    abstract public function getLastUtilisateurs();
    abstract public function getLastGroupUtilisateurs();


    public function getTableName(){
        return $this->nameTable;
    }
    
    public function getTableNameGroupe(){
        return $this->nameTableGroupe;
    }

    public function getTableNameDroit(){
        return $this->nameTableDroit;
    }
    
    public function getTableNameGroupRight(){
        return $this->nameTableGroupRight;
    }

}

?>
