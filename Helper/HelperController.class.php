<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Helper;

if( !defined('IN') ) die('Hacking Attempt');

Use Library\MainController;
Use Library\Tools;

/**
 * Description of HelperController
 *
 * @author hus
 */
class HelperController extends MainController{
    //put your code here
    protected $name ='';
    
    protected function init(){
        $this->page->addVar('tools', new Tools());
        parent::init();
    }
}
