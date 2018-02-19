<?php
/**
* Description of MailsFormatManager
*
* @author francis fozeu
*/
namespace Applications\Modules\MailsFormat\Models;

if( !defined('IN') ) die('Hacking Attempt');

use Library\Manager;

abstract class MailsFormatManager extends Manager{
    protected $name = 'Applications\Modules\MailsFormat\Models\MailsFormat';
    protected $nameTable ="mailsformat";
    // Inserer votre code ici
}
?>