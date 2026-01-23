<?php

require_once __DIR__ . "/../utilidades/u_conexion.php";

class D_Sesion
{
    //FUNCIÓN PARA OBTTENER USUARIO MEDIANTE CORREO, CONTRASEÑA Y ROL
    public static function obtenerUsuarioByCorreo($correo)
    {
        try {

            $instanciaConexion = U_Conexion::conectar();

            $sql = "SELECT * FROM usuarios WHERE correo=:correo";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':correo', $correo);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
}
