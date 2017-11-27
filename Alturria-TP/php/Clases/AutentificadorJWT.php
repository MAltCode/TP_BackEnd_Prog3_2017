<?php
use Firebase\JWT\JWT;
require_once "./composer/vendor/firebase/php-jwt/src/JWT.php";

class autentificadorJWT{
    private static $claveSecreta = "clavesecreta";
    private static $cod = 'HS256';
    static function CrearToken($datos){
        $time = time();
        $payload = array(
            'iat' => $time,
            'exp' => $time + 120*60,
            'data' => $datos,
            'app' => "apirestjwt"
        );
        return Firebase\JWT\JWT::encode($payload,self::$claveSecreta);
    }

    static function VerificarToken($token){
        $decodificado = Firebase\JWT\JWT::decode($token,self::$claveSecreta,[$cod]);
        return $decodificado;
    }
    public static function decodificarToken($token){
        return Firebase\JWT\JWT::decode($token, self::$claveSecreta, [self::$cod] )->data;    
    }
}
?>