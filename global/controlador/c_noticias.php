<?php
require_once __DIR__ . "/../utilidades/u_verificaciones.php";
require_once __DIR__ . "/../dao/d_noticias.php";
require_once __DIR__ . "/../utilidades/LimpiarDatos.php";

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
                    self::getNoticiasById($parametros['id'] ?? null);
                    break;
                case "listar5NoticiasRecientes":
                    self::getNoticiasRecientes();
                    break;
                case "paginacion":
                    self::getNoticiasPaginacion($parametros['pagina'] ?? 1);
                    break;
                case "obtenerCantidadPaginacion":
                    self::getCantidadPaginacion();
                    break;
                case "crearNoticia":
                    self::crearNoticia($parametros);
                    break;
                default:
                    echo json_encode([
                        'estado' => 400,
                        'éxito' => false,
                        'mensaje' => "Acción '$accion' no válida en el controlador de noticias"
                    ]);
            }
        }
    }

    private static function getNoticias()
    {
        $resultado = NoticiasDao::listarNoticias();
        echo json_encode([
            'estado' => 200,
            'éxito' => true,
            'datos' => $resultado
        ]);
    }

    private static function getNoticiasById($id)
    {
        if (!$id) {
            echo json_encode([
                'estado' => 400,
                'éxito' => false,
                'mensaje' => 'ID de noticia no proporcionado'
            ]);
            return;
        }

        $resultado = NoticiasDao::obtenerNoticiaPorId($id);
        
        if ($resultado) {
            echo json_encode([
                'estado' => 200,
                'éxito' => true,
                'datos' => $resultado
            ]);
        } else {
            echo json_encode([
                'estado' => 404,
                'éxito' => false,
                'mensaje' => 'Noticia no encontrada'
            ]);
        }
    }

    private static function getNoticiasRecientes()
    {
        $resultado = NoticiasDao::obtenerNoticiasRecientes();
        echo json_encode([
            'estado' => 200,
            'éxito' => true,
            'datos' => $resultado
        ]);
    }

    private static function getNoticiasPaginacion($pagina)
    {
        $pagina = intval($pagina);
        if ($pagina < 1) $pagina = 1;
        
        $resultado = NoticiasDao::obtenerNoticiasAPaginar($pagina);
        echo json_encode([
            'estado' => 200,
            'éxito' => true,
            'datos' => $resultado,
            'pagina_actual' => $pagina
        ]);
    }

    private static function getCantidadPaginacion()
    {
        $resultado = NoticiasDao::contarNoticias();
        echo json_encode([
            'estado' => 200,
            'éxito' => true,
            'total_paginas' => $resultado
        ]);
    }


    // Crear noticia usando las funciones genéricas de archivos
    private static function crearNoticia($parametros)
    {
        // Validar campos obligatorios
        $titulo = $parametros['titulo'] ?? '';
        $contenido = $parametros['contenido'] ?? '';
        
        if (empty($titulo) || empty($contenido)) {
            echo json_encode([
                'estado' => 400,
                'éxito' => false,
                'mensaje' => 'Título y contenido son obligatorios'
            ]);
            return;
        }

        // Obtener archivos
        $fotos = $parametros['fotos'] ?? null;

        // Validar fotos si existen
        if ($fotos && !LimpiarDatos::validarMultiplesArchivos($fotos, 'foto')) {
            echo json_encode([
                'estado' => 400,
                'éxito' => false,
                'mensaje' => 'Una o más fotos no son válidas (solo imágenes JPG, PNG, GIF, WEBP - máx 10MB)'
            ]);
            return;
        }

        // Insertar noticia en BD primero (sin archivos)
        $noticiaId = NoticiasDao::crearNoticia([
            'titulo' => $titulo,
            'contenido' => $contenido,
            'fecha_creacion' => date('Y-m-d H:i:s')
        ]);

        if (!$noticiaId) {
            echo json_encode([
                'estado' => 500,
                'éxito' => false,
                'mensaje' => 'Error al crear la noticia en la base de datos'
            ]);
            return;
        }

        $archivosGuardados = [
            'fotos' => [],
        ];

        // Procesar y guardar fotos usando función genérica
        if ($fotos) {
            $archivosGuardados['fotos'] = LimpiarDatos::guardarMultiplesArchivos($fotos, 'foto', $noticiaId);
            
            // Guardar referencias de fotos en BD
            if (!empty($archivosGuardados['fotos'])) {
                NoticiasDao::guardarFotosNoticia($noticiaId, $archivosGuardados['fotos']);
            }
        }

        echo json_encode([
            'estado' => 201,
            'éxito' => true,
            'mensaje' => 'Noticia creada exitosamente',
            'datos' => [
                'id' => $noticiaId,
                'titulo' => $titulo,
                'archivos' => $archivosGuardados
            ]
        ]);
    }
}
?>