<?php
namespace Library;
/**
 * Description of Fpdf
 *
 * @author fozeu wife
 */
use Library\Fpdf\FPDF;

class Pdf extends ApplicationComponent{
    //put your code here
    public function instanciatePDF($orientation='P', $unit='mm', $size='A4'){
        return new FPDF($orientation, $unit, $size);
    } 
    
}
?>
