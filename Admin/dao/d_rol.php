<?php
require_once __DIR__ . "/../../utilidades/u_conexion.php";
require_once __DIR__ . "/../modelo/m_rol.php";

class D_Rol
{
    // OBTENER TODOS LOS ROLES
    public static function obtenerRoles()
    {
        try {
            $pdo = ConexionUtil::conectar();

            $sql = "SELECT * FROM rol ORDER BY nombreRol ASC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $roles = [];
            
            foreach ($resultados as $fila) {
                $model = new RolModel();
                $model->hidratarDesdeArray($fila);
                $roles[] = $model;
            }

            return $roles;
        } catch (PDOException $e) {
            error_log("Error en obtenerRoles: " . $e->getMessage());
            return [];
        }
    }

    // OBTENER ROL POR ID
    public static function obtenerRolPorId($id)
    {
        try {
            $pdo = ConexionUtil::conectar();

            $sql = "SELECT * FROM rol WHERE idRol = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($resultado) {
                $model = new RolModel();
                return $model->hidratarDesdeArray($resultado);
            }
            
            return null;
        } catch (PDOException $e) {
            error_log("Error en obtenerRolPorId: " . $e->getMessage());
            return null;
        }
    }

    // OBTENER ROL POR NOMBRE
    public static function obtenerRolPorNombre($nombreRol)
    {
        try {
            $pdo = ConexionUtil::conectar();

            $sql = "SELECT * FROM rol WHERE nombreRol = :nombreRol";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nombreRol', $nombreRol);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($resultado) {
                $model = new RolModel();
                return $model->hidratarDesdeArray($resultado);
            }
            
            return null;
        } catch (PDOException $e) {
            error_log("Error en obtenerRolPorNombre: " . $e->getMessage());
            return null;
        }
    }

    // INSERTAR ROL CON TRANSACCIÓN
    public static function insertarRol($datos)
    {
        $pdo = null;
        try {
            $pdo = ConexionUtil::conectar();
            $pdo->beginTransaction();

            $sql = "INSERT INTO rol (nombreRol) VALUES (:nombreRol)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nombreRol', $datos['nombreRol']);
            
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
            error_log("Error en insertarRol: " . $e->getMessage());
            return null;
        }
    }

    // ACTUALIZAR ROL CON TRANSACCIÓN
    public static function actualizarRol($id, $datos)
    {
        $pdo = null;
        try {
            $pdo = ConexionUtil::conectar();
            $pdo->beginTransaction();

            $sql = "UPDATE rol SET nombreRol = :nombreRol WHERE idRol = :id";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':nombreRol', $datos['nombreRol']);
            
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
            error_log("Error en actualizarRol: " . $e->getMessage());
            return false;
        }
    }

    // ELIMINAR ROL CON TRANSACCIÓN
    public static function eliminarRol($id)
    {
        $pdo = null;
        try {
            $pdo = ConexionUtil::conectar();
            $pdo->beginTransaction();

            // Verificar si hay usuarios usando este rol
            $sqlCheck = "SELECT COUNT(*) as total FROM usuarios WHERE idRol = :idRol";
            $stmtCheck = $pdo->prepare($sqlCheck);
            $stmtCheck->bindParam(':idRol', $id, PDO::PARAM_INT);
            $stmtCheck->execute();
            $usuarios = $stmtCheck->fetch(PDO::FETCH_ASSOC);
            
            if ($usuarios['total'] > 0) {
                $pdo->rollBack();
                error_log("No se puede eliminar el rol porque tiene usuarios asignados");
                return false;
            }

            // Eliminar el rol
            $sql = "DELETE FROM rol WHERE idRol = :id";
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
            error_log("Error en eliminarRol: " . $e->getMessage());
            return false;
        }
    }

    // VERIFICAR SI EXISTE ROL POR NOMBRE
    public static function existeRolPorNombre($nombreRol, $excluirId = null)
    {
        try {
            $pdo = ConexionUtil::conectar();

            $sql = "SELECT COUNT(*) as total FROM rol WHERE nombreRol = :nombreRol";
            
            if ($excluirId !== null) {
                $sql .= " AND idRol != :excluirId";
            }
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nombreRol', $nombreRol);
            
            if ($excluirId !== null) {
                $stmt->bindParam(':excluirId', $excluirId, PDO::PARAM_INT);
            }
            
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['total'] > 0;
            
        } catch (PDOException $e) {
            error_log("Error en existeRolPorNombre: " . $e->getMessage());
            return false;
        }
    }
}
?>