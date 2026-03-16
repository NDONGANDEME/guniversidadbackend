<?php
require_once __DIR__ . "/../../utilidades/u_conexion.php";
require_once __DIR__ . "/../modelo/m_sesion.php";

class D_Sesion
{
    /**
     * OBTENER USUARIO COMPLETO CON SU ROL Y PERMISOS
     * SEGÚN MODELO: usuario -> rol -> rol_permiso -> permiso
     */
    public static function obtenerUsuarioCompleto($identificador)
    {
        $pdo = null;
        try {
            $pdo = ConexionUtil::conectar();
            
            // Iniciar transacción
            $pdo->beginTransaction();

            // Determinar si el identificador es email o nombre de usuario
            $campo = filter_var($identificador, FILTER_VALIDATE_EMAIL) ? 'correo' : 'nombreUsuario';
            
            // 1. OBTENER DATOS BÁSICOS DEL USUARIO CON SU ROL
            $sqlUsuario = "SELECT u.*, r.nombreRol, r.idRol
                          FROM usuarios u
                          INNER JOIN rol r ON u.idRol = r.idRol
                          WHERE u.$campo = :identificador
                          LIMIT 1";
            
            $stmt = $pdo->prepare($sqlUsuario);
            $stmt->bindParam(':identificador', $identificador);
            $stmt->execute();
            
            $datosUsuario = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$datosUsuario) {
                $pdo->rollBack();
                return null;
            }
            
            // Crear modelo y hidratar
            $model = new SesionModel();
            $model->hidratarDesdeArray($datosUsuario);
            
            // 2. OBTENER PERMISOS A TRAVÉS DEL ROL (tabla rol_permiso)
            $sqlPermisos = "SELECT p.idPermiso, p.nombrePermiso, p.tabla, p.accion
                           FROM permiso p
                           INNER JOIN rol_permiso rp ON p.idPermiso = rp.idPermiso
                           WHERE rp.idRol = :idRol";
            
            $stmtPermisos = $pdo->prepare($sqlPermisos);
            $stmtPermisos->bindParam(':idRol', $datosUsuario['idRol'], PDO::PARAM_INT);
            $stmtPermisos->execute();
            
            $permisos = $stmtPermisos->fetchAll(PDO::FETCH_ASSOC);
            $model->establecerPermisos($permisos);
            
            // Confirmar transacción
            $pdo->commit();
            
            return $model;
            
        } catch (PDOException $e) {
            // Revertir transacción en caso de error
            if ($pdo) {
                $pdo->rollBack();
            }
            error_log("Error en obtenerUsuarioCompleto: " . $e->getMessage());
            return null;
        }
    }

    /**
     * OBTENER USUARIO POR CORREO
     */
    public static function obtenerUsuarioPorCorreo($correo)
    {
        try {
            $pdo = ConexionUtil::conectar();
            
            $sql = "SELECT u.*, r.nombreRol, r.idRol
                    FROM usuarios u
                    INNER JOIN rol r ON u.idRol = r.idRol
                    WHERE u.correo = :correo
                    LIMIT 1";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':correo', $correo);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($resultado) {
                $model = new SesionModel();
                return $model->hidratarDesdeArray($resultado);
            }
            
            return null;
            
        } catch (PDOException $e) {
            error_log("Error en obtenerUsuarioPorCorreo: " . $e->getMessage());
            return null;
        }
    }

    /**
     * OBTENER USUARIO POR ID
     */
    public static function obtenerUsuarioPorId($id)
    {
        try {
            $pdo = ConexionUtil::conectar();

            $sql = "SELECT u.*, r.nombreRol, r.idRol
                    FROM usuarios u
                    INNER JOIN rol r ON u.idRol = r.idRol
                    WHERE u.idUsuario = :id
                    LIMIT 1";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($resultado) {
                $model = new SesionModel();
                return $model->hidratarDesdeArray($resultado);
            }
            
            return null;
            
        } catch (PDOException $e) {
            error_log("Error en obtenerUsuarioPorId: " . $e->getMessage());
            return null;
        }
    }

    /**
     * OBTENER USUARIO POR NOMBRE DE USUARIO
     */
    public static function obtenerUsuarioPorNombreUsuario($nombreUsuario)
    {
        try {
            $pdo = ConexionUtil::conectar();

            $sql = "SELECT u.*, r.nombreRol, r.idRol
                    FROM usuarios u
                    INNER JOIN rol r ON u.idRol = r.idRol
                    WHERE u.nombreUsuario = :nombreUsuario
                    LIMIT 1";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nombreUsuario', $nombreUsuario);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($resultado) {
                $model = new SesionModel();
                return $model->hidratarDesdeArray($resultado);
            }
            
            return null;
            
        } catch (PDOException $e) {
            error_log("Error en obtenerUsuarioPorNombreUsuario: " . $e->getMessage());
            return null;
        }
    }

    /**
     * OBTENER PERMISOS DE UN ROL
     */
    public static function obtenerPermisosPorRol($idRol)
    {
        try {
            $pdo = ConexionUtil::conectar();

            $sql = "SELECT p.nombrePermiso 
                    FROM permiso p
                    INNER JOIN rol_permiso rp ON p.idPermiso = rp.idPermiso
                    WHERE rp.idRol = :idRol";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':idRol', $idRol, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_COLUMN);
            
        } catch (PDOException $e) {
            error_log("Error en obtenerPermisosPorRol: " . $e->getMessage());
            return [];
        }
    }

    /**
     * OBTENER PERMISOS DETALLADOS DE UN ROL
     */
    public static function obtenerPermisosDetalladosPorRol($idRol)
    {
        try {
            $pdo = ConexionUtil::conectar();

            $sql = "SELECT p.idPermiso, p.nombrePermiso, p.tabla, p.accion
                    FROM permiso p
                    INNER JOIN rol_permiso rp ON p.idPermiso = rp.idPermiso
                    WHERE rp.idRol = :idRol";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':idRol', $idRol, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error en obtenerPermisosDetalladosPorRol: " . $e->getMessage());
            return [];
        }
    }

    /**
     * VERIFICAR SI UN ROL TIENE UN PERMISO ESPECÍFICO
     */
    public static function rolTienePermiso($idRol, $idPermiso)
    {
        try {
            $pdo = ConexionUtil::conectar();

            $sql = "SELECT COUNT(*) as total FROM rol_permiso 
                    WHERE idRol = :idRol AND idPermiso = :idPermiso";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':idRol', $idRol, PDO::PARAM_INT);
            $stmt->bindParam(':idPermiso', $idPermiso, PDO::PARAM_INT);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['total'] > 0;
            
        } catch (PDOException $e) {
            error_log("Error en rolTienePermiso: " . $e->getMessage());
            return false;
        }
    }

    /**
     * VERIFICAR SI UN USUARIO TIENE UN PERMISO ESPECÍFICO
     */
    public static function usuarioTienePermiso($idUsuario, $idPermiso)
    {
        try {
            $pdo = ConexionUtil::conectar();

            $sql = "SELECT COUNT(*) as total 
                    FROM usuarios u
                    INNER JOIN rol_permiso rp ON u.idRol = rp.idRol
                    WHERE u.idUsuario = :idUsuario AND rp.idPermiso = :idPermiso";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
            $stmt->bindParam(':idPermiso', $idPermiso, PDO::PARAM_INT);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['total'] > 0;
            
        } catch (PDOException $e) {
            error_log("Error en usuarioTienePermiso: " . $e->getMessage());
            return false;
        }
    }

    /**
     * ASIGNAR PERMISO A ROL
     */
    public static function asignarPermisoARol($idRol, $idPermiso)
    {
        $pdo = null;
        try {
            $pdo = ConexionUtil::conectar();
            $pdo->beginTransaction();

            // Verificar si ya tiene el permiso
            if (self::rolTienePermiso($idRol, $idPermiso)) {
                $pdo->rollBack();
                return false;
            }

            $sql = "INSERT INTO rol_permiso (idRol, idPermiso) 
                    VALUES (:idRol, :idPermiso)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':idRol', $idRol, PDO::PARAM_INT);
            $stmt->bindParam(':idPermiso', $idPermiso, PDO::PARAM_INT);
            
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
            error_log("Error en asignarPermisoARol: " . $e->getMessage());
            return false;
        }
    }

    /**
     * QUITAR PERMISO A ROL
     */
    public static function quitarPermisoARol($idRol, $idPermiso)
    {
        $pdo = null;
        try {
            $pdo = ConexionUtil::conectar();
            $pdo->beginTransaction();

            $sql = "DELETE FROM rol_permiso 
                    WHERE idRol = :idRol AND idPermiso = :idPermiso";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':idRol', $idRol, PDO::PARAM_INT);
            $stmt->bindParam(':idPermiso', $idPermiso, PDO::PARAM_INT);
            
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
            error_log("Error en quitarPermisoARol: " . $e->getMessage());
            return false;
        }
    }

    /**
     * ACTUALIZAR ÚLTIMO ACCESO DEL USUARIO
     */
    public static function actualizarUltimoAcceso($id)
    {
        $pdo = null;
        try {
            $pdo = ConexionUtil::conectar();
            $pdo->beginTransaction();
            
            $fechaActual = date('Y-m-d H:i:s');

            $sql = "UPDATE usuarios SET ultimoAcceso = :ultimoAcceso WHERE idUsuario = :id";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':ultimoAcceso', $fechaActual);
            
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
            error_log("Error en actualizarUltimoAcceso: " . $e->getMessage());
            return false;
        }
    }

    /**
     * ACTUALIZAR CONTRASEÑA
     */
    public static function actualizarContrasena($id, $nuevaContrasena)
    {
        $pdo = null;
        try {
            $pdo = ConexionUtil::conectar();
            $pdo->beginTransaction();

            $sql = "UPDATE usuarios SET contrasena = :contrasena WHERE idUsuario = :id";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':contrasena', $nuevaContrasena);
            
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
            error_log("Error en actualizarContrasena: " . $e->getMessage());
            return false;
        }
    }

    /**
     * VERIFICAR SI EL CORREO EXISTE
     */
    public static function existeCorreo($correo)
    {
        try {
            $pdo = ConexionUtil::conectar();

            $sql = "SELECT COUNT(*) as total FROM usuarios WHERE correo = :correo";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':correo', $correo);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['total'] > 0;
            
        } catch (PDOException $e) {
            error_log("Error en existeCorreo: " . $e->getMessage());
            return false;
        }
    }

    /**
     * VERIFICAR SI EL NOMBRE DE USUARIO EXISTE
     */
    public static function existeNombreUsuario($nombreUsuario)
    {
        try {
            $pdo = ConexionUtil::conectar();

            $sql = "SELECT COUNT(*) as total FROM usuarios WHERE nombreUsuario = :nombreUsuario";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nombreUsuario', $nombreUsuario);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['total'] > 0;
            
        } catch (PDOException $e) {
            error_log("Error en existeNombreUsuario: " . $e->getMessage());
            return false;
        }
    }

    /**
     * VERIFICAR ESTADO DEL USUARIO
     */
    public static function verificarEstadoUsuario($id)
    {
        try {
            $pdo = ConexionUtil::conectar();

            $sql = "SELECT estado FROM usuarios WHERE idUsuario = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado ? $resultado['estado'] == 'activo' : false;
            
        } catch (PDOException $e) {
            error_log("Error en verificarEstadoUsuario: " . $e->getMessage());
            return false;
        }
    }

    /**
     * OBTENER PREGUNTA DE RECUPERACIÓN POR CORREO
     */
    public static function obtenerPreguntaRecuperacion($correo)
    {
        try {
            $pdo = ConexionUtil::conectar();

            $sql = "SELECT idUsuario, preguntaRecuperacion FROM usuarios WHERE correo = :correo";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':correo', $correo);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error en obtenerPreguntaRecuperacion: " . $e->getMessage());
            return null;
        }
    }

    /**
     * VERIFICAR RESPUESTA DE RECUPERACIÓN
     */
    public static function verificarRespuestaRecuperacion($id, $respuesta)
    {
        try {
            $pdo = ConexionUtil::conectar();

            $sql = "SELECT respuestaRecuperacion FROM usuarios WHERE idUsuario = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($resultado) {
                return strtolower(trim($resultado['respuestaRecuperacion'])) === strtolower(trim($respuesta));
            }
            
            return false;
            
        } catch (PDOException $e) {
            error_log("Error en verificarRespuestaRecuperacion: " . $e->getMessage());
            return false;
        }
    }
}
?>