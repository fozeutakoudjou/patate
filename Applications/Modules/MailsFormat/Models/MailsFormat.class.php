<?php
/**
* Description of MailsFormat
*
* @author francis fozeu
*/
namespace Applications\Modules\MailsFormat\Models;

if( !defined('IN') ) die('Hacking Attempt');

use Library\Record;

class MailsFormat extends Record{
     protected $id;
     protected $template;
     protected $title;
     protected $content;
     protected $active;
     protected $date_add;
     protected $date_upd;
     protected $tabType = array('content'=>'html');

     // SETTERS
     public function setId($id){
        $this->id = $id;
    }
     public function setTemplate($template){
        $this->template = $template;
    }
     public function setTitle($title){
        $this->title = $title;
    }
     public function setContent($content){
        $this->content = $content;
    }
     public function setActive($active){
        $this->active = $active;
    }
     public function setDate_add($date_add){
        $this->date_add = $date_add;
    }
     public function setDate_upd($date_upd){
        $this->date_upd = $date_upd;
    }

           // GETTERS
     public function getId(){
        return $this->id;
    }
     public function getTemplate(){
        return $this->template;
    }
     public function getTitle(){
        return $this->title;
    }
     public function getContent(){
        return $this->content;
    }
     public function getActive(){
        return $this->active;
    }
     public function getDate_add(){
        return $this->date_add;
    }
     public function getDate_upd(){
        return $this->date_upd;
    } 

}
?>