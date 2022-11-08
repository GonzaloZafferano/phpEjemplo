<?php
//Zafferano Gonzalo
// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;

require __DIR__ . '/../vendor/autoload.php';

require_once './db/AccesoDatos.php';
 require_once './middlewares/Logger.php';
 require_once './middlewares/agregarUsuario.php';
 require_once './middlewares/borrarUsuario.php';
 require_once './middlewares/getYput.php';
 require_once './middlewares/credencialesMiddleware.php';
 require_once './middlewares/credencialesMiddlewareJSON.php';
 require_once './middlewares/credencialesMiddlewareJSONBD.php';
 require_once './middlewares/credencialesMiddlewareJSONBD2.php';





require_once './controllers/UsuarioController.php';
require_once './controllers/CredencialController.php';

// Load ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Instantiate App
$app = AppFactory::create();





//LA BASE TIENE QUE EMPEZAR CON '/app' y despues de ahi, viene el resto 
//de la ruta, por la cual trabajamos, preguntando si tiene esto o lo otro.
//http://localhost:666/app es el LINK BASE
$app->setBasePath('/app');  //AGREGO ESTA LINEA.




// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();



// Routes... AGRUPO POR RUTAS QUE EMPIECEN CON '/app/usuarios'
$app->group('/usuarios', function (RouteCollectorProxy $group) {
  
    //LEER
    //SI VIENE CON O SIN BARRA, Y ES GET. 
    //'/app/usuarios' o '/app/usuarios/' pero por GET.
    $group->get('[/]', \UsuarioController::class . ':TraerTodos'); 

  
    //SI VIENE CON UN PARAMETRO DESPUES DE LA BARRA Y ES GET.
    //EL PARAMETRO DEBE SER EL NOMBRE (NO EL ID)
    //'/app/usuarios/nombreDeUsuario'
//    $group->get('/{usuario}', \UsuarioController::class . ':TraerUno'); 
   
    /*
   
    //INSERTAR
    //SI VIENE CON O SIN BARRA, PERO ES POST.
    //'/app/usuarios/' o '/app/usuarios' pero por POST.
    $group->post('[/]', \UsuarioController::class . ':CargarUno'); 

    //MODIFICAR UNO
    $group->put('/{usuario}', \UsuarioController::class . ':ModificarUno'); 

    //BORRAR UNO
    $group->delete('[/]', \UsuarioController::class . ':BorrarUno'); 

*/


    //AGREGO EL MIDDLEWARE PARA LA APP.
    //LOGGIN

    //VA DIRECTO  AL VALIDAR USUARIO.
    //$group->post('/loggin', \UsuarioController::class . ':ValidarUsuario');
    //PASA POR EL MIDDLEWARE PRIMERO Y DESPUES.
   // $group->post('/loggin', \UsuarioController::class . ':ValidarUsuario')->add(new LoggerMiddleware());; 

  });






//Hacer un middleware de aplicación que tome usuario y
//contraseña y verifique en BD.
  $app->post('/usuarios/loggin', \UsuarioController::class . ':ValidarUsuario')->add(new LoggerMiddleware());; 



//  Hacer middleware de grupo, solo para post, que permita
//  agregar un nuevo usuario, sólo si el perfil es ‘admin’.
$app->group('/usuarios', function (RouteCollectorProxy $group) {

  $group->post('/agregar[/]',  \UsuarioController::class . ':CargarUno');

})->add(new AgregarUsuarioMiddleware());



//Hacer middleware de grupo, solo para delete, que
//permita borrar un usuario, si el perfil es ‘super_admin’.
$app->group('/usuarios', function (RouteCollectorProxy $group) {

  $group->delete('/borrar[/]',  \UsuarioController::class . ':BorrarUno');

})->add(new BorrarUsuarioMiddleware());


//Hacer middleware de ruta, solo para put y get, que tome
//el tiempo de demora entre que entra y sale la petición.
$app->group('/usuarios', function (RouteCollectorProxy $group) {
  
    $group->put('/tiempo[/]', \UsuarioController::class . ':CalcularTiempo')->add(new GetYPutMiddleware());
  
    $group->get('/tiempo[/]', \UsuarioController::class . ':CalcularTiempo')->add(new GetYPutMiddleware());

});



/*$argc
AGREGAR EL GRUPO /CREDENCIALES CON LOS VERBOS GET Y
POST (MOSTRAR QUE VERBO ES).
AL GRUPO, AGREGARLE UN MW QUE, DE ACUERDO EL VERBO,
VERIFIQUE CREDENCIALES O NO.
GET -> NO VERIFICA. ACCEDE AL VERBO.
POST-> VERIFICA; SE ENVIA: NOMBRE Y PERFIL.
*- SI EL PERFIL ES 'ADMINISTRADOR', MUESTRA EL NOMBRE Y
ACCEDE AL VERBO.
*/
$app->group('/credenciales', function (RouteCollectorProxy $group) {
  
  $group->post('[/]', \CredencialController::class . ':VerificarCredencialPOST');

  $group->get('[/]', \CredencialController::class . ':VerificarCredencialGET');

})->add(new credencialesMiddleware());



/*
AGREGAR EL GRUPO /JSON CON LOS VERBOS GET Y POST.
RETORNA UN JSON (MENSAJE, STATUS).
AL GRUPO, AGREGARLE UN MW QUE, DE ACUERDO EL VERBO,
VERIFIQUE CREDENCIALES O NO.
GET -> NO VERIFICA. ACCEDE AL VERBO. RETORNA
{"API=>GET", 200}.
POST-> VERIFICA; SE ENVIA (JSON): OBJ_JSON, CON NOMBRE Y
PERFIL.
*- SI EL PERFIL ES 'ADMINISTRADOR', ACCEDE AL VERBO.
RETORNA {"API=>POST", 200}.
*- SI NO, MUESTRA MENSAJE DE ERROR. NO ACCEDE AL VERBO.
RETORNA {"ERROR. NOMBRE", 403}
*/

$app->group('/JSON', function (RouteCollectorProxy $group) {
  
  $group->post('[/]', \CredencialController::class . ':VerificarCredencialJSONPOST');

  $group->get('[/]', \CredencialController::class . ':VerificarCredencialJSONGET');

})->add(new credencialesJSONMiddleware());



/*
AGREGAR EL GRUPO /JSON_BD CON LOS VERBOS GET Y POST (A
NIVEL RAIZ).
GET Y POST -> TRAEN (EN FORMATO JSON) TODOS LOS
USUARIO DE LA BASE DE DATOS. USUARIO->TRAERTODOS().
AGREGAR UN MW, PARA POST, QUE VERIFIQUE AL USUARIO
(CORREO Y CLAVE).
POST-> VERIFICADORA->VERIFICARUSUARIO(); SE ENVIA(JSON):
OBJ_JSON, CON CORREO Y CLAVE.
*- SI EXISTE EL USUARIO EN LA BASE DE DATOS
(VERIFICADORA::EXISTEUSUARIO($OBJ)), ACCEDE AL VERBO.
*- SI NO, MUESTRA MENSAJE DE ERROR. NO ACCEDE AL VERBO.
{"ERROR.", 403}
*/
$app->group('/JSON_bd', function (RouteCollectorProxy $group) {
  
  $group->post('[/]', \UsuarioController::class . ':TraerTodos')->add(new credencialesJSONBDMiddleware());

  $group->get('[/]', \UsuarioController::class . ':TraerTodos');

});


/*
AGREGAR, A NIVEL DE GRUPO UN MW, QUE VERIFIQUE:
GET-> ACCEDE AL VERBO. (NO HACE NADA NUEVO).
POST-> VERIFICA SI FUE ENVIADO EL PARAMETRO 'OBJ_JSON'.
*- SI NO, MUESTRA MENSAJE DE ERROR. NO ACCEDE AL VERBO.
{"ERROR.", 403}.
*- SI FUE ENVIADO, VERIFICA SI EXISTEN LOS ATRIBUTOS
'CORREO' Y 'CLAVE'.
*-*- SI ALGUNO NO EXISTE (O LOS DOS), MUESTRA MENSAJE DE
ERROR. NO ACCEDE AL VERBO. {"ERROR.", 403}.
*- SI EXISTEN, ACCEDE AL VERBO.
*/
$app->group('/JSON_bd2', function (RouteCollectorProxy $group) {
  
  $group->post('[/]', \UsuarioController::class . ':TraerTodos');

  $group->get('[/]', \UsuarioController::class . ':TraerTodos');

})->add(new credencialesJSONBD2Middleware());








//CONTROLLER
// JWT test routes
$app->group('/jwt', function (RouteCollectorProxy $group) {

  $group->post('/crearToken', \UsuarioController::class . ':crearToken');

});


require_once './middlewares/ValidarToken.php';

// Routes
$app->group('/usuarios123', function (RouteCollectorProxy $group) {
  $group->get('[/]', \UsuarioController::class . ':TraerTodos');
  $group->get('/{usuario}', \UsuarioController::class . ':TraerUno');
  $group->post('[/]', \UsuarioController::class . ':CargarUno');
})->add(new ValidarToken());







// '/app' o '/app/' PERO GET
$app->get('[/]', function (Request $request, Response $response) {    
    $response->getBody()->write("Slim Framework 4 PHP");
    return $response;

});



$app->run();
