<?php

class Usuario
{
    public $id;
    public $usuario;
    public $clave;

    public function crearUsuario()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO usuarios (usuario, clave) VALUES (:usuario, :clave)");
        $claveHash = password_hash($this->clave, PASSWORD_DEFAULT);
        $consulta->bindValue(':usuario', $this->usuario, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $claveHash);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, usuario, clave FROM usuarios WHERE fechaBaja is null");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Usuario');
    }

    public static function obtenerUsuario($usuario)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, usuario, clave FROM usuarios WHERE usuario = :usuario AND fechaBaja is null");
        $consulta->bindValue(':usuario', $usuario, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Usuario');
    }

    public static function modificarUsuario($usuario, $nombre, $clave)
    {
        try{
            if(isset($usuario) && isset($nombre) && isset($clave) && !empty(trim($nombre)) && !empty(trim($clave))){
                $objAccesoDato = AccesoDatos::obtenerInstancia();
                $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET usuario = :usuario, clave = :clave WHERE id = :id");
                $consulta->bindValue(':usuario', $nombre, PDO::PARAM_STR);
                $consulta->bindValue(':clave', $clave, PDO::PARAM_STR);
                $consulta->bindValue(':id', $usuario->id, PDO::PARAM_INT);
                $consulta->execute();
    
                return true;
            }
        }catch(Exception $ex){
            echo "Excepcion: " . $ex->getMessage();
        }
        return false;
    }

    public static function borrarUsuario($usuarioId)
    {
        try{

            if(Usuario::obtenerUsuarioPorId($usuarioId)){

                if(isset($usuarioId) && !empty($usuarioId)){
                    $objAccesoDato = AccesoDatos::obtenerInstancia();
                    $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET fechaBaja = :fechaBaja WHERE id = :id and fechaBaja is null");
                    $fecha = new DateTime(date("d-m-Y"));
                    $consulta->bindValue(':id', $usuarioId, PDO::PARAM_INT);
                    $consulta->bindValue(':fechaBaja', date_format($fecha, 'Y-m-d H:i:s'));
                    $consulta->execute();
                    
                    return true;
                }
            }

        }catch(Exception $ex){
            echo "Excepcion: " . $ex->getMessage();
        }
        return false;
    }

    public static function obtenerUsuarioPorId($usuarioId)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, usuario, clave FROM usuarios WHERE id = :usuarioId AND fechaBaja is null");
        $consulta->bindValue(':usuarioId', $usuarioId, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Usuario');
    }
}