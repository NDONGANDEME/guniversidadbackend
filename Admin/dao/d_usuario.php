<?php
require_once __DIR__ . "/../../utilidades/u_conexion.php";
require_once __DIR__ . "/../modelo/m_usuario.php";

class D_Usuario
{
    // CONSTANTE PARA EL NÚMERO DE REGISTROS POR PÁGINA
    const REGISTROS_POR_PAGINA = 30;

    // OBTENER TODOS LOS USUARIOS (solo lectura, no necesita transacción)
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

    // OBTENER EL NÚMERO DE PÁGINAS (30 usuarios por página)
    public static function contarUsuarios()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT COUNT(*) as total FROM usuarios";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

            return (int) ceil($resultado['total'] / self::REGISTROS_POR_PAGINA);
        } catch (PDOException $e) {
            error_log("Error en contarUsuarios: " . $e->getMessage());
            return 0;
        }
    }

    // OBTENER USUARIOS A PAGINAR
    public static function obtenerUsuariosAPaginar($pagina)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $saltos = ($pagina - 1) * self::REGISTROS_POR_PAGINA;
            $lote = self::REGISTROS_POR_PAGINA;

            $sql = "SELECT idUsuario, nombreUsuario, correo, foto, rol, estado, ultimoAcceso 
                    FROM usuarios 
                    ORDER BY idUsuario DESC 
                    LIMIT :lote OFFSET :saltos";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':lote', $lote, PDO::PARAM_INT);
            $stmt->bindParam(':saltos', $saltos, PDO::PARAM_INT);
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
            error_log("Error en obtenerUsuariosAPaginar: " . $e->getMessage());
            return [];
        }
    }

    // OBTENER USUARIO POR ID (solo lectura, no necesita transacción)
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

    // OBTENER USUARIO POR NOMBRE DE USUARIO (solo lectura)
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

    // BUSCAR USUARIOS POR TÉRMINO
    public static function buscarUsuarios($termino)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $terminoBusqueda = "%$termino%";

            $sql = "SELECT idUsuario, nombreUsuario, correo, foto, rol, estado, ultimoAcceso 
                    FROM usuarios 
                    WHERE nombreUsuario LIKE :termino 
                       OR correo LIKE :termino
                    ORDER BY idUsuario DESC";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':termino', $terminoBusqueda);
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
            error_log("Error en buscarUsuarios: " . $e->getMessage());
            return [];
        }
    }

    // BUSCAR USUARIOS POR TÉRMINO CON PAGINACIÓN
    public static function buscarUsuariosPaginados($termino, $pagina)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $terminoBusqueda = "%$termino%";
            
            $saltos = ($pagina - 1) * self::REGISTROS_POR_PAGINA;
            $lote = self::REGISTROS_POR_PAGINA;

            $sql = "SELECT idUsuario, nombreUsuario, correo, foto, rol, estado, ultimoAcceso 
                    FROM usuarios 
                    WHERE nombreUsuario LIKE :termino 
                       OR correo LIKE :termino
                    ORDER BY idUsuario DESC
                    LIMIT :lote OFFSET :saltos";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':termino', $terminoBusqueda);
            $stmt->bindParam(':lote', $lote, PDO::PARAM_INT);
            $stmt->bindParam(':saltos', $saltos, PDO::PARAM_INT);
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
            error_log("Error en buscarUsuariosPaginados: " . $e->getMessage());
            return [];
        }
    }

    // CONTAR RESULTADOS DE BÚSQUEDA
    public static function contarResultadosBusquedaUsuarios($termino)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $terminoBusqueda = "%$termino%";

            $sql = "SELECT COUNT(*) as total 
                    FROM usuarios 
                    WHERE nombreUsuario LIKE :termino 
                       OR correo LIKE :termino";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':termino', $terminoBusqueda);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int) ceil($resultado['total'] / self::REGISTROS_POR_PAGINA);
        } catch (PDOException $e) {
            error_log("Error en contarResultadosBusquedaUsuarios: " . $e->getMessage());
            return 0;
        }
    }

    // VERIFICAR SI EXISTE NOMBRE DE USUARIO (solo lectura)
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

    // VERIFICAR SI EXISTE CORREO (solo lectura)
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

    // INSERTAR USUARIO CON TRANSACCIÓN
    public static function insertarUsuario($datos)
    {
        $pdo = null;
        try {
            $pdo = ConexionUtil::conectar();
            
            // Iniciar transacción
            $pdo->beginTransaction();

            $sql = "INSERT INTO usuarios (
                        nombreUsuario, 
                        contrasena, 
                        correo, 
                        rol, 
                        estado,
                        foto
                    ) VALUES (
                        :nombreUsuario, 
                        :contrasena, 
                        :correo, 
                        :rol, 
                        :estado,
                        :foto
                    )";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nombreUsuario', $datos['nombreUsuario']);
            $stmt->bindParam(':contrasena', $datos['contrasena']);
            $stmt->bindParam(':correo', $datos['correo']);
            $stmt->bindParam(':rol', $datos['rol']);
            $stmt->bindParam(':estado', $datos['estado']);
            $stmt->bindParam(':foto', $datos['foto']);
            
            if ($stmt->execute()) {
                $id = $pdo->lastInsertId();
                $pdo->commit(); // Confirmar transacción
                return $id;
            } else {
                $pdo->rollBack(); // Revertir si falla
                return null;
            }
            
        } catch (PDOException $e) {
            if ($pdo) {
                $pdo->rollBack(); // Revertir en caso de error
            }
            error_log("Error en insertarUsuario: " . $e->getMessage());
            return null;
        }
    }

    // ACTUALIZAR USUARIO CON TRANSACCIÓN
    public static function actualizarUsuario($datos)
    {
        $pdo = null;
        try {
            $pdo = ConexionUtil::conectar();
            
            // Iniciar transacción
            $pdo->beginTransaction();

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
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $datos['id']);
            $stmt->bindParam(':nombreUsuario', $datos['nombreUsuario']);
            $stmt->bindParam(':correo', $datos['correo']);
            $stmt->bindParam(':rol', $datos['rol']);
            $stmt->bindParam(':preguntaRecuperacion', $datos['preguntaRecuperacion']);
            $stmt->bindParam(':respuestaRecuperacion', $datos['respuestaRecuperacion']);
            
            if (isset($datos['foto']) && $datos['foto'] !== null) {
                $stmt->bindParam(':foto', $datos['foto']);
            }
            
            $resultado = $stmt->execute();
            
            if ($resultado) {
                $pdo->commit(); // Confirmar transacción
                return true;
            } else {
                $pdo->rollBack(); // Revertir si falla
                return false;
            }
            
        } catch (PDOException $e) {
            if ($pdo) {
                $pdo->rollBack(); // Revertir en caso de error
            }
            error_log("Error en actualizarUsuario: " . $e->getMessage());
            return false;
        }
    }

    // CAMBIAR ESTADO DEL USUARIO CON TRANSACCIÓN
    public static function cambiarEstadoUsuario($id, $estado)
    {
        $pdo = null;
        try {
            $pdo = ConexionUtil::conectar();
            
            // Iniciar transacción
            $pdo->beginTransaction();

            $sql = "UPDATE usuarios SET estado = :estado WHERE idUsuario = :id";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':estado', $estado);
            
            $resultado = $stmt->execute();
            
            if ($resultado) {
                $pdo->commit(); // Confirmar transacción
                return true;
            } else {
                $pdo->rollBack(); // Revertir si falla
                return false;
            }
            
        } catch (PDOException $e) {
            if ($pdo) {
                $pdo->rollBack(); // Revertir en caso de error
            }
            error_log("Error en cambiarEstadoUsuario: " . $e->getMessage());
            return false;
        }
    }

    // VERIFICAR CONTRASEÑA EXISTENTE (solo lectura, no necesita transacción)
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