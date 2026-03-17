<?php
require_once __DIR__ . "/../dao/d_administrativo.php";
require_once __DIR__ . "/../dao/d_usuario.php";
require_once __DIR__ . "/../dao/d_facultad.php";
require_once __DIR__ . "/../../utilidades/LimpiarDatos.php";
require_once __DIR__ . "/../../utilidades/u_verificaciones.php";
require_once __DIR__ . "/../modelo/m_administrativo.php";
require_once __DIR__ . "../../../utilidades/u_permisos.php";

class AdministrativoController
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
        
        // Verificar que el actor sea admin para todas estas operaciones
        if (!isset($parametros['actor']) || $parametros['actor'] !== 'admin') {
            echo json_encode([
                'estado' => 403,
                'exito' => false,
                'mensaje' => 'Acceso denegado. Se requiere rol de administrador.',
                'resultado' => null
            ]);
            return;
        }

        // Verificar que que el usuario en cuestion tenga los permisos necesarios
        /*if (!PermisosUtil::usuarioTienePermiso($parametros['idUsuario'], $accion)) {
            echo json_encode([
                'estado' => 403,
                'exito' => false,
                'mensaje' => 'Acceso denegado. No cuentas con los permiso adecuados.',
                'resultado' => null
            ]);
            return;
        }*/

        switch ($accion) {
            // Operaciones de listado y consulta
            case "obtenerAdministrativos":
                self::obtenerAdministrativos();
                break;
                
            case "obtenerAdministrativoPorId":
                self::obtenerAdministrativoPorId($parametros['id'] ?? null);
                break;
                
            case "obtenerAdministrativosPorFacultad":
                self::obtenerAdministrativosPorFacultad($parametros['idFacultad'] ?? null);
                break;
                
            case "buscarAdministrativos":
                self::buscarAdministrativos($parametros['termino'] ?? '');
                break;
                
            case "obtenerCantidadPaginacion":
                self::obtenerCantidadPaginacion($parametros['idFacultad'] ?? null);
                break;
                
            case "obtenerAdministrativosPaginados":
                self::obtenerAdministrativosPaginados(
                    $parametros['pagina'] ?? 1,
                    $parametros['idFacultad'] ?? null
                );
                break;
                
            // Operaciones CRUD
            case "insertarAdministrativo":
                self::insertarAdministrativo($parametros);
                break;
                
            case "actualizarAdministrativo":
                self::actualizarAdministrativo($parametros);
                break;
                
            case "eliminarAdministrativo":
                self::eliminarAdministrativo($parametros['id'] ?? null);
                break;
                
            default:
                echo json_encode([
                    'estado' => 400,
                    'exito' => false,
                    'mensaje' => "Acción '$accion' no válida en controlador de administrativos",
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

    // ============================================
    // FUNCIONES DE LISTADO Y CONSULTA
    // ============================================

    // Obtener todos los administrativos
    private static function obtenerAdministrativos()
    {
        $administrativos = D_Administrativo::obtenerAdministrativos();
        $resultado = [];
        
        foreach ($administrativos as $admin) {
            $resultado[] = $admin->convertirAArray();
        }
        
        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Administrativos obtenidos correctamente',
            'resultado' => $resultado
        ]);
    }

    // Obtener administrativo por ID
    private static function obtenerAdministrativoPorId($id)
    {
        if (!$id) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'ID de administrativo no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        $administrativo = D_Administrativo::obtenerAdministrativoPorId($id);
        
        if ($administrativo) {
            echo json_encode([
                'estado' => 'exito',
                'exito' => true,
                'mensaje' => 'Administrativo obtenido correctamente',
                'resultado' => $administrativo->convertirAArray()
            ]);
        } else {
            echo json_encode([
                'estado' => 404,
                'exito' => false,
                'mensaje' => 'Administrativo no encontrado',
                'resultado' => $id
            ]);
        }
    }

    // Obtener administrativos por facultad
    private static function obtenerAdministrativosPorFacultad($idFacultad)
    {
        if (!$idFacultad) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'ID de facultad no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        $administrativos = D_Administrativo::obtenerAdministrativosPorFacultad($idFacultad);
        $resultado = [];
        
        foreach ($administrativos as $admin) {
            $resultado[] = $admin->convertirAArray();
        }
        
        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Administrativos por facultad obtenidos correctamente',
            'resultado' => $resultado
        ]);
    }

    // Buscar administrativos
    private static function buscarAdministrativos($termino)
    {
        if (empty($termino)) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'Término de búsqueda no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        $administrativos = D_Administrativo::buscarAdministrativos($termino);
        $resultado = [];
        
        foreach ($administrativos as $admin) {
            $resultado[] = $admin->convertirAArray();
        }
        
        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Búsqueda realizada correctamente',
            'resultado' => $resultado
        ]);
    }

    // Obtener cantidad de páginas para paginación
    private static function obtenerCantidadPaginacion($idFacultad = null)
    {
        $totalPaginas = D_Administrativo::contarAdministrativos($idFacultad);
        
        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Total de páginas obtenido correctamente',
            'resultado' => [
                'total_paginas' => $totalPaginas
            ]
        ]);
    }

    // Obtener administrativos paginados
    private static function obtenerAdministrativosPaginados($pagina, $idFacultad = null)
    {
        $pagina = intval($pagina);
        if ($pagina < 1) $pagina = 1;
        
        $administrativos = D_Administrativo::obtenerAdministrativosPaginados($pagina, $idFacultad);
        $resultado = [];
        
        foreach ($administrativos as $admin) {
            $resultado[] = $admin->convertirAArray();
        }
        
        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Administrativos paginados obtenidos correctamente',
            'resultado' => [
                'pagina_actual' => $pagina,
                'administrativos' => $resultado
            ]
        ]);
    }

    // ============================================
    // FUNCIONES CRUD
    // ============================================

    // Insertar administrativo
    private static function insertarAdministrativo($parametros)
    {
        // Validar campos obligatorios
        $idUsuario = $parametros['idUsuario'] ?? '';
        $nombreAdministrativo = $parametros['nombreAdministrativo'] ?? '';
        $apellidosAdministrativo = $parametros['apellidosAdministrativo'] ?? '';
        $idFacultad = $parametros['idFacultad'] ?? ''; // || empty($idFacultad)
        $correo = $parametros['correo'] ?? '';
        $telefono = $parametros['telefono'] ?? '';
        
        if (empty($idUsuario) || empty($nombreAdministrativo) || empty($apellidosAdministrativo) || empty($correo)) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'ID de usuario, nombre, apellidos, facultad y correo son obligatorios',
                'resultado' => null
            ]);
            return;
        }

        // Insertar administrativo
        $administrativoId = D_Administrativo::insertarAdministrativo($parametros);

        if (!$administrativoId) {
            echo json_encode([
                'estado' => 500,
                'exito' => false,
                'mensaje' => 'Error al crear el administrativo',
                'resultado' => null
            ]);
            return;
        }

        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Administrativo creado exitosamente',
            'resultado' => ['id' => $administrativoId]
        ]);
    }

    // Actualizar administrativo
    private static function actualizarAdministrativo($parametros)
    {
        $id = $parametros['idAdministrativo'] ?? null;
        
        if (!$id) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'ID de administrativo no proporcionado',
                'resultado' => $parametros
            ]);
            return;
        }

        // Verificar que existe
        $adminExistente = D_Administrativo::obtenerAdministrativoPorId($id);
        if (!$adminExistente) {
            echo json_encode([
                'estado' => 404,
                'exito' => false,
                'mensaje' => 'Administrativo no encontrado',
                'resultado' => null
            ]);
            return;
        }

        // Actualizar administrativo
        $actualizado = D_Administrativo::actualizarAdministrativo($id, [
            'nombreAdministrativo' => $parametros['nombreAdministrativo'] ?? $adminExistente->nombreAdministrativo,
            'apellidosAdministrativo' => $parametros['apellidosAdministrativo'] ?? $adminExistente->apellidosAdministrativo,
            'idFacultad' => $parametros['idFacultad'] ?? $adminExistente->idFacultad,
            'telefono' => $parametros['telefono'] ?? $adminExistente->telefono,
            'correo' => $parametros['correo'] ?? $adminExistente->correo
        ]);

        if (!$actualizado) {
            echo json_encode([
                'estado' => 500,
                'exito' => false,
                'mensaje' => 'Error al actualizar el administrativo',
                'resultado' => null
            ]);
            return;
        }

        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Administrativo actualizado exitosamente',
            'resultado' => ['id' => $id]
        ]);
    }

    // Eliminar administrativo
    private static function eliminarAdministrativo($id)
    {
        if (!$id) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'ID de administrativo no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        // Verificar que existe
        $adminExistente = D_Administrativo::obtenerAdministrativoPorId($id);
        if (!$adminExistente) {
            echo json_encode([
                'estado' => 404,
                'exito' => false,
                'mensaje' => 'Administrativo no encontrado',
                'resultado' => null
            ]);
            return;
        }

        // Eliminar administrativo (esto también eliminará el usuario asociado)
        $eliminado = D_Administrativo::eliminarAdministrativo($id);

        if ($eliminado) {
            echo json_encode([
                'estado' => 'exito',
                'exito' => true,
                'mensaje' => 'Administrativo eliminado exitosamente',
                'resultado' => ['id' => $id]
            ]);
        } else {
            echo json_encode([
                'estado' => 500,
                'exito' => false,
                'mensaje' => 'Error al eliminar el administrativo',
                'resultado' => null
            ]);
        }
    }
}
?>