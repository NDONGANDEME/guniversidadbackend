<?php
require_once __DIR__ . "/../dao/d_matricula_asignatura.php";
require_once __DIR__ . "/../dao/d_matricula.php";
require_once __DIR__ . "/../dao/d_plan_semestre_asignatura.php";
require_once __DIR__ . "/../dao/d_asignatura.php";
require_once __DIR__ . "/../../utilidades/u_verificaciones.php";
require_once __DIR__ . "/../modelo/m_matricula_asignatura.php";

class MatriculaAsignaturaController
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
            case "obtenerMatriculasAsignaturas":
                self::obtenerMatriculasAsignaturas();
                break;
                
            case "obtenerMatriculasAsignaturasPorMatricula":
                self::obtenerMatriculasAsignaturasPorMatricula($parametros['idMatricula'] ?? null);
                break;
                
            case "obtenerMatriculaAsignaturaPorId":
                self::obtenerMatriculaAsignaturaPorId($parametros['id'] ?? null);
                break;
                
            case "insertarMatriculaAsignatura":
                self::insertarMatriculaAsignatura($parametros);
                break;
                
            case "actualizarMatriculaAsignatura":
                self::actualizarMatriculaAsignatura($parametros);
                break;
                
            case "registrarNota":
                self::registrarNota($parametros);
                break;
                
            case "cambiarEstadoMatriculaAsignatura":
                self::cambiarEstadoMatriculaAsignatura($parametros);
                break;
                
            case "eliminarMatriculaAsignatura":
                self::eliminarMatriculaAsignatura($parametros['id'] ?? null);
                break;
                
            default:
                echo json_encode([
                    'estado' => 400,
                    'exito' => false,
                    'mensaje' => "Acción '$accion' no válida en controlador de matrículas de asignaturas",
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

    // Obtener todas las matrículas de asignaturas
    private static function obtenerMatriculasAsignaturas()
    {
        $registros = D_MatriculaAsignatura::obtenerMatriculasAsignaturas();
        $resultado = [];
        
        foreach ($registros as $registro) {
            $arr = $registro->convertirAArray();
            if (isset($registro->nombreEstudiante)) {
                $arr['nombreEstudiante'] = $registro->nombreEstudiante;
                $arr['apellidosEstudiante'] = $registro->apellidosEstudiante ?? '';
                $arr['nombreCompletoEstudiante'] = trim(($registro->nombreEstudiante ?? '') . ' ' . ($registro->apellidosEstudiante ?? ''));
            }
            if (isset($registro->nombreAsignatura)) {
                $arr['codigoAsignatura'] = $registro->codigoAsignatura ?? '';
                $arr['nombreAsignatura'] = $registro->nombreAsignatura ?? '';
                $arr['creditos'] = $registro->creditos ?? 0;
            }
            $resultado[] = $arr;
        }
        
        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Matrículas de asignaturas obtenidas correctamente',
            'resultado' => $resultado
        ]);
    }

    // Obtener matrículas de asignaturas por matrícula
    private static function obtenerMatriculasAsignaturasPorMatricula($idMatricula)
    {
        if (!$idMatricula) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'ID de matrícula no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        // Verificar que la matrícula existe
        $matricula = D_Matricula::obtenerMatriculaPorId($idMatricula);
        if (!$matricula) {
            echo json_encode([
                'estado' => 404,
                'exito' => false,
                'mensaje' => 'Matrícula no encontrada',
                'resultado' => null
            ]);
            return;
        }

        $registros = D_MatriculaAsignatura::obtenerMatriculasAsignaturasPorMatricula($idMatricula);
        $resultado = [];
        
        foreach ($registros as $registro) {
            $arr = $registro->convertirAArray();
            if (isset($registro->nombreAsignatura)) {
                $arr['codigoAsignatura'] = $registro->codigoAsignatura ?? '';
                $arr['nombreAsignatura'] = $registro->nombreAsignatura ?? '';
                $arr['creditos'] = $registro->creditos ?? 0;
            }
            $resultado[] = $arr;
        }
        
        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Asignaturas de la matrícula obtenidas correctamente',
            'resultado' => $resultado
        ]);
    }

    // Obtener matrícula de asignatura por ID
    private static function obtenerMatriculaAsignaturaPorId($id)
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

        $registro = D_MatriculaAsignatura::obtenerMatriculaAsignaturaPorId($id);
        
        if ($registro) {
            // Obtener información adicional
            $matricula = D_Matricula::obtenerMatriculaPorId($registro->idMatricula);
            $planSemestreAsignatura = D_PlanSemestreAsignatura::obtenerPlanSemestreAsignaturaPorId($registro->idPlanCursoAsignatura);
            
            $resultado = $registro->convertirAArray();
            
            if ($matricula) {
                $resultado['idEstudiante'] = $matricula->idEstudiante ?? null;
            }
            
            if ($planSemestreAsignatura) {
                $asignatura = D_Asignatura::obtenerAsignaturaPorId($planSemestreAsignatura->idAsignatura);
                if ($asignatura) {
                    $resultado['codigoAsignatura'] = $asignatura->codigoAsignatura;
                    $resultado['nombreAsignatura'] = $asignatura->nombreAsignatura;
                    $resultado['creditos'] = $planSemestreAsignatura->creditos;
                }
            }
            
            echo json_encode([
                'estado' => 'exito',
                'exito' => true,
                'mensaje' => 'Registro obtenido correctamente',
                'resultado' => $resultado
            ]);
        } else {
            echo json_encode([
                'estado' => 404,
                'exito' => false,
                'mensaje' => 'Registro no encontrado',
                'resultado' => null
            ]);
        }
    }

    // Insertar matrícula de asignatura
    private static function insertarMatriculaAsignatura($parametros)
    {
        // Validar campos obligatorios
        $idMatricula = $parametros['idMatricula'] ?? '';
        $idPlanCursoAsignatura = $parametros['idPlanCursoAsignatura'] ?? '';
        
        if (empty($idMatricula) || empty($idPlanCursoAsignatura)) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'Matrícula y asignatura son obligatorias',
                'resultado' => null
            ]);
            return;
        }

        // Verificar que la matrícula existe
        $matricula = D_Matricula::obtenerMatriculaPorId($idMatricula);
        if (!$matricula) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'La matrícula no existe',
                'resultado' => null
            ]);
            return;
        }

        // Verificar que el plan semestre asignatura existe
        $planSemestreAsignatura = D_PlanSemestreAsignatura::obtenerPlanSemestreAsignaturaPorId($idPlanCursoAsignatura);
        if (!$planSemestreAsignatura) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'La asignatura en el plan de estudios no existe',
                'resultado' => null
            ]);
            return;
        }

        // Verificar si ya está matriculada
        if (D_MatriculaAsignatura::existeAsignaturaMatriculada($idMatricula, $idPlanCursoAsignatura)) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'La asignatura ya está matriculada',
                'resultado' => null
            ]);
            return;
        }

        // Obtener número de veces matriculado (histórico)
        $vecesMatriculado = 1;
        $asignaturasExistentes = D_MatriculaAsignatura::obtenerMatriculasAsignaturasPorMatricula($idMatricula);
        foreach ($asignaturasExistentes as $asignatura) {
            if ($asignatura->idPlanCursoAsignatura == $idPlanCursoAsignatura) {
                $vecesMatriculado = $asignatura->numeroVecesMatriculado + 1;
                break;
            }
        }

        // Preparar datos
        $datos = [
            'idMatricula' => $idMatricula,
            'idPlanCursoAsignatura' => $idPlanCursoAsignatura,
            'convocatoria' => $parametros['convocatoria'] ?? 1,
            'notaFinal' => $parametros['notaFinal'] ?? null,
            'estado' => $parametros['estado'] ?? 'matriculado',
            'numeroVecesMatriculado' => $vecesMatriculado
        ];

        // Insertar
        $registroId = D_MatriculaAsignatura::insertarMatriculaAsignatura($datos);

        if (!$registroId) {
            echo json_encode([
                'estado' => 500,
                'exito' => false,
                'mensaje' => 'Error al matricular la asignatura',
                'resultado' => null
            ]);
            return;
        }

        // Obtener el registro creado
        $nuevoRegistro = D_MatriculaAsignatura::obtenerMatriculaAsignaturaPorId($registroId);
        $asignatura = D_Asignatura::obtenerAsignaturaPorId($planSemestreAsignatura->idAsignatura);
        
        $resultado = $nuevoRegistro->convertirAArray();
        if ($asignatura) {
            $resultado['codigoAsignatura'] = $asignatura->codigoAsignatura;
            $resultado['nombreAsignatura'] = $asignatura->nombreAsignatura;
            $resultado['creditos'] = $planSemestreAsignatura->creditos;
        }

        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Asignatura matriculada exitosamente',
            'resultado' => $resultado
        ]);
    }

    // Actualizar matrícula de asignatura
    private static function actualizarMatriculaAsignatura($parametros)
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
        $registroExistente = D_MatriculaAsignatura::obtenerMatriculaAsignaturaPorId($id);
        if (!$registroExistente) {
            echo json_encode([
                'estado' => 404,
                'exito' => false,
                'mensaje' => 'Registro no encontrado',
                'resultado' => null
            ]);
            return;
        }

        // Validar convocatoria si se proporciona
        $convocatoria = $parametros['convocatoria'] ?? $registroExistente->convocatoria;
        if (!is_numeric($convocatoria) || $convocatoria < 1 || $convocatoria > 3) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'La convocatoria debe ser un número entre 1 y 3',
                'resultado' => null
            ]);
            return;
        }

        // Validar nota si se proporciona
        if (isset($parametros['notaFinal']) && $parametros['notaFinal'] !== null) {
            $nota = floatval($parametros['notaFinal']);
            if ($nota < 0 || $nota > 10) {
                echo json_encode([
                    'estado' => 400,
                    'exito' => false,
                    'mensaje' => 'La nota debe estar entre 0 y 10',
                    'resultado' => null
                ]);
                return;
            }
        }

        // Preparar datos
        $datos = [
            'convocatoria' => $convocatoria,
            'notaFinal' => $parametros['notaFinal'] ?? $registroExistente->notaFinal,
            'estado' => $parametros['estado'] ?? $registroExistente->estado,
            'numeroVecesMatriculado' => $parametros['numeroVecesMatriculado'] ?? $registroExistente->numeroVecesMatriculado
        ];

        // Actualizar
        $actualizado = D_MatriculaAsignatura::actualizarMatriculaAsignatura($id, $datos);

        if (!$actualizado) {
            echo json_encode([
                'estado' => 500,
                'exito' => false,
                'mensaje' => 'Error al actualizar la matrícula de asignatura',
                'resultado' => null
            ]);
            return;
        }

        // Obtener el registro actualizado
        $registroActualizado = D_MatriculaAsignatura::obtenerMatriculaAsignaturaPorId($id);
        $planSemestreAsignatura = D_PlanSemestreAsignatura::obtenerPlanSemestreAsignaturaPorId($registroActualizado->idPlanCursoAsignatura);
        
        $resultado = $registroActualizado->convertirAArray();
        if ($planSemestreAsignatura) {
            $asignatura = D_Asignatura::obtenerAsignaturaPorId($planSemestreAsignatura->idAsignatura);
            if ($asignatura) {
                $resultado['codigoAsignatura'] = $asignatura->codigoAsignatura;
                $resultado['nombreAsignatura'] = $asignatura->nombreAsignatura;
                $resultado['creditos'] = $planSemestreAsignatura->creditos;
            }
        }

        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Matrícula de asignatura actualizada',
            'resultado' => $resultado
        ]);
    }

    // Registrar nota
    private static function registrarNota($parametros)
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

        if (!isset($parametros['nota'])) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'Nota no proporcionada',
                'resultado' => null
            ]);
            return;
        }

        $nota = floatval($parametros['nota']);
        if ($nota < 0 || $nota > 10) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'La nota debe estar entre 0 y 10',
                'resultado' => null
            ]);
            return;
        }

        // Verificar que existe
        $registroExistente = D_MatriculaAsignatura::obtenerMatriculaAsignaturaPorId($id);
        if (!$registroExistente) {
            echo json_encode([
                'estado' => 404,
                'exito' => false,
                'mensaje' => 'Registro no encontrado',
                'resultado' => null
            ]);
            return;
        }

        // Registrar nota
        $registrado = D_MatriculaAsignatura::registrarNota($id, $nota);

        if ($registrado) {
            // Obtener el registro actualizado
            $registroActualizado = D_MatriculaAsignatura::obtenerMatriculaAsignaturaPorId($id);
            $planSemestreAsignatura = D_PlanSemestreAsignatura::obtenerPlanSemestreAsignaturaPorId($registroActualizado->idPlanCursoAsignatura);
            
            $resultado = $registroActualizado->convertirAArray();
            if ($planSemestreAsignatura) {
                $asignatura = D_Asignatura::obtenerAsignaturaPorId($planSemestreAsignatura->idAsignatura);
                if ($asignatura) {
                    $resultado['codigoAsignatura'] = $asignatura->codigoAsignatura;
                    $resultado['nombreAsignatura'] = $asignatura->nombreAsignatura;
                }
            }
            
            echo json_encode([
                'estado' => 'exito',
                'exito' => true,
                'mensaje' => 'Nota registrada correctamente',
                'resultado' => $resultado
            ]);
        } else {
            echo json_encode([
                'estado' => 500,
                'exito' => false,
                'mensaje' => 'Error al registrar la nota',
                'resultado' => null
            ]);
        }
    }

    // Cambiar estado de matrícula de asignatura
    private static function cambiarEstadoMatriculaAsignatura($parametros)
    {
        $id = $parametros['id'] ?? null;
        $estado = $parametros['estado'] ?? '';
        
        if (!$id) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'ID de registro no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        if (empty($estado)) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'Estado no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        $estadosPermitidos = ['matriculado', 'aprobada', 'reprobada', 'anulada'];
        if (!in_array($estado, $estadosPermitidos)) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'Estado no válido. Los estados permitidos son: ' . implode(', ', $estadosPermitidos),
                'resultado' => null
            ]);
            return;
        }

        // Verificar que existe
        $registroExistente = D_MatriculaAsignatura::obtenerMatriculaAsignaturaPorId($id);
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
        $cambiado = D_MatriculaAsignatura::cambiarEstado($id, $estado);

        if ($cambiado) {
            echo json_encode([
                'estado' => 'exito',
                'exito' => true,
                'mensaje' => 'Estado actualizado correctamente',
                'resultado' => ['id' => $id, 'nuevoEstado' => $estado]
            ]);
        } else {
            echo json_encode([
                'estado' => 500,
                'exito' => false,
                'mensaje' => 'Error al cambiar el estado',
                'resultado' => null
            ]);
        }
    }

    // Eliminar matrícula de asignatura
    private static function eliminarMatriculaAsignatura($id)
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
        $registroExistente = D_MatriculaAsignatura::obtenerMatriculaAsignaturaPorId($id);
        if (!$registroExistente) {
            echo json_encode([
                'estado' => 404,
                'exito' => false,
                'mensaje' => 'Registro no encontrado',
                'resultado' => null
            ]);
            return;
        }

        // Para no perder el historial académico, mejor cambiar estado a 'anulada'
        // en lugar de eliminar físicamente
        $anulado = D_MatriculaAsignatura::cambiarEstado($id, 'anulada');

        if ($anulado) {
            echo json_encode([
                'estado' => 'exito',
                'exito' => true,
                'mensaje' => 'Matrícula de asignatura anulada correctamente',
                'resultado' => ['id' => $id]
            ]);
        } else {
            echo json_encode([
                'estado' => 500,
                'exito' => false,
                'mensaje' => 'Error al anular la matrícula de asignatura',
                'resultado' => null
            ]);
        }
    }
}
?>