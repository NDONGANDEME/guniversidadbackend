<?php
require_once __DIR__ . "/../../utilidades/u_conexion.php";
require_once __DIR__ . "/../modelo/m_departamento.php";

class D_Departamento
{
    // OBTENER TODOS LOS DEPARTAMENTOS
    public static function obtenerDepartamentos()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT d.*, f.nombreFacultad 
                    FROM departamento d
                    LEFT JOIN facultad f ON d.idFacultad = f.idFacultad
                    ORDER BY d.nombreDepartamento ASC";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $departamentos = [];
            
            foreach ($resultados as $fila) {
                $model = new DepartamentoModel();
                $model->hidratarDesdeArray($fila);
                // Añadir nombre de facultad si viene en la consulta
                if (isset($fila['nombreFacultad'])) {
                    $model->nombreFacultad = $fila['nombreFacultad'];
                }
                $departamentos[] = $model;
            }

            return $departamentos;
        } catch (PDOException $e) {
            error_log("Error en obtenerDepartamentos: " . $e->getMessage());
            return [];
        }
    }

    // OBTENER DEPARTAMENTO POR ID
    public static function obtenerDepartamentoPorId($id)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT * FROM departamento WHERE idDepartamento = :id";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($resultado) {
                $model = new DepartamentoModel();
                return $model->hidratarDesdeArray($resultado);
            }
            
            return null;
        } catch (PDOException $e) {
            error_log("Error en obtenerDepartamentoPorId: " . $e->getMessage());
            return null;
        }
    }

    // OBTENER DEPARTAMENTOS POR FACULTAD
    public static function obtenerDepartamentosPorFacultad($idFacultad)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT * FROM departamento WHERE idFacultad = :idFacultad ORDER BY nombreDepartamento ASC";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idFacultad', $idFacultad, PDO::PARAM_INT);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $departamentos = [];
            
            foreach ($resultados as $fila) {
                $model = new DepartamentoModel();
                $model->hidratarDesdeArray($fila);
                $departamentos[] = $model;
            }

            return $departamentos;
        } catch (PDOException $e) {
            error_log("Error en obtenerDepartamentosPorFacultad: " . $e->getMessage());
            return [];
        }
    }

    // INSERTAR DEPARTAMENTO
    public static function insertarDepartamento($datos)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "INSERT INTO departamento (nombreDepartamento, idFacultad) 
                    VALUES (:nombreDepartamento, :idFacultad)";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':nombreDepartamento', $datos['nombreDepartamento']);
            $stmt->bindParam(':idFacultad', $datos['idFacultad'], PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                return $instanciaConexion->lastInsertId();
            }
            
            return null;
        } catch (PDOException $e) {
            error_log("Error en insertarDepartamento: " . $e->getMessage());
            return null;
        }
    }

    // ACTUALIZAR DEPARTAMENTO
    public static function actualizarDepartamento($datos)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "UPDATE departamento SET 
                        nombreDepartamento = :nombreDepartamento,
                        idFacultad = :idFacultad
                    WHERE idDepartamento = :id";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':id', $datos['id'], PDO::PARAM_INT);
            $stmt->bindParam(':nombreDepartamento', $datos['nombreDepartamento']);
            $stmt->bindParam(':idFacultad', $datos['idFacultad'], PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en actualizarDepartamento: " . $e->getMessage());
            return false;
        }
    }

    // VERIFICAR SI EXISTE DEPARTAMENTO
    public static function existeDepartamento($nombreDepartamento, $excluirId = null)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT COUNT(*) as total FROM departamento WHERE nombreDepartamento = :nombreDepartamento";
            
            if ($excluirId !== null) {
                $sql .= " AND idDepartamento != :excluirId";
            }
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':nombreDepartamento', $nombreDepartamento);
            
            if ($excluirId !== null) {
                $stmt->bindParam(':excluirId', $excluirId, PDO::PARAM_INT);
            }
            
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['total'] > 0;
        } catch (PDOException $e) {
            error_log("Error en existeDepartamento: " . $e->getMessage());
            return false;
        }
    }
}
?>