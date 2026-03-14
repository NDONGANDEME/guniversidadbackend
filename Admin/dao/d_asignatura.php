<?php
require_once __DIR__ . "/../../utilidades/u_conexion.php";
require_once __DIR__ . "/../modelo/m_asignatura.php";

class D_Asignatura
{
    // OBTENER TODAS LAS ASIGNATURAS (solo lectura)
    public static function obtenerAsignaturas()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT a.*, f.nombreFacultad
                    FROM asignaturas a
                    LEFT JOIN facultad f ON a.idFacultad = f.idFacultad
                    ORDER BY a.nombreAsignatura ASC";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $asignaturas = [];
            
            foreach ($resultados as $fila) {
                $model = new AsignaturaModel();
                $model->hidratarDesdeArray($fila);
                $asignaturas[] = $model;
            }

            return $asignaturas;
        } catch (PDOException $e) {
            error_log("Error en obtenerAsignaturas: " . $e->getMessage());
            return [];
        }
    }

    // OBTENER ASIGNATURA POR ID (solo lectura)
    public static function obtenerAsignaturaPorId($id)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT * FROM asignaturas WHERE idAsignatura = :id";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($resultado) {
                $model = new AsignaturaModel();
                return $model->hidratarDesdeArray($resultado);
            }
            
            return null;
        } catch (PDOException $e) {
            error_log("Error en obtenerAsignaturaPorId: " . $e->getMessage());
            return null;
        }
    }

    // OBTENER ASIGNATURAS POR FACULTAD (solo lectura)
    public static function obtenerAsignaturasPorFacultad($idFacultad)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT DISTINCT a.* FROM asignaturas a WHERE a.idFacultad = :idFacultad ORDER BY a.nombreAsignatura ASC";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idFacultad', $idFacultad, PDO::PARAM_INT);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $asignaturas = [];
            
            foreach ($resultados as $fila) {
                $model = new AsignaturaModel();
                $model->hidratarDesdeArray($fila);
                $asignaturas[] = $model;
            }

            return $asignaturas;
        } catch (PDOException $e) {
            error_log("Error en obtenerAsignaturasPorFacultad: " . $e->getMessage());
            return [];
        }
    }

    // INSERTAR ASIGNATURA CON TRANSACCIÓN
    public static function insertarAsignatura($datos)
    {
        $pdo = null;
        try {
            $pdo = ConexionUtil::conectar();
            $pdo->beginTransaction();

            $sql = "INSERT INTO asignaturas (codigoAsignatura, nombreAsignatura, descripcion, idFacultad) 
                    VALUES (:codigoAsignatura, :nombreAsignatura, :descripcion, :idFacultad)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':codigoAsignatura', $datos['codigoAsignatura']);
            $stmt->bindParam(':nombreAsignatura', $datos['nombreAsignatura']);
            $stmt->bindParam(':descripcion', $datos['descripcion']);
            $stmt->bindParam(':idFacultad', $datos['idFacultad']);
            
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
            error_log("Error en insertarAsignatura: " . $e->getMessage());
            return null;
        }
    }

    // ACTUALIZAR ASIGNATURA CON TRANSACCIÓN
    public static function actualizarAsignatura($datos)
    {
        $pdo = null;
        try {
            $pdo = ConexionUtil::conectar();
            $pdo->beginTransaction();

            $sql = "UPDATE asignaturas SET 
                        codigoAsignatura = :codigoAsignatura,
                        nombreAsignatura = :nombreAsignatura,
                        descripcion = :descripcion
                    WHERE idAsignatura = :id";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $datos['id'], PDO::PARAM_INT);
            $stmt->bindParam(':codigoAsignatura', $datos['codigoAsignatura']);
            $stmt->bindParam(':nombreAsignatura', $datos['nombreAsignatura']);
            $stmt->bindParam(':descripcion', $datos['descripcion']);
            
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
            error_log("Error en actualizarAsignatura: " . $e->getMessage());
            return false;
        }
    }

    // VERIFICAR SI EXISTE ASIGNATURA POR CÓDIGO (solo lectura)
    public static function existeAsignaturaPorCodigo($codigoAsignatura, $excluirId = null)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT COUNT(*) as total FROM asignaturas WHERE codigoAsignatura = :codigoAsignatura";
            
            if ($excluirId !== null) {
                $sql .= " AND idAsignatura != :excluirId";
            }
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':codigoAsignatura', $codigoAsignatura);
            
            if ($excluirId !== null) {
                $stmt->bindParam(':excluirId', $excluirId, PDO::PARAM_INT);
            }
            
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['total'] > 0;
        } catch (PDOException $e) {
            error_log("Error en existeAsignaturaPorCodigo: " . $e->getMessage());
            return false;
        }
    }

    // VERIFICAR SI EXISTE ASIGNATURA POR NOMBRE (solo lectura)
    public static function existeAsignaturaPorNombre($nombreAsignatura, $excluirId = null)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT COUNT(*) as total FROM asignaturas WHERE nombreAsignatura = :nombreAsignatura";
            
            if ($excluirId !== null) {
                $sql .= " AND idAsignatura != :excluirId";
            }
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':nombreAsignatura', $nombreAsignatura);
            
            if ($excluirId !== null) {
                $stmt->bindParam(':excluirId', $excluirId, PDO::PARAM_INT);
            }
            
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['total'] > 0;
        } catch (PDOException $e) {
            error_log("Error en existeAsignaturaPorNombre: " . $e->getMessage());
            return false;
        }
    }
}
?>