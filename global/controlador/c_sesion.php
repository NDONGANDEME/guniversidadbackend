<?php

require_once __DIR__ . "../dao/d_sesion.php";
require_once __DIR__ . "../entidades/e_sesion.php";
require_once __DIR__ . "../../utilidades/u_verificaciones.php";

class SesionController
{
    public static function dispatch($accion, $parametros)
    {

        if ($accion == "iniciarSesion") {

            self::getUsuarioByCorreo($accion, $parametros);
        } else {
            echo json_encode(['estado' => 400, 'éxito' => false, 'mensaje' => "La accion '$accion' no esta disponible"]);
        }
    }

    public static function getUsuarioByCorreo($accion, $parametros)
    {
        if (VerificacionesUtil::validarSesion($accion, $parametros['correo'])) {
            $usuario = D_Sesion::obtenerUsuarioByCorreo($parametros['correo']);
            if ($usuario == null) {
                return null;
            } else {
                if (VerificacionesUtil::verificarContrasenas($parametros['contrasena'], $usuario['contrasena']));
                return $usuario;
            }
        }
    }
}
