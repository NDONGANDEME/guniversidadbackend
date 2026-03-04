<?php
require_once __DIR__ . "/../dao/d_estudiante_beca.php";
require_once __DIR__ . "/../../utilidades/u_verificaciones.php";
require_once __DIR__ . "/../modelo/m_estudiante_beca.php";

class EstudianteBecaController
{
    public static function dispatch($accion, $parametros)
    {
        // Verificar que el actor sea admin
        if (!isset($parametros['actor']) || $parametros['actor'] !== 'admin') {
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
            case "obtenerEstudiantesBeca":
                self::obtenerEstudiantesBeca();
                break;
                
            case "insertarEstudianteBecado":
                self::insertarEstudianteBecado($parametros);
                break;
                
            case "actualizarEstudianteBecado":
                self::actualizarEstudianteBecado($parametros);
                break;
                
            case "deshabilitarEstudianteBecado":
                self::cambiarEstadoEstudianteBecado($parametros['id'] ?? null, 'inactivo');
                break;
                
            case "habilitarEstudianteBecado":
                self::cambiarEstadoEstudianteBecado($parametros['id'] ?? null, 'activo');
                break;
                
            default:
                echo json_encode([
                    'estado' => 400,
                    'exito' => false,
                    'mensaje' => "Acción '$accion' no válida en controlador de estudiantes becados",
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

    // Obtener todos los registros de estudiantes becados
    private static function obtenerEstudiantesBeca()
    {
        $registros = D_EstudianteBeca::obtenerEstudiantesBeca();
        $resultado = [];
        
        foreach ($registros as $registro) {
            $arr = $registro->convertirAArray();
            if (isset($registro->nombreEstudiante)) {
                $arr['nombreEstudiante'] = $registro->nombreEstudiante;
            }
            if (isset($registro->institucionBeca)) {
                $arr['institucionBeca'] = $registro->institucionBeca;
                $arr['tipoBeca'] = $registro->tipoBeca;
            }
            $resultado[] = $arr;
        }
        
        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Registros de estudiantes becados obtenidos correctamente',
            'resultado' => $resultado
        ]);
    }

    // Insertar estudiante becado
    private static function insertarEstudianteBecado($parametros)
    {
        // Validar campos obligatorios
        $idEstudiante = $parametros['idEstudiante'] ?? '';
        $idBeca = $parametros['idBeca'] ?? '';
        $fechaInicio = $parametros['fechaInicio'] ?? date('Y-m-d');
        
        if (empty($idEstudiante) || empty($idBeca)) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'Estudiante y beca son obligatorios',
                'resultado' => null
            ]);
            return;
        }

        // Verificar si ya tiene una beca activa
        if (D_EstudianteBeca::existeBecaActivaParaEstudiante($idEstudiante)) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'El estudiante ya tiene una beca activa',
                'resultado' => null
            ]);
            return;
        }

        // Preparar datos
        $datos = [
            'idEstudiante' => $idEstudiante,
            'idBeca' => $idBeca,
            'fechaInicio' => $fechaInicio,
            'fechaFinal' => $parametros['fechaFinal'] ?? null,
            'estado' => $parametros['estado'] ?? 'activo',
            'observaciones' => $parametros['observaciones'] ?? ''
        ];

        // Insertar
        $registroId = D_EstudianteBeca::insertarEstudianteBecado($datos);

        if (!$registroId) {
            echo json_encode([
                'estado' => 500,
                'exito' => false,
                'mensaje' => 'Error al asignar la beca al estudiante',
                'resultado' => null
            ]);
            return;
        }

        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Beca asignada al estudiante exitosamente',
            'resultado' => ['id' => $registroId]
        ]);
    }

    // Actualizar estudiante becado
    private static function actualizarEstudianteBecado($parametros)
    {
        $id = $parametros['id'] ?? null;
        
        if (!$id) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'ID de registro no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        // Verificar que existe
        $registroExistente = D_EstudianteBeca::obtenerEstudianteBecaPorId($id);
        if (!$registroExistente) {
            echo json_encode([
                'estado' => 404,
                'exito' => false,
                'mensaje' => 'Registro no encontrado',
                'resultado' => null
            ]);
            return;
        }

        // Preparar datos
        $datos = [
            'idBeca' => $parametros['idBeca'] ?? $registroExistente->idBeca,
            'fechaInicio' => $parametros['fechaInicio'] ?? $registroExistente->fechaInicio,
            'fechaFinal' => $parametros['fechaFinal'] ?? $registroExistente->fechaFinal,
            'observaciones' => $parametros['observaciones'] ?? $registroExistente->observaciones
        ];

        // Actualizar
        $actualizado = D_EstudianteBeca::actualizarEstudianteBecado($id, $datos);

        if (!$actualizado) {
            echo json_encode([
                'estado' => 500,
                'exito' => false,
                'mensaje' => 'Error al actualizar el registro',
                'resultado' => null
            ]);
            return;
        }

        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Registro actualizado exitosamente',
            'resultado' => ['id' => $id]
        ]);
    }

    // Cambiar estado del estudiante becado
    private static function cambiarEstadoEstudianteBecado($id, $estado)
    {
        if (!$id) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'ID de registro no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        // Verificar que existe
        $registroExistente = D_EstudianteBeca::obtenerEstudianteBecaPorId($id);
        if (!$registroExistente) {
            echo json_encode([
                'estado' => 404,
                'exito' => false,
                'mensaje' => 'Registro no encontrado',
                'resultado' => null
            ]);
            return;
        }

        // Cambiar estado
        $cambiado = D_EstudianteBeca::cambiarEstadoEstudianteBecado($id, $estado);

        if ($cambiado) {
            $mensaje = $estado == 'activo' ? 'Beca habilitada para el estudiante' : 'Beca deshabilitada para el estudiante';
            echo json_encode([
                'estado' => 'exito',
                'exito' => true,
                'mensaje' => $mensaje,
                'resultado' => ['id' => $id, 'nuevoEstado' => $estado]
            ]);
        } else {
            echo json_encode([
                'estado' => 500,
                'exito' => false,
                'mensaje' => 'Error al cambiar el estado del registro',
                'resultado' => null
            ]);
        }
    }
}
?>