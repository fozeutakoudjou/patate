<?php
namespace core;
/**
 * Description of Cache
 *
 * @author ffozeu
 */
class Cache extends ApplicationComponent{
    //put your code here
    protected $duration = 0;
    
    public function __construct(Application $app){
        parent::__construct($app);
        $this->duration = __CACHE_LIFETIME__;
    }
    public function setCache($filename, $content){
        $file = _SITE_CACHE_DIR_.$filename.'.tpl.php';
        if(is_dir(_SITE_CACHE_DIR_)){
            file_put_contents($file, $content);
            @chmod($file, 0777);
        }
    }
    /**
     * verifie si le fichier est en cache
     * @param type $filenames
     * @return type
     */
    public function isCache($filenames){
        $returnval = false;
        if(is_array($filenames)){
            foreach ($filenames as $filename) {
                $file = _SITE_CACHE_DIR_.$filename.'.tpl.php';
                $returnval = $this->valideCache($file);
            }
        }else{
            $file = _SITE_CACHE_DIR_.$filenames.'.tpl.php';
            $returnval = $this->valideCache($file);
        }
        return $returnval;
    }
    /**
     * teste la validité du fichier en cache
     * @param type $file
     * @return boolean
     */
    private function valideCache($file){
        if(is_file($file) && file_exists($file)){
            $lifetime = time() - filemtime($file);
            if($this->duration > $lifetime)
               return true; 
        }
        return false;
    }
    /**
     * charge un fichier du cache
     * @param type $filename
     * @return type
     */
    public function load($filename){
        $file = _SITE_CACHE_DIR_.$filename.'.tpl.php';
        if(file_exists($file)){
           return file_get_contents($file);
        }
    }
    /**
     * supprime un fichier du cache
     * @param type $filename
     */
    public function delete($filename){
        $file = _SITE_CACHE_DIR_.$filename.'.tpl.php';
        if(file_exists($file)){
            unlink($file);
        }
    }
    /**
     * retourne le nom du cache dir
     * @return string|null
     */
    public function getCacheDir(){
        if(is_dir(_SITE_CACHE_DIR_))
            return _SITE_CACHE_DIR_;
        else 
            return null;
    }
    /**
     * nettoyage non recursif du répertoire du cache
     */
    public function clearCache(){
        $files = glob(_SITE_CACHE_DIR_.'*');
        foreach ($files as $file) {
            unlink($file);
        }
        
        $content = '<?php
                        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
                        header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");

                        header("Cache-Control: no-store, no-cache, must-revalidate");
                        header("Cache-Control: post-check=0, pre-check=0", false);
                        header("Pragma: no-cache");

                        header("Location: ../");
                        exit;

                    ?>';
        
        $filindex = _SITE_CACHE_DIR_.'index.php';
        file_put_contents($filindex, $content);
    }
}

?>
