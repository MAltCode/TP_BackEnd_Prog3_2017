<?php
date_default_timezone_set("America/Buenos_Aires");
require_once 'AccesoDatos.php';
class administracion{
	public static function cocherasLibres($request, $response){
		$objetoAccesoDatos = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT nroCochera FROM cocheras WHERE estado = 'libre'");
        $consulta->execute();
        return $response->withJson( $consulta->fetchAll() ,200);
	}

	public static function cocherasOcupadas($request, $response){
		$objetoAccesoDatos = AccesoDatos::dameUnObjetoAcceso(); 
		$consulta = $objetoAccesoDatos->RetornarConsulta("select nroCochera,operaciones.patente,marca,color,entrada from cocheras, operaciones, vehiculos	where estado='ocupado'	and salida is null	and nroCochera=idCochera and operaciones.patente= vehiculos.patente order by nroCochera asc");
        $consulta->execute();
        return $response->withJson( $consulta->fetchAll() ,200);
	}

	public static function patentesSalida($request, $response){
		$objetoAccesoDatos = AccesoDatos::dameUnObjetoAcceso(); 
		$consulta = $objetoAccesoDatos->RetornarConsulta("SELECT patente FROM operaciones where salida is null");
        $consulta->execute();
        return $response->withJson( $consulta->fetchAll() ,200);
	}

	public static function patenteSalida($request, $response){
		$objetoAccesoDatos = AccesoDatos::dameUnObjetoAcceso(); 
		$consulta = $objetoAccesoDatos->RetornarConsulta("SELECT nroCochera FROM cocheras");
        $consulta->execute();
        return $response->withJson( $consulta->fetchAll() ,200);
	}



}