<?php
require_once __DIR__ . "/../../utilidades/u_conexion.php";
require_once __DIR__ . "/../modelo/m_usuario.php";

class D_Usuario
{
    // OBTENER TODOS LOS USUARIOS
    public static function obtenerUsuarios()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT idUsuario, nombreUsuario, correo, foto, rol, estado, ultimoAcceso 
                    FROM usuarios 
                    ORDER BY idUsuario DESC";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $usuarios = [];
            
            foreach ($resultados as $fila) {
                $model = new UsuarioModel();
                $model->hidratarDesdeArray($fila);
                $usuarios[] = $model;
            }

            return $usuarios;
        } catch (PDOException $e) {
            error_log("Error en obtenerUsuarios: " . $e->getMessage());
            return [];
        }
    }

    // OBTENER USUARIO POR ID
    public static function obtenerUsuarioPorId($id)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT * FROM usuarios WHERE idUsuario = :id";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($resultado) {
                $model = new UsuarioModel();
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
                $model = new UsuarioModel();
                return $model->hidratarDesdeArray($resultado);
            }
            
            return null;
        } catch (PDOException $e) {
            error_log("Error en obtenerUsuarioPorNombreUsuario: " . $e->getMessage());
            return null;
        }
    }

    // VERIFICAR SI EXISTE NOMBRE DE USUARIO
    public static function existeNombreUsuario($nombreUsuario, $excluirId = null)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT COUNT(*) as total FROM usuarios WHERE nombreUsuario = :nombreUsuario";
            
            if ($excluirId !== null) {
                $sql .= " AND idUsuario != :excluirId";
            }
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':nombreUsuario', $nombreUsuario);
            
            if ($excluirId !== null) {
                $stmt->bindParam(':excluirId', $excluirId, PDO::PARAM_INT);
            }
            
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['total'] > 0;
        } catch (PDOException $e) {
            error_log("Error en existeNombreUsuario: " . $e->getMessage());
            return false;
        }
    }

    // VERIFICAR SI EXISTE CORREO
    public static function existeCorreo($correo, $excluirId = null)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT COUNT(*) as total FROM usuarios WHERE correo = :correo";
            
            if ($excluirId !== null) {
                $sql .= " AND idUsuario != :excluirId";
            }
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':correo', $correo);
            
            if ($excluirId !== null) {
                $stmt->bindParam(':excluirId', $excluirId, PDO::PARAM_INT);
            }
            
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['total'] > 0;
        } catch (PDOException $e) {
            error_log("Error en existeCorreo: " . $e->getMessage());
            return false;
        }
    }

    // INSERTAR USUARIO
    public static function insertarUsuario($datos)
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
                        respuestaRecuperacion,
                        foto
                    ) VALUES (
                        :nombreUsuario, 
                        :contrasena, 
                        :correo, 
                        :rol, 
                        :estado, 
                        :preguntaRecuperacion, 
                        :respuestaRecuperacion,
                        :foto
                    )";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':nombreUsuario', $datos['nombreUsuario']);
            $stmt->bindParam(':contrasena', $datos['contrasena']);
            $stmt->bindParam(':correo', $datos['correo']);
            $stmt->bindParam(':rol', $datos['rol']);
            $stmt->bindParam(':estado', $datos['estado']);
            $stmt->bindParam(':preguntaRecuperacion', $datos['preguntaRecuperacion']);
            $stmt->bindParam(':respuestaRecuperacion', $datos['respuestaRecuperacion']);
            $stmt->bindParam(':foto', $datos['foto']);
            
            if ($stmt->execute()) {
                return $instanciaConexion->lastInsertId();
            }
            
            return null;
        } catch (PDOException $e) {
            error_log("Error en insertarUsuario: " . $e->getMessage());
            return null;
        }
    }

    // ACTUALIZAR USUARIO
    public static function actualizarUsuario($datos)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "UPDATE usuarios SET 
                        nombreUsuario = :nombreUsuario,
                        correo = :correo,
                        rol = :rol,
                        preguntaRecuperacion = :preguntaRecuperacion,
                        respuestaRecuperacion = :respuestaRecuperacion";
            
            // Agregar foto solo si viene
            if (isset($datos['foto']) && $datos['foto'] !== null) {
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
            
            if (isset($datos['foto']) && $datos['foto'] !== null) {
                $stmt->bindParam(':foto', $datos['foto']);
            }
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en actualizarUsuario: " . $e->getMessage());
            return false;
        }
    }

    // CAMBIAR ESTADO DEL USUARIO (habilitar/deshabilitar)
    public static function cambiarEstadoUsuario($id, $estado)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "UPDATE usuarios SET estado = :estado WHERE idUsuario = :id";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':estado', $estado);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en cambiarEstadoUsuario: " . $e->getMessage());
            return false;
        }
    }

    // VERIFICAR CONTRASEÑA EXISTENTE
    public static function verificarContrasenaExistente($id, $contrasena)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT contrasena FROM usuarios WHERE idUsuario = :id";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($resultado) {
                return password_verify($contrasena, $resultado['contrasena']);
            }
            
            return false;
        } catch (PDOException $e) {
            error_log("Error en verificarContrasenaExistente: " . $e->getMessage());
            return false;
        }
    }
}
?>