<?php
require_once __DIR__ . "/../../utilidades/u_verificaciones.php";
require_once __DIR__ . "/../../utilidades/u_permisos.php";
require_once __DIR__ . "/../dao/d_rol.php";
require_once __DIR__ . "/../modelo/m_rol.php";

class RolController
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
            case "obtenerRoles":
                self::obtenerRoles();
                break;
                
            case "obtenerRolPorId":
                self::obtenerRolPorId($parametros['id'] ?? null);
                break;
                
            case "insertarRol":
                self::insertarRol($parametros);
                break;
                
            case "actualizarRol":
                self::actualizarRol($parametros);
                break;
                
            case "eliminarRol":
                self::eliminarRol($parametros['idRol'] ?? null);
                break;
                
            default:
                echo json_encode([
                    'estado' => 400,
                    'exito' => false,
                    'mensaje' => "Acción '$accion' no válida en controlador de roles",
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

    // Obtener todos los roles
    private static function obtenerRoles()
    {
        $roles = D_Rol::obtenerRoles();
        $resultado = [];
        
        foreach ($roles as $rol) {
            $resultado[] = $rol->convertirAArray();
        }
        
        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Roles obtenidos correctamente',
            'resultado' => $resultado
        ]);
    }

    // Obtener rol por ID
    private static function obtenerRolPorId($id)
    {
        if (!$id) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'ID de rol no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        $rol = D_Rol::obtenerRolPorId($id);
        
        if ($rol) {
            echo json_encode([
                'estado' => 'exito',
                'exito' => true,
                'mensaje' => 'Rol obtenido correctamente',
                'resultado' => $rol->convertirAArray()
            ]);
        } else {
            echo json_encode([
                'estado' => 404,
                'exito' => false,
                'mensaje' => 'Rol no encontrado',
                'resultado' => null
            ]);
        }
    }

    // Insertar rol
    private static function insertarRol($parametros)
    {
        // Validar campos obligatorios
        $nombreRol = $parametros['nombreRol'] ?? '';
        
        if (empty($nombreRol)) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'Nombre del rol es obligatorio',
                'resultado' => null
            ]);
            return;
        }

        // Verificar si ya existe
        if (D_Rol::existeRolPorNombre($nombreRol)) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'Ya existe un rol con ese nombre',
                'resultado' => null
            ]);
            return;
        }

        // Insertar rol
        $rolId = D_Rol::insertarRol(['nombreRol' => $nombreRol]);

        if (!$rolId) {
            echo json_encode([
                'estado' => 500,
                'exito' => false,
                'mensaje' => 'Error al crear el rol',
                'resultado' => null
            ]);
            return;
        }

        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Rol creado exitosamente',
            'resultado' => ['idRol' => $rolId, 'nombreRol' => $nombreRol]
        ]);
    }

    // Actualizar rol
    private static function actualizarRol($parametros)
    {
        $id = $parametros['idRol'] ?? null;
        
        if (!$id) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'ID de rol no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        // Verificar que el rol existe
        $rolExistente = D_Rol::obtenerRolPorId($id);
        if (!$rolExistente) {
            echo json_encode([
                'estado' => 404,
                'exito' => false,
                'mensaje' => 'Rol no encontrado',
                'resultado' => null
            ]);
            return;
        }

        // Validar nombre
        $nombreRol = $parametros['nombreRol'] ?? $rolExistente->nombreRol;
        
        if (empty($nombreRol)) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'Nombre del rol es obligatorio',
                'resultado' => null
            ]);
            return;
        }

        // Verificar si ya existe otro con el mismo nombre
        if ($nombreRol != $rolExistente->nombreRol && 
            D_Rol::existeRolPorNombre($nombreRol, $id)) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'Ya existe otro rol con ese nombre',
                'resultado' => null
            ]);
            return;
        }

        // Actualizar rol
        $actualizado = D_Rol::actualizarRol($id, ['nombreRol' => $nombreRol]);

        if ($actualizado) {
            echo json_encode([
                'estado' => 'exito',
                'exito' => true,
                'mensaje' => 'Rol actualizado exitosamente',
                'resultado' => ['id' => $id, 'nombreRol' => $nombreRol]
            ]);
        } else {
            echo json_encode([
                'estado' => 500,
                'exito' => false,
                'mensaje' => 'Error al actualizar el rol',
                'resultado' => null
            ]);
        }
    }

    // Eliminar rol
    private static function eliminarRol($id)
    {
        if (!$id) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'ID de rol no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        // Verificar que el rol existe
        $rolExistente = D_Rol::obtenerRolPorId($id);
        if (!$rolExistente) {
            echo json_encode([
                'estado' => 404,
                'exito' => false,
                'mensaje' => 'Rol no encontrado',
                'resultado' => null
            ]);
            return;
        }

        // Eliminar rol
        $eliminado = D_Rol::eliminarRol($id);

        if ($eliminado) {
            echo json_encode([
                'estado' => 'exito',
                'exito' => true,
                'mensaje' => 'Rol eliminado exitosamente',
                'resultado' => ['id' => $id]
            ]);
        } else {
            echo json_encode([
                'estado' => 500,
                'exito' => false,
                'mensaje' => 'Error al eliminar el rol',
                'resultado' => null
            ]);
        }
    }
}
?>