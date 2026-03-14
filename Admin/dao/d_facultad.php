<?php
require_once __DIR__ . "/../../utilidades/u_conexion.php";
require_once __DIR__ . "/../modelo/m_facultad.php";

class D_Facultad
{
    // OBTENER TODAS LAS FACULTADES (solo lectura)
    public static function obtenerFacultades()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT * FROM facultad ORDER BY nombreFacultad ASC";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $facultades = [];
            
            foreach ($resultados as $fila) {
                $model = new FacultadModel();
                $model->hidratarDesdeArray($fila);
                $facultades[] = $model;
            }

            return $facultades;
        } catch (PDOException $e) {
            error_log("Error en obtenerFacultades: " . $e->getMessage());
            return [];
        }
    }

    // OBTENER FACULTAD POR ID (solo lectura)
    public static function obtenerFacultadPorId($id)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT * FROM facultad WHERE idFacultad = :id";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($resultado) {
                $model = new FacultadModel();
                return $model->hidratarDesdeArray($resultado);
            }
            
            return null;
        } catch (PDOException $e) {
            error_log("Error en obtenerFacultadPorId: " . $e->getMessage());
            return null;
        }
    }

    // OBTENER FACULTAD POR DEPARTAMENTO (solo lectura)
    public static function obtenerFacultadPorDepartamento($idDepartamento)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            
            $sql = "SELECT f.idFacultad, f.nombreFacultad, f.direccionFacultad, f.correo, f.telefono
                    FROM facultad f
                    INNER JOIN departamento d ON f.idFacultad = d.idFacultad
                    WHERE d.idDepartamento = :idDepartamento
                    LIMIT 1";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idDepartamento', $idDepartamento, PDO::PARAM_INT);
            $stmt->execute();
            
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($resultado) {
                $model = new FacultadModel();
                return $model->hidratarDesdeArray($resultado);
            }
            
            return null;
        } catch (PDOException $e) {
            error_log("Error en obtenerFacultadPorDepartamento: " . $e->getMessage());
            return null;
        }
    }

    // INSERTAR FACULTAD CON TRANSACCIÓN
    public static function insertarFacultad($datos)
    {
        $pdo = null;
        try {
            $pdo = ConexionUtil::conectar();
            $pdo->beginTransaction();

            $sql = "INSERT INTO facultad (nombreFacultad, direccionFacultad, correo, telefono) 
                    VALUES (:nombreFacultad, :direccionFacultad, :correo, :telefono)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nombreFacultad', $datos['nombreFacultad']);
            $stmt->bindParam(':direccionFacultad', $datos['direccionFacultad']);
            $stmt->bindParam(':correo', $datos['correo']);
            $stmt->bindParam(':telefono', $datos['telefono']);
            
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
            error_log("Error en insertarFacultad: " . $e->getMessage());
            return null;
        }
    }

    // ACTUALIZAR FACULTAD CON TRANSACCIÓN
    public static function actualizarFacultad($id, $nombre, $direccion, $correo, $telefono)
    {
        $pdo = null;
        try {
            $pdo = ConexionUtil::conectar();
            $pdo->beginTransaction();

            $sql = "UPDATE facultad SET 
                        nombreFacultad = :nombreFacultad,
                        direccionFacultad = :direccionFacultad,
                        correo = :correo,
                        telefono = :telefono
                    WHERE idFacultad = :id";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':nombreFacultad', $nombre);
            $stmt->bindParam(':direccionFacultad', $direccion);
            $stmt->bindParam(':correo', $correo);
            $stmt->bindParam(':telefono', $telefono);
            
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
            error_log("Error en actualizarFacultad: " . $e->getMessage());
            return false;
        }
    }

    // ELIMINAR FACULTAD CON TRANSACCIÓN
    public static function eliminarFacultad($id)
    {
        $pdo = null;
        try {
            $pdo = ConexionUtil::conectar();
            $pdo->beginTransaction();

            $sql = "DELETE FROM facultad WHERE idFacultad = :id";
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
            error_log("Error en eliminarFacultad: " . $e->getMessage());
            return false;
        }
    }

    // VERIFICAR SI EXISTE NOMBRE DE FACULTAD (solo lectura)
    public static function existeNombreFacultad($nombreFacultad, $excluirId = null)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT COUNT(*) as total FROM facultad WHERE nombreFacultad = :nombreFacultad";
            
            if ($excluirId !== null) {
                $sql .= " AND idFacultad != :excluirId";
            }
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':nombreFacultad', $nombreFacultad);
            
            if ($excluirId !== null) {
                $stmt->bindParam(':excluirId', $excluirId, PDO::PARAM_INT);
            }
            
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['total'] > 0;
        } catch (PDOException $e) {
            error_log("Error en existeNombreFacultad: " . $e->getMessage());
            return false;
        }
    }
}
?>