<?php
require_once __DIR__ . "/../dao/d_noticias.php";
require_once __DIR__ . "/../../utilidades/LimpiarDatos.php";
require_once __DIR__ . "/../../utilidades/u_verificaciones.php";
require_once __DIR__ . "/../modelo/m_noticia.php";

class NoticiaController
{
    public static function dispatch($accion, $parametros)
    {
        if (VerificacionesUtil::validarDispatch($accion, $parametros)) {
            // Verificar sesión activa para todas las acciones
            if (!self::verificarSesionActiva()) {
                echo json_encode([
                    'estado' => 401,
                    'exito' => false,
                    'mensaje' => 'No hay sesión activa',
                    'resultado' => null
                ]);
                return;
            }

            switch ($accion) {
                // Operaciones de listado y consulta
                case "obtenerNoticias":
                    self::obtenerNoticias();
                    break;
                    
                case "obtenerNoticiaPorId":
                    self::obtenerNoticiaPorId($parametros['id'] ?? null);
                    break;
                    
                case "obtenerCantidadPaginacion":
                    self::obtenerCantidadPaginacion();
                    break;
                    
                // Operaciones CRUD
                case "insertarNoticia":
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
                        'exito' => false,
                        'mensaje' => "Accion '$accion' no valida en CRUD de noticias",
                        'resultado' => null
                    ]);
            }
        }
    }

    // VERIFICAR SI HAY UNA SESIÓN ACTIVA
    private static function verificarSesionActiva()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        return isset($_SESSION['usuario_id']) && isset($_SESSION['usuario_correo']);
    }

    // Obtener todas las noticias
    private static function obtenerNoticias()
    {
        $noticias = NoticiasDao::listarNoticias();
        $resultado = [];
        
        foreach ($noticias as $noticia) {
            $resultado[] = $noticia->convertirAArray();
        }
        
        echo json_encode([
            'estado' => 'exito',
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

        $noticia = NoticiasDao::obtenerNoticiaPorId($id);
        
        if ($noticia) {
            echo json_encode([
                'estado' => 'exito',
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

    // Obtener cantidad de páginas para paginación
    private static function obtenerCantidadPaginacion()
    {
        $totalPaginas = NoticiasDao::contarNoticias();
        
        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Total de páginas obtenido correctamente',
            'resultado' => [
                'total_paginas' => $totalPaginas
            ]
        ]);
    }

    // Crear noticia
    private static function crearNoticia($parametros)
    {
        // Validar campos obligatorios
        $asunto = $parametros['asunto'] ?? $parametros['titulo'] ?? '';
        $descripcion = $parametros['descripcion'] ?? $parametros['contenido'] ?? '';
        $tipo = $parametros['tipo'] ?? 'general';
        
        if (empty($asunto) || empty($descripcion)) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'Asunto y descripción son obligatorios',
                'resultado' => null
            ]);
            return;
        }

        // Obtener fotos del parámetro (ya vienen procesadas por el gateway)
        $fotos = $parametros['fotos'] ?? null;
        
        // DEBUG: Registrar qué estamos recibiendo (opcional, comentar en producción)
        if ($fotos) {
            error_log("Fotos recibidas en crearNoticia: " . print_r($fotos, true));
        }

        // Validar fotos si existen
        if ($fotos && !LimpiarDatos::validarMultiplesArchivos($fotos, 'foto')) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'Una o más fotos no son válidas (solo imágenes JPG, PNG, GIF, WEBP - máx 10MB)',
                'resultado' => null
            ]);
            return;
        }

        // Insertar noticia en BD
        $noticiaId = NoticiasDao::crearNoticia([
            'asunto' => $asunto,
            'descripcion' => $descripcion,
            'tipo' => $tipo,
            'fechaPublicacion' => date('Y-m-d H:i:s')
        ]);

        if (!$noticiaId) {
            echo json_encode([
                'estado' => 500,
                'exito' => false,
                'mensaje' => 'Error al crear la noticia en la base de datos',
                'resultado' => null
            ]);
            return;
        }

        // Procesar y guardar fotos
        if ($fotos) {
            $fotosGuardadas = LimpiarDatos::guardarMultiplesArchivos($fotos, 'foto', $noticiaId);
            
            if (!empty($fotosGuardadas)) {
                $guardado = NoticiasDao::guardarFotosNoticia($noticiaId, $fotosGuardadas);
                if (!$guardado) {
                    error_log("Error al guardar fotos en BD para noticia ID: " . $noticiaId);
                }
            }
        }

        // Obtener la noticia creada con sus fotos
        $noticiaCreada = NoticiasDao::obtenerNoticiaPorId($noticiaId);

        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Noticia creada exitosamente',
            'resultado' => $noticiaCreada ? $noticiaCreada->convertirAArray() : ['id' => $noticiaId]
        ]);
    }

    // Actualizar noticia
    private static function actualizarNoticia($parametros)
    {
        $id = $parametros['id'] ?? null;
        
        if (!$id) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'ID de noticia no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        // Verificar que la noticia existe
        $noticiaExistente = NoticiasDao::obtenerNoticiaPorId($id);
        if (!$noticiaExistente) {
            echo json_encode([
                'estado' => 404,
                'exito' => false,
                'mensaje' => 'Noticia no encontrada',
                'resultado' => null
            ]);
            return;
        }

        // Preparar datos a actualizar
        $asunto = $parametros['asunto'] ?? $parametros['titulo'] ?? $noticiaExistente->asunto;
        $descripcion = $parametros['descripcion'] ?? $parametros['contenido'] ?? $noticiaExistente->descripcion;
        $tipo = $parametros['tipo'] ?? $noticiaExistente->tipo;

        if (empty($asunto) || empty($descripcion)) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'Asunto y descripción son obligatorios',
                'resultado' => null
            ]);
            return;
        }

        // Obtener nuevas fotos si existen
        $fotos = $parametros['fotos'] ?? null;

        // DEBUG: Registrar qué estamos recibiendo
        if ($fotos) {
            error_log("Fotos recibidas en actualizarNoticia: " . print_r($fotos, true));
        }

        // Validar fotos si existen
        if ($fotos && !LimpiarDatos::validarMultiplesArchivos($fotos, 'foto')) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'Una o más fotos no son válidas',
                'resultado' => null
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
                'exito' => false,
                'mensaje' => 'Error al actualizar la noticia',
                'resultado' => null
            ]);
            return;
        }

        // Si hay nuevas fotos, reemplazar las existentes
        if ($fotos) {
            // Eliminar fotos antiguas (archivos físicos)
            $fotosAntiguas = NoticiasDao::obtenerFotosNoticia($id);
            foreach ($fotosAntiguas as $foto) {
                $rutaArchivo = __DIR__ . "/../../htdocs/guniversidadfrontend/public/img/" . $foto['url'];
                if (file_exists($rutaArchivo)) {
                    unlink($rutaArchivo);
                    error_log("Archivo eliminado: " . $rutaArchivo);
                }
            }
            
            // Eliminar registros de BD
            NoticiasDao::eliminarFotosNoticia($id);
            
            // Guardar nuevas fotos
            $fotosGuardadas = LimpiarDatos::guardarMultiplesArchivos($fotos, 'foto', $id);
            if (!empty($fotosGuardadas)) {
                $guardado = NoticiasDao::guardarFotosNoticia($id, $fotosGuardadas);
                if (!$guardado) {
                    error_log("Error al guardar fotos en BD para noticia ID: " . $id);
                }
            }
        }

        // Obtener noticia actualizada
        $noticiaActualizada = NoticiasDao::obtenerNoticiaPorId($id);

        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Noticia actualizada exitosamente',
            'resultado' => $noticiaActualizada->convertirAArray()
        ]);
    }

    // Eliminar noticia
    private static function eliminarNoticia($id)
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

        // Verificar que la noticia existe
        $noticiaExistente = NoticiasDao::obtenerNoticiaPorId($id);
        if (!$noticiaExistente) {
            echo json_encode([
                'estado' => 404,
                'exito' => false,
                'mensaje' => 'Noticia no encontrada',
                'resultado' => null
            ]);
            return;
        }

        // Eliminar archivos físicos antes de eliminar la noticia
        $fotos = NoticiasDao::obtenerFotosNoticia($id);
        foreach ($fotos as $foto) {
            $rutaArchivo = __DIR__ . "/../../htdocs/guniversidadfrontend/public/img/" . $foto['url'];
            if (file_exists($rutaArchivo)) {
                unlink($rutaArchivo);
                error_log("Archivo eliminado al eliminar noticia: " . $rutaArchivo);
            }
        }

        // Eliminar noticia (esto también eliminará los registros de fotos en BD por la FK)
        $eliminado = NoticiasDao::eliminarNoticia($id);

        if ($eliminado) {
            echo json_encode([
                'estado' => 'exito',
                'exito' => true,
                'mensaje' => 'Noticia eliminada exitosamente',
                'resultado' => ['id' => $id]
            ]);
        } else {
            echo json_encode([
                'estado' => 500,
                'exito' => false,
                'mensaje' => 'Error al eliminar la noticia',
                'resultado' => null
            ]);
        }
    }
}
?>