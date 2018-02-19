<?php
namespace Library;

/**
 * Description of ApplicationComponent
 *
 * @author FFOZEU
 */
abstract class ApplicationComponent {
    /*charge l'application en cours
     */
    protected $app;
    protected $path;


    public function __construct(Application $app,$path=null){
        $this->app = $app;
        $this->path = $path;
    }
    public function app(){
        return $this->app;
    }
    public function getPath(){
        return $this->path;
    }
}

?>
