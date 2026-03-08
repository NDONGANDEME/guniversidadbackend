<?php
require_once __DIR__ . "/../dao/d_matricula.php";
require_once __DIR__ . "/../dao/d_matriculaasignatura.php";
require_once __DIR__ . "/../../utilidades/u_verificaciones.php";
require_once __DIR__ . "/../modelo/m_matricula.php";

class MatriculaController
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
            case "obtenerMatriculas":
                self::obtenerMatriculas();
                break;
                
            case "obtenerMatriculasPorEstudiante":
                self::obtenerMatriculasPorEstudiante($parametros['idEstudiante'] ?? null);
                break;
                
            case "obtenerMatriculasPorAnioAcademico":
                self::obtenerMatriculasPorAnioAcademico($parametros['anioAcademico'] ?? null);
                break;
                
            case "insertarMatricula":
                self::insertarMatricula($parametros);
                break;
                
            case "actualizarMatricula":
                self::actualizarMatricula($parametros);
                break;
                
            case "anularMatricula":
                self::cambiarEstadoMatricula($parametros['id'] ?? null, 'anulada');
                break;
                
            case "obtenerMatriculasAsignaturas":
                self::obtenerMatriculasAsignaturas();
                break;
                
            case "obtenerMatriculasAsignaturasPorMatricula":
                self::obtenerMatriculasAsignaturasPorMatricula($parametros['idMatricula'] ?? null);
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
                
            default:
                echo json_encode([
                    'estado' => 400,
                    'exito' => false,
                    'mensaje' => "Acción '$accion' no válida en controlador de matrículas",
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

    // Obtener todas las matrículas
    private static function obtenerMatriculas()
    {
        $matriculas = D_Matricula::obtenerMatriculas();
        $resultado = [];
        
        foreach ($matriculas as $matricula) {
            $arr = $matricula->convertirAArray();
            if (isset($matricula->nombreEstudiante)) {
                $arr['nombreEstudiante'] = $matricula->nombreEstudiante;
                $arr['codigoEstudiante'] = $matricula->codigoEstudiante ?? '';
            }
            if (isset($matricula->nombrePlanEstudio)) {
                $arr['nombrePlanEstudio'] = $matricula->nombrePlanEstudio;
            }
            if (isset($matricula->semestre)) {
                $arr['numeroSemestre'] = $matricula->numeroSemestre;
            }
            $resultado[] = $arr;
        }
        
        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Matrículas obtenidas correctamente',
            'resultado' => $resultado
        ]);
    }

    // Obtener matrículas por estudiante
    private static function obtenerMatriculasPorEstudiante($idEstudiante)
    {
        if (!$idEstudiante) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'ID de estudiante no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        $matriculas = D_Matricula::obtenerMatriculasPorEstudiante($idEstudiante);
        $resultado = [];
        
        foreach ($matriculas as $matricula) {
            $arr = $matricula->convertirAArray();
            if (isset($matricula->nombrePlanEstudio)) {
                $arr['nombrePlanEstudio'] = $matricula->nombrePlanEstudio;
            }
            if (isset($matricula->semestre)) {
                $arr['numeroSemestre'] = $matricula->numeroSemestre;
            }
            $resultado[] = $arr;
        }
        
        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Matrículas del estudiante obtenidas correctamente',
            'resultado' => $resultado
        ]);
    }

    // Obtener matrículas por año académico
    private static function obtenerMatriculasPorAnioAcademico($anioAcademico)
    {
        if (!$anioAcademico) {
            $anioAcademico = date('Y');
        }

        $matriculas = D_Matricula::obtenerMatriculasPorAnioAcademico($anioAcademico);
        $resultado = [];
        
        foreach ($matriculas as $matricula) {
            $arr = $matricula->convertirAArray();
            if (isset($matricula->nombreEstudiante)) {
                $arr['nombreEstudiante'] = $matricula->nombreEstudiante;
            }
            if (isset($matricula->nombrePlanEstudio)) {
                $arr['nombrePlanEstudio'] = $matricula->nombrePlanEstudio;
            }
            if (isset($matricula->semestre)) {
                $arr['semestre'] = $matricula->semestre;
            }
            $resultado[] = $arr;
        }
        
        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Matrículas por año académico obtenidas correctamente',
            'resultado' => $resultado
        ]);
    }

    // Insertar matrícula
    private static function insertarMatricula($parametros)
    {
        // Validar campos obligatorios
        $idEstudiante = $parametros['idEstudiante'] ?? '';
        $idPlanEstudio = $parametros['idPlanEstudio'] ?? '';
        $idSemestre = $parametros['idSemestre'] ?? '';
        $cursoAcademico = $parametros['cursoAcademico'] ?? date('Y');
        
        if (empty($idEstudiante) || empty($idPlanEstudio) || empty($idSemestre)) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'Estudiante, plan de estudio y semestre son obligatorios',
                'resultado' => null
            ]);
            return;
        }

        // Verificar si ya tiene matrícula activa
        if (D_Matricula::existeMatriculaActiva($idEstudiante, $idSemestre, $cursoAcademico)) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'El estudiante ya tiene una matrícula activa para este semestre',
                'resultado' => null
            ]);
            return;
        }

        // Preparar datos
        $datos = [
            'idEstudiante' => $idEstudiante,
            'idPlanEstudio' => $idPlanEstudio,
            'idSemestre' => $idSemestre,
            'cursoAcademico' => $cursoAcademico,
            'fechaMatricula' => $parametros['fechaMatricula'] ?? date('Y-m-d'),
            'modalidadMatricula' => $parametros['modalidadMatricula'] ?? 'presencial',
            'totalCreditos' => $parametros['totalCreditos'] ?? 0,
            'estado' => $parametros['estado'] ?? 'Habilitada'
        ];

        // Insertar
        $matriculaId = D_Matricula::insertarMatricula($datos);

        if (!$matriculaId) {
            echo json_encode([
                'estado' => 500,
                'exito' => false,
                'mensaje' => 'Error al crear la matrícula',
                'resultado' => null
            ]);
            return;
        }

        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Matrícula creada exitosamente',
            'resultado' => ['id' => $matriculaId]
        ]);
    }

    // Actualizar matrícula
    private static function actualizarMatricula($parametros)
    {
        $id = $parametros['idMatricula'] ?? null;
        
        if (!$id) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'ID de matrícula no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        // Verificar que existe
        $matriculaExistente = D_Matricula::obtenerMatriculaPorId($id);
        if (!$matriculaExistente) {
            echo json_encode([
                'estado' => 404,
                'exito' => false,
                'mensaje' => 'Matrícula no encontrada',
                'resultado' => null
            ]);
            return;
        }

        // Preparar datos
        $datos = [
            'idPlanEstudio' => $parametros['idPlanEstudio'] ?? $matriculaExistente->idPlanEstudio,
            'idSemestre' => $parametros['idSemestre'] ?? $matriculaExistente->idSemestre,
            'modalidadMatricula' => $parametros['modalidadMatricula'] ?? $matriculaExistente->modalidadMatricula,
            'totalCreditos' => $parametros['totalCreditos'] ?? $matriculaExistente->totalCreditos,
            'estado' => $parametros['estado'] ?? $matriculaExistente->estado
        ];

        // Actualizar
        $actualizado = D_Matricula::actualizarMatricula($id, $datos);

        if (!$actualizado) {
            echo json_encode([
                'estado' => 500,
                'exito' => false,
                'mensaje' => 'Error al actualizar la matrícula',
                'resultado' => null
            ]);
            return;
        }

        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Matrícula actualizada exitosamente',
            'resultado' => ['id' => $id]
        ]);
    }

    // Cambiar estado de matrícula
    private static function cambiarEstadoMatricula($id, $estado)
    {
        if (!$id) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'ID de matrícula no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        $cambiado = D_Matricula::cambiarEstadoMatricula($id, $estado);

        if ($cambiado) {
            echo json_encode([
                'estado' => 'exito',
                'exito' => true,
                'mensaje' => 'Estado de matrícula actualizado',
                'resultado' => ['id' => $id, 'estado' => $estado]
            ]);
        } else {
            echo json_encode([
                'estado' => 500,
                'exito' => false,
                'mensaje' => 'Error al cambiar el estado de la matrícula',
                'resultado' => null
            ]);
        }
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
            }
            if (isset($registro->asignatura)) {
                $arr['asignatura'] = $registro->asignatura;
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

        $registros = D_MatriculaAsignatura::obtenerMatriculasAsignaturasPorMatricula($idMatricula);
        $resultado = [];
        
        foreach ($registros as $registro) {
            $arr = $registro->convertirAArray();
            if (isset($registro->asignatura)) {
                $arr['asignatura'] = $registro->asignatura;
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

    // Insertar matrícula de asignatura
    private static function insertarMatriculaAsignatura($parametros)
    {
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

        // Obtener número de veces matriculado
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

        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Asignatura matriculada exitosamente',
            'resultado' => ['id' => $registroId]
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

        // Preparar datos
        $datos = [
            'convocatoria' => $parametros['convocatoria'] ?? 1,
            'notaFinal' => $parametros['notaFinal'] ?? null,
            'estado' => $parametros['estado'] ?? 'matriculado',
            'numeroVecesMatriculado' => $parametros['numeroVecesMatriculado'] ?? 1
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

        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Matrícula de asignatura actualizada',
            'resultado' => ['id' => $id]
        ]);
    }

    // Registrar nota
    private static function registrarNota($parametros)
    {
        $id = $parametros['id'] ?? null;
        $nota = $parametros['nota'] ?? null;
        
        if (!$id) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'ID de registro no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        if ($nota === null || $nota < 0 || $nota > 10) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'Nota inválida (debe ser entre 0 y 10)',
                'resultado' => null
            ]);
            return;
        }

        $registrado = D_MatriculaAsignatura::registrarNota($id, $nota);

        if ($registrado) {
            echo json_encode([
                'estado' => 'exito',
                'exito' => true,
                'mensaje' => 'Nota registrada correctamente',
                'resultado' => ['id' => $id, 'nota' => $nota]
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
}
?>