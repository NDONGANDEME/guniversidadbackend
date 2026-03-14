<?php
require_once __DIR__ . "/../../utilidades/u_conexion.php";
require_once __DIR__ . "/../modelo/m_semestre.php";

class D_Semestre
{
    // OBTENER TODOS LOS SEMESTRES (solo lectura, no necesita transacción)
    public static function obtenerSemestres()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT s.*, c.nombreCurso
                    FROM semestre s
                    LEFT JOIN curso c ON s.idCurso = c.idCurso
                    ORDER BY s.numeroSemestre ASC";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $semestres = [];
            
            foreach ($resultados as $fila) {
                $model = new SemestreModel();
                $model->hidratarDesdeArray($fila);
                $semestres[] = $model;
            }

            return $semestres;
        } catch (PDOException $e) {
            error_log("Error en obtenerSemestres: " . $e->getMessage());
            return [];
        }
    }

    // OBTENER SEMESTRE POR ID (solo lectura, no necesita transacción)
    public static function obtenerSemestrePorId($id)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT * FROM semestre WHERE idSemestre = :id";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($resultado) {
                $model = new SemestreModel();
                return $model->hidratarDesdeArray($resultado);
            }
            
            return null;
        } catch (PDOException $e) {
            error_log("Error en obtenerSemestrePorId: " . $e->getMessage());
            return null;
        }
    }

    // INSERTAR SEMESTRE CON TRANSACCIÓN
    public static function insertarSemestre($datos)
    {
        $pdo = null;
        try {
            $pdo = ConexionUtil::conectar();
            $pdo->beginTransaction();

            $sql = "INSERT INTO semestre (numeroSemestre, tipoSemestre, idCurso) 
                    VALUES (:numeroSemestre, :tipoSemestre, :idCurso)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':numeroSemestre', $datos['numeroSemestre']);
            $stmt->bindParam(':tipoSemestre', $datos['tipoSemestre']);
            $stmt->bindParam(':idCurso', $datos['idCurso']);
            
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
            error_log("Error en insertarSemestre: " . $e->getMessage());
            return null;
        }
    }

    // ACTUALIZAR SEMESTRE CON TRANSACCIÓN
    public static function actualizarSemestre($datos)
    {
        $pdo = null;
        try {
            $pdo = ConexionUtil::conectar();
            $pdo->beginTransaction();

            $sql = "UPDATE semestre SET 
                        numeroSemestre = :numeroSemestre,
                        tipoSemestre = :tipoSemestre
                    WHERE idSemestre = :id";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $datos['id'], PDO::PARAM_INT);
            $stmt->bindParam(':numeroSemestre', $datos['numeroSemestre']);
            $stmt->bindParam(':tipoSemestre', $datos['tipoSemestre']);
            
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
            error_log("Error en actualizarSemestre: " . $e->getMessage());
            return false;
        }
    }

    // VERIFICAR SI EXISTE SEMESTRE (solo lectura, no necesita transacción)
    public static function existeSemestre($numeroSemestre, $tipoSemestre, $excluirId = null)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT COUNT(*) as total FROM semestre 
                    WHERE numeroSemestre = :numeroSemestre AND tipoSemestre = :tipoSemestre";
            
            if ($excluirId !== null) {
                $sql .= " AND idSemestre != :excluirId";
            }
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':numeroSemestre', $numeroSemestre);
            $stmt->bindParam(':tipoSemestre', $tipoSemestre);
            
            if ($excluirId !== null) {
                $stmt->bindParam(':excluirId', $excluirId, PDO::PARAM_INT);
            }
            
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['total'] > 0;
        } catch (PDOException $e) {
            error_log("Error en existeSemestre: " . $e->getMessage());
            return false;
        }
    }
}
?>