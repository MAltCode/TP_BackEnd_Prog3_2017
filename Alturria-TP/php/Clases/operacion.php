<?php
date_default_timezone_set("America/Buenos_Aires");
require_once 'AccesoDatos.php';
class operacion{
	public static function Existe($patente){
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta = $objetoAccesoDato->RetornarConsulta("select 1 from operaciones where patente=:patente and salida is null");	
        $consulta->bindValue(':patente',$patente, PDO::PARAM_STR);		
        $consulta->execute();
        return $consulta->fetchColumn();
    }

    public static function registrarOperacion($cochera ,$patente,$datosToken){
        $idEmpleado=$datosToken->id;
        $entrada = date("Y-m-d H:i:s"); 
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta =$objetoAccesoDato->RetornarConsulta("INSERT into operaciones (idCochera, patente, idEmpleado, entrada)values(:idCochera,:patente,:idEmpleado,:entrada)");
        $consulta->bindValue(':idCochera',$cochera, PDO::PARAM_INT);	
        $consulta->bindValue(':patente',$patente, PDO::PARAM_STR);	
        $consulta->bindValue(':idEmpleado',$idEmpleado, PDO::PARAM_STR);	
        $consulta->bindValue(':entrada',$entrada, PDO::PARAM_STR);	
        $consulta->execute();

        $objetoAccesoDatos2 = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta2 = $objetoAccesoDatos2->RetornarConsulta("update cocheras set estado='ocupado' WHERE nroCochera = :idCochera");
        $consulta2->bindValue(':idCochera',$cochera, PDO::PARAM_INT);
        $consulta2->execute();

        return $objetoAccesoDato->RetornarUltimoIdInsertado();
    }

    public static function registrarVehiculo($marca,$color ,$patente){
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta =$objetoAccesoDato->RetornarConsulta("INSERT into vehiculos (marca, patente, color)values(:marca,:patente,:color)");
        $consulta->bindValue(':marca',$marca, PDO::PARAM_INT);	
        $consulta->bindValue(':patente',$patente, PDO::PARAM_STR);	
        $consulta->bindValue(':color',$color, PDO::PARAM_STR);	
        $consulta->execute();
        return $objetoAccesoDato->RetornarUltimoIdInsertado();
    }

    public static function obtenerEntrada($patente){
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta = $objetoAccesoDato->RetornarConsulta("select entrada,idCochera from operaciones where patente=:patente and salida is null");	
        $consulta->bindValue(':patente',$patente, PDO::PARAM_STR);		
        $consulta->execute();
        return $consulta->fetchAll();
    }

    public static function obtenerCosto($segundos){
        //3-Cobro por hora$10 o media estadía $90(12hs) o estadia$170(24hs).
        $horas = $segundos / 60 / 60;
        $costo;
        if($horas<12){
            $costo = $horas * 10;
        }elseif ($horas>25) {
            $costo = $horas * 170;
        }else {
            $costo = $horas * 90;
        }
        return $costo;
    }

    public static function terminarOperacion($patente,$datosToken){
 
        $salidaStr = date("Y-m-d H:i:s");
        $salida = new DateTime($salidaStr); 
        $datos = operacion::obtenerEntrada($patente);
        $entrada = new DateTime($datos[0]['entrada']);

        $diff = $salida->diff($entrada);
        $segundos = ( ($diff->days * 24 ) * 60 ) + ( $diff->i * 60 ) + $diff->s;
        $costo = round(operacion::obtenerCosto($segundos),2);

        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta =$objetoAccesoDato->RetornarConsulta("update operaciones set salida = :salida, costo = :costo where patente=:patente and salida is null");
        $consulta->bindValue(':salida',$salidaStr, PDO::PARAM_STR);	
        $consulta->bindValue(':costo',$costo, PDO::PARAM_STR);	
        $consulta->bindValue(':patente',$patente, PDO::PARAM_STR);	
        $consulta->execute();

        operacion::actualizarCochera($datos[0]['idCochera']);
        return $objetoAccesoDato->RetornarUltimoIdInsertado();
    }

    public static function actualizarCochera($cochera){
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta =$objetoAccesoDato->RetornarConsulta("update cocheras set estado = 'libre' where nroCochera=:cochera");
        $consulta->bindValue(':cochera',$cochera, PDO::PARAM_STR);	
        $consulta->execute();

        return $objetoAccesoDato->RetornarUltimoIdInsertado();
    }
}