<?php
require_once './clases/AccesoDatos.php';
require_once './clases/operacion.php';

class operacionApi extends operacion{
    public static function ingresarVehiculo($request, $response){
        $ArrayDeParametros = $request->getParsedBody();
        $token = $ArrayDeParametros['token'];
        $marca = $ArrayDeParametros['marca'];
        $color = $ArrayDeParametros['color'];
        $cochera = $ArrayDeParametros['cochera'];
        $patente = $ArrayDeParametros['patente'];
        try{
            $datosToken = autentificadorJwt::decodificarToken($token);
        }
        catch(Exception $e){
            return $response->withJson($e->getMessage(), 511);
        }
        //Me fijo que no este estacionado, es decir que la patente no este en operaciones sin fecha de salida
        $existe=operacion::Existe($patente);

        if($existe){
            $retorno=array('error'=> "Esta patente ya está en el estacionamiento." );
            $newResponse = $response->withJson( $retorno ,409); 
        }else{
            operacion::registrarOperacion($cochera ,$patente,$datosToken);
            operacion::registrarVehiculo($marca,$color ,$patente);
            $retorno=array('ok'=> "Se guardo correctamente!" );
            $newResponse = $response->withJson( $retorno ,200); 
        }
        return $newResponse;
    }

    public static function sacarVehiculo($request, $response){
        $ArrayDeParametros = $request->getParsedBody();
        $token = $ArrayDeParametros['token'];
        $patente = $ArrayDeParametros['patente'];
        try{
            $datosToken = autentificadorJwt::decodificarToken($token);
        }
        catch(Exception $e){
            return $response->withJson($e->getMessage(), 511);
        }

        operacion::terminarOperacion($patente,$datosToken);
        $retorno=array('ok'=> "Se guardo correctamente!" );
        $newResponse = $response->withJson( $retorno ,200); 

        return $newResponse;
    }
    

	
}