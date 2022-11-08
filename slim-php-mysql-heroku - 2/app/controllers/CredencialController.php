<?php
require_once './models/Usuario.php';
require_once './interfaces/IApiUsable.php';

class CredencialController
{
    public function VerificarCredencialPOST($request, $response, $args)
    {
        $payload = json_encode(array("mensaje" => "Bienvenido"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function VerificarCredencialGET($request, $response, $args)
    {
        $payload = json_encode(array("mensaje" => "-"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function VerificarCredencialJSONPOST($request, $response, $args)
    {
        $payload = json_encode(array("mensaje" => "API => POST"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function VerificarCredencialJSONGET($request, $response, $args)
    {
        $payload = json_encode(array("mensaje" => "API => GET"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
}
