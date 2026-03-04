<?php
require_once __DIR__ . "/../../utilidades/u_conexion.php";
require_once __DIR__ . "/../modelo/m_estudiante_beca.php";

class D_EstudianteBeca
{
    // OBTENER TODOS LOS REGISTROS DE ESTUDIANTES BECADOS
    public static function obtenerEstudiantesBeca()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT eb.*, 
                           e.nombre as nombreEstudiante, e.apellidos as apellidosEstudiante, e.codigoEstudiante,
                           b.institucionBeca, b.tipoBeca
                    FROM estudiante_beca eb
                    INNER JOIN estudiantes e ON eb.idEstudiante = e.idEstudiante
                    INNER JOIN becas b ON eb.idBeca = b.idBeca
                    ORDER BY e.apellidos ASC, e.nombre ASC";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $registros = [];
            
            foreach ($resultados as $fila) {
                $model = new EstudianteBecaModel();
                $model->hidratarDesdeArray($fila);
                if (isset($fila['nombreEstudiante'])) {
                    $model->nombreEstudiante = $fila['nombreEstudiante'] . ' ' . ($fila['apellidosEstudiante'] ?? '');
                }
                if (isset($fila['institucionBeca'])) {
                    $model->institucionBeca = $fila['institucionBeca'];
                    $model->tipoBeca = $fila['tipoBeca'] ?? '';
                }
                $registros[] = $model;
            }

            return $registros;
        } catch (PDOException $e) {
            error_log("Error en obtenerEstudiantesBeca: " . $e->getMessage());
            return [];
        }
    }

    // OBTENER ESTUDIANTES BECA POR ID
    public static function obtenerEstudianteBecaPorId($id)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT eb.*, 
                           e.nombre as nombreEstudiante, e.apellidos as apellidosEstudiante,
                           b.institucionBeca, b.tipoBeca
                    FROM estudiante_beca eb
                    INNER JOIN estudiantes e ON eb.idEstudiante = e.idEstudiante
                    INNER JOIN becas b ON eb.idBeca = b.idBeca
                    WHERE eb.idEstudianteBecario = :id";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($resultado) {
                $model = new EstudianteBecaModel();
                return $model->hidratarDesdeArray($resultado);
            }
            
            return null;
        } catch (PDOException $e) {
            error_log("Error en obtenerEstudianteBecaPorId: " . $e->getMessage());
            return null;
        }
    }

    // OBTENER ESTUDIANTES BECA POR ESTUDIANTE
    public static function obtenerEstudiantesBecaPorEstudiante($idEstudiante)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT eb.*, b.institucionBeca, b.tipoBeca
                    FROM estudiante_beca eb
                    INNER JOIN becas b ON eb.idBeca = b.idBeca
                    WHERE eb.idEstudiante = :idEstudiante
                    ORDER BY eb.fechaInicio DESC";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idEstudiante', $idEstudiante, PDO::PARAM_INT);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $registros = [];
            
            foreach ($resultados as $fila) {
                $model = new EstudianteBecaModel();
                $model->hidratarDesdeArray($fila);
                if (isset($fila['institucionBeca'])) {
                    $model->institucionBeca = $fila['institucionBeca'];
                    $model->tipoBeca = $fila['tipoBeca'] ?? '';
                }
                $registros[] = $model;
            }

            return $registros;
        } catch (PDOException $e) {
            error_log("Error en obtenerEstudiantesBecaPorEstudiante: " . $e->getMessage());
            return [];
        }
    }

    // INSERTAR ESTUDIANTE BECADO
    public static function insertarEstudianteBecado($datos)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "INSERT INTO estudiante_beca (
                        idEstudiante, idBeca, fechaInicio, fechaFinal, estado, observaciones
                    ) VALUES (
                        :idEstudiante, :idBeca, :fechaInicio, :fechaFinal, :estado, :observaciones
                    )";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idEstudiante', $datos['idEstudiante'], PDO::PARAM_INT);
            $stmt->bindParam(':idBeca', $datos['idBeca'], PDO::PARAM_INT);
            $stmt->bindParam(':fechaInicio', $datos['fechaInicio']);
            $stmt->bindParam(':fechaFinal', $datos['fechaFinal']);
            $stmt->bindParam(':estado', $datos['estado']);
            $stmt->bindParam(':observaciones', $datos['observaciones']);
            
            if ($stmt->execute()) {
                return $instanciaConexion->lastInsertId();
            }
            
            return null;
        } catch (PDOException $e) {
            error_log("Error en insertarEstudianteBecado: " . $e->getMessage());
            return null;
        }
    }

    // ACTUALIZAR ESTUDIANTE BECADO
    public static function actualizarEstudianteBecado($id, $datos)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "UPDATE estudiante_beca SET 
                        idBeca = :idBeca,
                        fechaInicio = :fechaInicio,
                        fechaFinal = :fechaFinal,
                        observaciones = :observaciones
                    WHERE idEstudianteBecario = :id";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':idBeca', $datos['idBeca'], PDO::PARAM_INT);
            $stmt->bindParam(':fechaInicio', $datos['fechaInicio']);
            $stmt->bindParam(':fechaFinal', $datos['fechaFinal']);
            $stmt->bindParam(':observaciones', $datos['observaciones']);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en actualizarEstudianteBecado: " . $e->getMessage());
            return false;
        }
    }

    // CAMBIAR ESTADO ESTUDIANTE BECADO (habilitar/deshabilitar)
    public static function cambiarEstadoEstudianteBecado($id, $estado)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "UPDATE estudiante_beca SET estado = :estado WHERE idEstudianteBecario = :id";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':estado', $estado);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en cambiarEstadoEstudianteBecado: " . $e->getMessage());
            return false;
        }
    }

    // VERIFICAR SI EXISTE ASIGNACIÓN DE BECA ACTIVA PARA ESTUDIANTE
    public static function existeBecaActivaParaEstudiante($idEstudiante, $excluirId = null)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT COUNT(*) as total FROM estudiante_beca 
                    WHERE idEstudiante = :idEstudiante AND estado = 'activo'";
            
            if ($excluirId !== null) {
                $sql .= " AND idEstudianteBecario != :excluirId";
            }
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idEstudiante', $idEstudiante, PDO::PARAM_INT);
            
            if ($excluirId !== null) {
                $stmt->bindParam(':excluirId', $excluirId, PDO::PARAM_INT);
            }
            
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['total'] > 0;
        } catch (PDOException $e) {
            error_log("Error en existeBecaActivaParaEstudiante: " . $e->getMessage());
            return false;
        }
    }
}
?>