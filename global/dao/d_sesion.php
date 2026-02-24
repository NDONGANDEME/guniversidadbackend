<?php
require_once __DIR__ . "/../utilidades/u_conexion.php";

class D_Sesion
{
    /**
     * OBTENER USUARIO POR CORREO
     */
    public static function obtenerUsuarioByCorreo($correo)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT * FROM usuarios WHERE correo = :correo";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':correo', $correo);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Si no hay resultados, devolver null
            return $resultado ? $resultado : null;
            
        } catch (PDOException $e) {
            error_log("Error en obtenerUsuarioByCorreo: " . $e->getMessage());
            return null;
        }
    }

    /**
     * OBTENER USUARIO POR ID
     */
    public static function obtenerUsuarioById($id)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT idUsuario, nombreUsuario, correo, foto, rol, estado, ultimoAcceso,
                           preguntaRecuperacion, RespuestaRecuperacion
                    FROM usuarios 
                    WHERE idUsuario = :id";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado ? $resultado : null;
            
        } catch (PDOException $e) {
            error_log("Error en obtenerUsuarioById: " . $e->getMessage());
            return null;
        }
    }

    /**
     * OBTENER USUARIO POR NOMBRE DE USUARIO
     */
    public static function obtenerUsuarioByNombreUsuario($nombreUsuario)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT * FROM usuarios WHERE nombreUsuario = :nombreUsuario";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':nombreUsuario', $nombreUsuario);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado ? $resultado : null;
            
        } catch (PDOException $e) {
            error_log("Error en obtenerUsuarioByNombreUsuario: " . $e->getMessage());
            return null;
        }
    }

    /**
     * ACTUALIZAR ÚLTIMO ACCESO DEL USUARIO
     */
    public static function actualizarUltimoAcceso($id)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $fechaActual = date('Y-m-d H:i:s');

            $sql = "UPDATE usuarios SET ultimoAcceso = :ultimoAcceso WHERE idUsuario = :id";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':ultimoAcceso', $fechaActual);
            
            return $stmt->execute();
            
        } catch (PDOException $e) {
            error_log("Error en actualizarUltimoAcceso: " . $e->getMessage());
            return false;
        }
    }

    /**
     * VERIFICAR SI EL CORREO EXISTE
     */
    public static function existeCorreo($correo)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT COUNT(*) as total FROM usuarios WHERE correo = :correo";
            $stmt = $instanciaConexion->prepare($sql);
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
     * ACTUALIZAR CONTRASEÑA
     */
    public static function actualizarContrasena($id, $nuevaContrasena)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "UPDATE usuarios SET contrasena = :contrasena WHERE idUsuario = :id";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':contrasena', $nuevaContrasena);
            
            return $stmt->execute();
            
        } catch (PDOException $e) {
            error_log("Error en actualizarContrasena: " . $e->getMessage());
            return false;
        }
    }

    /**
     * VERIFICAR ESTADO DEL USUARIO
     */
    public static function verificarEstadoUsuario($id)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT estado FROM usuarios WHERE idUsuario = :id";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado ? $resultado['estado'] == 1 : false;
            
        } catch (PDOException $e) {
            error_log("Error en verificarEstadoUsuario: " . $e->getMessage());
            return false;
        }
    }

    /**
     * REGISTRAR INTENTO FALLIDO DE LOGIN (opcional)
     */
    public static function registrarIntentoFallido($correo)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $fecha = date('Y-m-d H:i:s');
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'desconocida';

            $sql = "INSERT INTO intentos_login (correo, ip, fecha) VALUES (:correo, :ip, :fecha)";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':correo', $correo);
            $stmt->bindParam(':ip', $ip);
            $stmt->bindParam(':fecha', $fecha);
            $stmt->execute();
            
            return true;
        } catch (PDOException $e) {
            error_log("Error en registrarIntentoFallido: " . $e->getMessage());
            return false;
        }
    }

    /**
     * OBTENER PREGUNTA DE RECUPERACIÓN POR CORREO
     */
    public static function obtenerPreguntaRecuperacion($correo)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT idUsuario, preguntaRecuperacion FROM usuarios WHERE correo = :correo";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':correo', $correo);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error en obtenerPreguntaRecuperacion: " . $e->getMessage());
            return null;
        }
    }
}
?>
