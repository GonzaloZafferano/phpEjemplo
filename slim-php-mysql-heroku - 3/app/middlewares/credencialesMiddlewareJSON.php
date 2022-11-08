<?php

use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

//require_once("../models/Usuario.php");

class credencialesJSONMiddleware
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
        //$method = $request->getMethod();
        /*
        $request->isGet()
        $request->isPost()
        $request->isPut()
        $request->isDelete()
        $request->isHead()
        $request->isPatch()
        $request->isOptions()
        */
        
        $mensaje = "";

      //$method =  $request->getMethod() ;
         $r = new Response();


        if($request->getMethod() === 'POST'){

            $parametros = $request->getParsedBody();

            if(isset($parametros['obj_json']) && strcasecmp(json_decode($parametros['obj_json'])->perfil, 'admin') == 0){

                $respuesta = $handler->handle($request); 

                $respuesta = json_decode($respuesta->getBody());
                
                $respuesta = json_encode($respuesta);

                $r = $r->withStatus(200);
            }else{
                $nombre = isset(json_decode($parametros['obj_json'])->nombre) ?
                json_decode($parametros['obj_json'])->nombre : "Sin nombre";

                $respuesta = json_encode(array("mensaje" => "ERROR. " . $nombre . " sin permisos."));

                $r = $r->withStatus(403);
            }


        }else if($request->getMethod() === 'GET'){ 

            $respuesta = $handler->handle($request); 

            $respuesta = json_decode($respuesta->getBody());
            
            $respuesta = json_encode($respuesta);
            
            $r = $r->withStatus(201);
        }  


       
        $r->getBody()->write($respuesta);

        return $r->withHeader('Content-Type', 'application/json');
    }
}