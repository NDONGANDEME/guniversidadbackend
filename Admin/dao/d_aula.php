<?php
require_once __DIR__ . "/../../utilidades/u_conexion.php";
require_once __DIR__ . "/../modelo/m_aula.php";

class D_Aula
{
    // CONSTANTE PARA EL NÚMERO DE REGISTROS POR PÁGINA
    const REGISTROS_POR_PAGINA = 30;

    // OBTENER TODAS LAS AULAS (solo lectura)
    public static function obtenerAulas()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT a.*, f.nombreFacultad 
                    FROM aulas a
                    LEFT JOIN facultad f ON a.idFacultad = f.idFacultad
                    ORDER BY a.nombreAula ASC";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $aulas = [];
            
            foreach ($resultados as $fila) {
                $model = new AulaModel();
                $model->hidratarDesdeArray($fila);
                if (isset($fila['nombreFacultad'])) {
                    $model->nombreFacultad = $fila['nombreFacultad'];
                }
                $aulas[] = $model;
            }

            return $aulas;
        } catch (PDOException $e) {
            error_log("Error en obtenerAulas: " . $e->getMessage());
            return [];
        }
    }

    // OBTENER EL NÚMERO DE PÁGINAS (30 aulas por página)
    public static function contarAulas()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT COUNT(*) as total FROM aulas";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

            return (int) ceil($resultado['total'] / self::REGISTROS_POR_PAGINA);
        } catch (PDOException $e) {
            error_log("Error en contarAulas: " . $e->getMessage());
            return 0;
        }
    }

    // OBTENER AULAS A PAGINAR
    public static function obtenerAulasAPaginar($pagina)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $saltos = ($pagina - 1) * self::REGISTROS_POR_PAGINA;
            $lote = self::REGISTROS_POR_PAGINA;

            $sql = "SELECT a.*, f.nombreFacultad 
                    FROM aulas a
                    LEFT JOIN facultad f ON a.idFacultad = f.idFacultad
                    ORDER BY a.nombreAula ASC 
                    LIMIT :lote OFFSET :saltos";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':lote', $lote, PDO::PARAM_INT);
            $stmt->bindParam(':saltos', $saltos, PDO::PARAM_INT);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $aulas = [];
            
            foreach ($resultados as $fila) {
                $model = new AulaModel();
                $model->hidratarDesdeArray($fila);
                if (isset($fila['nombreFacultad'])) {
                    $model->nombreFacultad = $fila['nombreFacultad'];
                }
                $aulas[] = $model;
            }

            return $aulas;
        } catch (PDOException $e) {
            error_log("Error en obtenerAulasAPaginar: " . $e->getMessage());
            return [];
        }
    }

    // OBTENER AULA POR ID (solo lectura)
    public static function obtenerAulaPorId($id)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT * FROM aulas WHERE idAula = :id";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($resultado) {
                $model = new AulaModel();
                return $model->hidratarDesdeArray($resultado);
            }
            
            return null;
        } catch (PDOException $e) {
            error_log("Error en obtenerAulaPorId: " . $e->getMessage());
            return null;
        }
    }

    // OBTENER AULAS POR FACULTAD (solo lectura)
    public static function obtenerAulasPorFacultad($idFacultad)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT * FROM aulas WHERE idFacultad = :idFacultad ORDER BY nombreAula ASC";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idFacultad', $idFacultad, PDO::PARAM_INT);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $aulas = [];
            
            foreach ($resultados as $fila) {
                $model = new AulaModel();
                $model->hidratarDesdeArray($fila);
                $aulas[] = $model;
            }

            return $aulas;
        } catch (PDOException $e) {
            error_log("Error en obtenerAulasPorFacultad: " . $e->getMessage());
            return [];
        }
    }

    // CONTAR AULAS POR FACULTAD
    public static function contarAulasPorFacultad($idFacultad)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT COUNT(*) as total FROM aulas WHERE idFacultad = :idFacultad";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idFacultad', $idFacultad, PDO::PARAM_INT);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int) $resultado['total'];
        } catch (PDOException $e) {
            error_log("Error en contarAulasPorFacultad: " . $e->getMessage());
            return 0;
        }
    }

    // OBTENER AULAS POR FACULTAD CON PAGINACIÓN
    public static function obtenerAulasPorFacultadPaginadas($idFacultad, $pagina)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $saltos = ($pagina - 1) * self::REGISTROS_POR_PAGINA;
            $lote = self::REGISTROS_POR_PAGINA;

            $sql = "SELECT a.*, f.nombreFacultad
                    FROM aulas a
                    LEFT JOIN facultad f ON a.idFacultad = f.idFacultad
                    WHERE a.idFacultad = :idFacultad 
                    ORDER BY a.nombreAula ASC 
                    LIMIT :lote OFFSET :saltos";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idFacultad', $idFacultad, PDO::PARAM_INT);
            $stmt->bindParam(':lote', $lote, PDO::PARAM_INT);
            $stmt->bindParam(':saltos', $saltos, PDO::PARAM_INT);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $aulas = [];
            
            foreach ($resultados as $fila) {
                $model = new AulaModel();
                $model->hidratarDesdeArray($fila);
                $aulas[] = $model;
            }

            return $aulas;
        } catch (PDOException $e) {
            error_log("Error en obtenerAulasPorFacultadPaginadas: " . $e->getMessage());
            return [];
        }
    }

    // BUSCAR AULAS POR TÉRMINO
    public static function buscarAulas($termino)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $terminoBusqueda = "%$termino%";

            $sql = "SELECT a.*, f.nombreFacultad
                    FROM aulas a
                    LEFT JOIN facultad f ON a.idFacultad = f.idFacultad
                    WHERE a.nombreAula LIKE :termino 
                    ORDER BY a.nombreAula ASC";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':termino', $terminoBusqueda);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $aulas = [];
            
            foreach ($resultados as $fila) {
                $model = new AulaModel();
                $model->hidratarDesdeArray($fila);
                $aulas[] = $model;
            }

            return $aulas;
        } catch (PDOException $e) {
            error_log("Error en buscarAulas: " . $e->getMessage());
            return [];
        }
    }

    // BUSCAR AULAS POR TÉRMINO CON PAGINACIÓN
    public static function buscarAulasPaginadas($termino, $pagina)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $terminoBusqueda = "%$termino%";
            
            $saltos = ($pagina - 1) * self::REGISTROS_POR_PAGINA;
            $lote = self::REGISTROS_POR_PAGINA;

            $sql = "SELECT a.*, f.nombreFacultad
                    FROM aulas a
                    LEFT JOIN facultad f ON a.idFacultad = f.idFacultad
                    WHERE a.nombreAula LIKE :termino 
                    ORDER BY a.nombreAula ASC
                    LIMIT :lote OFFSET :saltos";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':termino', $terminoBusqueda);
            $stmt->bindParam(':lote', $lote, PDO::PARAM_INT);
            $stmt->bindParam(':saltos', $saltos, PDO::PARAM_INT);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $aulas = [];
            
            foreach ($resultados as $fila) {
                $model = new AulaModel();
                $model->hidratarDesdeArray($fila);
                $aulas[] = $model;
            }

            return $aulas;
        } catch (PDOException $e) {
            error_log("Error en buscarAulasPaginadas: " . $e->getMessage());
            return [];
        }
    }

    // CONTAR RESULTADOS DE BÚSQUEDA
    public static function contarResultadosBusquedaAulas($termino)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $terminoBusqueda = "%$termino%";

            $sql = "SELECT COUNT(*) as total 
                    FROM aulas 
                    WHERE nombreAula LIKE :termino";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':termino', $terminoBusqueda);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int) ceil($resultado['total'] / self::REGISTROS_POR_PAGINA);
        } catch (PDOException $e) {
            error_log("Error en contarResultadosBusquedaAulas: " . $e->getMessage());
            return 0;
        }
    }

    // INSERTAR AULA CON TRANSACCIÓN
    public static function insertarAula($datos)
    {
        $pdo = null;
        try {
            $pdo = ConexionUtil::conectar();
            $pdo->beginTransaction();

            $sql = "INSERT INTO aulas (nombreAula, capacidad, idFacultad, estado) 
                    VALUES (:nombreAula, :capacidad, :idFacultad, :estado)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nombreAula', $datos['nombreAula']);
            $stmt->bindParam(':capacidad', $datos['capacidad'], PDO::PARAM_INT);
            $stmt->bindParam(':idFacultad', $datos['idFacultad'], PDO::PARAM_INT);
            $stmt->bindParam(':estado', $datos['estado']);
            
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
            error_log("Error en insertarAula: " . $e->getMessage());
            return null;
        }
    }

    // ACTUALIZAR AULA CON TRANSACCIÓN
    public static function actualizarAula($datos)
    {
        $pdo = null;
        try {
            $pdo = ConexionUtil::conectar();
            $pdo->beginTransaction();

            $sql = "UPDATE aulas SET 
                        nombreAula = :nombreAula,
                        capacidad = :capacidad,
                        idFacultad = :idFacultad
                    WHERE idAula = :id";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $datos['id'], PDO::PARAM_INT);
            $stmt->bindParam(':nombreAula', $datos['nombreAula']);
            $stmt->bindParam(':capacidad', $datos['capacidad'], PDO::PARAM_INT);
            $stmt->bindParam(':idFacultad', $datos['idFacultad'], PDO::PARAM_INT);
            
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
            error_log("Error en actualizarAula: " . $e->getMessage());
            return false;
        }
    }

    // ELIMINAR AULA CON TRANSACCIÓN
    public static function eliminarAula($id)
    {
        $pdo = null;
        try {
            $pdo = ConexionUtil::conectar();
            $pdo->beginTransaction();

            // Verificar si el aula tiene horarios asociados
            $sqlVerificar = "SELECT COUNT(*) as total FROM horarios WHERE idAula = :id";
            $stmtVerificar = $pdo->prepare($sqlVerificar);
            $stmtVerificar->bindParam(':id', $id, PDO::PARAM_INT);
            $stmtVerificar->execute();
            $resultado = $stmtVerificar->fetch(PDO::FETCH_ASSOC);
            
            if ($resultado['total'] > 0) {
                $pdo->rollBack();
                return false; // No se puede eliminar porque tiene horarios asociados
            }

            // Si no tiene horarios, proceder a eliminar
            $sql = "DELETE FROM aulas WHERE idAula = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                if ($stmt->rowCount() > 0) {
                    $pdo->commit();
                    return true;
                } else {
                    $pdo->rollBack();
                    return false;
                }
            } else {
                $pdo->rollBack();
                return false;
            }
            
        } catch (PDOException $e) {
            if ($pdo) {
                $pdo->rollBack();
            }
            error_log("Error en eliminarAula: " . $e->getMessage());
            return false;
        }
    }

    // VERIFICAR SI EXISTE AULA (solo lectura)
    public static function existeAula($nombreAula, $idFacultad, $excluirId = null)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT COUNT(*) as total FROM aulas 
                    WHERE nombreAula = :nombreAula AND idFacultad = :idFacultad";
            
            if ($excluirId !== null) {
                $sql .= " AND idAula != :excluirId";
            }
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':nombreAula', $nombreAula);
            $stmt->bindParam(':idFacultad', $idFacultad, PDO::PARAM_INT);
            
            if ($excluirId !== null) {
                $stmt->bindParam(':excluirId', $excluirId, PDO::PARAM_INT);
            }
            
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['total'] > 0;
        } catch (PDOException $e) {
            error_log("Error en existeAula: " . $e->getMessage());
            return false;
        }
    }

    // VERIFICAR SI EL AULA TIENE HORARIOS ASOCIADOS
    public static function tieneHorariosAsociados($id)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT COUNT(*) as total FROM horarios WHERE idAula = :id";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['total'] > 0;
        } catch (PDOException $e) {
            error_log("Error en tieneHorariosAsociados: " . $e->getMessage());
            return false;
        }
    }
}
?>