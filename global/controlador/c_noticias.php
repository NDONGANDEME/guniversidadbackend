<?php
require_once __DIR__ . "/../utilidades/u_verificaciones.php";
require_once __DIR__ . "/../dao/d_noticias.php";
require_once __DIR__ . "/../modelo/m_noticia.php";

class NoticiasController
{
    public static function dispatch($accion, $parametros)
    {
        if (VerificacionesUtil::validarDispatch($accion, $parametros)) {
            switch ($accion) {
                // Operaciones públicas (solo lectura)
                case "listarNoticias":
                    self::listarNoticias($parametros['tipo'] ?? null);
                    break;
                    
                case "obtenerNoticiaPorId":
                    self::obtenerNoticiaPorId($parametros['id'] ?? null);
                    break;
                    
                case "listarNoticiasRecientes":
                    self::listarNoticiasRecientes($parametros['tipo'] ?? null);
                    break;
                    
                case "obtenerNoticiasPaginadas":
                    self::obtenerNoticiasPaginadas(
                        $parametros['pagina'] ?? 1,
                        $parametros['tipo'] ?? null
                    );
                    break;
                    
                case "obtenerTotalPaginas":
                    self::obtenerTotalPaginas($parametros['tipo'] ?? null);
                    break;
                    
                case "buscarNoticias":
                    self::buscarNoticias(
                        $parametros['termino'] ?? '',
                        $parametros['tipo'] ?? null
                    );
                    break;
                    
                case "obtenerNoticiasPorTipo":
                    self::obtenerNoticiasPorTipo(
                        $parametros['tipo'] ?? '',
                        $parametros['limite'] ?? null
                    );
                    break;
                    
                default:
                    echo json_encode([
                        'estado' => 400,
                        'exito' => false,
                        'mensaje' => "Acción '$accion' no válida en el controlador de noticias",
                        'resultado' => null
                    ]);
            }
        }
    }

    // Listar todas las noticias
    private static function listarNoticias($tipo = null)
    {
        $noticias = D_Noticias::listarNoticias($tipo);
        $resultado = [];
        
        foreach ($noticias as $noticia) {
            $resultado[] = $noticia->convertirAArray();
        }
        
        echo json_encode([
            'estado' => 200,
            'exito' => true,
            'mensaje' => 'Noticias obtenidas correctamente',
            'resultado' => $resultado
        ]);
    }

    // Obtener noticia por ID
    private static function obtenerNoticiaPorId($id)
    {
        if (!$id) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'ID de noticia no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        $noticia = D_Noticias::obtenerNoticiaPorId($id);
        
        if ($noticia) {
            echo json_encode([
                'estado' => 200,
                'exito' => true,
                'mensaje' => 'Noticia obtenida correctamente',
                'resultado' => $noticia->convertirAArray()
            ]);
        } else {
            echo json_encode([
                'estado' => 404,
                'exito' => false,
                'mensaje' => 'Noticia no encontrada',
                'resultado' => null
            ]);
        }
    }

    // Listar las 5 noticias más recientes
    private static function listarNoticiasRecientes($tipo = null)
    {
        $noticias = D_Noticias::obtenerNoticiasRecientes($tipo);
        $resultado = [];
        
        foreach ($noticias as $noticia) {
            $resultado[] = $noticia->convertirAArray();
        }
        
        echo json_encode([
            'estado' => 200,
            'exito' => true,
            'mensaje' => 'Noticias recientes obtenidas correctamente',
            'resultado' => $resultado
        ]);
    }

    // Obtener noticias paginadas
    private static function obtenerNoticiasPaginadas($pagina, $tipo = null)
    {
        $pagina = intval($pagina);
        if ($pagina < 1) $pagina = 1;
        
        $noticias = D_Noticias::obtenerNoticiasAPaginar($pagina, $tipo);
        $resultado = [];
        
        foreach ($noticias as $noticia) {
            $resultado[] = $noticia->convertirAArray();
        }
        
        echo json_encode([
            'estado' => 200,
            'exito' => true,
            'mensaje' => 'Noticias paginadas obtenidas correctamente',
            'resultado' => [
                'pagina_actual' => $pagina,
                'noticias' => $resultado
            ]
        ]);
    }

    // Obtener total de páginas
    private static function obtenerTotalPaginas($tipo = null)
    {
        $totalPaginas = D_Noticias::contarNoticias($tipo);
        
        echo json_encode([
            'estado' => 200,
            'exito' => true,
            'mensaje' => 'Total de páginas obtenido correctamente',
            'resultado' => [
                'total_paginas' => $totalPaginas
            ]
        ]);
    }

    // Buscar noticias por término
    private static function buscarNoticias($termino, $tipo = null)
    {
        if (empty($termino)) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'Término de búsqueda no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        $noticias = D_Noticias::buscarNoticias($termino, $tipo);
        $resultado = [];
        
        foreach ($noticias as $noticia) {
            $resultado[] = $noticia->convertirAArray();
        }
        
        echo json_encode([
            'estado' => 200,
            'exito' => true,
            'mensaje' => 'Búsqueda realizada correctamente',
            'resultado' => $resultado
        ]);
    }

    // Obtener noticias por tipo
    private static function obtenerNoticiasPorTipo($tipo, $limite = null)
    {
        if (empty($tipo)) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'Tipo de noticia no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        $noticias = D_Noticias::obtenerNoticiasPorTipo($tipo, $limite);
        $resultado = [];
        
        foreach ($noticias as $noticia) {
            $resultado[] = $noticia->convertirAArray();
        }
        
        echo json_encode([
            'estado' => 200,
            'exito' => true,
            'mensaje' => 'Noticias por tipo obtenidas correctamente',
            'resultado' => $resultado
        ]);
    }
}
?>