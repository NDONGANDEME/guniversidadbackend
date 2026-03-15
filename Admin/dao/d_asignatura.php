<?php
require_once __DIR__ . "/../../utilidades/u_conexion.php";
require_once __DIR__ . "/../modelo/m_asignatura.php";

class D_Asignatura
{
    // CONSTANTE PARA EL NÚMERO DE REGISTROS POR PÁGINA
    const REGISTROS_POR_PAGINA = 30;

    // OBTENER TODAS LAS ASIGNATURAS (solo lectura)
    public static function obtenerAsignaturas()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT a.*, f.nombreFacultad
                    FROM asignaturas a
                    LEFT JOIN facultad f ON a.idFacultad = f.idFacultad
                    ORDER BY a.nombreAsignatura ASC";
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

    // OBTENER ASIGNATURA POR ID (solo lectura)
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

    // OBTENER ASIGNATURAS POR FACULTAD (solo lectura)
    public static function obtenerAsignaturasPorFacultad($idFacultad)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT DISTINCT a.* FROM asignaturas a WHERE a.idFacultad = :idFacultad ORDER BY a.nombreAsignatura ASC";
            
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

    // OBTENER EL NÚMERO DE PÁGINAS (30 asignaturas por página)
    public static function contarAsignaturas()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT COUNT(*) as total FROM asignaturas";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

            return (int) ceil($resultado['total'] / self::REGISTROS_POR_PAGINA);
        } catch (PDOException $e) {
            error_log("Error en contarAsignaturas: " . $e->getMessage());
            return 0;
        }
    }

    // OBTENER ASIGNATURAS A PAGINAR
    public static function obtenerAsignaturasAPaginar($pagina)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $saltos = ($pagina - 1) * self::REGISTROS_POR_PAGINA;
            $lote = self::REGISTROS_POR_PAGINA;

            $sql = "SELECT a.*, f.nombreFacultad
                    FROM asignaturas a
                    LEFT JOIN facultad f ON a.idFacultad = f.idFacultad
                    ORDER BY a.nombreAsignatura ASC 
                    LIMIT :lote OFFSET :saltos";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':lote', $lote, PDO::PARAM_INT);
            $stmt->bindParam(':saltos', $saltos, PDO::PARAM_INT);
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
            error_log("Error en obtenerAsignaturasAPaginar: " . $e->getMessage());
            return [];
        }
    }

    // CONTAR ASIGNATURAS POR FACULTAD
    public static function contarAsignaturasPorFacultad($idFacultad)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT COUNT(*) as total FROM asignaturas WHERE idFacultad = :idFacultad";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idFacultad', $idFacultad, PDO::PARAM_INT);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int) $resultado['total'];
        } catch (PDOException $e) {
            error_log("Error en contarAsignaturasPorFacultad: " . $e->getMessage());
            return 0;
        }
    }

    // OBTENER ASIGNATURAS POR FACULTAD CON PAGINACIÓN
    public static function obtenerAsignaturasPorFacultadPaginadas($idFacultad, $pagina)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $saltos = ($pagina - 1) * self::REGISTROS_POR_PAGINA;
            $lote = self::REGISTROS_POR_PAGINA;

            $sql = "SELECT a.*, f.nombreFacultad
                    FROM asignaturas a
                    LEFT JOIN facultad f ON a.idFacultad = f.idFacultad
                    WHERE a.idFacultad = :idFacultad 
                    ORDER BY a.nombreAsignatura ASC 
                    LIMIT :lote OFFSET :saltos";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idFacultad', $idFacultad, PDO::PARAM_INT);
            $stmt->bindParam(':lote', $lote, PDO::PARAM_INT);
            $stmt->bindParam(':saltos', $saltos, PDO::PARAM_INT);
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
            error_log("Error en obtenerAsignaturasPorFacultadPaginadas: " . $e->getMessage());
            return [];
        }
    }

    // BUSCAR ASIGNATURAS POR TÉRMINO
    public static function buscarAsignaturas($termino)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $terminoBusqueda = "%$termino%";

            $sql = "SELECT a.*, f.nombreFacultad
                    FROM asignaturas a
                    LEFT JOIN facultad f ON a.idFacultad = f.idFacultad
                    WHERE a.codigoAsignatura LIKE :termino 
                       OR a.nombreAsignatura LIKE :termino
                       OR a.descripcion LIKE :termino
                    ORDER BY a.nombreAsignatura ASC";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':termino', $terminoBusqueda);
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
            error_log("Error en buscarAsignaturas: " . $e->getMessage());
            return [];
        }
    }

    // BUSCAR ASIGNATURAS POR TÉRMINO CON PAGINACIÓN
    public static function buscarAsignaturasPaginadas($termino, $pagina)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $terminoBusqueda = "%$termino%";
            
            $saltos = ($pagina - 1) * self::REGISTROS_POR_PAGINA;
            $lote = self::REGISTROS_POR_PAGINA;

            $sql = "SELECT a.*, f.nombreFacultad
                    FROM asignaturas a
                    LEFT JOIN facultad f ON a.idFacultad = f.idFacultad
                    WHERE a.codigoAsignatura LIKE :termino 
                       OR a.nombreAsignatura LIKE :termino
                       OR a.descripcion LIKE :termino
                    ORDER BY a.nombreAsignatura ASC
                    LIMIT :lote OFFSET :saltos";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':termino', $terminoBusqueda);
            $stmt->bindParam(':lote', $lote, PDO::PARAM_INT);
            $stmt->bindParam(':saltos', $saltos, PDO::PARAM_INT);
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
            error_log("Error en buscarAsignaturasPaginadas: " . $e->getMessage());
            return [];
        }
    }

    // CONTAR RESULTADOS DE BÚSQUEDA
    public static function contarResultadosBusqueda($termino)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $terminoBusqueda = "%$termino%";

            $sql = "SELECT COUNT(*) as total 
                    FROM asignaturas 
                    WHERE codigoAsignatura LIKE :termino 
                       OR nombreAsignatura LIKE :termino
                       OR descripcion LIKE :termino";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':termino', $terminoBusqueda);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int) ceil($resultado['total'] / self::REGISTROS_POR_PAGINA);
        } catch (PDOException $e) {
            error_log("Error en contarResultadosBusqueda: " . $e->getMessage());
            return 0;
        }
    }

    // INSERTAR ASIGNATURA CON TRANSACCIÓN
    public static function insertarAsignatura($datos)
    {
        $pdo = null;
        try {
            $pdo = ConexionUtil::conectar();
            $pdo->beginTransaction();

            $sql = "INSERT INTO asignaturas (codigoAsignatura, nombreAsignatura, descripcion, idFacultad) 
                    VALUES (:codigoAsignatura, :nombreAsignatura, :descripcion, :idFacultad)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':codigoAsignatura', $datos['codigoAsignatura']);
            $stmt->bindParam(':nombreAsignatura', $datos['nombreAsignatura']);
            $stmt->bindParam(':descripcion', $datos['descripcion']);
            $stmt->bindParam(':idFacultad', $datos['idFacultad']);
            
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
            error_log("Error en insertarAsignatura: " . $e->getMessage());
            return null;
        }
    }

    // ACTUALIZAR ASIGNATURA CON TRANSACCIÓN
    public static function actualizarAsignatura($datos)
    {
        $pdo = null;
        try {
            $pdo = ConexionUtil::conectar();
            $pdo->beginTransaction();

            $sql = "UPDATE asignaturas SET 
                        codigoAsignatura = :codigoAsignatura,
                        nombreAsignatura = :nombreAsignatura,
                        descripcion = :descripcion
                    WHERE idAsignatura = :id";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $datos['id'], PDO::PARAM_INT);
            $stmt->bindParam(':codigoAsignatura', $datos['codigoAsignatura']);
            $stmt->bindParam(':nombreAsignatura', $datos['nombreAsignatura']);
            $stmt->bindParam(':descripcion', $datos['descripcion']);
            
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
            error_log("Error en actualizarAsignatura: " . $e->getMessage());
            return false;
        }
    }

    // ELIMINAR ASIGNATURA CON TRANSACCIÓN
    public static function eliminarAsignatura($id)
    {
        $pdo = null;
        try {
            $pdo = ConexionUtil::conectar();
            $pdo->beginTransaction();

            // Verificar si la asignatura tiene horarios asociados
            $sqlVerificar = "SELECT COUNT(*) as total FROM horarios WHERE idAsignatura = :id";
            $stmtVerificar = $pdo->prepare($sqlVerificar);
            $stmtVerificar->bindParam(':id', $id, PDO::PARAM_INT);
            $stmtVerificar->execute();
            $resultado = $stmtVerificar->fetch(PDO::FETCH_ASSOC);
            
            if ($resultado['total'] > 0) {
                $pdo->rollBack();
                return false; // No se puede eliminar porque tiene horarios asociados
            }

            // Si no tiene horarios, proceder a eliminar
            $sql = "DELETE FROM asignaturas WHERE idAsignatura = :id";
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
            error_log("Error en eliminarAsignatura: " . $e->getMessage());
            return false;
        }
    }

    // VERIFICAR SI EXISTE ASIGNATURA POR CÓDIGO (solo lectura)
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

    // VERIFICAR SI EXISTE ASIGNATURA POR NOMBRE (solo lectura)
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

    // VERIFICAR SI LA ASIGNATURA TIENE HORARIOS ASOCIADOS
    public static function tieneHorariosAsociados($id)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT COUNT(*) as total FROM horarios WHERE idAsignatura = :id";
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