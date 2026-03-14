<?php
require_once __DIR__ . "/../../utilidades/u_conexion.php";
require_once __DIR__ . "/../modelo/m_rolpermiso.php";

class D_RolPermiso
{
    // OBTENER TODAS LAS RELACIONES ROL-PERMISO
    public static function obtenerRolPermisos()
    {
        try {
            $pdo = ConexionUtil::conectar();

            $sql = "SELECT rp.*, r.nombreRol, p.nombrePermiso 
                    FROM rol_permiso rp
                    INNER JOIN rol r ON rp.idRol = r.idRol
                    INNER JOIN permiso p ON rp.idPermiso = p.idPermiso
                    ORDER BY r.nombreRol, p.nombrePermiso";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $relaciones = [];
            
            foreach ($resultados as $fila) {
                $model = new RolPermisoModel();
                $model->hidratarDesdeArray($fila);
                // Guardar datos adicionales en propiedades del modelo si quieres
                $relaciones[] = $model;
            }

            return $relaciones;
        } catch (PDOException $e) {
            error_log("Error en obtenerRolPermisos: " . $e->getMessage());
            return [];
        }
    }

    // OBTENER RELACIÓN POR ID
    public static function obtenerRolPermisoPorId($id)
    {
        try {
            $pdo = ConexionUtil::conectar();

            $sql = "SELECT * FROM rol_permiso WHERE idRolPermiso = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($resultado) {
                $model = new RolPermisoModel();
                return $model->hidratarDesdeArray($resultado);
            }
            
            return null;
        } catch (PDOException $e) {
            error_log("Error en obtenerRolPermisoPorId: " . $e->getMessage());
            return null;
        }
    }

    // OBTENER PERMISOS POR ROL
    public static function obtenerPermisosPorRol($idRol)
    {
        try {
            $pdo = ConexionUtil::conectar();

            $sql = "SELECT p.idPermiso, p.nombrePermiso, p.tabla, p.accion
                    FROM permiso p
                    INNER JOIN rol_permiso rp ON p.idPermiso = rp.idPermiso
                    WHERE rp.idRol = :idRol
                    ORDER BY p.nombrePermiso";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':idRol', $idRol, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error en obtenerPermisosPorRol: " . $e->getMessage());
            return [];
        }
    }

    // OBTENER ROLES POR PERMISO
    public static function obtenerRolesPorPermiso($idPermiso)
    {
        try {
            $pdo = ConexionUtil::conectar();

            $sql = "SELECT r.idRol, r.nombreRol
                    FROM rol r
                    INNER JOIN rol_permiso rp ON r.idRol = rp.idRol
                    WHERE rp.idPermiso = :idPermiso
                    ORDER BY r.nombreRol";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':idPermiso', $idPermiso, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error en obtenerRolesPorPermiso: " . $e->getMessage());
            return [];
        }
    }

    // VERIFICAR SI EXISTE RELACIÓN
    public static function existeRelacion($idRol, $idPermiso, $excluirId = null)
    {
        try {
            $pdo = ConexionUtil::conectar();

            $sql = "SELECT COUNT(*) as total FROM rol_permiso 
                    WHERE idRol = :idRol AND idPermiso = :idPermiso";
            
            if ($excluirId !== null) {
                $sql .= " AND idRolPermiso != :excluirId";
            }
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':idRol', $idRol, PDO::PARAM_INT);
            $stmt->bindParam(':idPermiso', $idPermiso, PDO::PARAM_INT);
            
            if ($excluirId !== null) {
                $stmt->bindParam(':excluirId', $excluirId, PDO::PARAM_INT);
            }
            
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['total'] > 0;
            
        } catch (PDOException $e) {
            error_log("Error en existeRelacion: " . $e->getMessage());
            return false;
        }
    }

    // INSERTAR RELACIÓN ROL-PERMISO CON TRANSACCIÓN
    public static function insertarRolPermiso($datos)
    {
        $pdo = null;
        try {
            $pdo = ConexionUtil::conectar();
            $pdo->beginTransaction();

            // Verificar si ya existe
            if (self::existeRelacion($datos['idRol'], $datos['idPermiso'])) {
                $pdo->rollBack();
                error_log("La relación ya existe");
                return null;
            }

            $sql = "INSERT INTO rol_permiso (idRol, idPermiso) 
                    VALUES (:idRol, :idPermiso)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':idRol', $datos['idRol'], PDO::PARAM_INT);
            $stmt->bindParam(':idPermiso', $datos['idPermiso'], PDO::PARAM_INT);
            
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
            error_log("Error en insertarRolPermiso: " . $e->getMessage());
            return null;
        }
    }

    // ELIMINAR RELACIÓN ROL-PERMISO CON TRANSACCIÓN
    public static function eliminarRolPermiso($id)
    {
        $pdo = null;
        try {
            $pdo = ConexionUtil::conectar();
            $pdo->beginTransaction();

            $sql = "DELETE FROM rol_permiso WHERE idRolPermiso = :id";
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
            error_log("Error en eliminarRolPermiso: " . $e->getMessage());
            return false;
        }
    }

    // ELIMINAR TODAS LAS RELACIONES DE UN ROL CON TRANSACCIÓN
    public static function eliminarRelacionesPorRol($idRol)
    {
        $pdo = null;
        try {
            $pdo = ConexionUtil::conectar();
            $pdo->beginTransaction();

            $sql = "DELETE FROM rol_permiso WHERE idRol = :idRol";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':idRol', $idRol, PDO::PARAM_INT);
            
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
            error_log("Error en eliminarRelacionesPorRol: " . $e->getMessage());
            return false;
        }
    }

    // ELIMINAR TODAS LAS RELACIONES DE UN PERMISO CON TRANSACCIÓN
    public static function eliminarRelacionesPorPermiso($idPermiso)
    {
        $pdo = null;
        try {
            $pdo = ConexionUtil::conectar();
            $pdo->beginTransaction();

            $sql = "DELETE FROM rol_permiso WHERE idPermiso = :idPermiso";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':idPermiso', $idPermiso, PDO::PARAM_INT);
            
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
            error_log("Error en eliminarRelacionesPorPermiso: " . $e->getMessage());
            return false;
        }
    }
}
?>