<?php
require_once __DIR__ . "/../../utilidades/u_conexion.php";
require_once __DIR__ . "/../modelo/m_prerrequisito.php";

class D_Prerrequisito
{
    // OBTENER TODOS LOS PRERREQUISITOS
    public static function obtenerPrerrequisitos()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT p.*, 
                           a1.codigoAsignatura as codigoAsignatura, 
                           a1.nombreAsignatura as nombreAsignatura,
                           a2.codigoAsignatura as codigoAsignaturaRequerida, 
                           a2.nombreAsignatura as nombreAsignaturaRequerida
                    FROM prerrequisitos p
                    INNER JOIN asignaturas a1 ON p.idAsignatura = a1.idAsignatura
                    INNER JOIN asignaturas a2 ON p.idAsignaturaRequerida = a2.idAsignatura
                    ORDER BY a1.nombreAsignatura, a2.nombreAsignatura";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $prerrequisitos = [];
            
            foreach ($resultados as $fila) {
                $model = new PrerrequisitoModel();
                $model->hidratarDesdeArray($fila);
                $model->codigoAsignatura = $fila['codigoAsignatura'];
                $model->nombreAsignatura = $fila['nombreAsignatura'];
                $model->codigoAsignaturaRequerida = $fila['codigoAsignaturaRequerida'];
                $model->nombreAsignaturaRequerida = $fila['nombreAsignaturaRequerida'];
                $prerrequisitos[] = $model;
            }

            return $prerrequisitos;
        } catch (PDOException $e) {
            error_log("Error en obtenerPrerrequisitos: " . $e->getMessage());
            return [];
        }
    }

    // OBTENER PRERREQUISITOS POR ASIGNATURA
    public static function obtenerPrerrequisitosPorAsignatura($idAsignatura)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT p.*, 
                           a.codigoAsignatura as codigoAsignaturaRequerida, 
                           a.nombreAsignatura as nombreAsignaturaRequerida
                    FROM prerrequisitos p
                    INNER JOIN asignaturas a ON p.idAsignaturaRequerida = a.idAsignatura
                    WHERE p.idAsignatura = :idAsignatura
                    ORDER BY a.nombreAsignatura";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idAsignatura', $idAsignatura, PDO::PARAM_INT);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $prerrequisitos = [];
            
            foreach ($resultados as $fila) {
                $model = new PrerrequisitoModel();
                $model->hidratarDesdeArray($fila);
                $model->codigoAsignaturaRequerida = $fila['codigoAsignaturaRequerida'];
                $model->nombreAsignaturaRequerida = $fila['nombreAsignaturaRequerida'];
                $prerrequisitos[] = $model;
            }

            return $prerrequisitos;
        } catch (PDOException $e) {
            error_log("Error en obtenerPrerrequisitosPorAsignatura: " . $e->getMessage());
            return [];
        }
    }

    // OBTENER PRERREQUISITOS QUE TIENEN UNA ASIGNATURA COMO REQUERIDA
    public static function obtenerAsignaturasQueRequeridasPor($idAsignaturaRequerida)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT p.*, 
                           a.codigoAsignatura, 
                           a.nombreAsignatura
                    FROM prerrequisitos p
                    INNER JOIN asignaturas a ON p.idAsignatura = a.idAsignatura
                    WHERE p.idAsignaturaRequerida = :idAsignaturaRequerida
                    ORDER BY a.nombreAsignatura";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idAsignaturaRequerida', $idAsignaturaRequerida, PDO::PARAM_INT);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $prerrequisitos = [];
            
            foreach ($resultados as $fila) {
                $model = new PrerrequisitoModel();
                $model->hidratarDesdeArray($fila);
                $model->codigoAsignatura = $fila['codigoAsignatura'];
                $model->nombreAsignatura = $fila['nombreAsignatura'];
                $prerrequisitos[] = $model;
            }

            return $prerrequisitos;
        } catch (PDOException $e) {
            error_log("Error en obtenerAsignaturasQueRequeridasPor: " . $e->getMessage());
            return [];
        }
    }

    // OBTENER PRERREQUISITO POR ID
    public static function obtenerPrerrequisitoPorId($id)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT * FROM prerrequisitos WHERE idPrerrequisito = :id";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($resultado) {
                $model = new PrerrequisitoModel();
                return $model->hidratarDesdeArray($resultado);
            }
            
            return null;
        } catch (PDOException $e) {
            error_log("Error en obtenerPrerrequisitoPorId: " . $e->getMessage());
            return null;
        }
    }

    // INSERTAR PRERREQUISITO
    public static function insertarPrerrequisito($datos)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "INSERT INTO prerrequisitos (idAsignatura, idAsignaturaRequerida) 
                    VALUES (:idAsignatura, :idAsignaturaRequerida)";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idAsignatura', $datos['idAsignatura'], PDO::PARAM_INT);
            $stmt->bindParam(':idAsignaturaRequerida', $datos['idAsignaturaRequerida'], PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                return $instanciaConexion->lastInsertId();
            }
            
            return null;
        } catch (PDOException $e) {
            error_log("Error en insertarPrerrequisito: " . $e->getMessage());
            return null;
        }
    }

    // ELIMINAR PRERREQUISITO
    public static function eliminarPrerrequisito($id)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "DELETE FROM prerrequisitos WHERE idPrerrequisito = :id";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en eliminarPrerrequisito: " . $e->getMessage());
            return false;
        }
    }

    // ELIMINAR TODOS LOS PRERREQUISITOS DE UNA ASIGNATURA
    public static function eliminarPrerrequisitosPorAsignatura($idAsignatura)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "DELETE FROM prerrequisitos WHERE idAsignatura = :idAsignatura";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idAsignatura', $idAsignatura, PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en eliminarPrerrequisitosPorAsignatura: " . $e->getMessage());
            return false;
        }
    }

    // VERIFICAR SI EXISTE PRERREQUISITO
    public static function existePrerrequisito($idAsignatura, $idAsignaturaRequerida)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT COUNT(*) as total FROM prerrequisitos 
                    WHERE idAsignatura = :idAsignatura AND idAsignaturaRequerida = :idAsignaturaRequerida";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idAsignatura', $idAsignatura, PDO::PARAM_INT);
            $stmt->bindParam(':idAsignaturaRequerida', $idAsignaturaRequerida, PDO::PARAM_INT);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['total'] > 0;
        } catch (PDOException $e) {
            error_log("Error en existePrerrequisito: " . $e->getMessage());
            return false;
        }
    }

    // VERIFICAR SI HAY PRERREQUISITOS CIRCULARES
    public static function existePrerrequisitoCircular($idAsignatura, $idAsignaturaRequerida)
    {
        try {
            // Si la asignatura requerida es la misma que la asignatura
            if ($idAsignatura == $idAsignaturaRequerida) {
                return true;
            }

            // Verificar si la asignatura requerida tiene como prerrequisito a la asignatura original
            $sql = "SELECT COUNT(*) as total FROM prerrequisitos 
                    WHERE idAsignatura = :idAsignaturaRequerida AND idAsignaturaRequerida = :idAsignatura";
            
            $instanciaConexion = ConexionUtil::conectar();
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idAsignaturaRequerida', $idAsignaturaRequerida, PDO::PARAM_INT);
            $stmt->bindParam(':idAsignatura', $idAsignatura, PDO::PARAM_INT);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['total'] > 0;
        } catch (PDOException $e) {
            error_log("Error en existePrerrequisitoCircular: " . $e->getMessage());
            return false;
        }
    }
}
?>