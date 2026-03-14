<?php
require_once __DIR__ . "/../../utilidades/u_verificaciones.php";
require_once __DIR__ . "/../../utilidades/u_permisos.php";
require_once __DIR__ . "/../dao/d_permiso.php";
require_once __DIR__ . "/../modelo/m_permiso.php";

class PermisoController
{
    public static function dispatch($accion, $parametros)
    {
        // Verificar sesión activa
        /*if (!self::verificarSesionActiva()) {
            echo json_encode([
                'estado' => 401,
                'exito' => false,
                'mensaje' => 'No hay sesión activa',
                'resultado' => null
            ]);
            return;
        }*/

        switch ($accion) {
            case "obtenerPermisos":
                self::obtenerPermisos();
                break;
                
            case "obtenerPermisoPorId":
                self::obtenerPermisoPorId($parametros['idPermiso'] ?? null);
                break;
                
            case "insertarPermiso":
                self::insertarPermiso($parametros);
                break;
                
            case "actualizarPermiso":
                self::actualizarPermiso($parametros);
                break;
                
            case "eliminarPermiso":
                self::eliminarPermiso($parametros['idPermiso'] ?? null);
                break;
                
            case "obtenerTablasPermisos":
                self::obtenerNombresTablas();
                break;
                
    
            default:
                echo json_encode([
                    'estado' => 400,
                    'exito' => false,
                    'mensaje' => "Acción '$accion' no válida en controlador de permisos",
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

    // Obtener todos los permisos
    private static function obtenerPermisos()
    {
        $permisos = D_Permiso::obtenerPermisos();
        $resultado = [];
        
        foreach ($permisos as $permiso) {
            $resultado[] = $permiso->convertirAArray();
        }
        
        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Permisos obtenidos correctamente',
            'resultado' => $resultado
        ]);
    }

    // Obtener permiso por ID
    private static function obtenerPermisoPorId($id)
    {
        if (!$id) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'ID de permiso no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        $permiso = D_Permiso::obtenerPermisoPorId($id);
        
        if ($permiso) {
            echo json_encode([
                'estado' => 'exito',
                'exito' => true,
                'mensaje' => 'Permiso obtenido correctamente',
                'resultado' => $permiso->convertirAArray()
            ]);
        } else {
            echo json_encode([
                'estado' => 404,
                'exito' => false,
                'mensaje' => 'Permiso no encontrado',
                'resultado' => null
            ]);
        }
    }

    // Insertar permiso
    private static function insertarPermiso($parametros)
    {
        // Validar campos obligatorios
        $nombrePermiso = $parametros['nombrePermiso'] ?? '';
        $tabla = $parametros['tabla'] ?? '';
        $accion = $parametros['accionPermiso'] ?? '';
        
        if (empty($nombrePermiso) || empty($tabla) || empty($accion)) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'Nombre, tabla y acción son obligatorios',
                'resultado' => null
            ]);
            return;
        }

        // Verificar si ya existe
        if (D_Permiso::existePermisoPorNombre($nombrePermiso)) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'Ya existe un permiso con ese nombre',
                'resultado' => null
            ]);
            return;
        }

        // Insertar permiso
        $permisoId = D_Permiso::insertarPermiso([
            'nombrePermiso' => $nombrePermiso,
            'tabla' => $tabla,
            'accionPermiso' => $accion
        ]);

        if (!$permisoId) {
            echo json_encode([
                'estado' => 500,
                'exito' => false,
                'mensaje' => 'Error al crear el permiso',
                'resultado' => null
            ]);
            return;
        }

        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Permiso creado exitosamente',
            'resultado' => ['idPermiso' => $permisoId]
        ]);
    }

    // Actualizar permiso
    private static function actualizarPermiso($parametros)
    {
        $id = $parametros['idPermiso'] ?? null;
        
        if (!$id) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'ID de permiso no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        // Verificar que el permiso existe
        $permisoExistente = D_Permiso::obtenerPermisoPorId($id);
        if (!$permisoExistente) {
            echo json_encode([
                'estado' => 404,
                'exito' => false,
                'mensaje' => 'Permiso no encontrado',
                'resultado' => null
            ]);
            return;
        }

        // Validar campos
        $nombrePermiso = $parametros['nombrePermiso'] ?? $permisoExistente->nombrePermiso;
        $tabla = $parametros['tabla'] ?? $permisoExistente->tabla;
        $accion = $parametros['accionPermiso'] ?? $permisoExistente->accionPermiso;

        // Verificar si ya existe otro con el mismo nombre
        if ($nombrePermiso != $permisoExistente->nombrePermiso && 
            D_Permiso::existePermisoPorNombre($nombrePermiso, $id)) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'Ya existe otro permiso con ese nombre',
                'resultado' => null
            ]);
            return;
        }

        // Actualizar permiso
        $actualizado = D_Permiso::actualizarPermiso($id, [
            'nombrePermiso' => $nombrePermiso,
            'tabla' => $tabla,
            'accionPermiso' => $accion
        ]);

        if ($actualizado) {
            echo json_encode([
                'estado' => 'exito',
                'exito' => true,
                'mensaje' => 'Permiso actualizado exitosamente',
                'resultado' => ['id' => $id]
            ]);
        } else {
            echo json_encode([
                'estado' => 500,
                'exito' => false,
                'mensaje' => 'Error al actualizar el permiso',
                'resultado' => null
            ]);
        }
    }

    // Eliminar permiso
    private static function eliminarPermiso($id)
    {
        if (!$id) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'ID de permiso no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        // Verificar que el permiso existe
        $permisoExistente = D_Permiso::obtenerPermisoPorId($id);
        if (!$permisoExistente) {
            echo json_encode([
                'estado' => 404,
                'exito' => false,
                'mensaje' => 'Permiso no encontrado',
                'resultado' => null
            ]);
            return;
        }

        // Eliminar permiso
        $eliminado = D_Permiso::eliminarPermiso($id);

        if ($eliminado) {
            echo json_encode([
                'estado' => 'exito',
                'exito' => true,
                'mensaje' => 'Permiso eliminado exitosamente',
                'resultado' => ['id' => $id]
            ]);
        } else {
            echo json_encode([
                'estado' => 500,
                'exito' => false,
                'mensaje' => 'Error al eliminar el permiso',
                'resultado' => null
            ]);
        }
    }

    // Obtener nombres de tablas en singular
    private static function obtenerNombresTablas()
    {
        $tablas = D_Permiso::obtenerNombresTablas();
        
        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Nombres de tablas obtenidos correctamente',
            'resultado' => $tablas
        ]);
    }
}
?>