<?php

require_once __DIR__ ."/u_conexion.php";
class PermisosUtil
{
    /**
     * VERIFICA SI UN USUARIO TIENE UN PERMISO ESPECÍFICO
     * @param int $idUsuario ID del usuario
     * @param string $nombrePermiso Nombre del permiso a verificar
     * @return bool True si tiene el permiso, false si no
     */
    public static function usuarioTienePermiso($idUsuario, $nombrePermiso)
    {
        // Validaciones básicas
        if (!$idUsuario || !is_numeric($idUsuario) || $idUsuario <= 0) {
            error_log("PermisosUtil: ID de usuario inválido");
            return false;
        }
        
        try {
            $pdo = ConexionUtil::conectar();
            
            // PASO 1: Obtener el rol del usuario
            $sqlUsuario = "SELECT idRol FROM usuarios WHERE idUsuario = :idUsuario";
            $stmt = $pdo->prepare($sqlUsuario);
            $stmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
            $stmt->execute();
            
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$usuario || !isset($usuario['idRol']) || !$usuario['idRol']) {
                error_log("PermisosUtil: Usuario no encontrado o sin rol asignado");
                return false;
            }
            
            $idRol = $usuario['idRol'];
            
            // PASO 2: Verificar si el rol tiene el permiso
            $sqlPermiso = "SELECT COUNT(*) as total 
                          FROM rol_permiso rp
                          INNER JOIN permiso p ON rp.idPermiso = p.idPermiso
                          WHERE rp.idRol = :idRol 
                          AND p.nombrePermiso = :nombrePermiso";
            
            $stmt = $pdo->prepare($sqlPermiso);
            $stmt->bindParam(':idRol', $idRol, PDO::PARAM_INT);
            $stmt->bindParam(':nombrePermiso', $nombrePermiso);
            $stmt->execute();
            
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $tienePermiso = ($resultado['total'] > 0);
            
            error_log("PermisosUtil: Usuario $idUsuario - Permiso '$nombrePermiso': " . ($tienePermiso ? 'SÍ' : 'NO'));
            
            return $tienePermiso;
            
        } catch (PDOException $e) {
            error_log("PermisosUtil: Error de BD - " . $e->getMessage());
            return false;
        } catch (Exception $e) {
            error_log("PermisosUtil: Error general - " . $e->getMessage());
            return false;
        }
    }
}
?>