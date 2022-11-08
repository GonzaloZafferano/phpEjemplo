<?php

use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

//require_once("../models/Usuario.php");

class credencialesMiddleware
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

        if($request->getMethod() === 'POST'){
            $mensaje .= "Verifico credenciales" . PHP_EOL;

            $parametros = $request->getParsedBody();

            if(isset($parametros['perfil']) && strcasecmp($parametros['perfil'], 'admin') == 0){
                $respuesta = $handler->handle($request); 

                $respuesta = json_decode($respuesta->getBody());

                $nombre = isset($parametros['nombre']) ? $parametros['nombre'] : "Sin nombre";
                $mensaje .= $respuesta->mensaje . " ". $nombre . PHP_EOL;
            }else{
                $mensaje .= "No tiene habilitado el ingreso". PHP_EOL;
            }


        }else if($request->getMethod() === 'GET'){
            $mensaje .= "No necesita Verificar credenciales" . PHP_EOL;

            $respuesta = $handler->handle($request); 

            $respuesta = json_decode($respuesta->getBody());

            $mensaje .= $respuesta->mensaje  . PHP_EOL;
        }  

        $mensaje .="API=> " . $request->getMethod() . PHP_EOL;
        $mensaje .= "vuelvo del controlador verificador";

        $r = new Response();
        $r->getBody()->write($mensaje);

        return $r->withHeader('Content-Type', 'application/json');
    }
}