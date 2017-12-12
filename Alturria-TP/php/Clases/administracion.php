<?php
date_default_timezone_set("America/Buenos_Aires");
require_once 'AccesoDatos.php';
require_once 'imagenes.php';

class administracion{
	public static function cocherasLibres($request, $response){
		$objetoAccesoDatos = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT nroCochera FROM cocheras WHERE estado = 'libre'");
        $consulta->execute();
        return $response->withJson( $consulta->fetchAll(PDO::FETCH_ASSOC) ,200);
	}

	public static function cocherasOcupadas($request, $response){
		$objetoAccesoDatos = AccesoDatos::dameUnObjetoAcceso(); 
		$consulta = $objetoAccesoDatos->RetornarConsulta("select nroCochera,operaciones.patente,marca,color,entrada from cocheras, operaciones, vehiculos	where estado='ocupado'	and salida is null	and nroCochera=idCochera and operaciones.patente= vehiculos.patente order by nroCochera asc");
        $consulta->execute();
        return $response->withJson( $consulta->fetchAll(PDO::FETCH_ASSOC) ,200);
	}

	public static function patentesSalida($request, $response){
		$objetoAccesoDatos = AccesoDatos::dameUnObjetoAcceso(); 
		$consulta = $objetoAccesoDatos->RetornarConsulta("SELECT patente FROM operaciones where salida is null");
        $consulta->execute();
        return $response->withJson( $consulta->fetchAll(PDO::FETCH_ASSOC),200);
	}

	public static function patenteSalida($request, $response){
		$objetoAccesoDatos = AccesoDatos::dameUnObjetoAcceso(); 
		$consulta = $objetoAccesoDatos->RetornarConsulta("SELECT nroCochera FROM cocheras");
        $consulta->execute();
        return $response->withJson( $consulta->fetchAll(PDO::FETCH_ASSOC) ,200);
	}

	public static function altaEmpleado($request, $response){
		$ArrayDeParametros = $request->getParsedBody();
		$foto = $_FILES['foto']['tmp_name'];
		
		$token = $ArrayDeParametros['token'];
		$nombre = $ArrayDeParametros['nombre'];
		$dni = $ArrayDeParametros['dni'];
		$apellido = $ArrayDeParametros['apellido'];
		$clave = $ArrayDeParametros['clave'];
		$usuario = $ArrayDeParametros['usuario'];
		$admin = $ArrayDeParametros['admin'];

		if($token =='' || $nombre =='' || $dni =='' || $apellido =='' || $clave =='' || $usuario =='' || empty($foto) ){
			return $response->withJson("Debe ingresar todos los datos", 501);
		}
		//Manejo la foto , la renombro a nombre-apellido
		if ( 0 < $_FILES['foto']['error'] ) {
			return $response->withJson( "error en la foto" ,501);
		}
		else {
			$nombreFoto=imagenes::enmarcar($_FILES);
			$foto = "../temp/".$nombreFoto;
			$extension= explode(".", $nombreFoto);
			$nombreNuevo = $nombre."-".$apellido.".".$extension[1];
			//var_dump($foto);die();
		}

		if($admin=="false"){
			$perfil="Empleado";
		}else{
			$perfil="Admin";
		}
		$objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
		$consulta =$objetoAccesoDato->RetornarConsulta("INSERT into usuarios (nombre, apellido, DNI, clave, perfil,foto,usuario,estado)values(:nombre,:apellido,:dni,:clave,:perfil,:foto,:usuario,'activo')");
		$consulta->bindValue(':nombre',$nombre, PDO::PARAM_STR);	
		$consulta->bindValue(':apellido',$apellido, PDO::PARAM_STR);	
		$consulta->bindValue(':clave',$clave, PDO::PARAM_STR);	
		$consulta->bindValue(':dni',$dni, PDO::PARAM_STR);	
		$consulta->bindValue(':usuario',$usuario, PDO::PARAM_STR);	
		$consulta->bindValue(':perfil',$perfil, PDO::PARAM_STR);	
		$consulta->bindValue(':foto',$nombreNuevo, PDO::PARAM_STR);	
		$retorno = $consulta->execute();
		$id = $objetoAccesoDato->RetornarUltimoIdInsertado();
		if($retorno){
			//echo $foto;die();
			rename($foto, '../fotos/' . $id."-". $nombreNuevo);
		}
		return $response->withJson( "Se inserto el usuario: ".$usuario." con id: ".$id ,501);
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
		$id = $ArrayDeParametros['id'];
		//	Analizo token
		try{
			$datosToken = autentificadorJwt::decodificarToken($token);
		}
		catch(Exception $e){
			return $response->withJson($e->getMessage(), 511);
		}

		$objetoAccesoDatos = AccesoDatos::dameUnObjetoAcceso(); 
		$consulta = $objetoAccesoDatos->RetornarConsulta("SELECT foto FROM usuarios where id=:id");
		$consulta->bindValue(':id',$id, PDO::PARAM_INT);	
		$consulta->execute();
		$fotoArray = $consulta->fetchAll(PDO::FETCH_ASSOC);
		
		/* testeado en postman. SI existe el registro pero no tiene foto, devuelve array(1) {
		[0]=>
		array(1) {
			["foto"]=>
			string(0) ""
		}
		Si no existe devuelve null, sino array con el nombre de la foto
		}*/
		if($fotoArray){
			$foto = $id."-".$fotoArray[0]['foto'];
			if($foto!=''){
				// aca trabajo la foto
				$fecha = date("Y-m-d");
				rename("../fotos/".$foto, "../borrados/borrado_".$fecha."---".$foto);
			}

			//tenga o no foto, lo borro
			$consulta2 = $objetoAccesoDatos->RetornarConsulta("delete FROM usuarios where id=:id");
			$consulta2->bindValue(':id',$id, PDO::PARAM_INT);	
			$consulta2->execute();
			return $response->withJson("empleado borrado", 206);
		}else{
			return $response->withJson("No existe ese ID", 206);
		}

	}
	
	public static function suspensionEmpleado($request, $response){
		$ArrayDeParametros = $request->getParsedBody();
		$token = $ArrayDeParametros['token'];
		$id = $ArrayDeParametros['id'];
		//	Analizo token
		try{
			$datosToken = autentificadorJwt::decodificarToken($token);
		}
		catch(Exception $e){
			return $response->withJson($e->getMessage(), 511);
		}

		$objetoAccesoDatos = AccesoDatos::dameUnObjetoAcceso(); 
		$consulta = $objetoAccesoDatos->RetornarConsulta("SELECT usuario FROM usuarios where id=:id");
		$consulta->bindValue(':id',$id, PDO::PARAM_INT);	
		$consulta->execute();
		$usuario = $consulta->fetchAll(PDO::FETCH_ASSOC);

		if($usuario){		
			$consulta = $objetoAccesoDatos->RetornarConsulta("update usuarios set estado='suspendido' where id=:id");
			$consulta->bindValue(':id',$id, PDO::PARAM_INT);	
			$consulta->execute();
			
			return $response->withJson("usuario ".$usuario[0]['usuario']." suspendido", 206);
		}else{
			return $response->withJson("No existe ese ID", 206);
		}
	}

	public static function activarEmpleado($request, $response){
		$ArrayDeParametros = $request->getParsedBody();
		$token = $ArrayDeParametros['token'];
		$id = $ArrayDeParametros['id'];
		//	Analizo token
		try{
			$datosToken = autentificadorJwt::decodificarToken($token);
		}
		catch(Exception $e){
			return $response->withJson($e->getMessage(), 511);
		}

		$objetoAccesoDatos = AccesoDatos::dameUnObjetoAcceso(); 
		$consulta = $objetoAccesoDatos->RetornarConsulta("SELECT usuario FROM usuarios where id=:id");
		$consulta->bindValue(':id',$id, PDO::PARAM_INT);	
		$consulta->execute();
		$usuario = $consulta->fetchAll(PDO::FETCH_ASSOC);

		if($usuario){		
			$consulta = $objetoAccesoDatos->RetornarConsulta("update usuarios set estado='activo' where id=:id");
			$consulta->bindValue(':id',$id, PDO::PARAM_INT);	
			$consulta->execute();
			
			return $response->withJson("usuario ".$usuario[0]['usuario']." activado", 206);
		}else{
			return $response->withJson("No existe ese ID", 206);
		}
	}

	public static function modificarEmpleado($request, $response){
		$ArrayDeParametros = $request->getParsedBody();
		$id = $ArrayDeParametros['id'];
		$token = $ArrayDeParametros['token'];
		$nombre = $ArrayDeParametros['nombre'];
		$dni = $ArrayDeParametros['dni'];
		$apellido = $ArrayDeParametros['apellido'];
		$clave = $ArrayDeParametros['clave'];
		$usuario = $ArrayDeParametros['usuario'];
		$admin = $ArrayDeParametros['admin'];

		//Manejo la foto nueva, la renombro a nombre-apellido
		if ( 0 < $_FILES['foto']['error'] ) {
			return $response->withJson( "error en la foto" ,501);
		}else{
			$nombreFoto=imagenes::enmarcar($_FILES);
			$foto = "../temp/".$nombreFoto;
			$extension= explode(".", $nombreFoto)  ;
			$nombreNuevo = $nombre."-".$apellido.".".$extension[1];
		}

		if($admin=="false"){
			$perfil="Empleado";
		}else{
			$perfil="Admin";
		}

		$objetoAccesoDatos = AccesoDatos::dameUnObjetoAcceso(); 
		$consulta = $objetoAccesoDatos->RetornarConsulta("SELECT foto FROM usuarios where id=:id");
		$consulta->bindValue(':id',$id, PDO::PARAM_INT);	
		$consulta->execute();
		$fotoArray = $consulta->fetchAll(PDO::FETCH_ASSOC);
		
		/* testeado en postman. SI existe el registro pero no tiene foto, devuelve array(1) {
		[0]=>
		array(1) {
			["foto"]=>
			string(0) ""
		}
		Si no existe devuelve null, sino array con el nombre de la foto
		}*/
		if($fotoArray){
			$fotoVieja = $fotoArray[0]['foto'];
			if($fotoVieja!=''){
				// aca trabajo la foto
				$fotoVieja = $id."-".$fotoArray[0]['foto'];
				$fecha = date("Y-m-d H_i_s");
				rename("../fotos/".$fotoVieja, "../backup/modificado".$fecha."-ID:".$fotoVieja);
			}

			//tenga o no foto, lo modifico sabiendo que existe el empleado
			$objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
			$consulta =$objetoAccesoDato->RetornarConsulta("update usuarios  set nombre=:nombre, apellido=:apellido, DNI=:dni, clave=:clave, perfil=:perfil,foto=:foto,usuario=:usuario where id=:id");
			$consulta->bindValue(':nombre',$nombre, PDO::PARAM_STR);	
			$consulta->bindValue(':apellido',$apellido, PDO::PARAM_STR);	
			$consulta->bindValue(':clave',$clave, PDO::PARAM_STR);	
			$consulta->bindValue(':dni',$dni, PDO::PARAM_STR);	
			$consulta->bindValue(':usuario',$usuario, PDO::PARAM_STR);	
			$consulta->bindValue(':perfil',$perfil, PDO::PARAM_STR);	
			$consulta->bindValue(':foto',$nombreNuevo, PDO::PARAM_STR);	
			$consulta->bindValue(':id',$id, PDO::PARAM_STR);	
			$retorno = $consulta->execute();
			if($retorno){
				$nombreNuevo = $id."-".$nombreNuevo;
				move_uploaded_file($_FILES['foto']['tmp_name'], '../fotos/' . $nombreNuevo);
			}
			return $response->withJson("usuario actualizado", 200);
		}else{
			return $response->withJson("No existe ese ID", 206);
		}


	}


	public function registrosLogueos($request, $response, $args ){
		$usuario = $request->getAttribute('usuario');
		$empleado = usuario::Existe($usuario);
		if($empleado){
			$retorno['empleado'] = $empleado;
			$logueos = usuario::logueos($empleado->id, $request->getAttribute('desde'), $request->getAttribute('hasta'));
			
			if($logueos){
				$retorno['logueos'] = $logueos;
				return $response->withJson($retorno);
			}
			return $response->withJson("No hay logueos en esa fecha",206);
		}
		return $response->withJson("No existe ese empleado", 206);
	}

	public function registrosOperaciones($request, $response, $args ){
		$usuario = $request->getAttribute('usuario');
		
		if($usuario != ''){
				$empleado = usuario::Existe($usuario);
				if($empleado){
					$retorno['empleado'] = $empleado;
					$cant = usuario::operacionesPorId($empleado->id);
					
					if($cant){
						$retorno['cantidad'] = $cant;
						return $response->withJson($retorno);
					}
					return $response->withJson("No hay operaciones",206);
				}
				return $response->withJson("No existe ese empleado", 206);
		}else{
			//TODOS
			$todos = usuario::operacionesTodos();
			if($todos){
				$retorno['Operaciones'] = $todos;
				return $response->withJson($retorno);
			}
		}
	
	}

	public function listarEmpleados($request, $response, $args ){
		$suspendido = $request->getAttribute('suspendido');

		$usuarios = usuario::listarUsuarios($suspendido);	
		if($usuarios){
			$retorno['usuarios'] = $usuarios;
			return $response->withJson($retorno);
		}
	}

	public function masOcupada($request, $response, $args ){
		$desde = $request->getAttribute('desde');
		$hasta = $request->getAttribute('hasta');
		if($desde==''){
			$masOcupada = operacion::masOcupada();	
		}else{
			$masOcupada = operacion::masOcupadaPorFecha($desde,$hasta);	
		}
		if($masOcupada){
			$retorno['menosOcupada'] = $masOcupada;
			return $response->withJson($retorno);
		}
	}

	public function menosOcupada($request, $response, $args ){
		$desde = $request->getAttribute('desde');
		$hasta = $request->getAttribute('hasta');
		if($desde==''){
			$menosOcupada = operacion::menosOcupada();	
		}else{
			$menosOcupada = operacion::menosOcupadaPorFecha($desde,$hasta);	
		}
		if($menosOcupada){
			$retorno['menosOcupada'] = $menosOcupada;
			return $response->withJson($retorno);
		}
	}

	public function nuncaOcupada($request, $response, $args ){
		$desde = $request->getAttribute('desde');
		$hasta = $request->getAttribute('hasta');
		if($desde==''){
			$nuncaOcupada = operacion::nuncaOcupada();	
		}else{
			$nuncaOcupada = operacion::nuncaOcupadaPorFecha($desde,$hasta);	
		}
		if($nuncaOcupada){
			$retorno['nuncaOcupada'] = $nuncaOcupada;
			return $response->withJson($retorno);
		}
	}

	public function operacionesPorPatente($request, $response, $args ){
		$patente = $request->getAttribute('patente');
		$desde = $request->getAttribute('desde');
		$hasta = $request->getAttribute('hasta');
		if($desde==''){
			$operacionesPorPatente = operacion::operacionesPorPatente($patente);	
		}else{
			$operacionesPorPatente = operacion::operacionesPorPatenteConFecha($patente,$desde,$hasta);	
		}
		if($operacionesPorPatente){
			$retorno['operacionesPorPatente'] = $operacionesPorPatente;
			return $response->withJson($retorno);
		}else{
			return $response->withJson("No se registran operaciones para la patente: ".$patente, 206);
		}
	}
	
	public function promediarImporte($request, $response, $args ){
		$mes = $request->getAttribute('mes');
		if($mes<1 || $mes>12){
			return $response->withJson( "El mes debe estar entre 1 y 12" ,500);
		}
		$inicioMostrar = '01-'.$mes.'-2017';
		$hastaMostrar = '30-'.$mes.'-2017';
		$inicio = '2017-'.$mes.'-01';
		$hasta = '2017-'.$mes.'-31';
		$objetoAccesoDatos = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT costo FROM operaciones WHERE salida>=:inicio and salida<=:hasta");
		$consulta->bindValue(':inicio',$inicio, PDO::PARAM_STR);	
		$consulta->bindValue(':hasta',$hasta, PDO::PARAM_STR);	
		$consulta->execute();
		$resultado=$consulta->fetchAll(PDO::FETCH_ASSOC);
		
		if(count($resultado) > 0){
			$cantidad =count($resultado);
			$total = 0;

			for ($i=0; $i < count($resultado); $i++) { 
				$total = $total + $resultado[$i]['costo'];
			}
			$final = $total / $cantidad;
			return $response->withJson( "hubo ".$cantidad." operaciones por $".$total." pesos, dando un promedio de ganancia de: ".$final ,200);
		}else{
			return $response->withJson( "No se registran operaciones en el periodo: ".$inicioMostrar." al ".$hastaMostrar ,200);
		}
        
	}

	public function facturacionEntreFechas($request, $response, $args ){
		$desde = $request->getAttribute('desde');
		$hasta = $request->getAttribute('hasta');
		if($desde>$hasta){
			return $response->withJson( "Desde no puede ser mayor a hasta" ,500);
		}

		$objetoAccesoDatos = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT costo FROM operaciones WHERE salida>=:desde and salida<=:hasta");
		$consulta->bindValue(':desde',$desde, PDO::PARAM_STR);	
		$consulta->bindValue(':hasta',$hasta, PDO::PARAM_STR);	
		$consulta->execute();
		$resultado=$consulta->fetchAll(PDO::FETCH_ASSOC);
		
		if(count($resultado) > 0){
			$cantidad =count($resultado);
			$total = 0;

			for ($i=0; $i < count($resultado); $i++) { 
				$total = $total + $resultado[$i]['costo'];
			}
			return $response->withJson( "hubo ".$cantidad." operaciones por $".$total ,200);
		}else{
			return $response->withJson( "No se registran operaciones en el periodo: ".$desde." al ".$hasta ,200);
		}
        
	}
	public function vehiculosEntreFechas($request, $response, $args ){
		$desde = $request->getAttribute('desde');
		$hasta = $request->getAttribute('hasta');
		if($desde>$hasta){
			return $response->withJson( "Desde no puede ser mayor a hasta" ,500);
		}

		$objetoAccesoDatos = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT patente ,count(*) 'nos visito' FROM operaciones WHERE entrada>=:desde and entrada<=:hasta group by patente");
		$consulta->bindValue(':desde',$desde, PDO::PARAM_STR);	
		$consulta->bindValue(':hasta',$hasta, PDO::PARAM_STR);	
		$consulta->execute();
		$resultado=$consulta->fetchAll(PDO::FETCH_ASSOC);
		
		if(count($resultado) > 0){

			return $response->withJson( $resultado ,200);
		}else{
			return $response->withJson( "No se registran operaciones en el periodo: ".$desde." al ".$hasta ,200);
		}
        
	}
	public function cocherasEntreFechas($request, $response, $args ){
		$desde = $request->getAttribute('desde');
		$hasta = $request->getAttribute('hasta');
		if($desde>$hasta){
			return $response->withJson( "Desde no puede ser mayor a hasta" ,500);
		}

		$objetoAccesoDatos = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT count(*) cantidad FROM operaciones WHERE entrada>=:desde and entrada<=:hasta and idCochera in (select nroCochera from cocheras where tipo='especial')");
		$consulta->bindValue(':desde',$desde, PDO::PARAM_STR);	
		$consulta->bindValue(':hasta',$hasta, PDO::PARAM_STR);	
		$consulta->execute();
		$resultado=$consulta->fetchAll(PDO::FETCH_ASSOC);
		$usoEspecial = $resultado[0]['cantidad'];
		$consulta = $objetoAccesoDatos->RetornarConsulta("SELECT count(*) cantidad FROM operaciones WHERE entrada>=:desde and entrada<=:hasta and idCochera in (select nroCochera from cocheras where tipo!='especial')");
		$consulta->bindValue(':desde',$desde, PDO::PARAM_STR);	
		$consulta->bindValue(':hasta',$hasta, PDO::PARAM_STR);	
		$consulta->execute();
		$resultado=$consulta->fetchAll(PDO::FETCH_ASSOC);
		$usoNormal = $resultado[0]['cantidad'];

		if(count($resultado) > 0){

			return $response->withJson( "Las cocheras especiales se utilizaron ".$usoEspecial." veces. Las cocheras no especiales se utilizaron ".$usoNormal ,200);
		}else{
			return $response->withJson( "No se registran operaciones en el periodo: ".$desde." al ".$hasta ,200);
		}
        
	}

	public function promediarUsuario($request, $response, $args ){
		$mes = $request->getAttribute('mes');
		if($mes<1 || $mes>12){
			return $response->withJson( "El mes debe estar entre 1 y 12" ,500);
		}
		$inicioMostrar = '01-'.$mes.'-2017';
		$hastaMostrar = '30-'.$mes.'-2017';
		$inicio = '2017-'.$mes.'-01';
		$hasta = '2017-'.$mes.'-31';
		$objetoAccesoDatos = AccesoDatos::dameUnObjetoAcceso(); 
		$consulta = $objetoAccesoDatos->RetornarConsulta("SELECT count(*) cantidad, usuario FROM operaciones, usuarios WHERE salida>=:inicio and salida<=:hasta and idEmpleado=usuarios.id group by usuario");
		$consulta->bindValue(':inicio',$inicio, PDO::PARAM_STR);	
		$consulta->bindValue(':hasta',$hasta, PDO::PARAM_STR);	
		$consulta->execute();
		$resultado=$consulta->fetchAll(PDO::FETCH_ASSOC);
		return $response->withJson( $resultado,200);

		if(count($resultado) > 0){
			$cantidadTotal =count($resultado);
			$total = 0;

			for ($i=0; $i < count($resultado); $i++) { 
				$total = $total + $resultado[$i]['costo'];
			}
			$final = $total / $cantidad;
			return $response->withJson( "hubo ".$cantidad." operaciones por $".$total." pesos, dando un promedio de ganancia de: ".$final ,200);
		}else{
			return $response->withJson( "No se registran operaciones en el periodo: ".$inicioMostrar." al ".$hastaMostrar ,200);
		}
        
	}
}
