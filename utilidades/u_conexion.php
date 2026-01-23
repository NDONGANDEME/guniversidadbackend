<?php

class ConexionUtil
{
    public static $conexion = null;

    public static function conectar()
    {
        if (self::$conexion === null) {
            $host = 'localhost';
            $base_datos = 'gfacultad';
            $usuario = 'root';
            $contrasena = '';

            try {
                self::$conexion = new PDO("mysql:host=$host;dbname=$base_datos;charset=utf8mb4", $usuario, $contrasena,);
                self::$conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$conexion->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                throw new Exception("Error de conexión: " . $e->getMessage());
            }
        }

        return self::$conexion;
    }

    public static function cerrar()
    {
        self::$conexion = null;
    }
}
