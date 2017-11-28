<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require_once './composer/vendor/autoload.php';
require_once './Clases/logueo.php';
require_once './Clases/administracion.php';
require_once './Clases/operacionApi.php';
require_once './Clases/AutentificadorJWT.php';

$config['displayErrorDetails'] = true;
$config['addContentLengthHeader'] = false;

$app = new \Slim\App(["settings" => $config]);

$app->group('/login', function () {
    $this->post('/', \logueo::class . ':ingresarYObtenerToken');
});

$app->group('/administracion', function () {
    $this->get('/cocherasLibres', \administracion::class . ':cocherasLibres');
    $this->post('/ingresarVehiculo', \operacionApi::class . ':ingresarVehiculo');
    $this->post('/cocherasOcupadas', \administracion::class . ':cocherasOcupadas');
    $this->post('/altaEmpleado', \administracion::class . ':altaEmpleado');
    $this->post('/bajaEmpleado', \administracion::class . ':bajaEmpleado');
    $this->post('/patentesSalida', \administracion::class . ':patentesSalida');
    $this->post('/confirmarSalidaVehiculo', \operacionApi::class . ':confirmarSalidaVehiculo');
    $this->post('/sacarVehiculo', \operacionApi::class . ':sacarVehiculo');
    
    $this->post('/datosToken', \administracion::class . ':datosToken');
    
    
    
});

$app->run();

?>