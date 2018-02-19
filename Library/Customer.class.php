<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Customer
 *
 * @author fozeu wife
 */
namespace Library;

class Customer extends User{
    //put your code here
    
    private $_name ='user';
    
    public function __construct(Application $app){
        parent::__construct($app);
    }
    
    
    
}

?>
