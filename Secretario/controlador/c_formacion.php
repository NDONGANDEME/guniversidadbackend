<?php
require_once __DIR__ . "/../dao/d_formacion.php";
require_once __DIR__ . "/../../utilidades/u_verificaciones.php";
require_once __DIR__ . "/../modelo/m_formacion.php";

class FormacionController
{
    public static function dispatch($accion, $parametros)
    {
        // Verificar que el actor sea admin
        if (!isset($parametros['actor']) || $parametros['actor'] !== 'secretario') {
            echo json_encode([
                'estado' => 403,
                'exito' => false,
                'mensaje' => 'Acceso denegado. Se requiere rol de administrador.',
                'resultado' => null
            ]);
            return;
        }

        // Verificar sesión activa
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
            case "obtenerFormaciones":
                self::obtenerFormaciones();
                break;
                
            case "obtenerFormacionesPorProfesor":
                self::obtenerFormacionesPorProfesor($parametros['idProfesor'] ?? null);
                break;
                
            case "insertarFormacion":
                self::insertarFormacion($parametros);
                break;
                
            case "actualizarFormacion":
                self::actualizarFormacion($parametros);
                break;
                
            case "eliminarFormacion":
                self::eliminarFormacion($parametros['id'] ?? null);
                break;
                
            default:
                echo json_encode([
                    'estado' => 400,
                    'exito' => false,
                    'mensaje' => "Acción '$accion' no válida en controlador de formación",
                    'resultado' => null
                ]);
        }
    }

    // Verificar sesión activa
    private static function verificarSesionActiva()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['usuario_id']) && isset($_SESSION['usuario_correo']);
    }

    // Obtener todas las formaciones
    private static function obtenerFormaciones()
    {
        $formaciones = D_Formacion::obtenerFormaciones();
        $resultado = [];
        
        foreach ($formaciones as $formacion) {
            $arr = $formacion->convertirAArray();
            if (isset($formacion->nombreProfesor)) {
                $arr['nombreProfesor'] = $formacion->nombreProfesor;
            }
            $resultado[] = $arr;
        }
        
        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Formaciones obtenidas correctamente',
            'resultado' => $resultado
        ]);
    }

    // Obtener formaciones por profesor
    private static function obtenerFormacionesPorProfesor($idProfesor)
    {
        if (!$idProfesor) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'ID de profesor no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        $formaciones = D_Formacion::obtenerFormacionesPorProfesor($idProfesor);
        $resultado = [];
        
        foreach ($formaciones as $formacion) {
            $resultado[] = $formacion->convertirAArray();
        }
        
        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Formaciones del profesor obtenidas correctamente',
            'resultado' => $resultado
        ]);
    }

    // Insertar formación
    private static function insertarFormacion($parametros)
    {
        // Validar campos obligatorios
        $institucion = $parametros['institucion'] ?? '';
        $titulo = $parametros['titulo'] ?? '';
        $idProfesor = $parametros['idProfesor'] ?? '';
        
        if (empty($institucion) || empty($titulo) || empty($idProfesor)) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'Institución, título y profesor son obligatorios',
                'resultado' => null
            ]);
            return;
        }

        // Preparar datos
        $datos = [
            'institucion' => $institucion,
            'tipoFormacion' => $parametros['tipoFormacion'] ?? '',
            'titulo' => $titulo,
            'nivel' => $parametros['nivel'] ?? '',
            'idProfesor' => $idProfesor
        ];

        // Insertar
        $formacionId = D_Formacion::insertarFormacion($datos);

        if (!$formacionId) {
            echo json_encode([
                'estado' => 500,
                'exito' => false,
                'mensaje' => 'Error al crear la formación',
                'resultado' => null
            ]);
            return;
        }

        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Formación creada exitosamente',
            'resultado' => ['id' => $formacionId]
        ]);
    }

    // Actualizar formación
    private static function actualizarFormacion($parametros)
    {
        $id = $parametros['id'] ?? null;
        
        if (!$id) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'ID de formación no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        // Verificar que existe
        $formacionExistente = D_Formacion::obtenerFormacionPorId($id);
        if (!$formacionExistente) {
            echo json_encode([
                'estado' => 404,
                'exito' => false,
                'mensaje' => 'Formación no encontrada',
                'resultado' => null
            ]);
            return;
        }

        // Preparar datos
        $datos = [
            'institucion' => $parametros['institucion'] ?? $formacionExistente->institucion,
            'tipoFormacion' => $parametros['tipoFormacion'] ?? $formacionExistente->tipoFormacion,
            'titulo' => $parametros['titulo'] ?? $formacionExistente->titulo,
            'nivel' => $parametros['nivel'] ?? $formacionExistente->nivel
        ];

        // Actualizar
        $actualizado = D_Formacion::actualizarFormacion($id, $datos);

        if (!$actualizado) {
            echo json_encode([
                'estado' => 500,
                'exito' => false,
                'mensaje' => 'Error al actualizar la formación',
                'resultado' => null
            ]);
            return;
        }

        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Formación actualizada exitosamente',
            'resultado' => ['id' => $id]
        ]);
    }

    // Eliminar formación
    private static function eliminarFormacion($id)
    {
        if (!$id) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'ID de formación no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        // Verificar que existe
        $formacionExistente = D_Formacion::obtenerFormacionPorId($id);
        if (!$formacionExistente) {
            echo json_encode([
                'estado' => 404,
                'exito' => false,
                'mensaje' => 'Formación no encontrada',
                'resultado' => null
            ]);
            return;
        }

        // Eliminar
        $eliminado = D_Formacion::eliminarFormacion($id);

        if ($eliminado) {
            echo json_encode([
                'estado' => 'exito',
                'exito' => true,
                'mensaje' => 'Formación eliminada exitosamente',
                'resultado' => ['id' => $id]
            ]);
        } else {
            echo json_encode([
                'estado' => 500,
                'exito' => false,
                'mensaje' => 'Error al eliminar la formación',
                'resultado' => null
            ]);
        }
    }
}
?>