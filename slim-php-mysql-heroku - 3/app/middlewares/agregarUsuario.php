<?php

use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

//require_once("../models/Usuario.php");

class AgregarUsuarioMiddleware
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
        $parametros = $request->getParsedBody();


        if(isset($parametros['perfil']) && strcasecmp($parametros['perfil'], 'admin') == 0){


            if(isset($parametros['usuario']) && isset($parametros['clave']) && !empty($parametros['usuario']) && !empty($parametros['clave'])){
           

                //SI LOS DATOS SON VALIDOS, VOY AL CONTROLLER DEL ENRUTADOR.
                // Continua al controller QUE ESTA EN EL ENRUTADOR. 
                //EN $response se guarda lo que retorna el metodo del controller.
                $respuesta = $handler->handle($request); //ESTA LINEA LLEVA AL CONTROLLER, SIN ESTA LINEA, NO LLAMA AL CONTROLLER.
                $existingContent = json_decode($respuesta->getBody());   

               
        
                $payload = json_encode($existingContent);
        
        
                //DESCOMENTO ESTO, Y COMENTO LO DEMAS PARA QUE PASE DIRECTO SALTEANDO EL CONTROLLER.
              //  $response = new Response();
              //  $response->getBody()->write("ENTRO");        
             //   $respuesta->getBody()->write($payload);
                
                sleep(2);
                    
           }else{
                
                //SI NO ESTAN SETEADAS LAS VARIABLES DE NOMBRE, CLAVE,
                //ENTONCES CARGAMOS UNA RESPONSE VACIA (algo hay que retornar).
                $respuesta = new Response();
                $payload = json_encode(array("mensaje" => "Debe ingresar usuario y contraseña."));
             //   $respuesta->getBody()->write($payload);
            } 

        }else{
            $respuesta = new Response();
            $payload = json_encode(array("mensaje" => "El perfil debe ser 'admin' para ingresar un usuario"));
          //  $respuesta->getBody()->write($payload);
        }

        $r = new Response();
        $r->getBody()->write($payload);

        return $r->withHeader('Content-Type', 'application/json');
    }
}