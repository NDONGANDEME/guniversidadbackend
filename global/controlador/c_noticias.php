<?php

require_once __DIR__ . "/../utilidades/u_verificaciones.php";
require_once __DIR__ . "/../dao/d_noticias.php";

class NoticiasController
{
    public static function dispatch($accion, $parametros)
    {
        if (VerificacionesUtil::validarDispatch($accion, $parametros)) {

            switch ($accion) {
                case "listarNoticias":
                    self::getNoticias();
                    break;
                case "obtenerNoticiaById":
                    self::getNoticiasById($parametros['id']);
                    break;
                case "listar5NoticiasRecientes":
                    self::getNoticiasRecientes();
                    break;
                case "paginacion":
                    self::getNoticiasPaginacion($parametros['pagina']);
                    break;
                case "obtenerCantidadPaginacion":
                    self::getCantidadPaginacion();
                    break;
                default:
    
                    echo json_encode([
                        'estado' => 400,
                        'éxito' => false,
                        'mensaje' => "Acción '$accion' no válida en el controlqdor de noticias"
                    ]);
            }
        }
    }


    private static function getNoticias(){
        NoticiasDao::listarNoticias();
    }

    private static function getNoticiasById($id){
        NoticiasDao::obtenerNoticiaPorId($id);
    }

    private static function getNoticiasRecientes(){
        NoticiasDao::obtenerNoticiasRecientes();
    }
    private static function getNoticiasPaginacion($pagina){
        NoticiasDao::obtenerNoticiasAPaginar($pagina);
    }
    private static function getCantidadPaginacion(){
    NoticiasDao::contarNoticias();
    }
}
