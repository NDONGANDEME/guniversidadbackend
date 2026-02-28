<?php
require_once __DIR__ . "/../../utilidades/u_conexion.php";
require_once __DIR__ . "/../modelo/m_curso.php";

class D_Curso
{
    // OBTENER TODOS LOS CURSOS
    public static function obtenerCursos()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT * FROM curso ORDER BY nombreCurso ASC";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $cursos = [];
            
            foreach ($resultados as $fila) {
                $model = new CursoModel();
                $model->hidratarDesdeArray($fila);
                $cursos[] = $model;
            }

            return $cursos;
        } catch (PDOException $e) {
            error_log("Error en obtenerCursos: " . $e->getMessage());
            return [];
        }
    }

    // OBTENER CURSO POR ID
    public static function obtenerCursoPorId($id)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT * FROM curso WHERE idCurso = :id";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($resultado) {
                $model = new CursoModel();
                return $model->hidratarDesdeArray($resultado);
            }
            
            return null;
        } catch (PDOException $e) {
            error_log("Error en obtenerCursoPorId: " . $e->getMessage());
            return null;
        }
    }

    // INSERTAR CURSO
    public static function insertarCurso($datos)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "INSERT INTO curso (nombreCurso, nivel) 
                    VALUES (:nombreCurso, :nivel)";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':nombreCurso', $datos['nombreCurso']);
            $stmt->bindParam(':nivel', $datos['nivel']);
            
            if ($stmt->execute()) {
                return $instanciaConexion->lastInsertId();
            }
            
            return null;
        } catch (PDOException $e) {
            error_log("Error en insertarCurso: " . $e->getMessage());
            return null;
        }
    }

    // ACTUALIZAR CURSO
    public static function actualizarCurso($datos)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "UPDATE curso SET 
                        nombreCurso = :nombreCurso,
                        nivel = :nivel
                    WHERE idCurso = :id";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':id', $datos['id'], PDO::PARAM_INT);
            $stmt->bindParam(':nombreCurso', $datos['nombreCurso']);
            $stmt->bindParam(':nivel', $datos['nivel']);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en actualizarCurso: " . $e->getMessage());
            return false;
        }
    }

    // VERIFICAR SI EXISTE CURSO
    public static function existeCurso($nombreCurso, $excluirId = null)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT COUNT(*) as total FROM curso WHERE nombreCurso = :nombreCurso";
            
            if ($excluirId !== null) {
                $sql .= " AND idCurso != :excluirId";
            }
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':nombreCurso', $nombreCurso);
            
            if ($excluirId !== null) {
                $stmt->bindParam(':excluirId', $excluirId, PDO::PARAM_INT);
            }
            
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['total'] > 0;
        } catch (PDOException $e) {
            error_log("Error en existeCurso: " . $e->getMessage());
            return false;
        }
    }
}
?>