<?php
require_once __DIR__ . "/../../utilidades/u_conexion.php";
require_once __DIR__ . "/../modelo/m_departamento.php";

class D_Departamento
{
    // CONSTANTE PARA EL NÚMERO DE REGISTROS POR PÁGINA
    const REGISTROS_POR_PAGINA = 8;

    // OBTENER TODOS LOS DEPARTAMENTOS (solo lectura)
    public static function obtenerDepartamentos()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT d.*, f.nombreFacultad
                    FROM departamento d
                    LEFT JOIN facultad f ON d.idFacultad = f.idFacultad
                    ORDER BY d.nombreDepartamento ASC";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $resultados;
        } catch (PDOException $e) {
            error_log("Error en obtenerDepartamentos: " . $e->getMessage());
            return [];
        }
    }

    // OBTENER EL NÚMERO DE PÁGINAS (30 departamentos por página)
    public static function contarDepartamentos()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT COUNT(*) as total FROM departamento";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

            return (int) ceil($resultado['total'] / self::REGISTROS_POR_PAGINA);
        } catch (PDOException $e) {
            error_log("Error en contarDepartamentos: " . $e->getMessage());
            return 0;
        }
    }

    // OBTENER DEPARTAMENTOS A PAGINAR
    public static function obtenerDepartamentosAPaginar($pagina)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $saltos = ($pagina - 1) * self::REGISTROS_POR_PAGINA;
            $lote = self::REGISTROS_POR_PAGINA;

            $sql = "SELECT d.*, f.nombreFacultad
                    FROM departamento d
                    LEFT JOIN facultad f ON d.idFacultad = f.idFacultad
                    ORDER BY d.nombreDepartamento ASC 
                    LIMIT :lote OFFSET :saltos";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':lote', $lote, PDO::PARAM_INT);
            $stmt->bindParam(':saltos', $saltos, PDO::PARAM_INT);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $departamentos = [];
            foreach ($resultados as $fila) {
                $model = new DepartamentoModel();
                $model->hidratarDesdeArray($fila);
                if (isset($fila['nombreFacultad'])) {
                    $model->nombreFacultad = $fila['nombreFacultad'];
                }
                $departamentos[] = $model;
            }

            return $departamentos;
        } catch (PDOException $e) {
            error_log("Error en obtenerDepartamentosAPaginar: " . $e->getMessage());
            return [];
        }
    }

    // OBTENER DEPARTAMENTO POR ID (solo lectura)
    public static function obtenerDepartamentoPorId($id)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT * FROM departamento WHERE idDepartamento = :id";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($resultado) {
                $model = new DepartamentoModel();
                return $model->hidratarDesdeArray($resultado);
            }
            
            return null;
        } catch (PDOException $e) {
            error_log("Error en obtenerDepartamentoPorId: " . $e->getMessage());
            return null;
        }
    }

    // OBTENER DEPARTAMENTOS POR FACULTAD (solo lectura)
    public static function obtenerDepartamentosPorFacultad($idFacultad)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT * FROM departamento WHERE idFacultad = :idFacultad ORDER BY nombreDepartamento ASC";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idFacultad', $idFacultad, PDO::PARAM_INT);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $departamentos = [];
            
            foreach ($resultados as $fila) {
                $model = new DepartamentoModel();
                $model->hidratarDesdeArray($fila);
                $departamentos[] = $model;
            }

            return $departamentos;
        } catch (PDOException $e) {
            error_log("Error en obtenerDepartamentosPorFacultad: " . $e->getMessage());
            return [];
        }
    }

    // CONTAR DEPARTAMENTOS POR FACULTAD
    public static function contarDepartamentosPorFacultad($idFacultad)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT COUNT(*) as total FROM departamento WHERE idFacultad = :idFacultad";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idFacultad', $idFacultad, PDO::PARAM_INT);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int) $resultado['total'];
        } catch (PDOException $e) {
            error_log("Error en contarDepartamentosPorFacultad: " . $e->getMessage());
            return 0;
        }
    }

    // OBTENER DEPARTAMENTOS POR FACULTAD CON PAGINACIÓN
    public static function obtenerDepartamentosPorFacultadPaginadas($idFacultad, $pagina)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $saltos = ($pagina - 1) * self::REGISTROS_POR_PAGINA;
            $lote = self::REGISTROS_POR_PAGINA;

            $sql = "SELECT d.*, f.nombreFacultad
                    FROM departamento d
                    LEFT JOIN facultad f ON d.idFacultad = f.idFacultad
                    WHERE d.idFacultad = :idFacultad 
                    ORDER BY d.nombreDepartamento ASC 
                    LIMIT :lote OFFSET :saltos";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idFacultad', $idFacultad, PDO::PARAM_INT);
            $stmt->bindParam(':lote', $lote, PDO::PARAM_INT);
            $stmt->bindParam(':saltos', $saltos, PDO::PARAM_INT);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $departamentos = [];
            
            foreach ($resultados as $fila) {
                $model = new DepartamentoModel();
                $model->hidratarDesdeArray($fila);
                if (isset($fila['nombreFacultad'])) {
                    $model->nombreFacultad = $fila['nombreFacultad'];
                }
                $departamentos[] = $model;
            }

            return $departamentos;
        } catch (PDOException $e) {
            error_log("Error en obtenerDepartamentosPorFacultadPaginadas: " . $e->getMessage());
            return [];
        }
    }

    // BUSCAR DEPARTAMENTOS POR TÉRMINO
    public static function buscarDepartamentos($termino)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $terminoBusqueda = "%$termino%";

            $sql = "SELECT d.*, f.nombreFacultad
                    FROM departamento d
                    LEFT JOIN facultad f ON d.idFacultad = f.idFacultad
                    WHERE d.nombreDepartamento LIKE :termino 
                    ORDER BY d.nombreDepartamento ASC";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':termino', $terminoBusqueda);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $departamentos = [];
            
            foreach ($resultados as $fila) {
                $model = new DepartamentoModel();
                $model->hidratarDesdeArray($fila);
                if (isset($fila['nombreFacultad'])) {
                    $model->nombreFacultad = $fila['nombreFacultad'];
                }
                $departamentos[] = $model;
            }

            return $departamentos;
        } catch (PDOException $e) {
            error_log("Error en buscarDepartamentos: " . $e->getMessage());
            return [];
        }
    }

    // BUSCAR DEPARTAMENTOS POR TÉRMINO CON PAGINACIÓN
    public static function buscarDepartamentosPaginados($termino, $pagina)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $terminoBusqueda = "%$termino%";
            
            $saltos = ($pagina - 1) * self::REGISTROS_POR_PAGINA;
            $lote = self::REGISTROS_POR_PAGINA;

            $sql = "SELECT d.*, f.nombreFacultad
                    FROM departamento d
                    LEFT JOIN facultad f ON d.idFacultad = f.idFacultad
                    WHERE d.nombreDepartamento LIKE :termino 
                    ORDER BY d.nombreDepartamento ASC
                    LIMIT :lote OFFSET :saltos";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':termino', $terminoBusqueda);
            $stmt->bindParam(':lote', $lote, PDO::PARAM_INT);
            $stmt->bindParam(':saltos', $saltos, PDO::PARAM_INT);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $departamentos = [];
            
            foreach ($resultados as $fila) {
                $model = new DepartamentoModel();
                $model->hidratarDesdeArray($fila);
                if (isset($fila['nombreFacultad'])) {
                    $model->nombreFacultad = $fila['nombreFacultad'];
                }
                $departamentos[] = $model;
            }

            return $departamentos;
        } catch (PDOException $e) {
            error_log("Error en buscarDepartamentosPaginados: " . $e->getMessage());
            return [];
        }
    }

    // CONTAR RESULTADOS DE BÚSQUEDA
    public static function contarResultadosBusquedaDepartamentos($termino)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $terminoBusqueda = "%$termino%";

            $sql = "SELECT COUNT(*) as total 
                    FROM departamento 
                    WHERE nombreDepartamento LIKE :termino";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':termino', $terminoBusqueda);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int) ceil($resultado['total'] / self::REGISTROS_POR_PAGINA);
        } catch (PDOException $e) {
            error_log("Error en contarResultadosBusquedaDepartamentos: " . $e->getMessage());
            return 0;
        }
    }

    // INSERTAR DEPARTAMENTO CON TRANSACCIÓN
    public static function insertarDepartamento($datos)
    {
        $pdo = null;
        try {
            $pdo = ConexionUtil::conectar();
            $pdo->beginTransaction();

            $sql = "INSERT INTO departamento (nombreDepartamento, idFacultad) 
                    VALUES (:nombreDepartamento, :idFacultad)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nombreDepartamento', $datos['nombreDepartamento']);
            $stmt->bindParam(':idFacultad', $datos['idFacultad'], PDO::PARAM_INT);
            
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
            error_log("Error en insertarDepartamento: " . $e->getMessage());
            return null;
        }
    }

    // ACTUALIZAR DEPARTAMENTO CON TRANSACCIÓN
    public static function actualizarDepartamento($datos)
    {
        $pdo = null;
        try {
            $pdo = ConexionUtil::conectar();
            $pdo->beginTransaction();

            $sql = "UPDATE departamento SET 
                        nombreDepartamento = :nombreDepartamento,
                        idFacultad = :idFacultad
                    WHERE idDepartamento = :id";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $datos['id'], PDO::PARAM_INT);
            $stmt->bindParam(':nombreDepartamento', $datos['nombreDepartamento']);
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
            error_log("Error en actualizarDepartamento: " . $e->getMessage());
            return false;
        }
    }

    // ELIMINAR DEPARTAMENTO CON TRANSACCIÓN
    public static function eliminarDepartamento($id)
    {
        $pdo = null;
        try {
            $pdo = ConexionUtil::conectar();
            $pdo->beginTransaction();

            // Verificar si el departamento tiene carreras asociadas
            $sqlVerificar = "SELECT COUNT(*) as total FROM carrera WHERE idDepartamento = :id";
            $stmtVerificar = $pdo->prepare($sqlVerificar);
            $stmtVerificar->bindParam(':id', $id, PDO::PARAM_INT);
            $stmtVerificar->execute();
            $resultado = $stmtVerificar->fetch(PDO::FETCH_ASSOC);
            
            if ($resultado['total'] > 0) {
                $pdo->rollBack();
                return false; // No se puede eliminar porque tiene carreras asociadas
            }

            // Si no tiene carreras, proceder a eliminar
            $sql = "DELETE FROM departamento WHERE idDepartamento = :id";
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
            error_log("Error en eliminarDepartamento: " . $e->getMessage());
            return false;
        }
    }

    // VERIFICAR SI EXISTE DEPARTAMENTO (solo lectura)
    public static function existeDepartamento($nombreDepartamento, $excluirId = null)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT COUNT(*) as total FROM departamento WHERE nombreDepartamento = :nombreDepartamento";
            
            if ($excluirId !== null) {
                $sql .= " AND idDepartamento != :excluirId";
            }
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':nombreDepartamento', $nombreDepartamento);
            
            if ($excluirId !== null) {
                $stmt->bindParam(':excluirId', $excluirId, PDO::PARAM_INT);
            }
            
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['total'] > 0;
        } catch (PDOException $e) {
            error_log("Error en existeDepartamento: " . $e->getMessage());
            return false;
        }
    }

    // VERIFICAR SI EL DEPARTAMENTO TIENE CARRERAS ASOCIADAS
    public static function tieneCarrerasAsociadas($id)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT COUNT(*) as total FROM carrera WHERE idDepartamento = :id";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['total'] > 0;
        } catch (PDOException $e) {
            error_log("Error en tieneCarrerasAsociadas: " . $e->getMessage());
            return false;
        }
    }
}
?>