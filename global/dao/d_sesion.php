<?php
require_once __DIR__ . "/../utilidades/u_conexion.php";
require_once __DIR__ . "/../modelo/m_sesion.php";

class D_Sesion
{
    // OBTENER USUARIO POR CORREO
    public static function obtenerUsuarioPorCorreo($correo)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT * FROM usuarios WHERE correo = :correo";
            $stmt = $instanciaConexion->prepare($sql);
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

    // OBTENER USUARIO POR ID
    public static function obtenerUsuarioPorId($id)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT idUsuario, nombreUsuario, correo, foto, rol, estado, ultimoAcceso,
                           preguntaRecuperacion, respuestaRecuperacion
                    FROM usuarios 
                    WHERE idUsuario = :id";
            
            $stmt = $instanciaConexion->prepare($sql);
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

    // OBTENER USUARIO POR NOMBRE DE USUARIO
    public static function obtenerUsuarioPorNombreUsuario($nombreUsuario)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT * FROM usuarios WHERE nombreUsuario = :nombreUsuario";
            $stmt = $instanciaConexion->prepare($sql);
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

    // ACTUALIZAR ÚLTIMO ACCESO DEL USUARIO
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

    // VERIFICAR SI EL CORREO EXISTE
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

    // VERIFICAR SI EL NOMBRE DE USUARIO EXISTE
    public static function existeNombreUsuario($nombreUsuario)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT COUNT(*) as total FROM usuarios WHERE nombreUsuario = :nombreUsuario";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':nombreUsuario', $nombreUsuario);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['total'] > 0;
            
        } catch (PDOException $e) {
            error_log("Error en existeNombreUsuario: " . $e->getMessage());
            return false;
        }
    }

    // ACTUALIZAR CONTRASEÑA
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

    // VERIFICAR ESTADO DEL USUARIO
    public static function verificarEstadoUsuario($id)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT estado FROM usuarios WHERE idUsuario = :id";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado ? $resultado['estado'] == 'activo' : false;
            
        } catch (PDOException $e) {
            error_log("Error en verificarEstadoUsuario: " . $e->getMessage());
            return false;
        }
    }

    // REGISTRAR INTENTO FALLIDO DE LOGIN
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

    // OBTENER PREGUNTA DE RECUPERACIÓN POR CORREO
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

    // VERIFICAR RESPUESTA DE RECUPERACIÓN
    public static function verificarRespuestaRecuperacion($id, $respuesta)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT respuestaRecuperacion FROM usuarios WHERE idUsuario = :id";
            $stmt = $instanciaConexion->prepare($sql);
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