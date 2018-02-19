<?php
/**
 * Description of Connexion
 *
 * @author FFOZEU
 */
namespace Applications\Modules\Utilisateurs\Models;

if( !defined('IN') ) die('Hacking Attempt');

use Library\Record;

class Utilisateurs extends Record{
    protected $id;
    protected $pseudo;
    protected $email;
    protected $password;
    protected $nom;
    protected $prenom;
    protected $adresse                      = "";
    protected $avatar                       = "";
    protected $is_active                    = 1;
    protected $newsletter                   = 1;
    
    protected $pays                         = "";
    protected $ville                        = "";
    protected $code_postal                  = "";
    protected $tel1                         = "";
    protected $tel2                         = "";
    protected $infos_complementaires        = "";
    protected $nbProspectToregister         = 0;
    protected $type_user                    = "";
    
    public function isAuthenticated() {

        if( !empty($_SESSION['pseudo']) AND !empty($_SESSION['password']) ) return TRUE;
		else return FALSE;

    }

    public function setId($id) {

        $this->id = $id;

    }
    
    public function setNbProspectToregister($nbProspectToregister) {
        $this->nbProspectToregister = $nbProspectToregister;
    }

    public function setNewsletter($newsletter){
        $this->newsletter = $newsletter;
    }

    public function setPseudo($pseudo) {

        $this->pseudo = $pseudo;

    }

    public function setEmail($email) {

        $this->email = $email;

    }

    public function setPassword($password) {

        $this->password = $password;

    }

    public function setNom($nom) {

        $this->nom = $nom;

    }

    public function setPrenom($prenom) {

        $this->prenom = $prenom;

    }

    public function setAdresse($adresse) {

        $this->adresse = $adresse;

    }
	
    public function setAvatar($avatar) {

        $this->avatar = $avatar;

    }

    public function setIs_active($actived) {

        $this->is_active = $actived;

    }
    
    public function setPays($pays) {

        $this->pays = $pays;
    }
    
    public function setVille($ville) {

        $this->ville = $ville;
    }
    
    public function setCode_postal($code_postale) {

        $this->code_postale = $code_postale;
    }
    
    public function setTel1($tel1) {

        $this->tel1 = $tel1;
    }
    
    public function setTel2($tel2) {

        $this->tel2 = $tel2;
    }
    
    public function setInfos_complementaires($infos_complementaires) {

        $this->infos_complementaires = $infos_complementaires;
    }

     
    public function getId() {

        return $this->id;

    }

    public function getPseudo() {

        return $this->pseudo;

    }

    public function getEmail() {

        return $this->email;

    }

    public function getNom() {

        return $this->nom;

    }

    public function getPrenom() {

        return $this->prenom;

    }

    public function getAdresse() {

        return $this->adresse;

    }

    
    public function getAvatar() {

        return $this->avatar;

    }

    public function getPassword(){
        return $this->password;
    }

    public function getConditions(){
        return $this->conditions;
    }

    public function getRole(){
        return $this->role;
    }

    public function getRegDate() {

        return $this->regDate;

    }

    public function getCountryName() {

        return $this->countryName;

    }

    public function getIs_active() {

        return $this->is_active;

    }

     public function getNewsletter(){
        return $this->newsletter;
    }
    
    public function getPays() {

        return $this->pays;
    }
    
    public function getVille() {

        return $this->ville;
    }
    
    public function getCode_postal() {

        return $this->code_postale;
    }
    
    public function getTel1() {

        return $this->tel1;
    }
    
    public function getTel2() {

        return $this->tel2;
    }
    
    public function getInfos_complementaires() {

        return $this->infos_complementaires;
    }
    
    public function getNbProspectToregister() {
        return $this->nbProspectToregister;
    }
}

?>
