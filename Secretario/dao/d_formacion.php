<?php
require_once __DIR__ . "/../../utilidades/u_conexion.php";
require_once __DIR__ . "/../modelo/m_formacion.php";

class D_Formacion
{
    // OBTENER TODAS LAS FORMACIONES
    public static function obtenerFormaciones()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT f.*, p.nombreProfesor, p.apellidosProfesor
                    FROM formacion f
                    INNER JOIN profesor p ON f.idProfesor = p.idProfesor
                    ORDER BY p.apellidosProfesor ASC, f.nivel DESC, f.titulo ASC";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $formaciones = [];
            
            foreach ($resultados as $fila) {
                $model = new FormacionModel();
                $model->hidratarDesdeArray($fila);
                if (isset($fila['nombreProfesor']) && isset($fila['apellidosProfesor'])) {
                    $model->nombreProfesor = $fila['nombreProfesor'];
                    $model->apellidosProfesor = $fila['apellidosProfesor'];
                }
                $formaciones[] = $model;
            }

            return $formaciones;
        } catch (PDOException $e) {
            error_log("Error en obtenerFormaciones: " . $e->getMessage());
            return [];
        }
    }

    // OBTENER FORMACIONES POR PROFESOR
    public static function obtenerFormacionesPorProfesor($idProfesor)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT * FROM formacion 
                    WHERE idProfesor = :idProfesor 
                    ORDER BY nivel DESC, titulo ASC";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idProfesor', $idProfesor, PDO::PARAM_INT);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $formaciones = [];
            
            foreach ($resultados as $fila) {
                $model = new FormacionModel();
                $model->hidratarDesdeArray($fila);
                $formaciones[] = $model;
            }

            return $formaciones;
        } catch (PDOException $e) {
            error_log("Error en obtenerFormacionesPorProfesor: " . $e->getMessage());
            return [];
        }
    }

    // OBTENER FORMACIÓN POR ID
    public static function obtenerFormacionPorId($id)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT * FROM formacion WHERE idFormacion = :id";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($resultado) {
                $model = new FormacionModel();
                return $model->hidratarDesdeArray($resultado);
            }
            
            return null;
        } catch (PDOException $e) {
            error_log("Error en obtenerFormacionPorId: " . $e->getMessage());
            return null;
        }
    }

    // INSERTAR FORMACIÓN
    public static function insertarFormacion($datos)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "INSERT INTO formacion (
                        institucion, tipoFormacion, titulo, nivel, idProfesor
                    ) VALUES (
                        :institucion, :tipoFormacion, :titulo, :nivel, :idProfesor
                    )";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':institucion', $datos['institucion']);
            $stmt->bindParam(':tipoFormacion', $datos['tipoFormacion']);
            $stmt->bindParam(':titulo', $datos['titulo']);
            $stmt->bindParam(':nivel', $datos['nivel']);
            $stmt->bindParam(':idProfesor', $datos['idProfesor'], PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                return $instanciaConexion->lastInsertId();
            }
            
            return null;
        } catch (PDOException $e) {
            error_log("Error en insertarFormacion: " . $e->getMessage());
            return null;
        }
    }

    // ACTUALIZAR FORMACIÓN
    public static function actualizarFormacion($id, $datos)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "UPDATE formacion SET 
                        institucion = :institucion,
                        tipoFormacion = :tipoFormacion,
                        titulo = :titulo,
                        nivel = :nivel
                    WHERE idFormacion = :id";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':institucion', $datos['institucion']);
            $stmt->bindParam(':tipoFormacion', $datos['tipoFormacion']);
            $stmt->bindParam(':titulo', $datos['titulo']);
            $stmt->bindParam(':nivel', $datos['nivel']);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en actualizarFormacion: " . $e->getMessage());
            return false;
        }
    }

    // ELIMINAR FORMACIÓN
    public static function eliminarFormacion($id)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "DELETE FROM formacion WHERE idFormacion = :id";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en eliminarFormacion: " . $e->getMessage());
            return false;
        }
    }
}
?>