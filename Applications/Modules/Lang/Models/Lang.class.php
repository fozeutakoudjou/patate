<?php
    /**
    * Description of Lang
    *
    * @author Luc Alfred MBIDA
    */
    namespace Applications\Modules\Lang\Models;

    if( !defined('IN') ) die('Hacking Attempt');

    use Library\Record;

    class Lang extends Record{
        protected $id_lang;
        protected $name;
        protected $active;
        protected $iso_code;
        protected $language_code;
        protected $date_format_lite;
        protected $date_format_full;
        protected $is_rtl;

        // SETTERS
        public function setId_lang($id_lang){
           $this->id_lang = $id_lang;
        }
        public function setName($name){
           $this->name = $name;
        }
        public function setActive($active){
           $this->active = $active;
        }
        public function setIso_code($iso_code){
           $this->iso_code = $iso_code;
        }
        public function setLanguage_code($language_code){
           $this->language_code = $language_code;
        }
        public function setDate_format_lite($date_format_lite){
           $this->date_format_lite = $date_format_lite;
        }
        public function setDate_format_full($date_format_full){
           $this->date_format_full = $date_format_full;
        }
        public function setIs_rtl($is_rtl){
           $this->is_rtl = $is_rtl;
        }

        // GETTERS
        public function getId_lang(){
           return $this->id_lang;
        }
        public function getName(){
           return $this->name;
        }
        public function getActive(){
           return $this->active;
        }
        public function getIso_code(){
           return $this->iso_code;
        }
        public function getLanguage_code(){
           return $this->language_code;
        }
        public function getDate_format_lite(){
           return $this->date_format_lite;
        }
        public function getDate_format_full(){
           return $this->date_format_full;
        }
        public function getIs_rtl(){
           return $this->is_rtl;
        } 

  }
?>