<?php
require_once 'AccesoDatos.php';
require_once 'operacion.php';

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
            return $response->withJson("Token invalido: ".$e->getMessage(), 511);
        }

        $libre=operacion::libre($cochera);
        if($libre==''){
            //cancelo todo, la cochera no existe
            return $response->withJson("No existe esa cochera" ,200); 
        }elseif ($libre=='ocupado') {
            return $response->withJson("cochera ocupada" ,200); 
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
        $costo = $ArrayDeParametros['costo'];
        $fecha = $ArrayDeParametros['fecha'];

        operacion::terminarOperacion($patente,$costo,$fecha);
        $retorno=array('ok'=> "Se guardo correctamente!" );
        $newResponse = $response->withJson( $retorno ,200); 
       return $newResponse;
    }
    public static function confirmarSalidaVehiculo($request, $response){
        $ArrayDeParametros = $request->getParsedBody();
        $token = $ArrayDeParametros['token'];
        $patente = $ArrayDeParametros['patente'];
        $retorno = operacion::confirmarSalidaVehiculo($patente,$datosToken);
        $newResponse = $response->withJson( $retorno ,200); 
        return $newResponse;
    }
    
    public static function terminarOperacion($request, $response){
        $ArrayDeParametros = $request->getParsedBody();
        $token = $ArrayDeParametros['token'];
        $patente = $ArrayDeParametros['patente'];
        $retorno = operacion::terminarOperacion($patente,$token);
        $newResponse = $response->withJson( $retorno ,200); 
        return $newResponse;
    }


	

}

