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
                case "actualizarNoticia":
                    self::actualizarNoticia($parametros);
                    break;
                case "eliminarNoticia":
                    self::eliminarNoticia($parametros['id'] ?? null);
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

    // Crear noticia usando las funciones adaptadas
    private static function crearNoticia($parametros)
    {
        // Validar campos obligatorios - ADAPTADO a los campos de tu BD
        $asunto = $parametros['asunto'] ?? $parametros['titulo'] ?? ''; // Acepta ambos nombres
        $descripcion = $parametros['descripcion'] ?? $parametros['contenido'] ?? '';
        $tipo = $parametros['tipo'] ?? 'general';
        
        if (empty($asunto) || empty($descripcion)) {
            echo json_encode([
                'estado' => 400,
                'éxito' => false,
                'mensaje' => 'Asunto y descripción son obligatorios'
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

        // Insertar noticia en BD primero (con los campos correctos)
        $noticiaId = NoticiasDao::crearNoticia([
            'asunto' => $asunto,
            'descripcion' => $descripcion,
            'tipo' => $tipo,
            'fechaPublicacion' => date('Y-m-d H:i:s')
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
            
            // Guardar referencias de fotos en BD (tabla foto)
            if (!empty($archivosGuardados['fotos'])) {
                NoticiasDao::guardarFotosNoticia($noticiaId, $archivosGuardados['fotos']);
            }
        }

        // Obtener la noticia creada con sus fotos
        $noticiaCreada = NoticiasDao::obtenerNoticiaPorId($noticiaId);

        echo json_encode([
            'estado' => 201,
            'éxito' => true,
            'mensaje' => 'Noticia creada exitosamente',
            'datos' => $noticiaCreada
        ]);
    }

    // NUEVA FUNCIÓN: Actualizar noticia
    private static function actualizarNoticia($parametros)
    {
        $id = $parametros['id'] ?? null;
        
        if (!$id) {
            echo json_encode([
                'estado' => 400,
                'éxito' => false,
                'mensaje' => 'ID de noticia no proporcionado'
            ]);
            return;
        }

        // Verificar que la noticia existe
        $noticiaExistente = NoticiasDao::obtenerNoticiaPorId($id);
        if (!$noticiaExistente) {
            echo json_encode([
                'estado' => 404,
                'éxito' => false,
                'mensaje' => 'Noticia no encontrada'
            ]);
            return;
        }

        // Preparar datos a actualizar
        $asunto = $parametros['asunto'] ?? $parametros['titulo'] ?? $noticiaExistente['asunto'];
        $descripcion = $parametros['descripcion'] ?? $parametros['contenido'] ?? $noticiaExistente['descripcion'];
        $tipo = $parametros['tipo'] ?? $noticiaExistente['tipo'];

        if (empty($asunto) || empty($descripcion)) {
            echo json_encode([
                'estado' => 400,
                'éxito' => false,
                'mensaje' => 'Asunto y descripción son obligatorios'
            ]);
            return;
        }

        // Obtener nuevas fotos si existen
        $fotos = $parametros['fotos'] ?? null;

        // Validar fotos si existen
        if ($fotos && !LimpiarDatos::validarMultiplesArchivos($fotos, 'foto')) {
            echo json_encode([
                'estado' => 400,
                'éxito' => false,
                'mensaje' => 'Una o más fotos no son válidas'
            ]);
            return;
        }

        // Actualizar noticia
        $actualizado = NoticiasDao::actualizarNoticia($id, [
            'asunto' => $asunto,
            'descripcion' => $descripcion,
            'tipo' => $tipo
        ]);

        if (!$actualizado) {
            echo json_encode([
                'estado' => 500,
                'éxito' => false,
                'mensaje' => 'Error al actualizar la noticia'
            ]);
            return;
        }

        // Si hay nuevas fotos, reemplazar las existentes
        if ($fotos) {
            // Eliminar fotos antiguas
            NoticiasDao::eliminarFotosNoticia($id);
            
            // Guardar nuevas fotos
            $fotosGuardadas = LimpiarDatos::guardarMultiplesArchivos($fotos, 'foto', $id);
            if (!empty($fotosGuardadas)) {
                NoticiasDao::guardarFotosNoticia($id, $fotosGuardadas);
            }
        }

        // Obtener noticia actualizada
        $noticiaActualizada = NoticiasDao::obtenerNoticiaPorId($id);

        echo json_encode([
            'estado' => 200,
            'éxito' => true,
            'mensaje' => 'Noticia actualizada exitosamente',
            'datos' => $noticiaActualizada
        ]);
    }

    // NUEVA FUNCIÓN: Eliminar noticia
    private static function eliminarNoticia($id)
    {
        if (!$id) {
            echo json_encode([
                'estado' => 400,
                'éxito' => false,
                'mensaje' => 'ID de noticia no proporcionado'
            ]);
            return;
        }

        // Verificar que la noticia existe
        $noticiaExistente = NoticiasDao::obtenerNoticiaPorId($id);
        if (!$noticiaExistente) {
            echo json_encode([
                'estado' => 404,
                'éxito' => false,
                'mensaje' => 'Noticia no encontrada'
            ]);
            return;
        }

        // Eliminar noticia (esto también eliminará las fotos por la función en el DAO)
        $eliminado = NoticiasDao::eliminarNoticia($id);

        if ($eliminado) {
            echo json_encode([
                'estado' => 200,
                'éxito' => true,
                'mensaje' => 'Noticia eliminada exitosamente'
            ]);
        } else {
            echo json_encode([
                'estado' => 500,
                'éxito' => false,
                'mensaje' => 'Error al eliminar la noticia'
            ]);
        }
    }
}
?>