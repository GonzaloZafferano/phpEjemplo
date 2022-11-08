<?php

use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

//require_once("../models/Usuario.php");

class GetYPutMiddleware
{
    /**
     * Example middleware invokable class
     *
     * @param  ServerRequest  $request PSR-7 request
     * @param  RequestHandler $handler PSR-15 request handler
     *
     * @return Response
     */
    
    public function __invoke(Request $request, RequestHandler $handler): Response
    {   
        //var_dump($request);

        $antes = date('Y-m-d H:i:s');

        //EN $respuesta se guarda lo que retorna el metodo del controller.
        //ME RETORNA UN STRING JSON.
        $respuesta = $handler->handle($request); //ESTA LINEA LLEVA AL CONTROLLER, SIN ESTA LINEA, NO LLAMA AL CONTROLLER.


        //LA RESPUESTA ES UN JSON STRING. DECODIFICA UN STRING JSON, y obtiene el objeto.
        //AHORA TENGO UN OBJETO.
        $existingContent = json_decode($respuesta->getBody());

        //AHORA QUE TENGO EL OBJETO, PUEDO AGREGARLE  PROPIEDADES.
        $existingContent->fechaAntes = $antes;
        $existingContent->fechaDespues = date('Y-m-d H:i:s');
           
        //CODIFICA NUEVAMENTE EL OBJETO A UN STRING JSON.
        $payload = json_encode($existingContent);
        
        $r = new Response();
        $r->getBody()->write($payload);

        return $r->withHeader('Content-Type', 'application/json');
    }
}