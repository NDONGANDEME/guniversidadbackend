<?php
require_once __DIR__ . "/../../utilidades/u_conexion.php";
require_once __DIR__ . "/../modelo/m_administrativo.php";

class D_Administrativo
{
    // ============================================
    // FUNCIONES DE OBTENCIÓN (SELECT)
    // ============================================

    // OBTENER TODOS LOS ADMINISTRATIVOS
    public static function obtenerAdministrativos()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT a.*, f.idFacultad, f.nombreFacultad
                    FROM administrativos a
                    LEFT JOIN facultad f ON a.idFacultad = f.idFacultad
                    ORDER BY a.apellidosAdministrativo ASC, a.nombreAdministrativo ASC";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $administrativos = [];
            
            foreach ($resultados as $fila) {
                $model = new AdministrativoModel();
                $model->hidratarDesdeArray($fila);
                $administrativos[] = $model;
            }

            return $administrativos;
        } catch (PDOException $e) {
            error_log("Error en obtenerAdministrativos: " . $e->getMessage());
            return [];
        }
    }

    // OBTENER ADMINISTRATIVO POR ID
    public static function obtenerAdministrativoPorId($id)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT a.*, f.idFacultad, f.nombreFacultad
                    FROM administrativos a
                    LEFT JOIN facultad f ON a.idFacultad = f.idFacultad
                    WHERE a.idAdministrativos = :id";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($resultado) {
                $model = new AdministrativoModel();
                return $model->hidratarDesdeArray($resultado);
            }
            
            return null;
        } catch (PDOException $e) {
            error_log("Error en obtenerAdministrativoPorId: " . $e->getMessage());
            return null;
        }
    }

    // OBTENER ADMINISTRATIVO POR ID DE USUARIO
    public static function obtenerAdministrativoPorIdUsuario($idUsuario)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT a.*, f.idFacultad, f.nombreFacultad
                    FROM administrativos a
                    LEFT JOIN facultad f ON a.idFacultad = f.idFacultad
                    WHERE a.idUsuario = :idUsuario";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($resultado) {
                $model = new AdministrativoModel();
                return $model->hidratarDesdeArray($resultado);
            }
            
            return null;
        } catch (PDOException $e) {
            error_log("Error en obtenerAdministrativoPorIdUsuario: " . $e->getMessage());
            return null;
        }
    }

    // OBTENER ADMINISTRATIVOS POR FACULTAD
    public static function obtenerAdministrativosPorFacultad($idFacultad)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT a.*, f.nombreFacultad
                    FROM administrativos a
                    LEFT JOIN facultad f ON a.idFacultad = f.idFacultad
                    WHERE a.idFacultad = :idFacultad
                    ORDER BY a.apellidosAdministrativo ASC, a.nombreAdministrativo ASC";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idFacultad', $idFacultad, PDO::PARAM_INT);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $administrativos = [];
            
            foreach ($resultados as $fila) {
                $model = new AdministrativoModel();
                $model->hidratarDesdeArray($fila);
                $administrativos[] = $model;
            }

            return $administrativos;
        } catch (PDOException $e) {
            error_log("Error en obtenerAdministrativosPorFacultad: " . $e->getMessage());
            return [];
        }
    }

    // BUSCAR ADMINISTRATIVOS POR TÉRMINO
    public static function buscarAdministrativos($termino)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $termino = "%$termino%";

            $sql = "SELECT a.*, f.nombreFacultad
                    FROM administrativos a
                    LEFT JOIN facultad f ON a.idFacultad = f.idFacultad
                    WHERE a.nombreAdministrativo LIKE :termino 
                       OR a.apellidosAdministrativo LIKE :termino
                       OR a.correo LIKE :termino
                       OR a.contacto LIKE :termino
                    ORDER BY a.apellidosAdministrativo ASC, a.nombreAdministrativo ASC";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':termino', $termino);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $administrativos = [];
            
            foreach ($resultados as $fila) {
                $model = new AdministrativoModel();
                $model->hidratarDesdeArray($fila);
                $administrativos[] = $model;
            }

            return $administrativos;
        } catch (PDOException $e) {
            error_log("Error en buscarAdministrativos: " . $e->getMessage());
            return [];
        }
    }

    // ============================================
    // FUNCIONES DE VERIFICACIÓN
    // ============================================

    // VERIFICAR SI EXISTE ADMINISTRATIVO POR ID DE USUARIO
    public static function existeAdministrativoPorIdUsuario($idUsuario, $excluirId = null)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT COUNT(*) as total FROM administrativos WHERE idUsuario = :idUsuario";
            
            if ($excluirId !== null) {
                $sql .= " AND idAdministrativos != :excluirId";
            }
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
            
            if ($excluirId !== null) {
                $stmt->bindParam(':excluirId', $excluirId, PDO::PARAM_INT);
            }
            
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['total'] > 0;
        } catch (PDOException $e) {
            error_log("Error en existeAdministrativoPorIdUsuario: " . $e->getMessage());
            return false;
        }
    }

    // ============================================
    // FUNCIONES CRUD
    // ============================================

    // INSERTAR ADMINISTRATIVO
    public static function insertarAdministrativo($datos)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "INSERT INTO administrativos (
                        idUsuario, 
                        nombreAdministrativo, 
                        apellidosAdministrativo, 
                        idFacultad,
                        telefono,
                        correo
                    ) VALUES (
                        :idUsuario, 
                        :nombreAdministrativo, 
                        :apellidosAdministrativo, 
                        :idFacultad,
                        :telefono,
                        :correo
                    )";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idUsuario', $datos['idUsuario'], PDO::PARAM_INT);
            $stmt->bindParam(':nombreAdministrativo', $datos['nombreAdministrativo']);
            $stmt->bindParam(':apellidosAdministrativo', $datos['apellidosAdministrativo']);
            $stmt->bindParam(':idFacultad', $datos['idFacultad'], PDO::PARAM_INT);
            $stmt->bindParam(':telefono', $datos['telefono']);
            $stmt->bindParam(':correo', $datos['correo']);
            
            if ($stmt->execute()) {
                return $instanciaConexion->lastInsertId();
            }
            
            return null;
        } catch (PDOException $e) {
            error_log("Error en insertarAdministrativo: " . $e->getMessage());
            return null;
        }
    }

    // ACTUALIZAR ADMINISTRATIVO
    public static function actualizarAdministrativo($id, $datos)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "UPDATE administrativos SET 
                        nombreAdministrativo = :nombreAdministrativo,
                        apellidosAdministrativo = :apellidosAdministrativo,
                        idFacultad = :idFacultad,
                        telefono = :telefono,
                        correo = :correo
                    WHERE idAdministrativos = :id";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':nombreAdministrativo', $datos['nombreAdministrativo']);
            $stmt->bindParam(':apellidosAdministrativo', $datos['apellidosAdministrativo']);
            $stmt->bindParam(':idFacultad', $datos['idFacultad'], PDO::PARAM_INT);
            $stmt->bindParam(':telefono', $datos['telefono']);
            $stmt->bindParam(':correo', $datos['correo']);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en actualizarAdministrativo: " . $e->getMessage());
            return false;
        }
    }

    // ELIMINAR ADMINISTRATIVO
    public static function eliminarAdministrativo($id)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            // Primero obtener el idUsuario antes de eliminar
            $administrativo = self::obtenerAdministrativoPorId($id);
            
            // Eliminar el registro de administrativos
            $sql = "DELETE FROM administrativos WHERE idAdministrativos = :id";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $resultado = $stmt->execute();

            // Si se eliminó correctamente y tiene usuario asociado, eliminar también el usuario
            if ($resultado && $administrativo && $administrativo->idUsuario) {
                self::eliminarUsuarioAsociado($administrativo->idUsuario);
            }
            
            return $resultado;
        } catch (PDOException $e) {
            error_log("Error en eliminarAdministrativo: " . $e->getMessage());
            return false;
        }
    }

    // ELIMINAR USUARIO ASOCIADO (función auxiliar)
    private static function eliminarUsuarioAsociado($idUsuario)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "DELETE FROM usuarios WHERE idUsuario = :idUsuario";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en eliminarUsuarioAsociado: " . $e->getMessage());
            return false;
        }
    }

    // ============================================
    // FUNCIONES DE PAGINACIÓN
    // ============================================

    // CONTAR ADMINISTRATIVOS (para paginación)
    public static function contarAdministrativos($idFacultad = null)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT COUNT(*) as total FROM administrativos";
            
            if ($idFacultad !== null) {
                $sql .= " WHERE idFacultad = :idFacultad";
                $stmt = $instanciaConexion->prepare($sql);
                $stmt->bindParam(':idFacultad', $idFacultad, PDO::PARAM_INT);
            } else {
                $stmt = $instanciaConexion->prepare($sql);
            }
            
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

            return (int) ceil($resultado['total'] / 20);
        } catch (PDOException $e) {
            error_log("Error en contarAdministrativos: " . $e->getMessage());
            return 0;
        }
    }

    // OBTENER ADMINISTRATIVOS PAGINADOS
    public static function obtenerAdministrativosPaginados($pagina, $idFacultad = null)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $saltos = ($pagina - 1) * 20;
            $lote = 20;

            $sql = "SELECT a.*, f.nombreFacultad
                    FROM administrativos a
                    LEFT JOIN facultad f ON a.idFacultad = f.idFacultad";
            
            if ($idFacultad !== null) {
                $sql .= " WHERE a.idFacultad = :idFacultad";
            }
            
            $sql .= " ORDER BY a.apellidosAdministrativo ASC, a.nombreAdministrativo ASC 
                      LIMIT :lote OFFSET :saltos";
            
            $stmt = $instanciaConexion->prepare($sql);
            
            if ($idFacultad !== null) {
                $stmt->bindParam(':idFacultad', $idFacultad, PDO::PARAM_INT);
            }
            
            $stmt->bindParam(':lote', $lote, PDO::PARAM_INT);
            $stmt->bindParam(':saltos', $saltos, PDO::PARAM_INT);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $administrativos = [];
            
            foreach ($resultados as $fila) {
                $model = new AdministrativoModel();
                $model->hidratarDesdeArray($fila);
                $administrativos[] = $model;
            }

            return $administrativos;
        } catch (PDOException $e) {
            error_log("Error en obtenerAdministrativosPaginados: " . $e->getMessage());
            return [];
        }
    }

}
?>