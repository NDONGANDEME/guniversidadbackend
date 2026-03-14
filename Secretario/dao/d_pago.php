<?php
require_once __DIR__ . "/../../utilidades/u_conexion.php";
require_once __DIR__ . "/../modelo/m_pago.php";

class D_Pago
{
    // OBTENER TODOS LOS PAGOS
    public static function obtenerPagos()
    {
        try {
            $pdo = ConexionUtil::conectar();

            $sql = "SELECT p.*, 
                           m.cursoAcademico, m.modalidadMatricula,
                           e.nombre as nombreEstudiante, e.apellidos as apellidosEstudiante, e.codigoEstudiante,
                           f.nombre as nombreFamiliar, f.apellidos as apellidosFamiliar
                    FROM pagos p
                    INNER JOIN matriculas m ON p.idMatricula = m.idMatricula
                    INNER JOIN estudiantes e ON m.idEstudiante = e.idEstudiante
                    LEFT JOIN familiares f ON p.idFamiliar = f.idFamiliar
                    ORDER BY p.fechaPago DESC";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $pagos = [];
            
            foreach ($resultados as $fila) {
                $model = new PagoModel();
                $model->hidratarDesdeArray($fila);
                
                if (isset($fila['nombreEstudiante'])) {
                    $model->nombreEstudiante = $fila['nombreEstudiante'];
                    $model->apellidosEstudiante = $fila['apellidosEstudiante'] ?? '';
                    $model->codigoEstudiante = $fila['codigoEstudiante'] ?? '';
                }
                
                if (isset($fila['nombreFamiliar'])) {
                    $model->nombreFamiliar = $fila['nombreFamiliar'];
                    $model->apellidosFamiliar = $fila['apellidosFamiliar'] ?? '';
                }
                
                $model->conceptoMatricula = 'Matrícula ' . ($fila['cursoAcademico'] ?? '') . ' - ' . ($fila['modalidadMatricula'] ?? '');
                
                $pagos[] = $model;
            }

            return $pagos;
        } catch (PDOException $e) {
            error_log("Error en obtenerPagos: " . $e->getMessage());
            return [];
        }
    }

    // OBTENER PAGO POR ID
    public static function obtenerPagoPorId($id)
    {
        try {
            $pdo = ConexionUtil::conectar();

            $sql = "SELECT p.*, 
                           m.cursoAcademico, m.modalidadMatricula,
                           e.nombre as nombreEstudiante, e.apellidos as apellidosEstudiante, e.codigoEstudiante,
                           f.nombre as nombreFamiliar, f.apellidos as apellidosFamiliar
                    FROM pagos p
                    INNER JOIN matriculas m ON p.idMatricula = m.idMatricula
                    INNER JOIN estudiantes e ON m.idEstudiante = e.idEstudiante
                    LEFT JOIN familiares f ON p.idFamiliar = f.idFamiliar
                    WHERE p.idPago = :id";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($resultado) {
                $model = new PagoModel();
                $model->hidratarDesdeArray($resultado);
                
                if (isset($resultado['nombreEstudiante'])) {
                    $model->nombreEstudiante = $resultado['nombreEstudiante'];
                    $model->apellidosEstudiante = $resultado['apellidosEstudiante'] ?? '';
                    $model->codigoEstudiante = $resultado['codigoEstudiante'] ?? '';
                }
                
                if (isset($resultado['nombreFamiliar'])) {
                    $model->nombreFamiliar = $resultado['nombreFamiliar'];
                    $model->apellidosFamiliar = $resultado['apellidosFamiliar'] ?? '';
                }
                
                $model->conceptoMatricula = 'Matrícula ' . ($resultado['cursoAcademico'] ?? '') . ' - ' . ($resultado['modalidadMatricula'] ?? '');
                
                return $model;
            }
            
            return null;
        } catch (PDOException $e) {
            error_log("Error en obtenerPagoPorId: " . $e->getMessage());
            return null;
        }
    }

    // OBTENER PAGOS POR MATRÍCULA
    public static function obtenerPagosPorMatricula($idMatricula)
    {
        try {
            $pdo = ConexionUtil::conectar();

            $sql = "SELECT p.*, 
                           f.nombre as nombreFamiliar, f.apellidos as apellidosFamiliar
                    FROM pagos p
                    LEFT JOIN familiares f ON p.idFamiliar = f.idFamiliar
                    WHERE p.idMatricula = :idMatricula
                    ORDER BY p.fechaPago DESC";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':idMatricula', $idMatricula, PDO::PARAM_INT);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $pagos = [];
            
            foreach ($resultados as $fila) {
                $model = new PagoModel();
                $model->hidratarDesdeArray($fila);
                
                if (isset($fila['nombreFamiliar'])) {
                    $model->nombreFamiliar = $fila['nombreFamiliar'];
                    $model->apellidosFamiliar = $fila['apellidosFamiliar'] ?? '';
                }
                
                $pagos[] = $model;
            }

            return $pagos;
        } catch (PDOException $e) {
            error_log("Error en obtenerPagosPorMatricula: " . $e->getMessage());
            return [];
        }
    }

    // OBTENER PAGOS POR FAMILIAR
    public static function obtenerPagosPorFamiliar($idFamiliar)
    {
        try {
            $pdo = ConexionUtil::conectar();

            $sql = "SELECT p.*, 
                           m.cursoAcademico,
                           e.nombre as nombreEstudiante, e.apellidos as apellidosEstudiante
                    FROM pagos p
                    INNER JOIN matriculas m ON p.idMatricula = m.idMatricula
                    INNER JOIN estudiantes e ON m.idEstudiante = e.idEstudiante
                    WHERE p.idFamiliar = :idFamiliar
                    ORDER BY p.fechaPago DESC";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':idFamiliar', $idFamiliar, PDO::PARAM_INT);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $pagos = [];
            
            foreach ($resultados as $fila) {
                $model = new PagoModel();
                $model->hidratarDesdeArray($fila);
                
                if (isset($fila['nombreEstudiante'])) {
                    $model->nombreEstudiante = $fila['nombreEstudiante'];
                    $model->apellidosEstudiante = $fila['apellidosEstudiante'] ?? '';
                }
                
                $pagos[] = $model;
            }

            return $pagos;
        } catch (PDOException $e) {
            error_log("Error en obtenerPagosPorFamiliar: " . $e->getMessage());
            return [];
        }
    }

    // INSERTAR PAGO CON TRANSACCIÓN
    public static function insertarPago($datos)
    {
        $pdo = null;
        try {
            $pdo = ConexionUtil::conectar();
            $pdo->beginTransaction();

            $sql = "INSERT INTO pagos (idMatricula, idFamiliar, cuota, monto, fechaPago) 
                    VALUES (:idMatricula, :idFamiliar, :cuota, :monto, :fechaPago)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':idMatricula', $datos['idMatricula'], PDO::PARAM_INT);
            $stmt->bindParam(':idFamiliar', $datos['idFamiliar'], PDO::PARAM_INT);
            $stmt->bindParam(':cuota', $datos['cuota']);
            $stmt->bindParam(':monto', $datos['monto']);
            $stmt->bindParam(':fechaPago', $datos['fechaPago']);
            
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
            error_log("Error en insertarPago: " . $e->getMessage());
            return null;
        }
    }

    // ACTUALIZAR PAGO CON TRANSACCIÓN
    public static function actualizarPago($id, $datos)
    {
        $pdo = null;
        try {
            $pdo = ConexionUtil::conectar();
            $pdo->beginTransaction();

            $sql = "UPDATE pagos SET 
                        idFamiliar = :idFamiliar,
                        cuota = :cuota,
                        monto = :monto
                    WHERE idPago = :id";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':idFamiliar', $datos['idFamiliar'], PDO::PARAM_INT);
            $stmt->bindParam(':cuota', $datos['cuota']);
            $stmt->bindParam(':monto', $datos['monto']);
            
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
            error_log("Error en actualizarPago: " . $e->getMessage());
            return false;
        }
    }

    // ELIMINAR PAGO CON TRANSACCIÓN
    public static function eliminarPago($id)
    {
        $pdo = null;
        try {
            $pdo = ConexionUtil::conectar();
            $pdo->beginTransaction();

            $sql = "DELETE FROM pagos WHERE idPago = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
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
            error_log("Error en eliminarPago: " . $e->getMessage());
            return false;
        }
    }

    // OBTENER TOTAL PAGADO POR MATRÍCULA
    public static function obtenerTotalPagadoPorMatricula($idMatricula)
    {
        try {
            $pdo = ConexionUtil::conectar();

            $sql = "SELECT SUM(monto) as total FROM pagos WHERE idMatricula = :idMatricula";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':idMatricula', $idMatricula, PDO::PARAM_INT);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['total'] ?? 0;
            
        } catch (PDOException $e) {
            error_log("Error en obtenerTotalPagadoPorMatricula: " . $e->getMessage());
            return 0;
        }
    }
}
?>