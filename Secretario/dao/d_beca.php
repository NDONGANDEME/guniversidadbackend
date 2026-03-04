<?php
require_once __DIR__ . "/../../utilidades/u_conexion.php";
require_once __DIR__ . "/../modelo/m_beca.php";

class D_Beca
{
    // OBTENER TODAS LAS BECAS (incluyendo inactivas)
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

    // OBTENER SOLO BECAS ACTIVAS
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

    // OBTENER BECA POR ID
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

    // INSERTAR BECA
    public static function insertarBeca($datos)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "INSERT INTO becas (institucionBeca, tipoBeca, estado) 
                    VALUES (:institucionBeca, :tipoBeca, :estado)";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':institucionBeca', $datos['institucionBeca']);
            $stmt->bindParam(':tipoBeca', $datos['tipoBeca']);
            $stmt->bindParam(':estado', $datos['estado']);
            
            if ($stmt->execute()) {
                return $instanciaConexion->lastInsertId();
            }
            
            return null;
        } catch (PDOException $e) {
            error_log("Error en insertarBeca: " . $e->getMessage());
            return null;
        }
    }

    // ACTUALIZAR BECA
    public static function actualizarBeca($id, $datos)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "UPDATE becas SET 
                        institucionBeca = :institucionBeca,
                        tipoBeca = :tipoBeca
                    WHERE idBeca = :id";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':institucionBeca', $datos['institucionBeca']);
            $stmt->bindParam(':tipoBeca', $datos['tipoBeca']);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en actualizarBeca: " . $e->getMessage());
            return false;
        }
    }

    // ELIMINAR BECA (soft delete - cambiar estado a 'inactivo')
    public static function eliminarBeca($id)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $estadoInactivo = 'inactivo';

            $sql = "UPDATE becas SET estado = :estado WHERE idBeca = :id";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':estado', $estadoInactivo);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en eliminarBeca: " . $e->getMessage());
            return false;
        }
    }

    // VERIFICAR SI EXISTE BECA
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