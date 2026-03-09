<?php
require_once __DIR__ . "/../dao/d_horario.php";
require_once __DIR__ . "/../../utilidades/u_verificaciones.php";
require_once __DIR__ . "/../modelo/m_horario.php";

class HorarioController
{
    public static function dispatch($accion, $parametros)
    {
        // Verificar que el actor sea secretario o admin
        if (!isset($parametros['actor']) || ($parametros['actor'] !== 'secretario' && $parametros['actor'] !== 'secretario')) {
            echo json_encode([
                'estado' => 403,
                'exito' => false,
                'mensaje' => 'Acceso denegado. Se requiere rol de secretario o administrador.',
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
            case "obtenerHorarios":
                self::obtenerHorarios();
                break;
                
            case "obtenerHorario":
                self::obtenerHorario($parametros['id'] ?? null);
                break;
                
            case "insertarHorario":
                self::insertarHorario($parametros);
                break;
                
            case "actualizarHorario":
                self::actualizarHorario($parametros);
                break;
                
            case "eliminarHorario":
                self::eliminarHorario($parametros['valor'] ?? $parametros['id'] ?? null);
                break;
                
            default:
                echo json_encode([
                    'estado' => 400,
                    'exito' => false,
                    'mensaje' => "Acción '$accion' no válida en controlador de horarios",
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

    // Obtener todos los horarios
    private static function obtenerHorarios()
    {
        $horarios = D_Horario::obtenerHorarios();
        $resultado = [];
        
        foreach ($horarios as $horario) {
            $resultado[] = $horario->convertirAArray();
        }
        
        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Horarios obtenidos correctamente',
            'resultado' => $resultado
        ]);
    }

    // Obtener horario por ID
    private static function obtenerHorario($id)
    {
        if (!$id) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'ID de horario no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        $horario = D_Horario::obtenerHorarioPorId($id);
        
        if ($horario) {
            echo json_encode([
                'estado' => 'exito',
                'exito' => true,
                'mensaje' => 'Horario obtenido correctamente',
                'resultado' => $horario->convertirAArray()
            ]);
        } else {
            echo json_encode([
                'estado' => 404,
                'exito' => false,
                'mensaje' => 'Horario no encontrado',
                'resultado' => null
            ]);
        }
    }

    // Insertar horario
    private static function insertarHorario($parametros)
    {
        // Validar campos obligatorios
        $nombre = $parametros['nombre'] ?? '';
        
        if (empty($nombre)) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'Nombre del horario es obligatorio',
                'resultado' => null
            ]);
            return;
        }

        // Verificar si ya existe
        if (D_Horario::existeHorarioPorNombre($nombre)) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'Ya existe un horario con ese nombre',
                'resultado' => null
            ]);
            return;
        }

        // Insertar
        $horarioId = D_Horario::insertarHorario(['nombre' => $nombre]);

        if (!$horarioId) {
            echo json_encode([
                'estado' => 500,
                'exito' => false,
                'mensaje' => 'Error al crear el horario',
                'resultado' => null
            ]);
            return;
        }

        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Horario creado exitosamente',
            'resultado' => ['id' => $horarioId, 'nombre' => $nombre]
        ]);
    }

    // Actualizar horario
    private static function actualizarHorario($parametros)
    {
        $id = $parametros['id'] ?? null;
        
        if (!$id) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'ID de horario no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        // Verificar que existe
        $horarioExistente = D_Horario::obtenerHorarioPorId($id);
        if (!$horarioExistente) {
            echo json_encode([
                'estado' => 404,
                'exito' => false,
                'mensaje' => 'Horario no encontrado',
                'resultado' => null
            ]);
            return;
        }

        // Validar nombre
        $nombre = $parametros['nombre'] ?? $horarioExistente->nombre;
        
        if (empty($nombre)) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'Nombre del horario es obligatorio',
                'resultado' => null
            ]);
            return;
        }

        // Verificar si ya existe otro con el mismo nombre
        if ($nombre != $horarioExistente->nombre && D_Horario::existeHorarioPorNombre($nombre, $id)) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'Ya existe otro horario con ese nombre',
                'resultado' => null
            ]);
            return;
        }

        // Actualizar
        $actualizado = D_Horario::actualizarHorario($id, ['nombre' => $nombre]);

        if ($actualizado) {
            echo json_encode([
                'estado' => 'exito',
                'exito' => true,
                'mensaje' => 'Horario actualizado exitosamente',
                'resultado' => ['id' => $id, 'nombre' => $nombre]
            ]);
        } else {
            echo json_encode([
                'estado' => 500,
                'exito' => false,
                'mensaje' => 'Error al actualizar el horario',
                'resultado' => null
            ]);
        }
    }

    // Eliminar horario
    private static function eliminarHorario($id)
    {
        if (!$id) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'ID de horario no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        // Verificar que existe
        $horarioExistente = D_Horario::obtenerHorarioPorId($id);
        if (!$horarioExistente) {
            echo json_encode([
                'estado' => 404,
                'exito' => false,
                'mensaje' => 'Horario no encontrado',
                'resultado' => null
            ]);
            return;
        }

        // Eliminar
        $eliminado = D_Horario::eliminarHorario($id);

        if ($eliminado) {
            echo json_encode([
                'estado' => 'exito',
                'exito' => true,
                'mensaje' => 'Horario eliminado exitosamente',
                'resultado' => ['id' => $id]
            ]);
        } else {
            echo json_encode([
                'estado' => 500,
                'exito' => false,
                'mensaje' => 'Error al eliminar el horario',
                'resultado' => null
            ]);
        }
    }
}
?>