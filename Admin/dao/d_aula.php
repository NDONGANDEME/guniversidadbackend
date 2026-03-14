<?php
require_once __DIR__ . "/../../utilidades/u_conexion.php";
require_once __DIR__ . "/../modelo/m_aula.php";

class D_Aula
{
    // OBTENER TODAS LAS AULAS (solo lectura)
    public static function obtenerAulas()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT a.*, f.nombreFacultad 
                    FROM aulas a
                    LEFT JOIN facultad f ON a.idFacultad = f.idFacultad
                    ORDER BY a.nombreAula ASC";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $aulas = [];
            
            foreach ($resultados as $fila) {
                $model = new AulaModel();
                $model->hidratarDesdeArray($fila);
                if (isset($fila['nombreFacultad'])) {
                    $model->nombreFacultad = $fila['nombreFacultad'];
                }
                $aulas[] = $model;
            }

            return $aulas;
        } catch (PDOException $e) {
            error_log("Error en obtenerAulas: " . $e->getMessage());
            return [];
        }
    }

    // OBTENER AULA POR ID (solo lectura)
    public static function obtenerAulaPorId($id)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT * FROM aulas WHERE idAula = :id";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($resultado) {
                $model = new AulaModel();
                return $model->hidratarDesdeArray($resultado);
            }
            
            return null;
        } catch (PDOException $e) {
            error_log("Error en obtenerAulaPorId: " . $e->getMessage());
            return null;
        }
    }

    // OBTENER AULAS POR FACULTAD (solo lectura)
    public static function obtenerAulasPorFacultad($idFacultad)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT * FROM aulas WHERE idFacultad = :idFacultad ORDER BY nombreAula ASC";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idFacultad', $idFacultad, PDO::PARAM_INT);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $aulas = [];
            
            foreach ($resultados as $fila) {
                $model = new AulaModel();
                $model->hidratarDesdeArray($fila);
                $aulas[] = $model;
            }

            return $aulas;
        } catch (PDOException $e) {
            error_log("Error en obtenerAulasPorFacultad: " . $e->getMessage());
            return [];
        }
    }

    // INSERTAR AULA CON TRANSACCIÓN
    public static function insertarAula($datos)
    {
        $pdo = null;
        try {
            $pdo = ConexionUtil::conectar();
            $pdo->beginTransaction();

            $sql = "INSERT INTO aulas (nombreAula, capacidad, idFacultad, estado) 
                    VALUES (:nombreAula, :capacidad, :idFacultad, :estado)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nombreAula', $datos['nombreAula']);
            $stmt->bindParam(':capacidad', $datos['capacidad'], PDO::PARAM_INT);
            $stmt->bindParam(':idFacultad', $datos['idFacultad'], PDO::PARAM_INT);
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
            error_log("Error en insertarAula: " . $e->getMessage());
            return null;
        }
    }

    // ACTUALIZAR AULA CON TRANSACCIÓN
    public static function actualizarAula($datos)
    {
        $pdo = null;
        try {
            $pdo = ConexionUtil::conectar();
            $pdo->beginTransaction();

            $sql = "UPDATE aulas SET 
                        nombreAula = :nombreAula,
                        capacidad = :capacidad,
                        idFacultad = :idFacultad
                    WHERE idAula = :id";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $datos['id'], PDO::PARAM_INT);
            $stmt->bindParam(':nombreAula', $datos['nombreAula']);
            $stmt->bindParam(':capacidad', $datos['capacidad'], PDO::PARAM_INT);
            $stmt->bindParam(':idFacultad', $datos['idFacultad'], PDO::PARAM_INT);
            
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
            error_log("Error en actualizarAula: " . $e->getMessage());
            return false;
        }
    }

    // VERIFICAR SI EXISTE AULA (solo lectura)
    public static function existeAula($nombreAula, $idFacultad, $excluirId = null)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT COUNT(*) as total FROM aulas 
                    WHERE nombreAula = :nombreAula AND idFacultad = :idFacultad";
            
            if ($excluirId !== null) {
                $sql .= " AND idAula != :excluirId";
            }
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':nombreAula', $nombreAula);
            $stmt->bindParam(':idFacultad', $idFacultad, PDO::PARAM_INT);
            
            if ($excluirId !== null) {
                $stmt->bindParam(':excluirId', $excluirId, PDO::PARAM_INT);
            }
            
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['total'] > 0;
        } catch (PDOException $e) {
            error_log("Error en existeAula: " . $e->getMessage());
            return false;
        }
    }
}
?>