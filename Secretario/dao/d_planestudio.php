<?php
require_once __DIR__ . "/../../utilidades/u_conexion.php";
require_once __DIR__ . "/../modelo/m_planestudio.php";

class D_PlanEstudio
{
    // CONSTANTE PARA EL NÚMERO DE REGISTROS POR PÁGINA
    const REGISTROS_POR_PAGINA = 8;

    // OBTENER TODOS LOS PLANES DE ESTUDIO (solo lectura)
    public static function obtenerPlanesEstudios()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT pe.*, c.nombreCarrera, f.nombreFacultad
                    FROM planestudio pe
                    LEFT JOIN carrera c ON pe.idCarrera = c.idCarrera
                    LEFT JOIN departamento d ON c.idDepartamento = d.idDepartamento
                    LEFT JOIN facultad f ON d.idFacultad = f.idFacultad
                    ORDER BY pe.fechaElaboracion DESC, pe.nombre ASC";
            $stmt = $instanciaConexion->prepare($sql);
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
            error_log("Error en obtenerPlanesEstudios: " . $e->getMessage());
            return [];
        }
    }

    // OBTENER PLANES DE ESTUDIO POR FACULTAD
    public static function obtenerPlanesEstudiosPorFacultad($idFacultad)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT pe.*, c.nombreCarrera, f.nombreFacultad
                    FROM planestudio pe
                    LEFT JOIN carrera c ON pe.idCarrera = c.idCarrera
                    LEFT JOIN departamento d ON c.idDepartamento = d.idDepartamento
                    LEFT JOIN facultad f ON d.idFacultad = f.idFacultad
                    WHERE f.idFacultad = :idFacultad
                    ORDER BY pe.fechaElaboracion DESC, pe.nombre ASC";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idFacultad', $idFacultad, PDO::PARAM_INT);
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
            error_log("Error en obtenerPlanesEstudiosPorFacultad: " . $e->getMessage());
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

            $sql = "SELECT pe.*, c.nombreCarrera, f.nombreFacultad
                    FROM planestudio pe
                    LEFT JOIN carrera c ON pe.idCarrera = c.idCarrera
                    LEFT JOIN departamento d ON c.idDepartamento = d.idDepartamento
                    LEFT JOIN facultad f ON d.idFacultad = f.idFacultad
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

    // CONTAR PLANES DE ESTUDIO POR FACULTAD (para paginación)
    public static function contarPlanesEstudiosPorFacultad($idFacultad)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT COUNT(*) as total 
                    FROM planestudio pe
                    LEFT JOIN carrera c ON pe.idCarrera = c.idCarrera
                    LEFT JOIN departamento d ON c.idDepartamento = d.idDepartamento
                    WHERE d.idFacultad = :idFacultad";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idFacultad', $idFacultad, PDO::PARAM_INT);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int) ceil($resultado['total'] / self::REGISTROS_POR_PAGINA);
        } catch (PDOException $e) {
            error_log("Error en contarPlanesEstudiosPorFacultad: " . $e->getMessage());
            return 0;
        }
    }

    // OBTENER PLANES DE ESTUDIO PAGINADOS POR FACULTAD
    public static function obtenerPlanesEstudiosPaginadosPorFacultad($pagina, $idFacultad)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $saltos = ($pagina - 1) * self::REGISTROS_POR_PAGINA;
            $lote = self::REGISTROS_POR_PAGINA;

            $sql = "SELECT pe.*, c.nombreCarrera, f.nombreFacultad
                    FROM planestudio pe
                    LEFT JOIN carrera c ON pe.idCarrera = c.idCarrera
                    LEFT JOIN departamento d ON c.idDepartamento = d.idDepartamento
                    LEFT JOIN facultad f ON d.idFacultad = f.idFacultad
                    WHERE f.idFacultad = :idFacultad
                    ORDER BY pe.fechaElaboracion DESC, pe.nombre ASC
                    LIMIT :lote OFFSET :saltos";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idFacultad', $idFacultad, PDO::PARAM_INT);
            $stmt->bindParam(':lote', $lote, PDO::PARAM_INT);
            $stmt->bindParam(':saltos', $saltos, PDO::PARAM_INT);
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
            error_log("Error en obtenerPlanesEstudiosPaginadosPorFacultad: " . $e->getMessage());
            return [];
        }
    }

    // INSERTAR PLAN DE ESTUDIO CON TRANSACCIÓN
    public static function insertarPlanEstudio($datos)
    {
        $pdo = null;
        try {
            $pdo = ConexionUtil::conectar();
            $pdo->beginTransaction();

            $sql = "INSERT INTO planestudio (
                        nombre, idCarrera, fechaElaboracion, periodoPlanEstudio, vigente
                    ) VALUES (
                        :nombre, :idCarrera, :fechaElaboracion, :periodoPlanEstudio, :vigente
                    )";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nombre', $datos['nombre']);
            $stmt->bindParam(':idCarrera', $datos['idCarrera'], PDO::PARAM_INT);
            $stmt->bindParam(':fechaElaboracion', $datos['fechaElaboracion']);
            $stmt->bindParam(':periodoPlanEstudio', $datos['periodoPlanEstudio']);
            $stmt->bindParam(':vigente', $datos['vigente']);
            
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
            error_log("Error en insertarPlanEstudio: " . $e->getMessage());
            return null;
        }
    }

    // ACTUALIZAR PLAN DE ESTUDIO CON TRANSACCIÓN
    public static function actualizarPlanEstudio($id, $datos)
    {
        $pdo = null;
        try {
            $pdo = ConexionUtil::conectar();
            $pdo->beginTransaction();

            $sql = "UPDATE planestudio SET 
                        nombre = :nombre,
                        idCarrera = :idCarrera,
                        fechaElaboracion = :fechaElaboracion,
                        periodoPlanEstudio = :periodoPlanEstudio
                    WHERE idPlanEstudio = :id";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':nombre', $datos['nombre']);
            $stmt->bindParam(':idCarrera', $datos['idCarrera'], PDO::PARAM_INT);
            $stmt->bindParam(':fechaElaboracion', $datos['fechaElaboracion']);
            $stmt->bindParam(':periodoPlanEstudio', $datos['periodoPlanEstudio']);
            
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
            error_log("Error en actualizarPlanEstudio: " . $e->getMessage());
            return false;
        }
    }

    // CAMBIAR VIGENCIA CON TRANSACCIÓN
    public static function cambiarVigenciaPlanEstudio($id, $vigente)
    {
        $pdo = null;
        try {
            $pdo = ConexionUtil::conectar();
            $pdo->beginTransaction();

            $sql = "UPDATE planestudio SET vigente = :vigente WHERE idPlanEstudio = :id";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':vigente', $vigente, PDO::PARAM_INT);
            
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
            error_log("Error en cambiarVigenciaPlanEstudio: " . $e->getMessage());
            return false;
        }
    }

    // VERIFICAR SI EXISTE PLAN DE ESTUDIO (solo lectura)
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