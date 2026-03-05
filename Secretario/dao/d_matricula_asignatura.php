<?php
require_once __DIR__ . "/../../utilidades/u_conexion.php";
require_once __DIR__ . "/../modelo/m_matricula_asignatura.php";

class D_MatriculaAsignatura
{
    // OBTENER TODAS LAS MATRÍCULAS DE ASIGNATURAS
    public static function obtenerMatriculasAsignaturas()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT ma.*, 
                           m.cursoAcademico, e.nombre as nombreEstudiante, e.apellidos as apellidosEstudiante,
                           a.codigoAsignatura, a.nombreAsignatura,
                           psa.creditos
                    FROM matricula_asignatura ma
                    INNER JOIN matriculas m ON ma.idMatricula = m.idMatricula
                    INNER JOIN estudiantes e ON m.idEstudiante = e.idEstudiante
                    INNER JOIN plan_semestre_asignatura psa ON ma.idPlanCursoAsignatura = psa.idPlanCursoAsignatura
                    INNER JOIN asignaturas a ON psa.idAsignatura = a.idAsignatura
                    ORDER BY m.cursoAcademico DESC, e.apellidos ASC, a.nombreAsignatura ASC";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $registros = [];
            
            foreach ($resultados as $fila) {
                $model = new MatriculaAsignaturaModel();
                $model->hidratarDesdeArray($fila);
                if (isset($fila['nombreEstudiante']) && isset($fila['apellidosEstudiante'])) {
                    $model->nombreEstudiante = $fila['nombreEstudiante'] ?? '';
                    $model->apellidosEstudiante = $fila['apellidosEstudiante'] ?? '';
                }
                if (isset($fila['nombreAsignatura'])) {
                    $model->codigoAsignatura = $fila['codigoAsignatura'] ?? '';
                    $model->creditos = $fila['creditos'] ?? 0;
                    $model->nombreAsignatura = $fila['nombreAsignatura'] ?? '';
                }
                $registros[] = $model;
            }

            return $registros;
        } catch (PDOException $e) {
            error_log("Error en obtenerMatriculasAsignaturas: " . $e->getMessage());
            return [];
        }
    }

    // OBTENER MATRÍCULAS DE ASIGNATURAS POR MATRÍCULA
    public static function obtenerMatriculasAsignaturasPorMatricula($idMatricula)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT ma.*, a.codigoAsignatura, a.nombreAsignatura, psa.creditos
                    FROM matricula_asignatura ma
                    INNER JOIN plan_semestre_asignatura psa ON ma.idPlanCursoAsignatura = psa.idPlanCursoAsignatura
                    INNER JOIN asignaturas a ON psa.idAsignatura = a.idAsignatura
                    WHERE ma.idMatricula = :idMatricula
                    ORDER BY a.nombreAsignatura ASC";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idMatricula', $idMatricula, PDO::PARAM_INT);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $registros = [];
            
            foreach ($resultados as $fila) {
                $model = new MatriculaAsignaturaModel();
                $model->hidratarDesdeArray($fila);
                if (isset($fila['nombreAsignatura'])) {
                    $model->codigoAsignatura = $fila['codigoAsignatura'] ?? '';
                    $model->nombreAsignatura = $fila['nombreAsignatura'] ?? '';
                    $model->creditos = $fila['creditos'] ?? 0;
                }
                $registros[] = $model;
            }

            return $registros;
        } catch (PDOException $e) {
            error_log("Error en obtenerMatriculasAsignaturasPorMatricula: " . $e->getMessage());
            return [];
        }
    }

    // OBTENER MATRÍCULA DE ASIGNATURA POR ID
    public static function obtenerMatriculaAsignaturaPorId($id)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT * FROM matricula_asignatura WHERE idMatriculaAsignatura = :id";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($resultado) {
                $model = new MatriculaAsignaturaModel();
                return $model->hidratarDesdeArray($resultado);
            }
            
            return null;
        } catch (PDOException $e) {
            error_log("Error en obtenerMatriculaAsignaturaPorId: " . $e->getMessage());
            return null;
        }
    }

    // INSERTAR MATRÍCULA DE ASIGNATURA
    public static function insertarMatriculaAsignatura($datos)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "INSERT INTO matricula_asignatura (
                        idMatricula, idPlanCursoAsignatura, convocatoria, notaFinal, estado, numeroVecesMatriculado
                    ) VALUES (
                        :idMatricula, :idPlanCursoAsignatura, :convocatoria, :notaFinal, :estado, :numeroVecesMatriculado
                    )";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idMatricula', $datos['idMatricula'], PDO::PARAM_INT);
            $stmt->bindParam(':idPlanCursoAsignatura', $datos['idPlanCursoAsignatura'], PDO::PARAM_INT);
            $stmt->bindParam(':convocatoria', $datos['convocatoria'], PDO::PARAM_INT);
            $stmt->bindParam(':notaFinal', $datos['notaFinal']);
            $stmt->bindParam(':estado', $datos['estado']);
            $stmt->bindParam(':numeroVecesMatriculado', $datos['numeroVecesMatriculado'], PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                return $instanciaConexion->lastInsertId();
            }
            
            return null;
        } catch (PDOException $e) {
            error_log("Error en insertarMatriculaAsignatura: " . $e->getMessage());
            return null;
        }
    }

    // ACTUALIZAR MATRÍCULA DE ASIGNATURA
    public static function actualizarMatriculaAsignatura($id, $datos)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "UPDATE matricula_asignatura SET 
                        convocatoria = :convocatoria,
                        notaFinal = :notaFinal,
                        estado = :estado,
                        numeroVecesMatriculado = :numeroVecesMatriculado
                    WHERE idMatriculaAsignatura = :id";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':convocatoria', $datos['convocatoria'], PDO::PARAM_INT);
            $stmt->bindParam(':notaFinal', $datos['notaFinal']);
            $stmt->bindParam(':estado', $datos['estado']);
            $stmt->bindParam(':numeroVecesMatriculado', $datos['numeroVecesMatriculado'], PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en actualizarMatriculaAsignatura: " . $e->getMessage());
            return false;
        }
    }

    // REGISTRAR NOTA
    public static function registrarNota($id, $nota)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "UPDATE matricula_asignatura SET 
                        notaFinal = :notaFinal,
                        estado = CASE 
                            WHEN :notaFinal >= 5 THEN 'aprobada'
                            ELSE 'reprobada'
                        END
                    WHERE idMatriculaAsignatura = :id";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':notaFinal', $nota);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en registrarNota: " . $e->getMessage());
            return false;
        }
    }

    // CAMBIAR ESTADO
    public static function cambiarEstado($id, $estado)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "UPDATE matricula_asignatura SET estado = :estado WHERE idMatriculaAsignatura = :id";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':estado', $estado);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en cambiarEstado: " . $e->getMessage());
            return false;
        }
    }

    // VERIFICAR SI YA ESTÁ MATRICULADA LA ASIGNATURA
    public static function existeAsignaturaMatriculada($idMatricula, $idPlanCursoAsignatura)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT COUNT(*) as total FROM matricula_asignatura 
                    WHERE idMatricula = :idMatricula AND idPlanCursoAsignatura = :idPlanCursoAsignatura";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idMatricula', $idMatricula, PDO::PARAM_INT);
            $stmt->bindParam(':idPlanCursoAsignatura', $idPlanCursoAsignatura, PDO::PARAM_INT);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['total'] > 0;
        } catch (PDOException $e) {
            error_log("Error en existeAsignaturaMatriculada: " . $e->getMessage());
            return false;
        }
    }
}
?>