<?php
require_once __DIR__ . "/../../utilidades/u_conexion.php";
require_once __DIR__ . "/../modelo/m_aula.php";

class D_Aula
{
    // OBTENER TODAS LAS AULAS
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

    // OBTENER AULA POR ID
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

    // OBTENER AULAS POR FACULTAD
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

    // INSERTAR AULA
    public static function insertarAula($datos)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "INSERT INTO aulas (nombreAula, capacidad, idFacultad) 
                    VALUES (:nombreAula, :capacidad, :idFacultad)";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':nombreAula', $datos['nombreAula']);
            $stmt->bindParam(':capacidad', $datos['capacidad'], PDO::PARAM_INT);
            $stmt->bindParam(':idFacultad', $datos['idFacultad'], PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                return $instanciaConexion->lastInsertId();
            }
            
            return null;
        } catch (PDOException $e) {
            error_log("Error en insertarAula: " . $e->getMessage());
            return null;
        }
    }

    // ACTUALIZAR AULA
    public static function actualizarAula($datos)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "UPDATE aulas SET 
                        nombreAula = :nombreAula,
                        capacidad = :capacidad,
                        idFacultad = :idFacultad
                    WHERE idAula = :id";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':id', $datos['id'], PDO::PARAM_INT);
            $stmt->bindParam(':nombreAula', $datos['nombreAula']);
            $stmt->bindParam(':capacidad', $datos['capacidad'], PDO::PARAM_INT);
            $stmt->bindParam(':idFacultad', $datos['idFacultad'], PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en actualizarAula: " . $e->getMessage());
            return false;
        }
    }

    // VERIFICAR SI EXISTE AULA
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