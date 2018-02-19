<?php
 /**
  * Description of AbonnementManager
  *
  * @author Luc Alfred MBIDA
  **/

   namespace Applications\Modules\Menu\Models;

   if( !defined('IN') ) die('Hacking Attempt');

    use Library\Manager;

   abstract class MenuManager extends Manager{
        protected $name = 'Applications\Modules\Menu\Models\Menu';
        protected $nameTable = 'menu';
                                        
    }
 ?>