<?php
require_once __DIR__ . "/../dao/d_clase.php";
require_once __DIR__ . "/../dao/d_clase_horario.php";
require_once __DIR__ . "/../dao/d_plan_semestre_asignatura.php";
require_once __DIR__ . "/../dao/d_aula.php";
require_once __DIR__ . "/../dao/d_profesor.php";
require_once __DIR__ . "/../dao/d_horario.php";
require_once __DIR__ . "/../../utilidades/u_verificaciones.php";
require_once __DIR__ . "/../modelo/m_clase.php";

class ClaseController
{
    public static function dispatch($accion, $parametros)
    {
        // Verificar que el actor sea secretario o admin
        if (!isset($parametros['actor']) || ($parametros['actor'] !== 'secretario')) {
            echo json_encode([
                'estado' => 403,
                'exito' => false,
                'mensaje' => 'Acceso denegado. Se requiere rol de secretario.',
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
            // Acciones de Clase
            case "obtenerClases":
                self::obtenerClases();
                break;
                
            case "obtenerClasePorId":
                self::obtenerClasePorId($parametros['id'] ?? null);
                break;
                
            case "insertarClase":
                self::insertarClase($parametros);
                break;
                
            case "actualizarClase":
                self::actualizarClase($parametros);
                break;
                
            case "eliminarClase":
                self::eliminarClase($parametros['valor'] ?? $parametros['id'] ?? null);
                break;
                
            // Acciones de ClaseHorario
            case "obtenerClaseHorarios":
                self::obtenerClaseHorarios();
                break;
                
            case "obtenerClaseHorarioPorId":
                self::obtenerClaseHorarioPorId($parametros['id'] ?? null);
                break;
                
            case "obtenerHorariosPorClase":
                self::obtenerHorariosPorClase($parametros['idClase'] ?? null);
                break;
                
            case "insertarClaseHorario":
                self::insertarClaseHorario($parametros);
                break;
                
            case "actualizarClaseHorario":
                self::actualizarClaseHorario($parametros);
                break;
                
            case "eliminarClaseHorario":
                self::eliminarClaseHorario($parametros['valor'] ?? $parametros['id'] ?? null);
                break;
                
            default:
                echo json_encode([
                    'estado' => 400,
                    'exito' => false,
                    'mensaje' => "Acción '$accion' no válida en controlador de clases",
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

    // ============= MÉTODOS PARA CLASE =============

    // Obtener todas las clases
    private static function obtenerClases()
    {
        $clases = D_Clase::obtenerClases();
        $resultado = [];
        
        foreach ($clases as $clase) {
            $resultado[] = $clase->convertirAArray();
        }
        
        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Clases obtenidas correctamente',
            'resultado' => $resultado
        ]);
    }

    // Obtener clase por ID
    private static function obtenerClasePorId($id)
    {
        if (!$id) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'ID de clase no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        $clase = D_Clase::obtenerClasePorId($id);
        
        if ($clase) {
            echo json_encode([
                'estado' => 'exito',
                'exito' => true,
                'mensaje' => 'Clase obtenida correctamente',
                'resultado' => $clase->convertirAArray()
            ]);
        } else {
            echo json_encode([
                'estado' => 404,
                'exito' => false,
                'mensaje' => 'Clase no encontrada',
                'resultado' => null
            ]);
        }
    }

    // Insertar clase
    private static function insertarClase($parametros)
    {
        // Validar campos obligatorios
        $idPlanCursoAsignatura = $parametros['idPlanCursoAsignatura'] ?? '';
        $idAula = $parametros['idAula'] ?? '';
        $idProfesor = $parametros['idProfesor'] ?? '';
        $diaSemanal = $parametros['diaSemanal'] ?? '';
        $horaInicio = $parametros['horaInicio'] ?? '';
        $horaFinal = $parametros['horaFinal'] ?? '';
        
        if (empty($idPlanCursoAsignatura) || empty($idAula) || empty($idProfesor) || 
            empty($diaSemanal) || empty($horaInicio) || empty($horaFinal)) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'Todos los campos son obligatorios',
                'resultado' => null
            ]);
            return;
        }

        // Validar día de la semana
        $diasPermitidos = ['Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes'];
        if (!in_array($diaSemanal, $diasPermitidos)) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'Día semanal no válido',
                'resultado' => null
            ]);
            return;
        }

        // Validar horas
        if ($horaInicio >= $horaFinal) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'La hora de inicio debe ser menor a la hora final',
                'resultado' => null
            ]);
            return;
        }

        // Validar que existan los IDs
        $planSemestre = D_PlanSemestreAsignatura::obtenerPlanSemestreAsignaturaPorId($idPlanCursoAsignatura);
        if (!$planSemestre) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'La asignatura en el plan de estudios no existe',
                'resultado' => null
            ]);
            return;
        }

        $aula = D_Aula::obtenerAulaPorId($idAula);
        if (!$aula) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'El aula no existe',
                'resultado' => null
            ]);
            return;
        }

        $profesor = D_Profesor::obtenerProfesorPorId($idProfesor);
        if (!$profesor) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'El profesor no existe',
                'resultado' => null
            ]);
            return;
        }

        // Verificar disponibilidad
        $ocupado = D_Clase::verificarDisponibilidad($idAula, $idProfesor, $diaSemanal, $horaInicio, $horaFinal);
        if ($ocupado) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'El aula o el profesor no están disponibles en ese horario',
                'resultado' => null
            ]);
            return;
        }

        // Preparar datos
        $datos = [
            'idPlanCursoAsignatura' => $idPlanCursoAsignatura,
            'idAula' => $idAula,
            'idProfesor' => $idProfesor,
            'diaSemanal' => $diaSemanal,
            'horaInicio' => $horaInicio,
            'horaFinal' => $horaFinal,
            'tipoSesion' => $parametros['tipoSesion'] ?? '',
            'observaciones' => $parametros['observaciones'] ?? ''
        ];

        // Insertar
        $claseId = D_Clase::insertarClase($datos);

        if (!$claseId) {
            echo json_encode([
                'estado' => 500,
                'exito' => false,
                'mensaje' => 'Error al crear la clase',
                'resultado' => null
            ]);
            return;
        }

        // Obtener la clase creada
        $nuevaClase = D_Clase::obtenerClasePorId($claseId);

        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Clase creada exitosamente',
            'resultado' => $nuevaClase ? $nuevaClase->convertirAArray() : ['id' => $claseId]
        ]);
    }

    // Actualizar clase
    private static function actualizarClase($parametros)
    {
        $id = $parametros['idClase'] ?? null;
        
        if (!$id) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'ID de clase no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        // Verificar que existe
        $claseExistente = D_Clase::obtenerClasePorId($id);
        if (!$claseExistente) {
            echo json_encode([
                'estado' => 404,
                'exito' => false,
                'mensaje' => 'Clase no encontrada',
                'resultado' => null
            ]);
            return;
        }

        // Validar día de la semana si se proporciona
        if (isset($parametros['diaSemanal'])) {
            $diasPermitidos = ['Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes'];
            if (!in_array($parametros['diaSemanal'], $diasPermitidos)) {
                echo json_encode([
                    'estado' => 400,
                    'exito' => false,
                    'mensaje' => 'Día semanal no válido',
                    'resultado' => null
                ]);
                return;
            }
        }

        // Validar horas
        $horaInicio = $parametros['horaInicio'] ?? $claseExistente->horaInicio;
        $horaFinal = $parametros['horaFinal'] ?? $claseExistente->horaFinal;
        
        if ($horaInicio >= $horaFinal) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'La hora de inicio debe ser menor a la hora final',
                'resultado' => null
            ]);
            return;
        }

        // Verificar disponibilidad si cambió algún dato relevante
        $idAula = $parametros['idAula'] ?? $claseExistente->idAula;
        $idProfesor = $parametros['idProfesor'] ?? $claseExistente->idProfesor;
        $diaSemanal = $parametros['diaSemanal'] ?? $claseExistente->diaSemanal;

        if ($idAula != $claseExistente->idAula || $idProfesor != $claseExistente->idProfesor || 
            $diaSemanal != $claseExistente->diaSemanal || $horaInicio != $claseExistente->horaInicio || 
            $horaFinal != $claseExistente->horaFinal) {
            
            $ocupado = D_Clase::verificarDisponibilidad($idAula, $idProfesor, $diaSemanal, $horaInicio, $horaFinal, $id);
            if ($ocupado) {
                echo json_encode([
                    'estado' => 400,
                    'exito' => false,
                    'mensaje' => 'El aula o el profesor no están disponibles en ese horario',
                    'resultado' => null
                ]);
                return;
            }
        }

        // Preparar datos
        $datos = [
            'idPlanCursoAsignatura' => $parametros['idPlanCursoAsignatura'] ?? $claseExistente->idPlanCursoAsignatura,
            'idAula' => $idAula,
            'idProfesor' => $idProfesor,
            'diaSemanal' => $diaSemanal,
            'horaInicio' => $horaInicio,
            'horaFinal' => $horaFinal,
            'tipoSesion' => $parametros['tipoSesion'] ?? $claseExistente->tipoSesion,
            'observaciones' => $parametros['observaciones'] ?? $claseExistente->observaciones
        ];

        // Actualizar
        $actualizado = D_Clase::actualizarClase($id, $datos);

        if (!$actualizado) {
            echo json_encode([
                'estado' => 500,
                'exito' => false,
                'mensaje' => 'Error al actualizar la clase',
                'resultado' => null
            ]);
            return;
        }

        // Obtener la clase actualizada
        $claseActualizada = D_Clase::obtenerClasePorId($id);

        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Clase actualizada exitosamente',
            'resultado' => $claseActualizada->convertirAArray()
        ]);
    }

    // Eliminar clase
    private static function eliminarClase($id)
    {
        if (!$id) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'ID de clase no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        // Verificar que existe
        $claseExistente = D_Clase::obtenerClasePorId($id);
        if (!$claseExistente) {
            echo json_encode([
                'estado' => 404,
                'exito' => false,
                'mensaje' => 'Clase no encontrada',
                'resultado' => null
            ]);
            return;
        }

        // Eliminar
        $eliminado = D_Clase::eliminarClase($id);

        if ($eliminado) {
            echo json_encode([
                'estado' => 'exito',
                'exito' => true,
                'mensaje' => 'Clase eliminada exitosamente',
                'resultado' => ['id' => $id]
            ]);
        } else {
            echo json_encode([
                'estado' => 500,
                'exito' => false,
                'mensaje' => 'Error al eliminar la clase',
                'resultado' => null
            ]);
        }
    }

    // ============= MÉTODOS PARA CLASE HORARIO =============

    // Obtener todos los clase horarios
    private static function obtenerClaseHorarios()
    {
        $registros = D_ClaseHorario::obtenerClaseHorarios();
        $resultado = [];
        
        foreach ($registros as $registro) {
            $resultado[] = $registro->convertirAArray();
        }
        
        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Asignaciones de horarios obtenidas correctamente',
            'resultado' => $resultado
        ]);
    }

    // Obtener clase horario por ID
    private static function obtenerClaseHorarioPorId($id)
    {
        if (!$id) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'ID de asignación no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        $registro = D_ClaseHorario::obtenerClaseHorarioPorId($id);
        
        if ($registro) {
            echo json_encode([
                'estado' => 'exito',
                'exito' => true,
                'mensaje' => 'Asignación obtenida correctamente',
                'resultado' => $registro->convertirAArray()
            ]);
        } else {
            echo json_encode([
                'estado' => 404,
                'exito' => false,
                'mensaje' => 'Asignación no encontrada',
                'resultado' => null
            ]);
        }
    }

    // Obtener horarios por clase
    private static function obtenerHorariosPorClase($idClase)
    {
        if (!$idClase) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'ID de clase no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        $horarios = D_ClaseHorario::obtenerHorariosPorClase($idClase);
        $resultado = [];
        
        foreach ($horarios as $horario) {
            $resultado[] = $horario->convertirAArray();
        }
        
        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Horarios de la clase obtenidos correctamente',
            'resultado' => $resultado
        ]);
    }

    // Insertar clase horario
    private static function insertarClaseHorario($parametros)
    {
        // Validar campos obligatorios
        $idClase = $parametros['idClase'] ?? '';
        $idHorario = $parametros['idHorario'] ?? '';
        
        if (empty($idClase) || empty($idHorario)) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'Clase y horario son obligatorios',
                'resultado' => null
            ]);
            return;
        }

        // Validar que existan
        $clase = D_Clase::obtenerClasePorId($idClase);
        if (!$clase) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'La clase no existe',
                'resultado' => null
            ]);
            return;
        }

        $horario = D_Horario::obtenerHorarioPorId($idHorario);
        if (!$horario) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'El horario no existe',
                'resultado' => null
            ]);
            return;
        }

        // Verificar si ya existe la asignación
        if (D_ClaseHorario::existeAsignacion($idClase, $idHorario)) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'Esta clase ya tiene asignado ese horario',
                'resultado' => null
            ]);
            return;
        }

        // Preparar datos
        $datos = [
            'idClase' => $idClase,
            'idHorario' => $idHorario
        ];

        // Insertar
        $asignacionId = D_ClaseHorario::insertarClaseHorario($datos);

        if (!$asignacionId) {
            echo json_encode([
                'estado' => 500,
                'exito' => false,
                'mensaje' => 'Error al asignar el horario',
                'resultado' => null
            ]);
            return;
        }

        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Horario asignado exitosamente',
            'resultado' => ['id' => $asignacionId, 'idClase' => $idClase, 'idHorario' => $idHorario]
        ]);
    }

    // Actualizar clase horario
    private static function actualizarClaseHorario($parametros)
    {
        $id = $parametros['idClaseHorario'] ?? null;
        
        if (!$id) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'ID de asignación no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        // Verificar que existe
        $asignacionExistente = D_ClaseHorario::obtenerClaseHorarioPorId($id);
        if (!$asignacionExistente) {
            echo json_encode([
                'estado' => 404,
                'exito' => false,
                'mensaje' => 'Asignación no encontrada',
                'resultado' => null
            ]);
            return;
        }

        // Validar nuevo horario
        $idHorario = $parametros['idHorario'] ?? $asignacionExistente->idHorario;
        
        $horario = D_Horario::obtenerHorarioPorId($idHorario);
        if (!$horario) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'El horario no existe',
                'resultado' => null
            ]);
            return;
        }

        // Verificar si ya existe otra asignación igual
        if ($idHorario != $asignacionExistente->idHorario) {
            if (D_ClaseHorario::existeAsignacion($asignacionExistente->idClase, $idHorario, $id)) {
                echo json_encode([
                    'estado' => 400,
                    'exito' => false,
                    'mensaje' => 'La clase ya tiene asignado ese horario',
                    'resultado' => null
                ]);
                return;
            }
        }

        // Preparar datos
        $datos = [
            'idHorario' => $idHorario
        ];

        // Actualizar
        $actualizado = D_ClaseHorario::actualizarClaseHorario($id, $datos);

        if ($actualizado) {
            echo json_encode([
                'estado' => 'exito',
                'exito' => true,
                'mensaje' => 'Asignación actualizada exitosamente',
                'resultado' => ['id' => $id]
            ]);
        } else {
            echo json_encode([
                'estado' => 500,
                'exito' => false,
                'mensaje' => 'Error al actualizar la asignación',
                'resultado' => null
            ]);
        }
    }

    // Eliminar clase horario
    private static function eliminarClaseHorario($id)
    {
        if (!$id) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'ID de asignación no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        // Verificar que existe
        $asignacionExistente = D_ClaseHorario::obtenerClaseHorarioPorId($id);
        if (!$asignacionExistente) {
            echo json_encode([
                'estado' => 404,
                'exito' => false,
                'mensaje' => 'Asignación no encontrada',
                'resultado' => null
            ]);
            return;
        }

        // Eliminar
        $eliminado = D_ClaseHorario::eliminarClaseHorario($id);

        if ($eliminado) {
            echo json_encode([
                'estado' => 'exito',
                'exito' => true,
                'mensaje' => 'Asignación eliminada exitosamente',
                'resultado' => ['id' => $id]
            ]);
        } else {
            echo json_encode([
                'estado' => 500,
                'exito' => false,
                'mensaje' => 'Error al eliminar la asignación',
                'resultado' => null
            ]);
        }
    }
}
?>