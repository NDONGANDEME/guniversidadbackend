<?php
require_once __DIR__ . "/../../utilidades/u_conexion.php";
require_once __DIR__ . "/../modelo/m_beca.php";

class D_Beca
{
    // OBTENER TODAS LAS BECAS (solo lectura)
    public static function obtenerBecas()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT * FROM becas ORDER BY institucionBeca ASC";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $becas = [];
            
            foreach ($resultados as $fila) {
                $model = new BecaModel();
                $model->hidratarDesdeArray($fila);
                $becas[] = $model;
            }

            return $becas;
        } catch (PDOException $e) {
            error_log("Error en obtenerBecas: " . $e->getMessage());
            return [];
        }
    }

    // OBTENER SOLO BECAS ACTIVAS (solo lectura)
    public static function obtenerBecasActivas()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT * FROM becas WHERE estado = 'activo' ORDER BY institucionBeca ASC";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $becas = [];
            
            foreach ($resultados as $fila) {
                $model = new BecaModel();
                $model->hidratarDesdeArray($fila);
                $becas[] = $model;
            }

            return $becas;
        } catch (PDOException $e) {
            error_log("Error en obtenerBecasActivas: " . $e->getMessage());
            return [];
        }
    }

    // OBTENER BECA POR ID (solo lectura)
    public static function obtenerBecaPorId($id)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT * FROM becas WHERE idBeca = :id";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($resultado) {
                $model = new BecaModel();
                return $model->hidratarDesdeArray($resultado);
            }
            
            return null;
        } catch (PDOException $e) {
            error_log("Error en obtenerBecaPorId: " . $e->getMessage());
            return null;
        }
    }

    // INSERTAR BECA CON TRANSACCIÓN
    public static function insertarBeca($datos)
    {
        $pdo = null;
        try {
            $pdo = ConexionUtil::conectar();
            $pdo->beginTransaction();

            $sql = "INSERT INTO becas (institucionBeca, tipoBeca, estado) 
                    VALUES (:institucionBeca, :tipoBeca, :estado)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':institucionBeca', $datos['institucionBeca']);
            $stmt->bindParam(':tipoBeca', $datos['tipoBeca']);
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
            error_log("Error en insertarBeca: " . $e->getMessage());
            return null;
        }
    }

    // ACTUALIZAR BECA CON TRANSACCIÓN
    public static function actualizarBeca($id, $datos)
    {
        $pdo = null;
        try {
            $pdo = ConexionUtil::conectar();
            $pdo->beginTransaction();

            $sql = "UPDATE becas SET 
                        institucionBeca = :institucionBeca,
                        tipoBeca = :tipoBeca
                    WHERE idBeca = :id";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':institucionBeca', $datos['institucionBeca']);
            $stmt->bindParam(':tipoBeca', $datos['tipoBeca']);
            
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
            error_log("Error en actualizarBeca: " . $e->getMessage());
            return false;
        }
    }

    // ELIMINAR BECA (soft delete) CON TRANSACCIÓN
    public static function eliminarBeca($id)
    {
        $pdo = null;
        try {
            $pdo = ConexionUtil::conectar();
            $pdo->beginTransaction();
            
            $estadoInactivo = 'inactivo';

            $sql = "UPDATE becas SET estado = :estado WHERE idBeca = :id";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':estado', $estadoInactivo);
            
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
            error_log("Error en eliminarBeca: " . $e->getMessage());
            return false;
        }
    }

    // VERIFICAR SI EXISTE BECA (solo lectura)
    public static function existeBeca($institucionBeca, $tipoBeca, $excluirId = null)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT COUNT(*) as total FROM becas 
                    WHERE institucionBeca = :institucionBeca AND tipoBeca = :tipoBeca";
            
            if ($excluirId !== null) {
                $sql .= " AND idBeca != :excluirId";
            }
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':institucionBeca', $institucionBeca);
            $stmt->bindParam(':tipoBeca', $tipoBeca);
            
            if ($excluirId !== null) {
                $stmt->bindParam(':excluirId', $excluirId, PDO::PARAM_INT);
            }
            
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['total'] > 0;
        } catch (PDOException $e) {
            error_log("Error en existeBeca: " . $e->getMessage());
            return false;
        }
    }
}
?>