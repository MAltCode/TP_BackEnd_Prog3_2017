<?php
date_default_timezone_set("America/Buenos_Aires");
require_once "usuario.php";
class logueo{
    public function ingresarYObtenerToken($request, $response){
    $token="";
    $ArrayDeParametros = $request->getParsedBody();
   // return $ArrayDeParametros;
    $usuario = $ArrayDeParametros['usuario'];
    $clave = $ArrayDeParametros['clave'];
    if( $usuario && $clave){
        $usuario = usuario::usuarioExistente($usuario,$clave);
        if($usuario && ($usuario->perfil == "Admin" || $usuario->perfil == "Empleado")){
          $token= autentificadorJWT::crearToken(array(
            'nombre'=> $usuario->nombre,
            'apellido'=> $usuario->apellido,
            'perfil'=> $usuario->perfil,
            'id'=> $usuario->id,
            'foto'=> $usuario->foto,
            'usuario'=> $usuario->usuario));

            usuario::registrarLogIn($usuario->id);

          $newResponse = $response->withJson( $token ,200); 
        }
        else{
          $retorno=array('error'=> "El usuario no es valido!" );
          $newResponse = $response->withJson( $retorno ,409); 
        }
    }else{
          $retorno=array('error'=> "Faltan los datos del usuario y su clave" );
          $newResponse = $response->withJson( $retorno  ,411); 
    }
    return $newResponse;
    }
}
?>