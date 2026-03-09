<?php
require_once __DIR__ . "/../../utilidades/u_conexion.php";
require_once __DIR__ . "/../modelo/m_planestudio.php";

class D_PlanEstudio
{
    // OBTENER TODOS LOS PLANES DE ESTUDIO
    public static function obtenerPlanesEstudios()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT pe.*, c.nombreCarrera 
                    FROM planestudio pe
                    LEFT JOIN carrera c ON pe.idCarrera = c.idCarrera
                    ORDER BY pe.fechaElaboracion DESC, pe.nombre ASC";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $planes = [];
            
            foreach ($resultados as $fila) {
                $model = new PlanEstudioModel();
                $model->hidratarDesdeArray($fila);
                if (isset($fila['nombreCarrera'])) {
                    $model->nombreCarrera = $fila['nombreCarrera'];
                }
                $planes[] = $model;
            }

            return $planes;
        } catch (PDOException $e) {
            error_log("Error en obtenerPlanesEstudios: " . $e->getMessage());
            return [];
        }
    }

    // OBTENER PLANES DE ESTUDIO POR CARRERA
    public static function obtenerPlanesEstudioPorCarrera($idCarrera)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT * FROM planestudio 
                    WHERE idCarrera = :idCarrera 
                    ORDER BY fechaElaboracion DESC, periodoPlanEstudio DESC";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idCarrera', $idCarrera, PDO::PARAM_INT);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $planes = [];
            
            foreach ($resultados as $fila) {
                $model = new PlanEstudioModel();
                $model->hidratarDesdeArray($fila);
                $planes[] = $model;
            }

            return $planes;
        } catch (PDOException $e) {
            error_log("Error en obtenerPlanesEstudioPorCarrera: " . $e->getMessage());
            return [];
        }
    }

    // OBTENER PLAN DE ESTUDIO POR ID
    public static function obtenerPlanEstudioPorId($id)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT pe.*, c.nombreCarrera 
                    FROM planestudio pe
                    LEFT JOIN carrera c ON pe.idCarrera = c.idCarrera
                    WHERE pe.idPlanEstudio = :id";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($resultado) {
                $model = new PlanEstudioModel();
                return $model->hidratarDesdeArray($resultado);
            }
            
            return null;
        } catch (PDOException $e) {
            error_log("Error en obtenerPlanEstudioPorId: " . $e->getMessage());
            return null;
        }
    }

    // INSERTAR PLAN DE ESTUDIO
    public static function insertarPlanEstudio($datos)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "INSERT INTO planestudio (
                        nombre, idCarrera, fechaElaboracion, periodoPlanEstudio, vigente
                    ) VALUES (
                        :nombre, :idCarrera, :fechaElaboracion, :periodoPlanEstudio, :vigente
                    )";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':nombre', $datos['nombre']);
            $stmt->bindParam(':idCarrera', $datos['idCarrera'], PDO::PARAM_INT);
            $stmt->bindParam(':fechaElaboracion', $datos['fechaElaboracion']);
            $stmt->bindParam(':periodoPlanEstudio', $datos['periodoPlanEstudio']);
            $stmt->bindParam(':vigente', $datos['vigente']);
            
            if ($stmt->execute()) {
                return $instanciaConexion->lastInsertId();
            }
            
            return null;
        } catch (PDOException $e) {
            error_log("Error en insertarPlanEstudio: " . $e->getMessage());
            return null;
        }
    }

    // ACTUALIZAR PLAN DE ESTUDIO
    public static function actualizarPlanEstudio($id, $datos)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "UPDATE planestudio SET 
                        nombre = :nombre,
                        idCarrera = :idCarrera,
                        fechaElaboracion = :fechaElaboracion,
                        periodoPlanEstudio = :periodoPlanEstudio
                    WHERE idPlanEstudio = :id";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':nombre', $datos['nombre']);
            $stmt->bindParam(':idCarrera', $datos['idCarrera'], PDO::PARAM_INT);
            $stmt->bindParam(':fechaElaboracion', $datos['fechaElaboracion']);
            $stmt->bindParam(':periodoPlanEstudio', $datos['periodoPlanEstudio']);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en actualizarPlanEstudio: " . $e->getMessage());
            return false;
        }
    }

    // CAMBIAR VIGENCIA (habilitar/deshabilitar)
    public static function cambiarVigenciaPlanEstudio($id, $vigente)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "UPDATE planestudio SET vigente = :vigente WHERE idPlanEstudio = :id";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':vigente', $vigente, PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en cambiarVigenciaPlanEstudio: " . $e->getMessage());
            return false;
        }
    }

    // VERIFICAR SI EXISTE PLAN DE ESTUDIO
    public static function existePlanEstudio($nombre, $idCarrera, $excluirId = null)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT COUNT(*) as total FROM planestudio 
                    WHERE nombre = :nombre AND idCarrera = :idCarrera";
            
            if ($excluirId !== null) {
                $sql .= " AND idPlanEstudio != :excluirId";
            }
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':idCarrera', $idCarrera, PDO::PARAM_INT);
            
            if ($excluirId !== null) {
                $stmt->bindParam(':excluirId', $excluirId, PDO::PARAM_INT);
            }
            
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['total'] > 0;
        } catch (PDOException $e) {
            error_log("Error en existePlanEstudio: " . $e->getMessage());
            return false;
        }
    }
}
?>