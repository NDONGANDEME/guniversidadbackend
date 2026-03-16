<?php
require_once __DIR__ . "/../../utilidades/u_conexion.php";
require_once __DIR__ . "/../modelo/m_clase.php";
require_once __DIR__ . "/d_clasehorario.php";

class D_Clase
{
    // OBTENER TODAS LAS CLASES (solo lectura)
    public static function obtenerClases()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT c.*, 
                           a.nombreAula,
                           p.nombreProfesor, p.apellidosProfesor,
                           asig.codigoAsignatura, asig.nombreAsignatura,
                           pe.nombre as nombrePlanEstudio
                    FROM clase c
                    LEFT JOIN aulas a ON c.idAula = a.idAula
                    LEFT JOIN profesor p ON c.idProfesor = p.idProfesor
                    LEFT JOIN plan_semestre_asignatura psa ON c.idPlanCursoAsignatura = psa.idPlanCursoAsignatura
                    LEFT JOIN asignaturas asig ON psa.idAsignatura = asig.idAsignatura
                    LEFT JOIN planestudio pe ON psa.idPlanEstudio = pe.idPlanEstudio
                    ORDER BY c.diaSemanal, c.horaInicio";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $clases = [];
            
            foreach ($resultados as $fila) {
                $model = new ClaseModel();
                $model->hidratarDesdeArray($fila);
                
                // Añadir información adicional
                if (isset($fila['nombreAula'])) {
                    $model->nombreAula = $fila['nombreAula'];
                }
                if (isset($fila['nombreProfesor'])) {
                    $model->nombreProfesor = $fila['nombreProfesor'];
                    $model->apellidosProfesor = $fila['apellidosProfesor'] ?? '';
                }
                if (isset($fila['nombreAsignatura'])) {
                    $model->codigoAsignatura = $fila['codigoAsignatura'] ?? '';
                    $model->nombreAsignatura = $fila['nombreAsignatura'];
                    $model->asignatura = ($fila['codigoAsignatura'] ?? '') . ' - ' . $fila['nombreAsignatura'];
                }
                if (isset($fila['nombrePlanEstudio'])) {
                    $model->planEstudio = $fila['nombrePlanEstudio'];
                }
                
                // Obtener horarios asociados
                $horarios = D_ClaseHorario::obtenerHorariosPorClase($model->idClase);
                $model->establecerHorarios($horarios);
                
                $clases[] = $model;
            }

            return $clases;
        } catch (PDOException $e) {
            error_log("Error en obtenerClases: " . $e->getMessage());
            return [];
        }
    }

    // OBTENER CLASE POR ID (solo lectura)
    public static function obtenerClasePorId($id)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT c.*, 
                           a.nombreAula,
                           p.nombreProfesor, p.apellidosProfesor,
                           asig.codigoAsignatura, asig.nombreAsignatura,
                           pe.nombre as nombrePlanEstudio
                    FROM clase c
                    LEFT JOIN aulas a ON c.idAula = a.idAula
                    LEFT JOIN profesor p ON c.idProfesor = p.idProfesor
                    LEFT JOIN plan_semestre_asignatura psa ON c.idPlanCursoAsignatura = psa.idPlanCursoAsignatura
                    LEFT JOIN asignaturas asig ON psa.idAsignatura = asig.idAsignatura
                    LEFT JOIN planestudio pe ON psa.idPlanEstudio = pe.idPlanEstudio
                    WHERE c.idClase = :id";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($resultado) {
                $model = new ClaseModel();
                $model->hidratarDesdeArray($resultado);
                
                if (isset($resultado['nombreAula'])) {
                    $model->nombreAula = $resultado['nombreAula'];
                }
                if (isset($resultado['nombreProfesor'])) {
                    $model->nombreProfesor = $resultado['nombreProfesor'];
                    $model->apellidosProfesor = $resultado['apellidosProfesor'] ?? '';
                }
                if (isset($resultado['nombreAsignatura'])) {
                    $model->codigoAsignatura = $resultado['codigoAsignatura'] ?? '';
                    $model->nombreAsignatura = $resultado['nombreAsignatura'];
                }
                if (isset($resultado['nombrePlanEstudio'])) {
                    $model->planEstudio = $resultado['nombrePlanEstudio'];
                }
                
                // Obtener horarios asociados
                $horarios = D_ClaseHorario::obtenerHorariosPorClase($id);
                $model->establecerHorarios($horarios);
                
                return $model;
            }
            
            return null;
        } catch (PDOException $e) {
            error_log("Error en obtenerClasePorId: " . $e->getMessage());
            return null;
        }
    }

    // INSERTAR CLASE CON TRANSACCIÓN
    public static function insertarClase($datos)
    {
        $pdo = null;
        try {
            $pdo = ConexionUtil::conectar();
            $pdo->beginTransaction();

            $sql = "INSERT INTO clase (
                        idPlanCursoAsignatura, idAula, idProfesor, diaSemanal, 
                        horaInicio, horaFinal, tipoSesion, observaciones
                    ) VALUES (
                        :idPlanCursoAsignatura, :idAula, :idProfesor, :diaSemanal,
                        :horaInicio, :horaFinal, :tipoSesion, :observaciones
                    )";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':idPlanCursoAsignatura', $datos['idPlanCursoAsignatura'], PDO::PARAM_INT);
            $stmt->bindParam(':idAula', $datos['idAula'], PDO::PARAM_INT);
            $stmt->bindParam(':idProfesor', $datos['idProfesor'], PDO::PARAM_INT);
            $stmt->bindParam(':diaSemanal', $datos['diaSemanal']);
            $stmt->bindParam(':horaInicio', $datos['horaInicio']);
            $stmt->bindParam(':horaFinal', $datos['horaFinal']);
            $stmt->bindParam(':tipoSesion', $datos['tipoSesion']);
            $stmt->bindParam(':observaciones', $datos['observaciones']);
            
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
            error_log("Error en insertarClase: " . $e->getMessage());
            return null;
        }
    }

    // ACTUALIZAR CLASE CON TRANSACCIÓN
    public static function actualizarClase($id, $datos)
    {
        $pdo = null;
        try {
            $pdo = ConexionUtil::conectar();
            $pdo->beginTransaction();

            $sql = "UPDATE clase SET 
                        idPlanCursoAsignatura = :idPlanCursoAsignatura,
                        idAula = :idAula,
                        idProfesor = :idProfesor,
                        diaSemanal = :diaSemanal,
                        horaInicio = :horaInicio,
                        horaFinal = :horaFinal,
                        tipoSesion = :tipoSesion,
                        observaciones = :observaciones
                    WHERE idClase = :id";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':idPlanCursoAsignatura', $datos['idPlanCursoAsignatura'], PDO::PARAM_INT);
            $stmt->bindParam(':idAula', $datos['idAula'], PDO::PARAM_INT);
            $stmt->bindParam(':idProfesor', $datos['idProfesor'], PDO::PARAM_INT);
            $stmt->bindParam(':diaSemanal', $datos['diaSemanal']);
            $stmt->bindParam(':horaInicio', $datos['horaInicio']);
            $stmt->bindParam(':horaFinal', $datos['horaFinal']);
            $stmt->bindParam(':tipoSesion', $datos['tipoSesion']);
            $stmt->bindParam(':observaciones', $datos['observaciones']);
            
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
            error_log("Error en actualizarClase: " . $e->getMessage());
            return false;
        }
    }

    // ELIMINAR CLASE CON TRANSACCIÓN
    public static function eliminarClase($id)
    {
        $pdo = null;
        try {
            $pdo = ConexionUtil::conectar();
            $pdo->beginTransaction();

            // Primero eliminar los horarios asociados
            D_ClaseHorario::eliminarHorariosPorClase($id);

            // Luego eliminar la clase
            $sql = "DELETE FROM clase WHERE idClase = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
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
            error_log("Error en eliminarClase: " . $e->getMessage());
            return false;
        }
    }

    // VERIFICAR DISPONIBILIDAD DE AULA Y PROFESOR (solo lectura)
    public static function verificarDisponibilidad($idAula, $idProfesor, $diaSemanal, $horaInicio, $horaFinal, $excluirId = null)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT COUNT(*) as total FROM clase 
                    WHERE diaSemanal = :diaSemanal 
                    AND (
                        (horaInicio <= :horaInicio AND horaFinal > :horaInicio)
                        OR (horaInicio < :horaFinal AND horaFinal >= :horaFinal)
                        OR (horaInicio >= :horaInicio AND horaFinal <= :horaFinal)
                    )
                    AND (idAula = :idAula OR idProfesor = :idProfesor)";
            
            if ($excluirId !== null) {
                $sql .= " AND idClase != :excluirId";
            }
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':diaSemanal', $diaSemanal);
            $stmt->bindParam(':horaInicio', $horaInicio);
            $stmt->bindParam(':horaFinal', $horaFinal);
            $stmt->bindParam(':idAula', $idAula, PDO::PARAM_INT);
            $stmt->bindParam(':idProfesor', $idProfesor, PDO::PARAM_INT);
            
            if ($excluirId !== null) {
                $stmt->bindParam(':excluirId', $excluirId, PDO::PARAM_INT);
            }
            
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['total'] > 0;
        } catch (PDOException $e) {
            error_log("Error en verificarDisponibilidad: " . $e->getMessage());
            return true; // Por seguridad, si hay error asumimos que no está disponible
        }
    }
}
?>