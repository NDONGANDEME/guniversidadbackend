<?php
require_once __DIR__ . "/../../utilidades/u_conexion.php";
require_once __DIR__ . "/../modelo/m_carrera.php";

class D_Carrera
{
    // OBTENER TODAS LAS CARRERAS (solo lectura)
    public static function obtenerCarreras()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT c.*, d.nombreDepartamento 
                    FROM carrera c
                    LEFT JOIN departamento d ON c.idDepartamento = d.idDepartamento
                    ORDER BY c.nombreCarrera ASC";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $carreras = [];
            
            foreach ($resultados as $fila) {
                $model = new CarreraModel();
                $model->hidratarDesdeArray($fila);
                if (isset($fila['nombreDepartamento'])) {
                    $model->nombreDepartamento = $fila['nombreDepartamento'];
                }
                $carreras[] = $model;
            }

            return $carreras;
        } catch (PDOException $e) {
            error_log("Error en obtenerCarreras: " . $e->getMessage());
            return [];
        }
    }

    // OBTENER CARRERA POR ID (solo lectura)
    public static function obtenerCarreraPorId($id)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT * FROM carrera WHERE idCarrera = :id";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($resultado) {
                $model = new CarreraModel();
                return $model->hidratarDesdeArray($resultado);
            }
            
            return null;
        } catch (PDOException $e) {
            error_log("Error en obtenerCarreraPorId: " . $e->getMessage());
            return null;
        }
    }

    // OBTENER CARRERAS POR DEPARTAMENTO (solo lectura)
    public static function obtenerCarrerasPorDepartamento($idDepartamento)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT * FROM carrera WHERE idDepartamento = :idDepartamento ORDER BY nombreCarrera ASC";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idDepartamento', $idDepartamento, PDO::PARAM_INT);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $carreras = [];
            
            foreach ($resultados as $fila) {
                $model = new CarreraModel();
                $model->hidratarDesdeArray($fila);
                $carreras[] = $model;
            }

            return $carreras;
        } catch (PDOException $e) {
            error_log("Error en obtenerCarrerasPorDepartamento: " . $e->getMessage());
            return [];
        }
    }

    // INSERTAR CARRERA CON TRANSACCIÓN
    public static function insertarCarrera($datos)
    {
        $pdo = null;
        try {
            $pdo = ConexionUtil::conectar();
            $pdo->beginTransaction();

            $sql = "INSERT INTO carrera (nombreCarrera, idDepartamento, estado) 
                    VALUES (:nombreCarrera, :idDepartamento, :estado)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nombreCarrera', $datos['nombreCarrera']);
            $stmt->bindParam(':idDepartamento', $datos['idDepartamento'], PDO::PARAM_INT);
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
            error_log("Error en insertarCarrera: " . $e->getMessage());
            return null;
        }
    }

    // ACTUALIZAR CARRERA CON TRANSACCIÓN
    public static function actualizarCarrera($datos)
    {
        $pdo = null;
        try {
            $pdo = ConexionUtil::conectar();
            $pdo->beginTransaction();

            $sql = "UPDATE carrera SET 
                        nombreCarrera = :nombreCarrera,
                        idDepartamento = :idDepartamento,
                        estado = :estado
                    WHERE idCarrera = :id";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $datos['idCarrera'], PDO::PARAM_INT);
            $stmt->bindParam(':nombreCarrera', $datos['nombreCarrera']);
            $stmt->bindParam(':idDepartamento', $datos['idDepartamento'], PDO::PARAM_INT);
            $stmt->bindParam(':estado', $datos['estado']);
            
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
            error_log("Error en actualizarCarrera: " . $e->getMessage());
            return false;
        }
    }

    // CAMBIAR ESTADO DE CARRERA CON TRANSACCIÓN
    public static function cambiarEstadoCarrera($id, $nuevoEstado)
    {
        $pdo = null;
        try {
            $pdo = ConexionUtil::conectar();
            $pdo->beginTransaction();

            $sql = "UPDATE carrera SET estado = :estado WHERE idCarrera = :id";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':estado', $nuevoEstado);
            
            $resultado = $stmt->execute();
            
            if ($resultado && $stmt->rowCount() > 0) {
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
            error_log("Error en cambiarEstadoCarrera: " . $e->getMessage());
            return false;
        }
    }

    // ELIMINAR CARRERA CON TRANSACCIÓN
    public static function eliminarCarrera($id)
    {
        $pdo = null;
        try {
            $pdo = ConexionUtil::conectar();
            $pdo->beginTransaction();

            // Verificar si la carrera tiene asignaturas asociadas
            $sqlVerificar = "SELECT COUNT(*) as total FROM asignatura WHERE idCarrera = :id";
            $stmtVerificar = $pdo->prepare($sqlVerificar);
            $stmtVerificar->bindParam(':id', $id, PDO::PARAM_INT);
            $stmtVerificar->execute();
            $resultado = $stmtVerificar->fetch(PDO::FETCH_ASSOC);
            
            if ($resultado['total'] > 0) {
                $pdo->rollBack();
                return false; // No se puede eliminar porque tiene asignaturas asociadas
            }

            // Si no tiene asignaturas, proceder a eliminar
            $sql = "DELETE FROM carrera WHERE idCarrera = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                if ($stmt->rowCount() > 0) {
                    $pdo->commit();
                    return true;
                } else {
                    $pdo->rollBack();
                    return false;
                }
            } else {
                $pdo->rollBack();
                return false;
            }
            
        } catch (PDOException $e) {
            if ($pdo) {
                $pdo->rollBack();
            }
            error_log("Error en eliminarCarrera: " . $e->getMessage());
            return false;
        }
    }

    // VERIFICAR SI EXISTE CARRERA (solo lectura)
    public static function existeCarrera($nombreCarrera, $excluirId = null)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT COUNT(*) as total FROM carrera WHERE nombreCarrera = :nombreCarrera";
            
            if ($excluirId !== null) {
                $sql .= " AND idCarrera != :excluirId";
            }
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':nombreCarrera', $nombreCarrera);
            
            if ($excluirId !== null) {
                $stmt->bindParam(':excluirId', $excluirId, PDO::PARAM_INT);
            }
            
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['total'] > 0;
        } catch (PDOException $e) {
            error_log("Error en existeCarrera: " . $e->getMessage());
            return false;
        }
    }

    // VERIFICAR SI LA CARRERA TIENE ASIGNATURAS ASOCIADAS
    public static function tieneAsignaturasAsociadas($id)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT COUNT(*) as total FROM asignatura WHERE idCarrera = :id";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['total'] > 0;
        } catch (PDOException $e) {
            error_log("Error en tieneAsignaturasAsociadas: " . $e->getMessage());
            return false;
        }
    }
}
?>