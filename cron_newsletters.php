<?php
    //require dirname(__FILE__).'/Library/autoload.php';
    //header("Location: "._BASE_URI_.'cronnewsletters.html');
?>
<?php
use Applications\Frontend\FrontendApplication;

//dirname(__FILE__).
//require 'Library/autoload.php';
require dirname(__FILE__).'/../Library/autoload.php';

$app = new FrontendApplication;
$app->run(true,'/cronnewsletters.html');
?>