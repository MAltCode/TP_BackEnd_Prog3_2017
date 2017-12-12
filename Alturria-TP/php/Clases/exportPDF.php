<?php
//echo getcwd();die();
require_once 'AccesoDatos.php';
require_once './Clases/fpdf181/fpdf.php';
class exportPDF extends FPDF{
    //Page header
    function Header()
    {
        //Logo
        $this->Image('../Imagenes/logo.png',10,8,33);

    }
     
    //Page footer
    function Footer(){
        //Position at 1.5 cm from bottom
        $this->SetY(-15);
        //Arial italic 8
        $this->SetFont('Arial','I',8);
        //Page number
        $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
    }
     
    public static function historicoPatentePDF($request, $response){
        $ArrayDeParametros = $request->getParsedBody();
                $patente = $ArrayDeParametros['patente'];
                
    $objetoAccesoDatos = AccesoDatos::dameUnObjetoAcceso(); 
    $consulta = $objetoAccesoDatos->RetornarConsulta("select * from operaciones where patente=:patente order by id asc");
    $consulta->bindValue(':patente',$patente, PDO::PARAM_STR);
    $consulta->setFetchMode(PDO::FETCH_ASSOC);
    
    $consulta->execute();
    if($consulta->rowCount() == 0){
        return false;   
    }
    $array = $consulta->fetchAll();
    //Instanciation of inherited class
    $pdf=new exportPDF('P','mm','A4');
    $pdf->AliasNbPages();
    $pdf->AddPage();

            //Arial bold 15
            $pdf->SetFont('Arial','B',15);
            //Move to the right
            $pdf->Cell(50);
            //Title
            $pdf->Cell(0,10,'Mostrando informacion de la patente: '.$patente,1,0,'C');
            //Line break
            $pdf->Ln(30);

    $pdf->SetFont('Times','',12);

    for($i=0;$i<count($array);$i++){
        $datos = "Cochera: ".$array[$i]['idCochera']." ingreso el ".$array[$i]['entrada']." y salio el ".$array[$i]['salida']." Se cobro $".$array[$i]['costo'];
 
        $pdf->Cell(0,10,$datos,0,1);        
    }

        $pdf->Output('prueba','I');
    }
}
?>