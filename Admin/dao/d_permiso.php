<?php
require_once __DIR__ . "/../../utilidades/u_conexion.php";
require_once __DIR__ . "/../modelo/m_permiso.php";

class D_Permiso
{
    // OBTENER TODOS LOS PERMISOS
    public static function obtenerPermisos()
    {
        try {
            $pdo = ConexionUtil::conectar();

            $sql = "SELECT * FROM permiso ORDER BY nombrePermiso ASC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $permisos = [];
            
            foreach ($resultados as $fila) {
                $model = new PermisoModel();
                $model->hidratarDesdeArray($fila);
                $permisos[] = $model;
            }

            return $permisos;
        } catch (PDOException $e) {
            error_log("Error en obtenerPermisos: " . $e->getMessage());
            return [];
        }
    }

    // OBTENER PERMISO POR ID
    public static function obtenerPermisoPorId($id)
    {
        try {
            $pdo = ConexionUtil::conectar();

            $sql = "SELECT * FROM permiso WHERE idPermiso = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($resultado) {
                $model = new PermisoModel();
                return $model->hidratarDesdeArray($resultado);
            }
            
            return null;
        } catch (PDOException $e) {
            error_log("Error en obtenerPermisoPorId: " . $e->getMessage());
            return null;
        }
    }

    // OBTENER PERMISO POR NOMBRE
    public static function obtenerPermisoPorNombre($nombrePermiso)
    {
        try {
            $pdo = ConexionUtil::conectar();

            $sql = "SELECT * FROM permiso WHERE nombrePermiso = :nombrePermiso";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nombrePermiso', $nombrePermiso);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($resultado) {
                $model = new PermisoModel();
                return $model->hidratarDesdeArray($resultado);
            }
            
            return null;
        } catch (PDOException $e) {
            error_log("Error en obtenerPermisoPorNombre: " . $e->getMessage());
            return null;
        }
    }

    // INSERTAR PERMISO CON TRANSACCIÓN
    public static function insertarPermiso($datos)
    {
        $pdo = null;
        try {
            $pdo = ConexionUtil::conectar();
            $pdo->beginTransaction();

            $sql = "INSERT INTO permiso (nombrePermiso, tabla, accion) 
                    VALUES (:nombrePermiso, :tabla, :accion)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nombrePermiso', $datos['nombrePermiso']);
            $stmt->bindParam(':tabla', $datos['tabla']);
            $stmt->bindParam(':accion', $datos['accionPermiso']);
            
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
            error_log("Error en insertarPermiso: " . $e->getMessage());
            return null;
        }
    }

    // ACTUALIZAR PERMISO CON TRANSACCIÓN
    public static function actualizarPermiso($id, $datos)
    {
        $pdo = null;
        try {
            $pdo = ConexionUtil::conectar();
            $pdo->beginTransaction();

            $sql = "UPDATE permiso SET 
                        nombrePermiso = :nombrePermiso,
                        tabla = :tabla,
                        accion = :accion
                    WHERE idPermiso = :id";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':nombrePermiso', $datos['nombrePermiso']);
            $stmt->bindParam(':tabla', $datos['tabla']);
            $stmt->bindParam(':accion', $datos['accionPermiso']);
            
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
            error_log("Error en actualizarPermiso: " . $e->getMessage());
            return false;
        }
    }

    // ELIMINAR PERMISO CON TRANSACCIÓN
    public static function eliminarPermiso($id)
    {
        $pdo = null;
        try {
            $pdo = ConexionUtil::conectar();
            $pdo->beginTransaction();

            // Primero eliminar las relaciones en rol_permiso
            $sqlRelaciones = "DELETE FROM rol_permiso WHERE idPermiso = :id";
            $stmtRelaciones = $pdo->prepare($sqlRelaciones);
            $stmtRelaciones->bindParam(':id', $id, PDO::PARAM_INT);
            $stmtRelaciones->execute();

            // Luego eliminar el permiso
            $sql = "DELETE FROM permiso WHERE idPermiso = :id";
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
            error_log("Error en eliminarPermiso: " . $e->getMessage());
            return false;
        }
    }

    // VERIFICAR SI EXISTE PERMISO POR NOMBRE
    public static function existePermisoPorNombre($nombrePermiso, $excluirId = null)
    {
        try {
            $pdo = ConexionUtil::conectar();

            $sql = "SELECT COUNT(*) as total FROM permiso WHERE nombrePermiso = :nombrePermiso";
            
            if ($excluirId !== null) {
                $sql .= " AND idPermiso != :excluirId";
            }
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nombrePermiso', $nombrePermiso);
            
            if ($excluirId !== null) {
                $stmt->bindParam(':excluirId', $excluirId, PDO::PARAM_INT);
            }
            
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['total'] > 0;
            
        } catch (PDOException $e) {
            error_log("Error en existePermisoPorNombre: " . $e->getMessage());
            return false;
        }
    }

    /**
     * OBTENER LOS NOMBRES DE TODAS LAS TABLAS DE LA BASE DE DATOS
     * CON LOS NOMBRES EN SINGULAR
     * @return array Lista de nombres de tablas en singular
     */
    public static function obtenerNombresTablas()
    {
        try {
            $pdo = ConexionUtil::conectar();

            // Consulta para obtener todas las tablas de la base de datos
            $sql = "SHOW TABLES";
            $stmt = $pdo->prepare($sql);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_NUM);
            $tablas = [];
            
            foreach ($resultados as $fila) {
                $nombreTabla = $fila[0];
                $tablas[] = self::convertirASingular($nombreTabla);
            }
            
            // Ordenar alfabéticamente
            sort($tablas);
            
            return $tablas;
            
        } catch (PDOException $e) {
            error_log("Error en obtenerNombresTablas: " . $e->getMessage());
            return [];
        }
    }

    /**
     * CONVERTIR NOMBRE DE TABLA DE PLURAL A SINGULAR
     * @param string $nombreTabla Nombre de la tabla en plural
     * @return string Nombre de la tabla en singular
     */
    private static function convertirASingular($nombreTabla)
    {
        $nombreTabla = strtolower($nombreTabla);
        
        // Reglas de conversión plural a singular en español
        $reglas = [
            // Palabras que terminan en 'es'
            '/ases$/' => 'as',
            '/eses$/' => 'es',
            '/ises$/' => 'is',
            '/oses$/' => 'os',
            
            // Palabras que terminan en 's' pero no en 'es'
            '/as$/' => 'a',
            '/es$/' => 'e',
            '/is$/' => 'i',
            '/os$/' => 'o',
            '/us$/' => 'u',
            
            // Casos especiales comunes en bases de datos
            '/ciones$/' => 'cion',
            '/dades$/' => 'dad',
            '/mientos$/' => 'miento',
            '/tudes$/' => 'tud',
            
            // Eliminar 's' final para plurales regulares
            '/s$/' => ''
        ];
        
        // Aplicar reglas
        foreach ($reglas as $patron => $reemplazo) {
            $nuevoNombre = preg_replace($patron, $reemplazo, $nombreTabla);
            if ($nuevoNombre !== $nombreTabla) {
                return ucfirst($nuevoNombre);
            }
        }
        
        // Si no se aplicó ninguna regla, devolver el nombre original con primera letra mayúscula
        return ucfirst($nombreTabla);
    }

}
?>