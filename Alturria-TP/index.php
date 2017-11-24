<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require_once './composer/vendor/autoload.php';
require_once './clases/logueo.php';
require_once './clases/administracion.php';
require_once './clases/operacionApi.php';
require_once './clases/AutentificadorJWT.php';

$config['displayErrorDetails'] = true;
$config['addContentLengthHeader'] = false;

$app = new \Slim\App(["settings" => $config]);

$app->get('/', function (Request $request, Response $response) {
    return $response->withRedirect("./login.html");
    
});


$app->group('/login', function () {
    $this->post('/', \logueo::class . ':ingresarYObtenerToken');
});

$app->group('/administracion', function () {
    $this->get('/cocherasLibres', \administracion::class . ':cocherasLibres');

    $this->post('/cocherasOcupadas', \administracion::class . ':cocherasOcupadas');
    $this->post('/patentesSalida', \administracion::class . ':patentesSalida');
    
    $this->post('/ingresarVehiculo', \operacionApi::class . ':ingresarVehiculo');
    $this->post('/sacarVehiculo', \operacionApi::class . ':sacarVehiculo');
    
});

$app->run();

?>