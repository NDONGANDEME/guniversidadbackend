<?php
require_once __DIR__ . "/../dao/d_noticias.php";
require_once __DIR__ . "/../utilidades/LimpiarDatos.php";
require_once __DIR__ . "/../utilidades/u_verificaciones.php";

class NoticiasController
{
    public static function dispatch($accion, $parametros)
    {
        if (VerificacionesUtil::validarDispatch($accion, $parametros)) {
            // Verificar sesión activa para todas las acciones CRUD
            if (!self::verificarSesionActiva()) {
                echo json_encode([
                    'estado' => 401,
                    'éxito' => false,
                    'mensaje' => 'No hay sesión activa'
                ]);
                return;
            }

            switch ($accion) {
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
                        'mensaje' => "Acción '$accion' no válida en CRUD de noticias"
                    ]);
            }
        }
    }

    /**
     * VERIFICAR SI HAY UNA SESIÓN ACTIVA
     */
    private static function verificarSesionActiva()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['usuario_correo'])) {
            return false;
        }
        
        return true;
    }

    /**
     * Crear noticia
     */
    private static function crearNoticia($parametros)
    {
        // Validar campos obligatorios
        $asunto = $parametros['asunto'] ?? $parametros['titulo'] ?? '';
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
                'éxito' => false,
                'mensaje' => 'Error al crear la noticia en la base de datos'
            ]);
            return;
        }

        // Procesar y guardar fotos
        if ($fotos) {
            $fotosGuardadas = LimpiarDatos::guardarMultiplesArchivos($fotos, 'foto', $noticiaId);
            
            if (!empty($fotosGuardadas)) {
                NoticiasDao::guardarFotosNoticia($noticiaId, $fotosGuardadas);
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

    /**
     * Actualizar noticia
     */
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

    /**
     * Eliminar noticia
     */
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

        // Eliminar noticia
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