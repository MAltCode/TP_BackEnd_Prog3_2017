<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
class VerificarJWT
{
    public function VerificarToken($request, $response, $next){
        if($request->isPost()){
            $ArrayDeParametros = $request->getParsedBody();
            $token = $ArrayDeParametros['token'];
            try{
                $datosToken = autentificadorJwt::decodificarToken($token);
            }
            catch(Exception $e){
                return $response->withJson("Token invalido: ".$e->getMessage(), 511);
            }

            if($datosToken){
                $request = $request->withAttribute('datos', $datosToken);
                return $next($request, $response);    
            }
            return $response->withJson("Token invalido",400);
        }

    }

	public function VerificarTokenAdmin($request, $response, $next){
        if($request->isPost()){
            $ArrayDeParametros = $request->getParsedBody();
            $token = $ArrayDeParametros['token'];
            try{
                $datosToken = autentificadorJwt::decodificarToken($token);
            }
            catch(Exception $e){
                return $response->withJson("Token invalido: ".$e->getMessage(), 511);
            }

            if($datosToken->perfil=='Admin'){	
                $request = $request->withAttribute('datos', $datosToken);
                return $next($request, $response);
            }else{
                return $response->withJson("Esta funcion es solo para administradores",400);
            }
        }
    }
    
}
?>