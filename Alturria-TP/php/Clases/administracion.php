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

	public static function altaEmpleado($request, $response){
		$ArrayDeParametros = $request->getParsedBody();
		//var_dump($ArrayDeParametros);die();
		$token = $ArrayDeParametros['token'];
		$nombre = $ArrayDeParametros['nombre'];
		$dni = $ArrayDeParametros['dni'];
		$apellido = $ArrayDeParametros['apellido'];
		$clave = $ArrayDeParametros['clave'];
		$usuario = $ArrayDeParametros['usuario'];
		$admin = $ArrayDeParametros['admin'];

		//	Analizo token
		try{
			$datosToken = autentificadorJwt::decodificarToken($token);
		}
		catch(Exception $e){
			return $response->withJson($e->getMessage(), 511);
		}
		//Manejo la foto
		if ( 0 < $_FILES['file']['error'] ) {
			return $response->withJson( "error en la foto" ,501);
		}
		else {
			$foto = $_FILES['file']['name'];
			
			$extension= explode(".", $foto)  ;

			$nombreNuevo = $nombre.$apellido.".".$extension[1];
		}

		if($admin=="false"){
			$perfil="Empleado";
		}else{
			$perfil="Admin";
		}

		$objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
		$consulta =$objetoAccesoDato->RetornarConsulta("INSERT into usuarios (nombre, apellido, DNI, clave, perfil,foto,usuario)values(:nombre,:apellido,:dni,:clave,:perfil,:foto,:usuario)");
		$consulta->bindValue(':nombre',$nombre, PDO::PARAM_STR);	
		$consulta->bindValue(':apellido',$apellido, PDO::PARAM_STR);	
		$consulta->bindValue(':clave',$clave, PDO::PARAM_STR);	
		$consulta->bindValue(':dni',$dni, PDO::PARAM_STR);	
		$consulta->bindValue(':usuario',$usuario, PDO::PARAM_STR);	
		$consulta->bindValue(':perfil',$perfil, PDO::PARAM_STR);	
		$consulta->bindValue(':foto',$nombreNuevo, PDO::PARAM_STR);	
		$retorno = $consulta->execute();

		if($retorno){
			move_uploaded_file($_FILES['file']['tmp_name'], '../fotos/' . $nombreNuevo);
		}

		return $objetoAccesoDato->RetornarUltimoIdInsertado();

	}

	public static function datosToken($request, $response){
		$ArrayDeParametros = $request->getParsedBody();
		$token = $ArrayDeParametros['token'];
		try{
			$datosToken = autentificadorJwt::decodificarToken($token);
		}
		catch(Exception $e){
			return $response->withJson($e->getMessage(), 511);
		}
		return $response->withJson( $datosToken ,200);
	}

	public static function bajaEmpleado($request, $response){
		$ArrayDeParametros = $request->getParsedBody();
		$token = $ArrayDeParametros['token'];
		$usuario = $ArrayDeParametros['usuario'];
		try{
			$datosToken = autentificadorJwt::decodificarToken($token);
		}
		catch(Exception $e){
			return $response->withJson($e->getMessage(), 511);
		}
		return $response->withJson( $datosToken ,200);
	}
}