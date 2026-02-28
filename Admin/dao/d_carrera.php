<?php
require_once __DIR__ . "/../../utilidades/u_conexion.php";
require_once __DIR__ . "/../modelo/m_carrera.php";

class D_Carrera
{
    // OBTENER TODAS LAS CARRERAS
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

    // OBTENER CARRERA POR ID
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

    // OBTENER CARRERAS POR DEPARTAMENTO
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

    // INSERTAR CARRERA
    public static function insertarCarrera($datos)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "INSERT INTO carrera (nombreCarrera, idDepartamento) 
                    VALUES (:nombreCarrera, :idDepartamento)";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':nombreCarrera', $datos['nombreCarrera']);
            $stmt->bindParam(':idDepartamento', $datos['idDepartamento'], PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                return $instanciaConexion->lastInsertId();
            }
            
            return null;
        } catch (PDOException $e) {
            error_log("Error en insertarCarrera: " . $e->getMessage());
            return null;
        }
    }

    // ACTUALIZAR CARRERA
    public static function actualizarCarrera($datos)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "UPDATE carrera SET 
                        nombreCarrera = :nombreCarrera,
                        idDepartamento = :idDepartamento
                    WHERE idCarrera = :id";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':id', $datos['id'], PDO::PARAM_INT);
            $stmt->bindParam(':nombreCarrera', $datos['nombreCarrera']);
            $stmt->bindParam(':idDepartamento', $datos['idDepartamento'], PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en actualizarCarrera: " . $e->getMessage());
            return false;
        }
    }

    // VERIFICAR SI EXISTE CARRERA
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
}
?>