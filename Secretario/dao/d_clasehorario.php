<?php
require_once __DIR__ . "/../../utilidades/u_conexion.php";
require_once __DIR__ . "/../modelo/m_clase_horario.php";

class D_ClaseHorario
{
    // OBTENER TODOS LOS CLASE HORARIOS (solo lectura)
    public static function obtenerClaseHorarios()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT ch.*, h.nombre as nombreHorario
                    FROM clase_horario ch
                    INNER JOIN horario h ON ch.idHorario = h.idHorario
                    ORDER BY h.nombre";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $registros = [];
            
            foreach ($resultados as $fila) {
                $model = new ClaseHorarioModel();
                $model->hidratarDesdeArray($fila);
                if (isset($fila['nombreHorario'])) {
                    $model->nombreHorario = $fila['nombreHorario'];
                }
                $registros[] = $model;
            }

            return $registros;
        } catch (PDOException $e) {
            error_log("Error en obtenerClaseHorarios: " . $e->getMessage());
            return [];
        }
    }

    // OBTENER HORARIOS POR CLASE (solo lectura)
    public static function obtenerHorariosPorClase($idClase)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT ch.*, h.nombre as nombreHorario
                    FROM clase_horario ch
                    INNER JOIN horario h ON ch.idHorario = h.idHorario
                    WHERE ch.idClase = :idClase
                    ORDER BY h.nombre";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idClase', $idClase, PDO::PARAM_INT);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $horarios = [];
            
            foreach ($resultados as $fila) {
                $model = new ClaseHorarioModel();
                $model->hidratarDesdeArray($fila);
                if (isset($fila['nombreHorario'])) {
                    $model->nombreHorario = $fila['nombreHorario'];
                }
                $horarios[] = $model;
            }

            return $horarios;
        } catch (PDOException $e) {
            error_log("Error en obtenerHorariosPorClase: " . $e->getMessage());
            return [];
        }
    }

    // OBTENER CLASE HORARIO POR ID (solo lectura)
    public static function obtenerClaseHorarioPorId($id)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT ch.*, h.nombre as nombreHorario
                    FROM clase_horario ch
                    INNER JOIN horario h ON ch.idHorario = h.idHorario
                    WHERE ch.idClaseHorario = :id";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($resultado) {
                $model = new ClaseHorarioModel();
                $model->hidratarDesdeArray($resultado);
                if (isset($resultado['nombreHorario'])) {
                    $model->nombreHorario = $resultado['nombreHorario'];
                }
                return $model;
            }
            
            return null;
        } catch (PDOException $e) {
            error_log("Error en obtenerClaseHorarioPorId: " . $e->getMessage());
            return null;
        }
    }

    // INSERTAR CLASE HORARIO CON TRANSACCIÓN
    public static function insertarClaseHorario($datos)
    {
        $pdo = null;
        try {
            $pdo = ConexionUtil::conectar();
            $pdo->beginTransaction();

            $sql = "INSERT INTO clase_horario (idClase, idHorario) 
                    VALUES (:idClase, :idHorario)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':idClase', $datos['idClase'], PDO::PARAM_INT);
            $stmt->bindParam(':idHorario', $datos['idHorario'], PDO::PARAM_INT);
            
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
            error_log("Error en insertarClaseHorario: " . $e->getMessage());
            return null;
        }
    }

    // ACTUALIZAR CLASE HORARIO CON TRANSACCIÓN
    public static function actualizarClaseHorario($id, $datos)
    {
        $pdo = null;
        try {
            $pdo = ConexionUtil::conectar();
            $pdo->beginTransaction();

            $sql = "UPDATE clase_horario SET 
                        idHorario = :idHorario
                    WHERE idClaseHorario = :id";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':idHorario', $datos['idHorario'], PDO::PARAM_INT);
            
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
            error_log("Error en actualizarClaseHorario: " . $e->getMessage());
            return false;
        }
    }

    // ELIMINAR CLASE HORARIO CON TRANSACCIÓN
    public static function eliminarClaseHorario($id)
    {
        $pdo = null;
        try {
            $pdo = ConexionUtil::conectar();
            $pdo->beginTransaction();

            $sql = "DELETE FROM clase_horario WHERE idClaseHorario = :id";
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
            error_log("Error en eliminarClaseHorario: " . $e->getMessage());
            return false;
        }
    }

    // ELIMINAR HORARIOS POR CLASE CON TRANSACCIÓN
    public static function eliminarHorariosPorClase($idClase)
    {
        $pdo = null;
        try {
            $pdo = ConexionUtil::conectar();
            $pdo->beginTransaction();

            $sql = "DELETE FROM clase_horario WHERE idClase = :idClase";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':idClase', $idClase, PDO::PARAM_INT);
            
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
            error_log("Error en eliminarHorariosPorClase: " . $e->getMessage());
            return false;
        }
    }

    // VERIFICAR SI EXISTE ASIGNACIÓN (solo lectura)
    public static function existeAsignacion($idClase, $idHorario, $excluirId = null)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT COUNT(*) as total FROM clase_horario 
                    WHERE idClase = :idClase AND idHorario = :idHorario";
            
            if ($excluirId !== null) {
                $sql .= " AND idClaseHorario != :excluirId";
            }
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idClase', $idClase, PDO::PARAM_INT);
            $stmt->bindParam(':idHorario', $idHorario, PDO::PARAM_INT);
            
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
}
?>