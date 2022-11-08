<?php

use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

//require_once("../models/Usuario.php");

class ValidarToken
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
        $header = $request->getHeaderLine('Authorization'); //OBTENGO EL HEADER "Authorization".
        $token = trim(explode("Bearer", $header)[1]);
        $esValido = false;
    
        try {
            AutentificadorJWT::verificarToken($token);
            $esValido = true;
        } catch (Exception $e) {

            //SI ENTRA ACA, "esValido" queda en FALSE.
            $payload = json_encode(array('error' => $e->getMessage()));
        }
    
        if ($esValido) {
          //  $payload = json_encode(array('valid' => $esValido));

            $respuesta = $handler->handle($request); //ESTA LINEA LLEVA AL CONTROLLER, SIN ESTA LINEA, NO LLAMA AL CONTROLLER.
            $existingContent = json_decode($respuesta->getBody());   

           
    
            $payload = json_encode($existingContent);
        }

        $response = new Response();    
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');

    }
}