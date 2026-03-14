<?php
require_once __DIR__ . "/../../utilidades/u_conexion.php";
require_once __DIR__ . "/../modelo/m_horario.php";

class D_Horario
{
    // OBTENER TODOS LOS HORARIOS (solo lectura)
    public static function obtenerHorarios()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT * FROM horario ORDER BY nombre ASC";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $horarios = [];
            
            foreach ($resultados as $fila) {
                $model = new HorarioModel();
                $model->hidratarDesdeArray($fila);
                $horarios[] = $model;
            }

            return $horarios;
        } catch (PDOException $e) {
            error_log("Error en obtenerHorarios: " . $e->getMessage());
            return [];
        }
    }

    // OBTENER HORARIO POR ID (solo lectura)
    public static function obtenerHorarioPorId($id)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT * FROM horario WHERE idHorario = :id";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($resultado) {
                $model = new HorarioModel();
                return $model->hidratarDesdeArray($resultado);
            }
            
            return null;
        } catch (PDOException $e) {
            error_log("Error en obtenerHorarioPorId: " . $e->getMessage());
            return null;
        }
    }

    // INSERTAR HORARIO CON TRANSACCIÓN
    public static function insertarHorario($datos)
    {
        $pdo = null;
        try {
            $pdo = ConexionUtil::conectar();
            $pdo->beginTransaction();

            $sql = "INSERT INTO horario (nombre) VALUES (:nombre)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nombre', $datos['nombre']);
            
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
            error_log("Error en insertarHorario: " . $e->getMessage());
            return null;
        }
    }

    // ACTUALIZAR HORARIO CON TRANSACCIÓN
    public static function actualizarHorario($id, $datos)
    {
        $pdo = null;
        try {
            $pdo = ConexionUtil::conectar();
            $pdo->beginTransaction();

            $sql = "UPDATE horario SET nombre = :nombre WHERE idHorario = :id";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':nombre', $datos['nombre']);
            
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
            error_log("Error en actualizarHorario: " . $e->getMessage());
            return false;
        }
    }

    // ELIMINAR HORARIO CON TRANSACCIÓN
    public static function eliminarHorario($id)
    {
        $pdo = null;
        try {
            $pdo = ConexionUtil::conectar();
            $pdo->beginTransaction();

            $sql = "DELETE FROM horario WHERE idHorario = :id";
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
            error_log("Error en eliminarHorario: " . $e->getMessage());
            return false;
        }
    }

    // VERIFICAR SI EXISTE HORARIO POR NOMBRE (solo lectura)
    public static function existeHorarioPorNombre($nombre, $excluirId = null)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT COUNT(*) as total FROM horario WHERE nombre = :nombre";
            
            if ($excluirId !== null) {
                $sql .= " AND idHorario != :excluirId";
            }
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':nombre', $nombre);
            
            if ($excluirId !== null) {
                $stmt->bindParam(':excluirId', $excluirId, PDO::PARAM_INT);
            }
            
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['total'] > 0;
        } catch (PDOException $e) {
            error_log("Error en existeHorarioPorNombre: " . $e->getMessage());
            return false;
        }
    }
}
?>