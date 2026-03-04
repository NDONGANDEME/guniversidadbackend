<?php
require_once __DIR__ . "/../../utilidades/u_conexion.php";
require_once __DIR__ . "/../modelo/m_facultad.php";

class D_Facultad
{
    // OBTENER TODAS LAS FACULTADES
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

    // OBTENER FACULTAD POR ID
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

    // ========== NUEVA FUNCIÓN ==========
    // OBTENER FACULTAD POR DEPARTAMENTO
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
    // ===================================

    // INSERTAR FACULTAD
    public static function insertarFacultad($datos)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "INSERT INTO facultad (nombreFacultad, direccionFacultad, correo, telefono) 
                    VALUES (:nombreFacultad, :direccionFacultad, :correo, :telefono)";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':nombreFacultad', $datos['nombreFacultad']);
            $stmt->bindParam(':direccionFacultad', $datos['direccionFacultad']);
            $stmt->bindParam(':correo', $datos['correo']);
            $stmt->bindParam(':telefono', $datos['telefono']);
            
            if ($stmt->execute()) {
                return $instanciaConexion->lastInsertId();
            }
            
            return null;
        } catch (PDOException $e) {
            error_log("Error en insertarFacultad: " . $e->getMessage());
            return null;
        }
    }

    // ACTUALIZAR FACULTAD
    public static function actualizarFacultad($id, $nombre, $direccion, $correo, $telefono)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "UPDATE facultad SET 
                        nombreFacultad = :nombreFacultad,
                        direccionFacultad = :direccionFacultad,
                        correo = :correo,
                        telefono = :telefono
                    WHERE idFacultad = :id";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':nombreFacultad', $nombre);
            $stmt->bindParam(':direccionFacultad', $direccion);
            $stmt->bindParam(':correo', $correo);
            $stmt->bindParam(':telefono', $telefono);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en actualizarFacultad: " . $e->getMessage());
            return false;
        }
    }

    // ELIMINAR FACULTAD (físicamente ya que no tiene campo estado)
    public static function eliminarFacultad($id)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "DELETE FROM facultad WHERE idFacultad = :id";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en eliminarFacultad: " . $e->getMessage());
            return false;
        }
    }

    // VERIFICAR SI EXISTE NOMBRE DE FACULTAD
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