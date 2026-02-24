<?php
require_once __DIR__ . "/../../utilidades/u_conexion.php";

class UsuariosDao
{
    /**
     * FUNCIÓN PARA OBTENER EL NÚMERO DE PÁGINAS (20 usuarios por página)
     */
    public static function contarUsuarios()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT COUNT(*) as total FROM usuarios";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

            return (int) ceil($resultado['total'] / 20);
        } catch (PDOException $e) {
            return 0;
        }
    }

    /**
     * FUNCIÓN PARA OBTENER USUARIOS A PAGINAR
     */
    public static function obtenerUsuariosPaginados($pagina)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $saltos = ($pagina - 1) * 20;
            $lote = 20;

            $sql = "SELECT idUsuario, nombreUsuario, correo, foto, rol, estado, ultimoAcceso 
                    FROM usuarios 
                    ORDER BY idUsuario DESC 
                    LIMIT :lote OFFSET :saltos";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':lote', $lote, PDO::PARAM_INT);
            $stmt->bindParam(':saltos', $saltos, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * FUNCIÓN PARA LISTAR TODOS LOS USUARIOS (sin contraseñas)
     */
    public static function listarUsuarios()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT idUsuario, nombreUsuario, correo, foto, rol, estado, ultimoAcceso 
                    FROM usuarios 
                    ORDER BY idUsuario DESC";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * FUNCIÓN PARA LISTAR USUARIOS POR ROL
     */
    public static function listarUsuariosPorRol($rol)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT idUsuario, nombreUsuario, correo, foto, rol, estado, ultimoAcceso 
                    FROM usuarios 
                    WHERE rol = :rol 
                    ORDER BY idUsuario DESC";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':rol', $rol);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * FUNCIÓN PARA LISTAR USUARIOS ACTIVOS
     */
    public static function listarUsuariosActivos()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT idUsuario, nombreUsuario, correo, foto, rol, estado, ultimoAcceso 
                    FROM usuarios 
                    WHERE estado = 1 
                    ORDER BY idUsuario DESC";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * FUNCIÓN PARA OBTENER USUARIO POR ID (incluye todos los campos)
     */
    public static function obtenerUsuarioPorId(int $id)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT * FROM usuarios WHERE idUsuario = :id";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }

    /**
     * FUNCIÓN PARA OBTENER USUARIO POR NOMBRE DE USUARIO
     */
    public static function obtenerUsuarioPorNombreUsuario($nombreUsuario)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT * FROM usuarios WHERE nombreUsuario = :nombreUsuario";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':nombreUsuario', $nombreUsuario);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }

    /**
     * FUNCIÓN PARA OBTENER USUARIO POR CORREO
     */
    public static function obtenerUsuarioPorCorreo($correo)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT * FROM usuarios WHERE correo = :correo";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':correo', $correo);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }

    /**
     * FUNCIÓN PARA VERIFICAR SI EXISTE NOMBRE DE USUARIO
     */
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
            return false;
        }
    }

    /**
     * FUNCIÓN PARA VERIFICAR SI EXISTE CORREO
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
            return false;
        }
    }

    /**
     * FUNCIÓN PARA CREAR NUEVO USUARIO
     */
    public static function crearUsuario($datos)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "INSERT INTO usuarios (
                        nombreUsuario, 
                        contrasena, 
                        correo, 
                        rol, 
                        estado, 
                        preguntaRecuperacion, 
                        RespuestaRecuperacion
                    ) VALUES (
                        :nombreUsuario, 
                        :contrasena, 
                        :correo, 
                        :rol, 
                        :estado, 
                        :preguntaRecuperacion, 
                        :respuestaRecuperacion
                    )";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':nombreUsuario', $datos['nombreUsuario']);
            $stmt->bindParam(':contrasena', $datos['contrasena']);
            $stmt->bindParam(':correo', $datos['correo']);
            $stmt->bindParam(':rol', $datos['rol']);
            $stmt->bindParam(':estado', $datos['estado']);
            $stmt->bindParam(':preguntaRecuperacion', $datos['preguntaRecuperacion']);
            $stmt->bindParam(':respuestaRecuperacion', $datos['respuestaRecuperacion']);
            
            if ($stmt->execute()) {
                return $instanciaConexion->lastInsertId();
            }
            
            return null;
        } catch (PDOException $e) {
            return null;
        }
    }

    /**
     * FUNCIÓN PARA ACTUALIZAR USUARIO (sin contraseña)
     */
    public static function actualizarUsuario($datos)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "UPDATE usuarios SET 
                        nombreUsuario = :nombreUsuario,
                        correo = :correo,
                        rol = :rol,
                        preguntaRecuperacion = :preguntaRecuperacion,
                        RespuestaRecuperacion = :respuestaRecuperacion";
            
            // Agregar foto solo si viene
            if (isset($datos['foto'])) {
                $sql .= ", foto = :foto";
            }
            
            $sql .= " WHERE idUsuario = :id";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':id', $datos['id']);
            $stmt->bindParam(':nombreUsuario', $datos['nombreUsuario']);
            $stmt->bindParam(':correo', $datos['correo']);
            $stmt->bindParam(':rol', $datos['rol']);
            $stmt->bindParam(':preguntaRecuperacion', $datos['preguntaRecuperacion']);
            $stmt->bindParam(':respuestaRecuperacion', $datos['respuestaRecuperacion']);
            
            if (isset($datos['foto'])) {
                $stmt->bindParam(':foto', $datos['foto']);
            }
            
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * FUNCIÓN PARA ACTUALIZAR SOLO LA FOTO DEL USUARIO
     */
    public static function actualizarFotoUsuario($id, $foto)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "UPDATE usuarios SET foto = :foto WHERE idUsuario = :id";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':foto', $foto);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * FUNCIÓN PARA ACTUALIZAR CONTRASEÑA
     */
    public static function actualizarContrasena($id, $nuevaContrasena)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "UPDATE usuarios SET contrasena = :contrasena WHERE idUsuario = :id";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':contrasena', $nuevaContrasena);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * FUNCIÓN PARA ACTUALIZAR ÚLTIMO ACCESO
     */
    public static function actualizarUltimoAcceso($id)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $fechaActual = date('Y-m-d H:i:s');

            $sql = "UPDATE usuarios SET ultimoAcceso = :ultimoAcceso WHERE idUsuario = :id";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':ultimoAcceso', $fechaActual);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * FUNCIÓN PARA CAMBIAR ESTADO DEL USUARIO (activar/desactivar)
     */
    public static function cambiarEstadoUsuario($id, $estado)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "UPDATE usuarios SET estado = :estado WHERE idUsuario = :id";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':estado', $estado);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * FUNCIÓN PARA ELIMINAR USUARIO (soft delete - cambiar estado a 0)
     */
    public static function eliminarUsuario($id)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $estadoInactivo = 0;

            $sql = "UPDATE usuarios SET estado = :estado WHERE idUsuario = :id";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':estado', $estadoInactivo);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * FUNCIÓN PARA ELIMINAR USUARIO FÍSICAMENTE (si se requiere)
     */
    public static function eliminarUsuarioFisico($id)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "DELETE FROM usuarios WHERE idUsuario = :id";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':id', $id);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * FUNCIÓN PARA BUSCAR USUARIOS (búsqueda genérica)
     */
    public static function buscarUsuarios($termino)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $termino = "%$termino%";

            $sql = "SELECT idUsuario, nombreUsuario, correo, foto, rol, estado, ultimoAcceso 
                    FROM usuarios 
                    WHERE nombreUsuario LIKE :termino 
                       OR correo LIKE :termino 
                       OR rol LIKE :termino 
                    ORDER BY idUsuario DESC";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':termino', $termino);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * FUNCIÓN PARA OBTENER ESTADÍSTICAS DE USUARIOS
     */
    public static function obtenerEstadisticas()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $estadisticas = [];

            // Total de usuarios
            $sql = "SELECT COUNT(*) as total FROM usuarios";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            $estadisticas['total'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            // Usuarios activos
            $sql = "SELECT COUNT(*) as total FROM usuarios WHERE estado = 1";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            $estadisticas['activos'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            // Usuarios inactivos
            $sql = "SELECT COUNT(*) as total FROM usuarios WHERE estado = 0";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            $estadisticas['inactivos'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            // Usuarios por rol
            $sql = "SELECT rol, COUNT(*) as cantidad FROM usuarios GROUP BY rol";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            $estadisticas['por_rol'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $estadisticas;
        } catch (PDOException $e) {
            return [];
        }
    }
}
?>