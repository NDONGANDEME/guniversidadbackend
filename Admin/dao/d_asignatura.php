<?php
require_once __DIR__ . "/../../utilidades/u_conexion.php";
require_once __DIR__ . "/../modelo/m_asignatura.php";

class D_Asignatura
{
    // OBTENER TODAS LAS ASIGNATURAS
    public static function obtenerAsignaturas()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT * FROM asignaturas ORDER BY nombreAsignatura ASC";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $asignaturas = [];
            
            foreach ($resultados as $fila) {
                $model = new AsignaturaModel();
                $model->hidratarDesdeArray($fila);
                $asignaturas[] = $model;
            }

            return $asignaturas;
        } catch (PDOException $e) {
            error_log("Error en obtenerAsignaturas: " . $e->getMessage());
            return [];
        }
    }

    // OBTENER ASIGNATURA POR ID
    public static function obtenerAsignaturaPorId($id)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT * FROM asignaturas WHERE idAsignatura = :id";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($resultado) {
                $model = new AsignaturaModel();
                return $model->hidratarDesdeArray($resultado);
            }
            
            return null;
        } catch (PDOException $e) {
            error_log("Error en obtenerAsignaturaPorId: " . $e->getMessage());
            return null;
        }
    }

    // OBTENER ASIGNATURAS POR FACULTAD (a través de plan_curso_asignatura)
    public static function obtenerAsignaturasPorFacultad($idFacultad)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT DISTINCT a.* 
                    FROM asignaturas a
                    INNER JOIN plan_curso_asignatura pca ON a.idAsignatura = pca.idAsignatura
                    INNER JOIN planestudio pe ON pca.idPlanEstudio = pe.idPlanEstudio
                    INNER JOIN carrera c ON pe.idCarrera = c.idCarrera
                    INNER JOIN departamento d ON c.idDepartamento = d.idDepartamento
                    WHERE d.idFacultad = :idFacultad
                    ORDER BY a.nombreAsignatura ASC";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idFacultad', $idFacultad, PDO::PARAM_INT);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $asignaturas = [];
            
            foreach ($resultados as $fila) {
                $model = new AsignaturaModel();
                $model->hidratarDesdeArray($fila);
                $asignaturas[] = $model;
            }

            return $asignaturas;
        } catch (PDOException $e) {
            error_log("Error en obtenerAsignaturasPorFacultad: " . $e->getMessage());
            return [];
        }
    }

    // INSERTAR ASIGNATURA
    public static function insertarAsignatura($datos)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "INSERT INTO asignaturas (codigoAsignatura, nombreAsignatura, descripcion) 
                    VALUES (:codigoAsignatura, :nombreAsignatura, :descripcion)";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':codigoAsignatura', $datos['codigoAsignatura']);
            $stmt->bindParam(':nombreAsignatura', $datos['nombreAsignatura']);
            $stmt->bindParam(':descripcion', $datos['descripcion']);
            
            if ($stmt->execute()) {
                return $instanciaConexion->lastInsertId();
            }
            
            return null;
        } catch (PDOException $e) {
            error_log("Error en insertarAsignatura: " . $e->getMessage());
            return null;
        }
    }

    // ACTUALIZAR ASIGNATURA
    public static function actualizarAsignatura($datos)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "UPDATE asignaturas SET 
                        codigoAsignatura = :codigoAsignatura,
                        nombreAsignatura = :nombreAsignatura,
                        descripcion = :descripcion
                    WHERE idAsignatura = :id";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':id', $datos['id'], PDO::PARAM_INT);
            $stmt->bindParam(':codigoAsignatura', $datos['codigoAsignatura']);
            $stmt->bindParam(':nombreAsignatura', $datos['nombreAsignatura']);
            $stmt->bindParam(':descripcion', $datos['descripcion']);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en actualizarAsignatura: " . $e->getMessage());
            return false;
        }
    }

    // VERIFICAR SI EXISTE ASIGNATURA POR CÓDIGO
    public static function existeAsignaturaPorCodigo($codigoAsignatura, $excluirId = null)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT COUNT(*) as total FROM asignaturas WHERE codigoAsignatura = :codigoAsignatura";
            
            if ($excluirId !== null) {
                $sql .= " AND idAsignatura != :excluirId";
            }
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':codigoAsignatura', $codigoAsignatura);
            
            if ($excluirId !== null) {
                $stmt->bindParam(':excluirId', $excluirId, PDO::PARAM_INT);
            }
            
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['total'] > 0;
        } catch (PDOException $e) {
            error_log("Error en existeAsignaturaPorCodigo: " . $e->getMessage());
            return false;
        }
    }

    // VERIFICAR SI EXISTE ASIGNATURA POR NOMBRE
    public static function existeAsignaturaPorNombre($nombreAsignatura, $excluirId = null)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT COUNT(*) as total FROM asignaturas WHERE nombreAsignatura = :nombreAsignatura";
            
            if ($excluirId !== null) {
                $sql .= " AND idAsignatura != :excluirId";
            }
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':nombreAsignatura', $nombreAsignatura);
            
            if ($excluirId !== null) {
                $stmt->bindParam(':excluirId', $excluirId, PDO::PARAM_INT);
            }
            
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['total'] > 0;
        } catch (PDOException $e) {
            error_log("Error en existeAsignaturaPorNombre: " . $e->getMessage());
            return false;
        }
    }
}
?>