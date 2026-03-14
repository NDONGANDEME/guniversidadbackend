<?php
require_once __DIR__ . "/../../utilidades/u_conexion.php";
require_once __DIR__ . "/../modelo/m_curso.php";

class D_Curso
{
    // OBTENER TODOS LOS CURSOS (solo lectura)
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

    // OBTENER CURSO POR ID (solo lectura)
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

    // INSERTAR CURSO CON TRANSACCIÓN
    public static function insertarCurso($datos)
    {
        $pdo = null;
        try {
            $pdo = ConexionUtil::conectar();
            $pdo->beginTransaction();

            $sql = "INSERT INTO curso (nombreCurso, nivel) 
                    VALUES (:nombreCurso, :nivel)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nombreCurso', $datos['nombreCurso']);
            $stmt->bindParam(':nivel', $datos['nivel']);
            
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
            error_log("Error en insertarCurso: " . $e->getMessage());
            return null;
        }
    }

    // ACTUALIZAR CURSO CON TRANSACCIÓN
    public static function actualizarCurso($datos)
    {
        $pdo = null;
        try {
            $pdo = ConexionUtil::conectar();
            $pdo->beginTransaction();

            $sql = "UPDATE curso SET 
                        nombreCurso = :nombreCurso,
                        nivel = :nivel
                    WHERE idCurso = :id";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $datos['id'], PDO::PARAM_INT);
            $stmt->bindParam(':nombreCurso', $datos['nombreCurso']);
            $stmt->bindParam(':nivel', $datos['nivel']);
            
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
            error_log("Error en actualizarCurso: " . $e->getMessage());
            return false;
        }
    }

    // VERIFICAR SI EXISTE CURSO (solo lectura)
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