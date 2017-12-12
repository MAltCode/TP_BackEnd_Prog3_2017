<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require_once './composer/vendor/autoload.php';
require_once './Clases/logueo.php';
require_once './Clases/administracion.php';
require_once './Clases/operacionApi.php';
require_once './Clases/AutentificadorJWT.php';
require_once './Clases/verificarJWT.php';
require_once './Clases/export.php';
require_once './Clases/exportPDF.php';

$config['displayErrorDetails'] = true;
$config['addContentLengthHeader'] = false;

$app = new \Slim\App(["settings" => $config]);

$app->add(function($request, $response, $next){
    $response = $next($request, $response);

    return $response
            ->withHeader('Access-Control-Allow-Origin', 'http://alturriatp.000webhostapp.com/')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST');
});

$app->group('/login', function () {
    $this->post('/', \logueo::class . ':ingresarYObtenerToken');
});

$app->group('/administracion', function () {
    $this->post('/cocherasLibres', \administracion::class . ':cocherasLibres');
    $this->post('/ingresarVehiculo', \operacionApi::class . ':ingresarVehiculo');
    $this->post('/cocherasOcupadas', \administracion::class . ':cocherasOcupadas');
    $this->post('/altaEmpleado', \administracion::class . ':altaEmpleado')->add(\verificarJWT::class . ':VerificarTokenAdmin');
    $this->post('/bajaEmpleado', \administracion::class . ':bajaEmpleado')->add(\verificarJWT::class . ':VerificarTokenAdmin');
    $this->post('/suspensionEmpleado', \administracion::class . ':suspensionEmpleado')->add(\verificarJWT::class . ':VerificarTokenAdmin');
    $this->post('/activarEmpleado', \administracion::class . ':activarEmpleado')->add(\verificarJWT::class . ':VerificarTokenAdmin');
    $this->post('/modificarEmpleado', \administracion::class . ':modificarEmpleado')->add(\verificarJWT::class . ':VerificarTokenAdmin');
    $this->post('/patentesSalida', \administracion::class . ':patentesSalida');
    $this->post('/confirmarSalidaVehiculo', \operacionApi::class . ':confirmarSalidaVehiculo');
    $this->post('/sacarVehiculo', \operacionApi::class . ':sacarVehiculo');
    $this->post('/terminarOperacion', \operacionApi::class . ':terminarOperacion');
    
    $this->post('/datosToken', \administracion::class . ':datosToken');
})->add(\verificarJWT::class . ':VerificarToken');


$app->group('/listados', function () {
    // a- Los días y horarios que se Ingresaron al sistema.
    $this->post('/logueos/{usuario}/{desde}[/{hasta}]',\administracion::class . ':registrosLogueos');
    // b- Cantidad de operaciones por cada uno.
    $this->post('/operaciones/[{usuario}]',\administracion::class . ':registrosOperaciones');
    // Listado de empleados
    $this->post('/empleados/[{suspendido}]',\administracion::class . ':listarEmpleados');
    // cochera mas utilizada
    $this->post('/masOcupada/[{desde}/{hasta}]', \administracion::class . ':masOcupada');
    // cochera menos utilizada
    $this->post('/menosOcupada/[{desde}/{hasta}]', \administracion::class . ':menosOcupada');
    // cochera nunca utilizada
    $this->post('/nuncaOcupada/[{desde}/{hasta}]', \administracion::class . ':nuncaOcupada');
    // operaciones por patente
    $this->post('/operacionesPorPatente/{patente}/[{desde}/{hasta}]', \administracion::class . ':operacionesPorPatente');
    // Promedio mensual de datos: a-0,50% importe
    $this->post('/promediarImporte/{mes}', \administracion::class . ':promediarImporte');
    //10- (2pt) datos entre FECHAS :a-0.50% facturación - cantidad de vehículo
    $this->post('/facturacionEntreFechas/{desde}/{hasta}', \administracion::class . ':facturacionEntreFechas');
    //c-100% cantidades de veces que vino el mismo vehículo, cuántos vehículos sin repetir(distintos se estacionaron)
    $this->post('/vehiculosEntreFechas/{desde}/{hasta}', \administracion::class . ':vehiculosEntreFechas');
    //b-0.75% usos de cocheras para discapacitados y no .
    $this->post('/cocherasEntreFechas/{desde}/{hasta}', \administracion::class . ':cocherasEntreFechas');

    //11- (2pt) Promedio mensual de datos: c-100% cochera y usuario .
    $this->post('/promediarUsuario/{mes}', \administracion::class . ':promediarUsuario');

})->add(\verificarJWT::class . ':VerificarToken')->add(\verificarJWT::class . ':VerificarTokenAdmin');

$app->group('/export', function () {
    $this->post('/cocherasExcel', \export::class . ':cocherasExcel');
    $this->post('/historicoPatentePDF', \exportPDF::class . ':historicoPatentePDF');
})->add(\verificarJWT::class . ':VerificarToken');

$app->run();

?>