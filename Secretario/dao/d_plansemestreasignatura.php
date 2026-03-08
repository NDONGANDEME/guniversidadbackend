<?php
require_once __DIR__ . "/../../utilidades/u_conexion.php";
require_once __DIR__ . "/../modelo/m_plansemestreasignatura.php";

class D_PlanSemestreAsignatura
{
    // OBTENER TODOS LOS REGISTROS DE PLAN SEMESTRE ASIGNATURA
    public static function obtenerPlanSemestreAsignaturas()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT psa.*, 
                           pe.nombre as nombrePlanEstudio,
                           s.numeroSemestre, s.tipoSemestre,
                           a.codigoAsignatura, a.nombreAsignatura
                    FROM plan_semestre_asignatura psa
                    INNER JOIN planestudio pe ON psa.idPlanEstudio = pe.idPlanEstudio
                    INNER JOIN semestre s ON psa.idSemestre = s.idSemestre
                    INNER JOIN asignaturas a ON psa.idAsignatura = a.idAsignatura
                    ORDER BY pe.nombre, s.numeroSemestre, a.nombreAsignatura";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $registros = [];
            
            foreach ($resultados as $fila) {
                $model = new PlanSemestreAsignaturaModel();
                $model->hidratarDesdeArray($fila);
                if (isset($fila['nombrePlanEstudio'])) {
                    $model->nombrePlanEstudio = $fila['nombrePlanEstudio'];
                }
                if (isset($fila['numeroSemestre'])) {
                    $model->semestre = $fila['numeroSemestre'] . ' - ' . ($fila['tipoSemestre'] ?? '');
                }
                if (isset($fila['nombreAsignatura'])) {
                    $model->asignatura = $fila['codigoAsignatura'] . ' - ' . $fila['nombreAsignatura'];
                }
                $registros[] = $model;
            }

            return $registros;
        } catch (PDOException $e) {
            error_log("Error en obtenerPlanSemestreAsignaturas: " . $e->getMessage());
            return [];
        }
    }

    // OBTENER PLAN SEMESTRE ASIGNATURA POR ID
    public static function obtenerPlanSemestreAsignaturaPorId($id)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT * FROM plan_semestre_asignatura WHERE idPlanCursoAsignatura = :id";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($resultado) {
                $model = new PlanSemestreAsignaturaModel();
                return $model->hidratarDesdeArray($resultado);
            }
            
            return null;
        } catch (PDOException $e) {
            error_log("Error en obtenerPlanSemestreAsignaturaPorId: " . $e->getMessage());
            return null;
        }
    }

    // OBTENER PLAN SEMESTRE ASIGNATURA POR PLAN ESTUDIO
    public static function obtenerPlanSemestreAsignaturaPorPlanEstudio($idPlanEstudio)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT psa.*, 
                           s.numeroSemestre, s.tipoSemestre,
                           a.codigoAsignatura, a.nombreAsignatura
                    FROM plan_semestre_asignatura psa
                    INNER JOIN semestre s ON psa.idSemestre = s.idSemestre
                    INNER JOIN asignaturas a ON psa.idAsignatura = a.idAsignatura
                    WHERE psa.idPlanEstudio = :idPlanEstudio
                    ORDER BY s.numeroSemestre, a.nombreAsignatura";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idPlanEstudio', $idPlanEstudio, PDO::PARAM_INT);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $registros = [];
            
            foreach ($resultados as $fila) {
                $model = new PlanSemestreAsignaturaModel();
                $model->hidratarDesdeArray($fila);
                if (isset($fila['numeroSemestre'])) {
                    $model->semestre = $fila['numeroSemestre'] . ' - ' . ($fila['tipoSemestre'] ?? '');
                }
                if (isset($fila['nombreAsignatura'])) {
                    $model->asignatura = $fila['codigoAsignatura'] . ' - ' . $fila['nombreAsignatura'];
                }
                $registros[] = $model;
            }

            return $registros;
        } catch (PDOException $e) {
            error_log("Error en obtenerPlanSemestreAsignaturaPorPlanEstudio: " . $e->getMessage());
            return [];
        }
    }

    // INSERTAR PLAN SEMESTRE ASIGNATURA
    public static function insertarPlanSemestreAsignatura($datos)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "INSERT INTO plan_semestre_asignatura (
                        idPlanEstudio, idSemestre, idAsignatura, creditos, modalidad
                    ) VALUES (
                        :idPlanEstudio, :idSemestre, :idAsignatura, :creditos, :modalidad
                    )";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idPlanEstudio', $datos['idPlanEstudio'], PDO::PARAM_INT);
            $stmt->bindParam(':idSemestre', $datos['idSemestre'], PDO::PARAM_INT);
            $stmt->bindParam(':idAsignatura', $datos['idAsignatura'], PDO::PARAM_INT);
            $stmt->bindParam(':creditos', $datos['creditos'], PDO::PARAM_INT);
            $stmt->bindParam(':modalidad', $datos['modalidad']);
            
            if ($stmt->execute()) {
                return $instanciaConexion->lastInsertId();
            }
            
            return null;
        } catch (PDOException $e) {
            error_log("Error en insertarPlanSemestreAsignatura: " . $e->getMessage());
            return null;
        }
    }

    // ACTUALIZAR PLAN SEMESTRE ASIGNATURA
    public static function actualizarPlanSemestreAsignatura($id, $datos)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "UPDATE plan_semestre_asignatura SET 
                        idSemestre = :idSemestre,
                        idAsignatura = :idAsignatura,
                        creditos = :creditos,
                        modalidad = :modalidad
                    WHERE idPlanCursoAsignatura = :id";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':idSemestre', $datos['idSemestre'], PDO::PARAM_INT);
            $stmt->bindParam(':idAsignatura', $datos['idAsignatura'], PDO::PARAM_INT);
            $stmt->bindParam(':creditos', $datos['creditos'], PDO::PARAM_INT);
            $stmt->bindParam(':modalidad', $datos['modalidad']);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en actualizarPlanSemestreAsignatura: " . $e->getMessage());
            return false;
        }
    }

    // VERIFICAR SI EXISTE ASIGNACIÓN
    public static function existeAsignacion($idPlanEstudio, $idSemestre, $idAsignatura, $excluirId = null)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT COUNT(*) as total FROM plan_semestre_asignatura 
                    WHERE idPlanEstudio = :idPlanEstudio 
                      AND idSemestre = :idSemestre 
                      AND idAsignatura = :idAsignatura";
            
            if ($excluirId !== null) {
                $sql .= " AND idPlanCursoAsignatura != :excluirId";
            }
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idPlanEstudio', $idPlanEstudio, PDO::PARAM_INT);
            $stmt->bindParam(':idSemestre', $idSemestre, PDO::PARAM_INT);
            $stmt->bindParam(':idAsignatura', $idAsignatura, PDO::PARAM_INT);
            
            if ($excluirId !== null) {
                $stmt->bindParam(':excluirId', $excluirId, PDO::PARAM_INT);
            }
            
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['total'] > 0;
        } catch (PDOException $e) {
            error_log("Error en existeAsignacion: " . $e->getMessage());
            return false;
        }
    }


    // Añadir al final de d_plansemestreasignatura.php
    public static function eliminarPlanSemestreAsignatura($id)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "DELETE FROM plan_semestre_asignatura WHERE idPlanCursoAsignatura = :id";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en eliminarPlanSemestreAsignatura: " . $e->getMessage());
            return false;
        }
    }
}
?>