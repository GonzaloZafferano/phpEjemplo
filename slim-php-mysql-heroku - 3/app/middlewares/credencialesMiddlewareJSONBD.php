<?php

use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

require_once './models/Usuario.php';



class credencialesJSONBDMiddleware
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
        //$method =  $request->getMethod() ;
         
        $r = new Response();

        $respuesta = "-";

        $parametros = $request->getParsedBody();

        if(isset($parametros['obj'])){

            $parametros = json_decode($parametros['obj']);
            $nombre = $parametros->nombre;
            $pass = $parametros->clave;

            $usuario = Usuario::obtenerUsuario($nombre);
        
            if($usuario){
        
                //echo "AAAA" . password_verify($clave, $usuario->clave) . "bbb"; 1 true, vacio false.
        
                if(password_verify($pass, $usuario->clave)){
                // if(strcmp($usuario->clave, $clave) == 0){
        
                    //CONVIERTE UN OBJETO A JSON STRING.
                    $respuesta = $handler->handle($request); //ESTA LINEA LLEVA AL CONTROLLER, SIN ESTA LINEA, NO LLAMA AL CONTROLLER.
                    $respuesta = json_decode($respuesta->getBody());                     
            
                    $respuesta = json_encode($respuesta);
                    $r = $r->withStatus(201);

                }else{
                    //PASS NO EXISTE
                    //CONVIERTE UN OBJETO A JSON STRING.
                    $respuesta = json_encode(array("mensaje" => "Clave incorrecta"));
                    $r = $r->withStatus(403);
                }
            }else{
                //CONVIERTE UN OBJETO A JSON STRING.
                $respuesta = json_encode(array("mensaje" => "Usuario incorrecto!"));
                $r = $r->withStatus(403);
            }
        }else{
            $respuesta = json_encode(array("mensaje" => "ERROR. Faltan datos para operar."));

            $r = $r->withStatus(403);
        }

        $r->getBody()->write($respuesta);

        return $r->withHeader('Content-Type', 'application/json');
    }
}