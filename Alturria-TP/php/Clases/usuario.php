<?php
require_once 'AccesoDatos.php';
class usuario{
	public static function usuarioExistente($email,$clave){
		// Va a retornar el rol del usuario. Si no existe, retorna null.
		$objetoAccesoDatos = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT id,nombre,apellido,email,perfil FROM usuarios WHERE email = :email AND clave = :clave");
        $consulta->bindValue(':email', $email, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $clave, PDO::PARAM_STR);
		$consulta->setFetchMode(PDO::FETCH_CLASS, "usuario");
		$consulta->execute();
        return $consulta->fetchObject('usuario');
	}




}