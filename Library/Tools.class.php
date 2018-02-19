<?php
namespace Library;

if( !defined('IN') ) die('Hacking Attempt');
/**
 * Description of Tools
 *
 * @author FFOZEU
 */

class Tools {
    public static $errors = array();

#########################
#   Gestion des images  #
#########################
    /**
    * Check icon upload
    *
    * @param array $file Upload $_FILE value
    * @param integer $maxFileSize Maximum upload size
    */
   public static function checkIco($file, $maxFileSize)
    {
        if ($file['size'] > $maxFileSize)
            return ('Image is too large').' ('.($file['size'] / 1000).'ko). '.('Maximum allowed:').' '.($maxFileSize / 1000).'ko';
        if (substr($file['name'], -4) != '.ico')
            return ('Image format not recognized, allowed formats are: .ico');
        if ($file['error'])
            return ('Error while uploading image; please change your server\'s settings.');
        return false;
    }
    /**
    * Check image upload
    *
    * @param array $file Upload $_FILE value
    * @param integer $maxFileSize Maximum upload size
    */
   public static function checkImage($file, $maxFileSize)
    {
        if ($file['size'] > $maxFileSize)
            return 'Image is too large ('.($file['size'] / 1000).'KB). Maximum allowed: '.($maxFileSize / 1000).'KB';
        if (!isPicture($file))
            return 'Image format not recognized, allowed formats are: .gif, .jpg, .png';
        if ($file['error'])
            return 'Error while uploading image; please change your server\'s settings. Error code: '.$file['error'];
        return false;
    }
    /**
     *
     * @param type $file
     * @return type 
     */
    public static function checkImageUploadError($file)
    {
        if ($file['error'])
        {
            switch ($file['error'])
            {
                case 1:
                    return ('The file is too large.');
                    break;

            case 2:
                    return ('The file is too large.');
                    break;

                case 3:
                    return ('The file was partialy uploaded');
                    break;

                case 4:
                    return ('The file is empty');
                    break;
            }
        }
    }
    /**
     *
     * @param type $sourceFile
     * @param type $destFile
     * @param type $destWidth
     * @param type $destHeight
     * @param string $fileType
     * @return boolean 
     */
    public static function imageResize($sourceFile, $destFile, $destWidth = NULL, $destHeight = NULL, $fileType = 'jpg')
    {
        if (!file_exists($sourceFile))
            return false;
        list($sourceWidth, $sourceHeight, $type, $attr) = getimagesize($sourceFile);
        // If PS_IMAGE_QUALITY is activated, the generated image will be a PNG with .jpg as a file extension.
        // This allow for higher quality and for transparency. JPG source files will also benefit from a higher quality
        // because JPG reencoding by GD, even with max quality setting, degrades the image.
        if ($type == IMAGETYPE_PNG)
            $fileType = 'png';

        if (!$sourceWidth)
            return false;
        if ($destWidth == NULL) $destWidth = $sourceWidth;
        if ($destHeight == NULL) $destHeight = $sourceHeight;

        $sourceImage = self::createSrcImage($type, $sourceFile);

        $widthDiff = $destWidth / $sourceWidth;
        $heightDiff = $destHeight / $sourceHeight;

        if ($widthDiff > 1 AND $heightDiff > 1)
        {
            $nextWidth = $sourceWidth;
            $nextHeight = $sourceHeight;
        }
        else
        {
            if ($widthDiff > $heightDiff)
            {
                $nextHeight = $destHeight;
                $nextWidth = round(($sourceWidth * $nextHeight) / $sourceHeight);
                $destWidth =  $nextWidth;
            }
            else
            {
                $nextWidth = $destWidth;
                $nextHeight = round($sourceHeight * $destWidth / $sourceWidth);
                $destHeight = $nextHeight;
            }
        }

        $destImage = imagecreatetruecolor($destWidth, $destHeight);

        // If image is a PNG and the output is PNG, fill with transparency. Else fill with white background.
        if ($fileType == 'png' && $type == IMAGETYPE_PNG)
        {
            imagealphablending($destImage, false);
            imagesavealpha($destImage, true);	
            $transparent = imagecolorallocatealpha($destImage, 255, 255, 255, 127);
            imagefilledrectangle($destImage, 0, 0, $destWidth, $destHeight, $transparent);
        }else
        {
            $white = imagecolorallocate($destImage, 255, 255, 255);
            imagefilledrectangle($destImage, 0, 0, $destWidth, $destHeight, $white);
        }

        imagecopyresampled($destImage, $sourceImage, (int)(($destWidth - $nextWidth) / 2), (int)(($destHeight - $nextHeight) / 2), 0, 0, $nextWidth, $nextHeight, $sourceWidth, $sourceHeight);

        return (self::returnDestImage($fileType, $destImage, $destFile));
    }
    
    /**
     * generate image
     * @param type $type
     * @param type $ressource
     * @param type $filename
     * @return type 
     */
    public static function returnDestImage($type, $ressource, $filename)
    {
        $flag = false;
        switch ($type)
        {
            case 'gif':
                $flag = imagegif($ressource, $filename);
                break;
            case 'png':
                $quality = 7 ;
                $flag = imagepng($ressource, $filename, (int)$quality);
                break;		
            case 'jpg':
            case 'jpeg':
            default:
                $quality = 90 ;
                $flag = imagejpeg($ressource, $filename, (int)$quality);
                break;
        }
        imagedestroy($ressource);
        @chmod($filename, 0664);
        return $flag;
    }
    /**
     * image cut
     * @param type $srcFile
     * @param type $destFile
     * @param type $destWidth
     * @param type $destHeight
     * @param type $fileType
     * @param type $destX
     * @param type $destY
     * @return boolean 
     */
    public static function imageCut($srcFile, $destFile, $destWidth = NULL, $destHeight = NULL, $fileType = 'jpg', $destX = 0, $destY = 0)
    {
        if (!isset($srcFile['tmp_name']) OR !file_exists($srcFile['tmp_name']))
            return false;

        // Source infos
        $srcInfos = getimagesize($srcFile['tmp_name']);
        $src['width'] = $srcInfos[0];
        $src['height'] = $srcInfos[1];
        $src['ressource'] = createSrcImage($srcInfos[2], $srcFile['tmp_name']);

        // Destination infos
        $dest['x'] = $destX;
        $dest['y'] = $destY;
        $dest['width'] = $destWidth != NULL ? $destWidth : $src['width'];
        $dest['height'] = $destHeight != NULL ? $destHeight : $src['height'];
        $dest['ressource'] = createDestImage($dest['width'], $dest['height']);

        $white = imagecolorallocate($dest['ressource'], 255, 255, 255);
        imagecopyresampled($dest['ressource'], $src['ressource'], 0, 0, $dest['x'], $dest['y'], $dest['width'], $dest['height'], $dest['width'], $dest['height']);
        imagecolortransparent($dest['ressource'], $white);
        $return = self::returnDestImage($fileType, $dest['ressource'], $destFile);
        return	($return);
    }
    /**
     *
     * @param type $type
     * @param type $filename
     * @return type 
     */
    public static function createSrcImage($type, $filename)
    {
        switch ($type)
        {
            case 1:
                return imagecreatefromgif($filename);
                break;
            case 3:
                return imagecreatefrompng($filename);
                break;
            case 2:
            default:
                return imagecreatefromjpeg($filename);
                break;
        }
    }
    /**
     *
     * @param type $width
     * @param type $height
     * @return type 
     */
    public static function createDestImage($width, $height){
        $image = imagecreatetruecolor($width, $height);
        $white = imagecolorallocate($image, 255, 255, 255);
        imagefill($image, 0, 0, $white);
        return $image;
    }

    /**
     * return array of differents value of image width
     * @return int 
     */
    public static function getArrayWidthHeight($type){
        $out = array();
        switch ($type) {
            case 'category':
                $out = array('cat'=>array('width'=>118,'height'=>89));
                break;
            
            case 'annonces':
                $out = array(
                    'meduim'=>array('width'=>132,'height'=>105),
                    'small'=>array('width'=>60,'height'=>57),
                    'big'=>array('width'=>316,'height'=>239),
                    'other'=>array('width'=>96,'height'=>67),
                    'speciale'=>array('width'=>107,'height'=>70),
                    'une'=>array('width'=>209,'height'=>127),
                    );
                break;

            case 'adversiting':
                $out = array(
                    'pub_1'=>array('width'=>584,'height'=>134),
                    'pub_2'=>array('width'=>227,'height'=>322),
                    'pub_3'=>array('width'=>229,'height'=>359),
                    'pub_4'=>array('width'=>232,'height'=>276),
                    'pub_5'=>array('width'=>592,'height'=>127),
                    'pub_6'=>array('width'=>578,'height'=>114),
                    );
                break;
            
            default:
                array('meduim'=>array('width'=>118,'height'=>89));
                break;
        }
        return $out;
    }
    /**
     * supprime un fichier
     * @param type $filename
     * @param type $directory 
     */
    public static function deleteFile($filename,$directory = _SITE_UPLOAD_DIR_){
        $filedel = $directory.$filename;
        if(file_exists($filedel)){
            unlink($filedel);
        }
                
    }
#########################
#Fin gestion des images #
#########################
    /**
     * upload image on your server
     * @param type $source
     * @param type $destination
     * @param type $multiple
     * @param type $filePost
     * @return boolean 
     */
    public static function addFile($source, $destination = _SITE_UPLOAD_DIR_, $multiple=true, $filePost=null,$type='default',$namefinal=null){
        
        if(empty($_FILES[$source]['tmp_name'])){
            if(isset($_POST[$filePost]))
                return $_POST[$filePost];
            return false ;
        }
        $fichier = basename($_FILES[$source]['name']);
        $path_parts = pathinfo($_FILES[$source]['tmp_name']);
        $filename = $path_parts['filename'];
		$taille_maxi = 2097152; //2Mo
		$taille = filesize($_FILES[$source]['tmp_name']);
		$extensions = array('.GIF','.JPEG','.JPG','.PNG','.png', '.gif', '.jpg', '.jpeg');
        $extensions_otherFile = array('.SWF', '.swf');
		$extension = strrchr($_FILES[$source]['name'], '.');
		//Début des vérifications de sécurité...
        if((!in_array($extension, $extensions) && !in_array($extension, $extensions_otherFile)))
            self::$errors[] = _NO_EXTENSION_FILE_;
		if($taille>$taille_maxi)
			 self::$errors[] = _BIG_FILE_;
        if(!sizeof(self::$errors)){
           
            $fichier = date('d-m-Y-H-i-s').strtr($fichier,'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ','AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy');
            $fichier = preg_replace('/([^.a-z0-9]+)/i', '-', $fichier);
            if($namefinal!=null)
                $fichier = $namefinal.substr($fichier, strlen($fichier)-4);
            if(move_uploaded_file($_FILES[$source]['tmp_name'], $destination . $fichier)){
                //si c'est une image et que l'on souhaite créé les miniatures
                if(in_array($extension, $extensions) && $multiple){
                    $tabArrayVal = self::getArrayWidthHeight($type);
                    foreach ($tabArrayVal as $key => $value)
                        self::imageResize($destination . $fichier, $destination . $key.$fichier, $value['width'], $value['height']);
                }
                return $fichier ;
            }else
                return self::$errors[] = _ERROR_UPLOADING_;                
        }else
            return self::$errors;
    }
    
    public static function createFileImage($source, $destination = _SITE_UPLOAD_DIR_, $width=100, $height=100, $finalname="default", $ext='jpg', $deleteOriginal = true){
        
        if(empty($_FILES[$source]['tmp_name'])){
            if(isset($_POST[$filePost]))
                return $_POST[$filePost];
            return false ;
        }
        $fichier = basename($_FILES[$source]['name']);
        $path_parts = pathinfo($_FILES[$source]['tmp_name']);
        $filename = $path_parts['filename'];
		$taille_maxi = 2097152; //2Mo
		$taille = filesize($_FILES[$source]['tmp_name']);
		$extensions = array('.GIF','.JPEG','.JPG','.PNG','.png', '.gif', '.jpg', '.jpeg');
        $extensions_otherFile = array('.SWF', '.swf');
		$extension = strrchr($_FILES[$source]['name'], '.');
		//Début des vérifications de sécurité...
        if((!in_array($extension, $extensions) && !in_array($extension, $extensions_otherFile)))
            self::$errors[] = _NO_EXTENSION_FILE_;
		if($taille>$taille_maxi)
			 self::$errors[] = _BIG_FILE_;
        if(!sizeof(self::$errors)){
            $fichier = $finalname.'.'.$ext;
            
            if(move_uploaded_file($_FILES[$source]['tmp_name'], $destination . $fichier)){
                //si c'est une image et que l'on souhaite créé les miniatures
                self::imageResize($destination . $fichier, $destination.$fichier, $width, $height);
                return $fichier ;
            }else
                return self::$errors[] = _ERROR_UPLOADING_;                
        }else
            return self::$errors;
    }
    
    public function getLinkImage($mod='Annonce',$type='meduim',$image='', $defaultImg = 'default.png'){
        if($image != ''){
            if(file_exists(_SITE_UPLOAD_DIR_.$mod.'/'.$type.$image))
                $img = _UPLOAD_DIR_.$mod.'/'.$type.$image;
            else
               $img = _UPLOAD_DIR_.$mod.'/'.$type.($defaultImg!=''?$defaultImg:'default.png');
            
        }else{
            $img = _UPLOAD_DIR_.$mod.'/'.$type.($defaultImg!=''?$defaultImg:'default.png');
        }
        return $img;
    }
    
    public function getLinkFlash($mod='Annonce',$image=''){
        return _UPLOAD_DIR_.$mod.'/'.$image;
    }
    
    public function getLinkAnnonce($category,$annonce){
        if(is_object($annonce))
            $link = _BASE_URI_.$category->getLink_rewrite().'/'.$annonce->getLink_rewrite().'.html';
        elseif(is_object($category))
            $link = _BASE_URI_.$category->getLink_rewrite().'/'.(string)$annonce.'.html';
        else
            $link = _BASE_URI_.(string)$category.'/'.(string)$annonce.'.html';
        return $link;
    }
    
     public function getLinkAnnonce2($category,$annonce){
        if(is_object($annonce))
            $link =$category->getLink_rewrite().'/'.$annonce->getLink_rewrite().'.html';
        elseif(is_object($category))
            $link = $category->getLink_rewrite().'/'.(string)$annonce.'.html';
        else
            $link = (string)$category.'/'.(string)$annonce.'.html';
        return $link;
    }
    
    public function getLinkCategory($category){
        $link = _BASE_URI_.$category->getLink_rewrite();
        return $link;
    }


    public function displayPrice($price,$currency='Fcfa'){
        $price = number_format($price, 0, '', '.');
        return $price.' '.$currency;
    }
    
    /**
     * inclure some template for default theme
     * @param string $file
     * @param array $tabVars
     * @param type $application
     * @param type $templates
     */
    public function includeFileTemplates($file,array $tabVars,$application='Frontend', $templates='Default'){
        //var_dump($file);
        extract($tabVars);
        $sep = DIRECTORY_SEPARATOR;
        $file = _SITE_APP_DIR.ucfirst($application).$sep.'Templates'.$sep.ucfirst($templates).$sep.$file.'.tpl.php';
        if(file_exists($file))
            require $file;
    }
    
    public function includeView($view,$mod,array $tabVars,$application='Frontend'){
        extract($tabVars);
        $file = _SITE_MOD_DIR.$mod.'/'.$application.'/Views/'.$view.'.tpl.php';
        if(file_exists($file))
            require $file;
    }
    
    /**
    * fonction permettant de sectionner une chaine selon une longueur donné
    * @param type $str
    * @param type $length
    * @return type 
    */
    public function getWordWrap($str, $length=200){
        $val_affiche='';
        if(strlen($str) > $length){
            $tab=explode(' ',$str);
            foreach($tab as $val){
                if($this->strlen($val_affiche)<$length){
                    $val_affiche .=$val.' ';
                }else{
                    $val_affiche .='...';
                    break;
                }
            }
        }
        return !empty($val_affiche)?$val_affiche:$str;
    }
    
    /**
	 * Convert \n and \r\n and \r to <br />
	 *
	 * @param string $string String to transform
	 * @return string New string
	 */
	public static function nl2br($str)
	{
		return str_replace(array("\r\n", "\r", "\n"), '<br />', $str);
	}
    
    /**
	* Truncate strings
	*
	* @param string $str
	* @param integer $max_length Max length
	* @param string $suffix Suffix optional
	* @return string $str truncated
	*/
	/* CAUTION : Use it only on module hookEvents.
	** For other purposes use the smarty function instead */
	public function truncate($str, $max_length, $suffix = '...')
	{
	 	if ($this->strlen($str) <= $max_length)
	 		return $str;
	 	$str = utf8_decode($str);
	 	return (utf8_encode(substr($str, 0, $max_length - $this->strlen($suffix)).$suffix));
	}
    
    public static function truncatestaitc($str, $max_length, $suffix = '...')
	{
	 	if (Tools::strlen2($str) <= $max_length)
	 		return $str;
	 	$str = utf8_decode($str);
	 	return (utf8_encode(substr($str, 0, $max_length - Tools::strlen2($suffix)).$suffix));
	}
    
    public function strlen($str, $encoding = 'UTF-8')
	{
		if (is_array($str))
			return false;
		$str = html_entity_decode($str, ENT_COMPAT, 'UTF-8');
		if (function_exists('mb_strlen'))
			return mb_strlen($str, $encoding);
		return strlen($str);
	}
    
    public static function strlen2($str, $encoding = 'UTF-8')
	{
		if (is_array($str))
			return false;
		$str = html_entity_decode($str, ENT_COMPAT, 'UTF-8');
		if (function_exists('mb_strlen'))
			return mb_strlen($str, $encoding);
		return strlen($str);
	}
        
    public function smiley($texte) {
        $texte = str_replace(';)', '<img alt="Image" src="'._UPLOAD_DIR_.'smiley/clin_d-oeil.png" />', $texte);
        $texte = str_replace(':)', '<img alt="Image" src="'._UPLOAD_DIR_.'smiley/sourire.png" />', $texte);
        $texte = str_replace(':D', '<img alt="Image" src="'._UPLOAD_DIR_.'smiley/mort_de_rire.png" />', $texte);
        $texte = str_replace(':mdr:', '<img alt="Image" src="'._UPLOAD_DIR_.'smiley/pleure_de_rire.png" />', $texte);
        $texte = str_replace(':mouais:', '<img alt="Image" src="'._UPLOAD_DIR_.'smiley/mouai.png" />', $texte);
        $texte = str_replace(':(', '<img alt="Image" src="'._UPLOAD_DIR_.'smiley/pas_content.png" />', $texte);
        $texte = str_replace(':bof:', '<img alt="Image" src="'._UPLOAD_DIR_.'smiley/bof.png" />', $texte);
        $texte = str_replace(':mad:', '<img alt="Image" src="'._UPLOAD_DIR_.'smiley/en_colere.png" />', $texte);
        $texte = str_replace('(*^_^*)', '<img alt="Image" src="'._UPLOAD_DIR_.'smiley/rougir.gif" />', $texte);
        $texte = str_replace(':diable:', '<img alt="Image" src="'._UPLOAD_DIR_.'smiley/diable.png" />', $texte);
        $texte = str_replace(':star:', '<img alt="Image" src="'._UPLOAD_DIR_.'smiley/lunettes.png" />', $texte);
        $texte = str_replace(':oO:', '<img alt="Image" src="'._UPLOAD_DIR_.'smiley/surprit.png" />', $texte);
        $texte = str_replace(':rigolo:', '<img alt="Image" src="'._UPLOAD_DIR_.'smiley/rigolo.png" />', $texte);
        $texte = str_replace(':-*', '<img alt="Image" src="'._UPLOAD_DIR_.'smiley/bisou.gif" />', $texte);
        $texte = str_replace(':prosterne:', '<img alt="Image" src="'._UPLOAD_DIR_.'smiley/prosterne.gif" />', $texte);
        $texte = str_replace(':siffler:', '<img alt="Image" src="'._UPLOAD_DIR_.'smiley/siffler.gif" />', $texte);
        $texte = str_replace(':-P', '<img alt="Image" src="'._UPLOAD_DIR_.'smiley/tire_la_langue.png" />', $texte);
        $texte = str_replace(":'(", '<img alt="Image" src="'._UPLOAD_DIR_.'smiley/triste.png" />', $texte);
        $texte = str_replace(':eek:', '<img alt="Image" src="'._UPLOAD_DIR_.'smiley/eek.gif" />', $texte);
        $texte = str_replace(':rolleyes:', '<img alt="Image" src="'._UPLOAD_DIR_.'smiley/rolleyes.gif" />', $texte);
        $texte = str_replace(':??:', '<img alt="Image" src="'._UPLOAD_DIR_.'smiley/confused.gif" />', $texte);
        $texte = str_replace("\\\\\'", "'", $texte);
        
        return $texte;
    }
    
    public function getArrayImgExtension(){
        return array('.GIF','.JPEG','.JPG','.PNG','.png', '.gif', '.jpg', '.jpeg');
        
    }
    public function getArraySWFExtension(){
        return array('.SWF', '.swf');
    }
    
    public static function display(){
        
    }
    /**
     * verifie une url 
     * @param type $chaine
     * @param type $strict
     * @return type
     */
    public static function checkUrl($chaine, $strict=false){
        if($strict && !empty($chaine)){
            if(substr($chaine, 0, 4)!='http'){
                return 'http://'.$chaine;
            }else
                return $chaine;
        }else
            return empty($chaine)?'#':$chaine;
    }
    /**
     * crypte un chaine et renvoi le résultat
     * @param type $chaine
     * @return type
     */
    public static function cryptString($chaine){
        return md5(_COOKIE_KEY_.$chaine);
    }

	public static function getFieldKey($field, $lang){
		$code = is_string($lang)?$lang:$lang->getIso_code();
		return $field.'_'.$code;
    }
	
	public static function removeLangKey($field){
        $lenght = strlen($field)-3;
        $result = substr($field, 0, $lenght);
        return $result;
    }
	
	public static function getLangFormField($fullField, $field){
		return str_replace($field.'_', '', $fullField);
    }
}

?>
