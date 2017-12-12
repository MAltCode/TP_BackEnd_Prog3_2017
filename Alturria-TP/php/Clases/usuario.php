<?php

require_once 'AccesoDatos.php';

class usuario{
	public static function usuarioExistente($usuario,$clave){
		// Va a retornar el rol del usuario. Si no existe, retorna null.
		$objetoAccesoDatos = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT id,nombre,apellido,usuario,perfil,foto FROM usuarios WHERE usuario = :usuario AND clave = :clave");
        $consulta->bindValue(':usuario', $usuario, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $clave, PDO::PARAM_STR);
		$consulta->setFetchMode(PDO::FETCH_CLASS, "usuario");
		$consulta->execute();
        return $consulta->fetchObject('usuario');
	}

	public static function Existe($usuario){
		// Va a retornar el rol del usuario. Si no existe, retorna null.
		$objetoAccesoDatos = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT id,nombre,apellido,usuario FROM usuarios WHERE usuario = :usuario");
        $consulta->bindValue(':usuario', $usuario, PDO::PARAM_STR);
		$consulta->setFetchMode(PDO::FETCH_CLASS, "usuario");
		$consulta->execute();
        return $consulta->fetchObject('usuario');
	}

	public static function registrarLogIn($id){
		$dia = date("Y-m-d"); 
		$hora = date("H:i:s"); 
		$objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
		$consulta =$objetoAccesoDato->RetornarConsulta("INSERT into auditorialogin (idempleado, dia, hora)values(:id,:dia,:hora)");
		$consulta->bindValue(':id',$id, PDO::PARAM_INT);	
		$consulta->bindValue(':dia',$dia, PDO::PARAM_STR);	
		$consulta->bindValue(':hora',$hora, PDO::PARAM_STR);	
		$consulta->execute();
	}

	public function logueos($id, $desde, $hasta = NULL){
		$objetoAccesoDatos = accesoDatos::DameUnObjetoAcceso();
		if(!isset($hasta)){
			$consulta = $objetoAccesoDatos->retornarConsulta("SELECT dia,hora FROM auditorialogin WHERE idempleado = :id AND dia >= :desde");	
		}
		else{
			$consulta = $objetoAccesoDatos->retornarConsulta("SELECT dia,hora FROM auditorialogin WHERE idempleado = :id
															  AND dia BETWEEN :desde AND :hasta");
			$consulta->bindValue(":hasta", $hasta, PDO::PARAM_STR);
		}
		$consulta->bindValue(":id", $id, PDO::PARAM_INT);
		$consulta->bindValue(":desde", $desde, PDO::PARAM_STR);
		$consulta->setFetchMode(PDO::FETCH_ASSOC);
		
		$consulta->execute();
		if($consulta->rowCount() == 0){
			return false;   
		}
		
		return $consulta->fetchAll();
	}

	public function operacionesPorId($id){
		$objetoAccesoDatos = accesoDatos::DameUnObjetoAcceso();
		$consulta = $objetoAccesoDatos->retornarConsulta("SELECT count(*) as operaciones FROM operaciones WHERE idempleado = :id");
		$consulta->bindValue(":id", $id, PDO::PARAM_INT);
		$consulta->setFetchMode(PDO::FETCH_ASSOC);
		
		$consulta->execute();
		if($consulta->rowCount() == 0){
			return false;   
		}
		
		return $consulta->fetchAll();
	}

	public function operacionesTodos(){
		$objetoAccesoDatos = accesoDatos::DameUnObjetoAcceso();
		$consulta = $objetoAccesoDatos->retornarConsulta("SELECT count(*) as operaciones,usuario FROM operaciones, usuarios WHERE usuarios.ID=idempleado group by idempleado");

		$consulta->setFetchMode(PDO::FETCH_ASSOC);
		
		$consulta->execute();
		if($consulta->rowCount() == 0){
			return false;   
		}
		
		return $consulta->fetchAll();
	}

	public function listarUsuarios($suspendido){
		$objetoAccesoDatos = accesoDatos::DameUnObjetoAcceso();

		if($suspendido == "suspendidos"){
			$consulta = $objetoAccesoDatos->retornarConsulta("SELECT id,nombre,apellido,usuario,dni, perfil, estado FROM usuarios WHERE estado='suspendido'");
		}elseif ($suspendido=="activos") {
			$consulta = $objetoAccesoDatos->retornarConsulta("SELECT id,nombre,apellido,usuario,dni, perfil, estado FROM usuarios WHERE estado='activo'");
		}else {
			$consulta = $objetoAccesoDatos->retornarConsulta("SELECT id,nombre,apellido,usuario,dni, perfil, estado FROM usuarios");
		}
		

		$consulta->setFetchMode(PDO::FETCH_ASSOC);
		
		$consulta->execute();
		if($consulta->rowCount() == 0){
			return false;   
		}
		
		return $consulta->fetchAll();
	}

	
	
}
?>