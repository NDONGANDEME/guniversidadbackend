<?php
require_once __DIR__ . "/../../utilidades/u_conexion.php";
require_once __DIR__ . "/../modelo/m_horario.php";

class D_Horario
{
    // OBTENER TODOS LOS HORARIOS
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

    // OBTENER HORARIO POR ID
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

    // INSERTAR HORARIO
    public static function insertarHorario($datos)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "INSERT INTO horario (nombre) VALUES (:nombre)";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':nombre', $datos['nombre']);
            
            if ($stmt->execute()) {
                return $instanciaConexion->lastInsertId();
            }
            
            return null;
        } catch (PDOException $e) {
            error_log("Error en insertarHorario: " . $e->getMessage());
            return null;
        }
    }

    // ACTUALIZAR HORARIO
    public static function actualizarHorario($id, $datos)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "UPDATE horario SET nombre = :nombre WHERE idHorario = :id";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':nombre', $datos['nombre']);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en actualizarHorario: " . $e->getMessage());
            return false;
        }
    }

    // ELIMINAR HORARIO
    public static function eliminarHorario($id)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "DELETE FROM horario WHERE idHorario = :id";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en eliminarHorario: " . $e->getMessage());
            return false;
        }
    }

    // VERIFICAR SI EXISTE HORARIO POR NOMBRE
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