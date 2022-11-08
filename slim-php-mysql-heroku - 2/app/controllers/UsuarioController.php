<?php
require_once './models/Usuario.php';
require_once './interfaces/IApiUsable.php';

class UsuarioController extends Usuario implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $usuario = $parametros['usuario'];
        $clave = $parametros['clave'];

        // Creamos el usuario
        $usr = new Usuario();
        $usr->usuario = $usuario;
        $usr->clave = $clave;
        $usr->crearUsuario();

        $payload = json_encode(array("mensaje" => "Usuario creado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        // Buscamos usuario por nombre
        $usr = $args['usuario'];
        $usuario = Usuario::obtenerUsuario($usr);

        if($usuario){
          $payload = json_encode($usuario);
  
          $response->getBody()->write($payload);
          return $response
            ->withHeader('Content-Type', 'application/json');           
        }else{
          $payload = json_encode(array("mensaje" => "No se encontro al usuario: " . $usr));
    
          $response->getBody()->write($payload);
          return $response
            ->withHeader('Content-Type', 'application/json');

        }
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Usuario::obtenerTodos();
        $payload = json_encode(array("listaUsuario" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args)
    {
      //OBTENGO LOS PARAMETROS DEL BODY DE LA PETICION PUT.
      $parametros = $request->getParsedBody();

      //OBTENGO EL NOMBRE DEL USUARIO QUE VIENE EN LA URL.
      $userName = $args["usuario"];

      //OBTENGO EL USUARIO A PARTIR DEL NOMBRE.
      $usuario = Usuario::obtenerUsuario($userName);

      if($usuario){
        $nombre = $parametros['nombre'];
        $clave = $parametros['clave'];
  
        if(Usuario::modificarUsuario($usuario, $nombre, $clave)){
  
          $payload = json_encode(array("mensaje" => "Usuario modificado con exito"));
    
          $response->getBody()->write($payload);
          return $response
            ->withHeader('Content-Type', 'application/json');

        }else{
          $payload = json_encode(array("mensaje" => "No se ha podido modificar el usuario. Revise que los datos enviados sean correctos."));  
          $response->getBody()->write($payload);
          return $response
            ->withHeader('Content-Type', 'application/json');
        }

      }else{
        $payload = json_encode(array("mensaje" => "No se ha encontrado al usuario: " . $userName));  
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
      }
    }

    public function BorrarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $usuarioId = $parametros['usuarioId'];

        if(Usuario::borrarUsuario($usuarioId)){

          //CONVIERTE UN OBJETO A JSON STRING.
          $payload = json_encode(array("mensaje" => "Usuario borrado con exito"));
  
          $response->getBody()->write($payload);
          return $response
            ->withHeader('Content-Type', 'application/json');
        }else{
          //CONVIERTE UN OBJETO A JSON STRING.
          $payload = json_encode(array("mensaje" => "No se ha encontrado al usuario para borrar."));
  
          $response->getBody()->write($payload);
          return $response
            ->withHeader('Content-Type', 'application/json');
        }
    }

    public function ValidarUsuario($request, $response, $args){

      $parametros = $request->getParsedBody();
      $nombre = $parametros['usuario'];
      $clave = $parametros['clave'];

      $usuario = Usuario::obtenerUsuario($nombre);

      //CONVIERTE UN OBJETO A JSON STRING.
      $payload = json_encode(array("mensaje" => "Usuario incorrecto!"));

      if($usuario){

        //echo "AAAA" . password_verify($clave, $usuario->clave) . "bbb"; 1 true, vacio false.

        if(password_verify($clave, $usuario->clave)){
       // if(strcmp($usuario->clave, $clave) == 0){

          //CONVIERTE UN OBJETO A JSON STRING.
          $payload = json_encode(array("mensaje" => "Bienvenido!"));
        }else{
          //PASS NO EXISTE
          //CONVIERTE UN OBJETO A JSON STRING.
          $payload = json_encode(array("mensaje" => "Clave incorrecta"));
        }
      }

      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }
    
    public function CalcularTiempo($request, $response, $args){

      //CONVIERTE UN OBJETO A JSON STRING.
      $payload = json_encode(array("mensaje" => "Demorando..."));

      sleep(3);
    
      //RETORNA UN JSON STRING.
      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }
}
