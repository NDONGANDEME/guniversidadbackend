<?php
require_once __DIR__ . "/../../utilidades/u_verificaciones.php";
require_once __DIR__ . "/../../utilidades/u_permisos_controlador.php";
require_once __DIR__ . "/../dao/d_rol_permiso.php";
require_once __DIR__ . "/../modelo/m_rol_permiso.php";

class RolPermisoController
{
    public static function dispatch($accion, $parametros)
    {
        // Verificar sesión activa
        if (!self::verificarSesionActiva()) {
            echo json_encode([
                'estado' => 401,
                'exito' => false,
                'mensaje' => 'No hay sesión activa',
                'resultado' => null
            ]);
            return;
        }

        switch ($accion) {
            case "obtenerRolPermisos":
                self::obtenerRolPermisos();
                break;
                
            case "obtenerRolPermisoPorId":
                self::obtenerRolPermisoPorId($parametros['id'] ?? null);
                break;
                
            case "obtenerPermisosPorRol":
                self::obtenerPermisosPorRol($parametros['idRol'] ?? null);
                break;
                
            case "obtenerRolesPorPermiso":
                self::obtenerRolesPorPermiso($parametros['idPermiso'] ?? null);
                break;
                
            case "insertarPermisRol":
                self::asignarPermisoARol($parametros);
                break;
                
            case "eliminarPermisoRol":
                self::quitarPermisoDeRol($parametros);
                break;
                
            case "quitarTodosPermisoRol":
                self::quitarTodosPermisosDeRol($parametros['idRol'] ?? null);
                break;
                
            default:
                echo json_encode([
                    'estado' => 400,
                    'exito' => false,
                    'mensaje' => "Acción '$accion' no válida en controlador de relaciones rol-permiso",
                    'resultado' => null
                ]);
        }
    }

    // Verificar sesión activa
    private static function verificarSesionActiva()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['usuario_id']) && isset($_SESSION['usuario_correo']);
    }

    // Obtener todas las relaciones rol-permiso
    private static function obtenerRolPermisos()
    {
        $relaciones = D_RolPermiso::obtenerRolPermisos();
        $resultado = [];
        
        foreach ($relaciones as $relacion) {
            $resultado[] = $relacion->convertirAArray();
        }
        
        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Relaciones rol-permiso obtenidas correctamente',
            'resultado' => $resultado
        ]);
    }

    // Obtener relación por ID
    private static function obtenerRolPermisoPorId($id)
    {
        if (!$id) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'ID de relación no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        $relacion = D_RolPermiso::obtenerRolPermisoPorId($id);
        
        if ($relacion) {
            echo json_encode([
                'estado' => 'exito',
                'exito' => true,
                'mensaje' => 'Relación obtenida correctamente',
                'resultado' => $relacion->convertirAArray()
            ]);
        } else {
            echo json_encode([
                'estado' => 404,
                'exito' => false,
                'mensaje' => 'Relación no encontrada',
                'resultado' => null
            ]);
        }
    }

    // Obtener permisos por rol
    private static function obtenerPermisosPorRol($idRol)
    {
        if (!$idRol) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'ID de rol no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        $permisos = D_RolPermiso::obtenerPermisosPorRol($idRol);
        
        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Permisos del rol obtenidos correctamente',
            'resultado' => $permisos
        ]);
    }

    // Obtener roles por permiso
    private static function obtenerRolesPorPermiso($idPermiso)
    {
        if (!$idPermiso) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'ID de permiso no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        $roles = D_RolPermiso::obtenerRolesPorPermiso($idPermiso);
        
        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Roles que tienen este permiso obtenidos correctamente',
            'resultado' => $roles
        ]);
    }

    // Asignar permiso a rol
    private static function asignarPermisoARol($parametros)
    {
        $idRol = $parametros['idRol'] ?? null;
        $idPermiso = $parametros['idPermiso'] ?? null;
        
        if (!$idRol || !$idPermiso) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'Rol y permiso son obligatorios',
                'resultado' => null
            ]);
            return;
        }

        // Verificar si ya existe la relación
        if (D_RolPermiso::existeRelacion($idRol, $idPermiso)) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'El rol ya tiene asignado este permiso',
                'resultado' => null
            ]);
            return;
        }

        // Insertar relación
        $relacionId = D_RolPermiso::insertarRolPermiso([
            'idRol' => $idRol,
            'idPermiso' => $idPermiso
        ]);

        if ($relacionId) {
            echo json_encode([
                'estado' => 'exito',
                'exito' => true,
                'mensaje' => 'Permiso asignado al rol exitosamente',
                'resultado' => ['id' => $relacionId]
            ]);
        } else {
            echo json_encode([
                'estado' => 500,
                'exito' => false,
                'mensaje' => 'Error al asignar el permiso al rol',
                'resultado' => null
            ]);
        }
    }

    // Quitar permiso de rol
    private static function quitarPermisoDeRol($parametros)
    {
        $idRelacion = $parametros['id'] ?? $parametros['idRolPermiso'] ?? null;
        
        if (!$idRelacion) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'ID de relación no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        // Verificar que la relación existe
        $relacion = D_RolPermiso::obtenerRolPermisoPorId($idRelacion);
        if (!$relacion) {
            echo json_encode([
                'estado' => 404,
                'exito' => false,
                'mensaje' => 'Relación no encontrada',
                'resultado' => null
            ]);
            return;
        }

        // Eliminar relación
        $eliminado = D_RolPermiso::eliminarRolPermiso($idRelacion);

        if ($eliminado) {
            echo json_encode([
                'estado' => 'exito',
                'exito' => true,
                'mensaje' => 'Permiso quitado del rol exitosamente',
                'resultado' => ['id' => $idRelacion]
            ]);
        } else {
            echo json_encode([
                'estado' => 500,
                'exito' => false,
                'mensaje' => 'Error al quitar el permiso del rol',
                'resultado' => null
            ]);
        }
    }

    // Quitar todos los permisos de un rol
    private static function quitarTodosPermisosDeRol($idRol)
    {
        if (!$idRol) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'ID de rol no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        // Verificar que el rol existe (opcional, podrías consultar D_Rol)
        
        // Eliminar todas las relaciones del rol
        $eliminado = D_RolPermiso::eliminarRelacionesPorRol($idRol);

        if ($eliminado) {
            echo json_encode([
                'estado' => 'exito',
                'exito' => true,
                'mensaje' => 'Todos los permisos han sido quitados del rol',
                'resultado' => ['idRol' => $idRol]
            ]);
        } else {
            echo json_encode([
                'estado' => 500,
                'exito' => false,
                'mensaje' => 'Error al quitar los permisos del rol',
                'resultado' => null
            ]);
        }
    }
}
?>