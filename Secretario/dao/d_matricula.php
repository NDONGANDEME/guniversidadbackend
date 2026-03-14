<?php
require_once __DIR__ . "/../../utilidades/u_conexion.php";
require_once __DIR__ . "/../modelo/m_matricula.php";
require_once __DIR__ . "/../modelo/m_matriculaasignatura.php";

class D_Matricula
{
    // OBTENER TODAS LAS MATRÍCULAS (solo lectura)
    public static function obtenerMatriculas()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT m.*, 
                           e.nombre as nombreEstudiante, e.apellidos as apellidosEstudiante, e.codigoEstudiante,
                           pe.nombre as nombrePlanEstudio,
                           s.numeroSemestre, s.tipoSemestre
                    FROM matriculas m
                    INNER JOIN estudiantes e ON m.idEstudiante = e.idEstudiante
                    INNER JOIN planestudio pe ON m.idPlanEstudio = pe.idPlanEstudio
                    INNER JOIN semestre s ON m.idSemestre = s.idSemestre
                    ORDER BY m.cursoAcademico DESC, m.fechaMatricula DESC";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $matriculas = [];
            
            foreach ($resultados as $fila) {
                $model = new MatriculaModel();
                $model->hidratarDesdeArray($fila);
                if (isset($fila['nombreEstudiante'])) {
                    $model->nombreEstudiante = $fila['nombreEstudiante'] . ' ' . ($fila['apellidosEstudiante'] ?? '');
                    $model->codigoEstudiante = $fila['codigoEstudiante'] ?? '';
                }
                if (isset($fila['nombrePlanEstudio'])) {
                    $model->nombrePlanEstudio = $fila['nombrePlanEstudio'];
                }
                if (isset($fila['numeroSemestre'])) {
                    $model->numeroSemestre = $fila['numeroSemestre'] . ' - ' . ($fila['tipoSemestre'] ?? '');
                }
                $matriculas[] = $model;
            }

            return $matriculas;
        } catch (PDOException $e) {
            error_log("Error en obtenerMatriculas: " . $e->getMessage());
            return [];
        }
    }

    // OBTENER MATRÍCULA POR ID (solo lectura)
    public static function obtenerMatriculaPorId($id)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT * FROM matriculas WHERE idMatricula = :id";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($resultado) {
                $model = new MatriculaModel();
                return $model->hidratarDesdeArray($resultado);
            }
            
            return null;
        } catch (PDOException $e) {
            error_log("Error en obtenerMatriculaPorId: " . $e->getMessage());
            return null;
        }
    }

    // OBTENER MATRÍCULAS POR ESTUDIANTE (solo lectura)
    public static function obtenerMatriculasPorEstudiante($idEstudiante)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT m.*, pe.nombre as nombrePlanEstudio,
                           s.numeroSemestre, s.tipoSemestre
                    FROM matriculas m
                    INNER JOIN planestudio pe ON m.idPlanEstudio = pe.idPlanEstudio
                    INNER JOIN semestre s ON m.idSemestre = s.idSemestre
                    WHERE m.idEstudiante = :idEstudiante
                    ORDER BY m.cursoAcademico DESC, m.fechaMatricula DESC";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idEstudiante', $idEstudiante, PDO::PARAM_INT);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $matriculas = [];
            
            foreach ($resultados as $fila) {
                $model = new MatriculaModel();
                $model->hidratarDesdeArray($fila);
                if (isset($fila['nombrePlanEstudio'])) {
                    $model->nombrePlanEstudio = $fila['nombrePlanEstudio'];
                }
                if (isset($fila['numeroSemestre'])) {
                    $model->numeroSemestre = $fila['numeroSemestre'] . ' - ' . ($fila['tipoSemestre'] ?? '');
                }
                $matriculas[] = $model;
            }

            return $matriculas;
        } catch (PDOException $e) {
            error_log("Error en obtenerMatriculasPorEstudiante: " . $e->getMessage());
            return [];
        }
    }

    // OBTENER MATRÍCULAS POR PLAN DE ESTUDIO (solo lectura)
    public static function obtenerMatriculasPorPlanEstudio($idPlanEstudio)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT m.*, e.nombre as nombreEstudiante, e.apellidos as apellidosEstudiante
                    FROM matriculas m
                    INNER JOIN estudiantes e ON m.idEstudiante = e.idEstudiante
                    WHERE m.idPlanEstudio = :idPlanEstudio
                    ORDER BY e.apellidos ASC, e.nombre ASC";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idPlanEstudio', $idPlanEstudio, PDO::PARAM_INT);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $matriculas = [];
            
            foreach ($resultados as $fila) {
                $model = new MatriculaModel();
                $model->hidratarDesdeArray($fila);
                if (isset($fila['nombreEstudiante'])) {
                    $model->nombreEstudiante = $fila['nombreEstudiante'] . ' ' . ($fila['apellidosEstudiante'] ?? '');
                }
                $matriculas[] = $model;
            }

            return $matriculas;
        } catch (PDOException $e) {
            error_log("Error en obtenerMatriculasPorPlanEstudio: " . $e->getMessage());
            return [];
        }
    }

    // OBTENER MATRÍCULAS POR AÑO ACADÉMICO (solo lectura)
    public static function obtenerMatriculasPorAnioAcademico($anioAcademico)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT m.*, e.nombre as nombreEstudiante, e.apellidos as apellidosEstudiante,
                           pe.nombre as nombrePlanEstudio,
                           s.numeroSemestre, s.tipoSemestre
                    FROM matriculas m
                    INNER JOIN estudiantes e ON m.idEstudiante = e.idEstudiante
                    INNER JOIN planestudio pe ON m.idPlanEstudio = pe.idPlanEstudio
                    INNER JOIN semestre s ON m.idSemestre = s.idSemestre
                    WHERE m.cursoAcademico = :anioAcademico
                    ORDER BY e.apellidos ASC, e.nombre ASC";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':anioAcademico', $anioAcademico);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $matriculas = [];
            
            foreach ($resultados as $fila) {
                $model = new MatriculaModel();
                $model->hidratarDesdeArray($fila);
                if (isset($fila['nombreEstudiante'])) {
                    $model->nombreEstudiante = $fila['nombreEstudiante'] . ' ' . ($fila['apellidosEstudiante'] ?? '');
                }
                if (isset($fila['nombrePlanEstudio'])) {
                    $model->nombrePlanEstudio = $fila['nombrePlanEstudio'];
                }
                if (isset($fila['numeroSemestre'])) {
                    $model->numeroSemestre = $fila['numeroSemestre'] . ' - ' . ($fila['tipoSemestre'] ?? '');
                }
                $matriculas[] = $model;
            }

            return $matriculas;
        } catch (PDOException $e) {
            error_log("Error en obtenerMatriculasPorAnioAcademico: " . $e->getMessage());
            return [];
        }
    }

    // INSERTAR MATRÍCULA CON TRANSACCIÓN
    public static function insertarMatricula($datos)
    {
        $pdo = null;
        try {
            $pdo = ConexionUtil::conectar();
            $pdo->beginTransaction();

            $sql = "INSERT INTO matriculas (
                        idEstudiante, idPlanEstudio, idSemestre, cursoAcademico,
                        fechaMatricula, modalidadMatricula, totalCreditos, estado
                    ) VALUES (
                        :idEstudiante, :idPlanEstudio, :idSemestre, :cursoAcademico,
                        :fechaMatricula, :modalidadMatricula, :totalCreditos, :estado
                    )";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':idEstudiante', $datos['idEstudiante'], PDO::PARAM_INT);
            $stmt->bindParam(':idPlanEstudio', $datos['idPlanEstudio'], PDO::PARAM_INT);
            $stmt->bindParam(':idSemestre', $datos['idSemestre'], PDO::PARAM_INT);
            $stmt->bindParam(':cursoAcademico', $datos['cursoAcademico']);
            $stmt->bindParam(':fechaMatricula', $datos['fechaMatricula']);
            $stmt->bindParam(':modalidadMatricula', $datos['modalidadMatricula']);
            $stmt->bindParam(':totalCreditos', $datos['totalCreditos'], PDO::PARAM_INT);
            $stmt->bindParam(':estado', $datos['estado']);
            
            if ($stmt->execute()) {
                $id = $pdo->lastInsertId();
                $pdo->commit();
                return $id;
            } else {
                $pdo->rollBack();
                return null;
            }
            
        } catch (PDOException $e) {
            if ($pdo) {
                $pdo->rollBack();
            }
            error_log("Error en insertarMatricula: " . $e->getMessage());
            return null;
        }
    }

    // ACTUALIZAR MATRÍCULA CON TRANSACCIÓN
    public static function actualizarMatricula($id, $datos)
    {
        $pdo = null;
        try {
            $pdo = ConexionUtil::conectar();
            $pdo->beginTransaction();

            $sql = "UPDATE matriculas SET 
                        idPlanEstudio = :idPlanEstudio,
                        idSemestre = :idSemestre,
                        modalidadMatricula = :modalidadMatricula,
                        totalCreditos = :totalCreditos,
                        estado = :estado
                    WHERE idMatricula = :id";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':idPlanEstudio', $datos['idPlanEstudio'], PDO::PARAM_INT);
            $stmt->bindParam(':idSemestre', $datos['idSemestre'], PDO::PARAM_INT);
            $stmt->bindParam(':modalidadMatricula', $datos['modalidadMatricula']);
            $stmt->bindParam(':totalCreditos', $datos['totalCreditos'], PDO::PARAM_INT);
            $stmt->bindParam(':estado', $datos['estado']);
            
            $resultado = $stmt->execute();
            
            if ($resultado) {
                $pdo->commit();
                return true;
            } else {
                $pdo->rollBack();
                return false;
            }
            
        } catch (PDOException $e) {
            if ($pdo) {
                $pdo->rollBack();
            }
            error_log("Error en actualizarMatricula: " . $e->getMessage());
            return false;
        }
    }

    // CAMBIAR ESTADO DE MATRÍCULA CON TRANSACCIÓN
    public static function cambiarEstadoMatricula($id, $estado)
    {
        $pdo = null;
        try {
            $pdo = ConexionUtil::conectar();
            $pdo->beginTransaction();

            $sql = "UPDATE matriculas SET estado = :estado WHERE idMatricula = :id";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':estado', $estado);
            
            $resultado = $stmt->execute();
            
            if ($resultado) {
                $pdo->commit();
                return true;
            } else {
                $pdo->rollBack();
                return false;
            }
            
        } catch (PDOException $e) {
            if ($pdo) {
                $pdo->rollBack();
            }
            error_log("Error en cambiarEstadoMatricula: " . $e->getMessage());
            return false;
        }
    }

    // ELIMINAR MATRÍCULA (soft delete) CON TRANSACCIÓN
    public static function eliminarMatricula($id)
    {
        return self::cambiarEstadoMatricula($id, 'anulada');
    }

    // VERIFICAR SI EXISTE MATRÍCULA ACTIVA (solo lectura)
    public static function existeMatriculaActiva($idEstudiante, $idSemestre, $cursoAcademico)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT COUNT(*) as total FROM matriculas 
                    WHERE idEstudiante = :idEstudiante 
                      AND idSemestre = :idSemestre
                      AND cursoAcademico = :cursoAcademico
                      AND estado = 'activa'";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idEstudiante', $idEstudiante, PDO::PARAM_INT);
            $stmt->bindParam(':idSemestre', $idSemestre, PDO::PARAM_INT);
            $stmt->bindParam(':cursoAcademico', $cursoAcademico);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['total'] > 0;
        } catch (PDOException $e) {
            error_log("Error en existeMatriculaActiva: " . $e->getMessage());
            return false;
        }
    }
}
?>