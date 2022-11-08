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



// '/app' o '/app/' PERO GET
$app->get('[/]', function (Request $request, Response $response) {    
    $response->getBody()->write("Slim Framework 4.2 PHP");
    return $response;

});



$app->run();
