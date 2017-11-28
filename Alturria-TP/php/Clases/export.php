<?php
require_once 'AccesoDatos.php';
require_once 'PHPExcel.php';
class export{

	public static function cocheras($request, $response){
		$objetoAccesoDatos = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta = $objetoAccesoDatos->RetornarConsulta("select * from cocheras;");
        $consulta->execute();

        $objPHPExcel = new PHPExcel();
        $row = 1;

        $objPHPExcel->getActiveSheet()->setCellValue('A'.$row, "piso");
        $objPHPExcel->getActiveSheet()->setCellValue('B'.$row, "Cochera");
        $objPHPExcel->getActiveSheet()->setCellValue('C'.$row, "Estado");
        $objPHPExcel->getActiveSheet()->setCellValue('D'.$row, "Tipo");

        while($subrow = $consulta->fetch())
        {
            $row++;
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $subrow['piso']);
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$row, $subrow['nroCochera']);
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$row, $subrow['estado']);
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$row, $subrow['tipo']);
        }
        $excelWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $fecha = date("Y-m-d H:i:s"); 
        $nombre = "Cocheras ".$fecha;
        $excelWriter->save('../export/'.$nombre.'.xlsx');

        var_dump($excelWriter);
    }
}
?>