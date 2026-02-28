<?php
require_once __DIR__ . "/../../utilidades/u_conexion.php";
require_once __DIR__ . "/../modelo/m_semestre.php";

class D_Semestre
{
    // OBTENER TODOS LOS SEMESTRES
    public static function obtenerSemestres()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT * FROM semestre ORDER BY numeroSemestre ASC";
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

    // OBTENER SEMESTRE POR ID
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

    // INSERTAR SEMESTRE
    public static function insertarSemestre($datos)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "INSERT INTO semestre (numeroSemestre, tipoSemestre) 
                    VALUES (:numeroSemestre, :tipoSemestre)";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':numeroSemestre', $datos['numeroSemestre']);
            $stmt->bindParam(':tipoSemestre', $datos['tipoSemestre']);
            
            if ($stmt->execute()) {
                return $instanciaConexion->lastInsertId();
            }
            
            return null;
        } catch (PDOException $e) {
            error_log("Error en insertarSemestre: " . $e->getMessage());
            return null;
        }
    }

    // ACTUALIZAR SEMESTRE
    public static function actualizarSemestre($datos)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "UPDATE semestre SET 
                        numeroSemestre = :numeroSemestre,
                        tipoSemestre = :tipoSemestre
                    WHERE idSemestre = :id";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':id', $datos['id'], PDO::PARAM_INT);
            $stmt->bindParam(':numeroSemestre', $datos['numeroSemestre']);
            $stmt->bindParam(':tipoSemestre', $datos['tipoSemestre']);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en actualizarSemestre: " . $e->getMessage());
            return false;
        }
    }

    // VERIFICAR SI EXISTE SEMESTRE
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