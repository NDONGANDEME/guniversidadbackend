<?php
require_once __DIR__ . "/../../utilidades/u_verificaciones.php";
require_once __DIR__ . "/../dao/d_asignatura.php";
require_once __DIR__ . "/../modelo/m_asignatura.php";
require_once __DIR__ . "/../../utilidades/u_permisos.php";

class AsignaturaController
{
    public static function dispatch($accion, $parametros)
    {
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

        switch ($accion) {
            case "obtenerAsignaturas":
                self::obtenerAsignaturas();
                break;
                
            case "obtenerAsignaturasAPaginar":
                self::obtenerAsignaturasAPaginar($parametros);
                break;
                
            case "obtenerTotalPaginasAsignatura":
                self::obtenerTotalPaginas();
                break;
                
            case "obtenerAsignaturasPorFacultad":
                self::obtenerAsignaturasPorFacultad($parametros);
                break;
                
            case "obtenerAsignaturasPorFacultadPaginadas":
                self::obtenerAsignaturasPorFacultadPaginadas($parametros);
                break;
                
            case "buscarAsignaturas":
                self::buscarAsignaturas($parametros);
                break;
                
            case "insertarAsignatura":
                if (PermisosUtil::usuarioTienePermiso($parametros['idUsuario'], $parametros['accion'])) {
                    self::insertarAsignatura($parametros);
                    break;
                } else {
                    echo json_encode([
                        'estado' => 403,
                        'exito' => false,
                        'mensaje' => 'Acceso denegado. No tienes los permisos necesarios.',
                        'resultado' => null
                    ]);
                    break;
                }
                
            case "actualizarAsignatura":
                self::actualizarAsignatura($parametros);
                break;
                
            case "eliminarAsignatura":
                self::eliminarAsignatura($parametros);
                break;
                
            default:
                echo json_encode([
                    'estado' => 400,
                    'exito' => false,
                    'mensaje' => "Acción '$accion' no válida en el controlador de asignaturas",
                    'resultado' => null
                ]);
        }
    }

    // Verificar si hay sesión activa
    private static function verificarSesionActiva()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        return isset($_SESSION['usuario_id']) && isset($_SESSION['usuario_correo']);
    }

    // Obtener todas las asignaturas
    private static function obtenerAsignaturas()
    {
        /*if (!self::verificarSesionActiva()) {
            echo json_encode([
                'estado' => 401,
                'exito' => false,
                'mensaje' => 'No hay sesión activa',
                'resultado' => null
            ]);
            return;
        }*/

        $asignaturas = D_Asignatura::obtenerAsignaturas();
        $resultado = [];
        
        foreach ($asignaturas as $asignatura) {
            $resultado[] = $asignatura->convertirAArray();
        }
        
        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Asignaturas obtenidas correctamente',
            'resultado' => $resultado
        ]);
    }

    // Obtener asignaturas paginadas
    private static function obtenerAsignaturasAPaginar($parametros)
    {
        if (!self::verificarSesionActiva()) {
            echo json_encode([
                'estado' => 401,
                'exito' => false,
                'mensaje' => 'No hay sesión activa',
                'resultado' => null
            ]);
            return;
        }

        $pagina = $parametros['pagina'] ?? 1;
        $pagina = intval($pagina);
        if ($pagina < 1) $pagina = 1;
        
        $asignaturas = D_Asignatura::obtenerAsignaturasAPaginar($pagina);
        $resultado = [];
        
        foreach ($asignaturas as $asignatura) {
            $resultado[] = $asignatura->convertirAArray();
        }
        
        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Asignaturas paginadas obtenidas correctamente',
            'resultado' => [
                'pagina_actual' => $pagina,
                'asignaturas' => $resultado
            ]
        ]);
    }

    // Obtener total de páginas
    private static function obtenerTotalPaginas()
    {
        if (!self::verificarSesionActiva()) {
            echo json_encode([
                'estado' => 401,
                'exito' => false,
                'mensaje' => 'No hay sesión activa',
                'resultado' => null
            ]);
            return;
        }

        $totalPaginas = D_Asignatura::contarAsignaturas();
        
        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Total de páginas obtenido correctamente',
            'resultado' => [
                'total_paginas' => $totalPaginas,
                'registros_por_pagina' => D_Asignatura::REGISTROS_POR_PAGINA
            ]
        ]);
    }

    // Obtener asignaturas por facultad
    private static function obtenerAsignaturasPorFacultad($parametros)
    {
        /*if (!self::verificarSesionActiva()) {
            echo json_encode([
                'estado' => 401,
                'exito' => false,
                'mensaje' => 'No hay sesión activa',
                'resultado' => null
            ]);
            return;
        }*/

        $idFacultad = $parametros['idFacultad'] ?? null;

        if (!$idFacultad) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'ID de facultad no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        $asignaturas = D_Asignatura::obtenerAsignaturasPorFacultad($idFacultad);
        $resultado = [];
        
        foreach ($asignaturas as $asignatura) {
            $resultado[] = $asignatura->convertirAArray();
        }
        
        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Asignaturas por facultad obtenidas correctamente',
            'resultado' => $resultado
        ]);
    }

    // Obtener asignaturas por facultad paginadas
    private static function obtenerAsignaturasPorFacultadPaginadas($parametros)
    {
        if (!self::verificarSesionActiva()) {
            echo json_encode([
                'estado' => 401,
                'exito' => false,
                'mensaje' => 'No hay sesión activa',
                'resultado' => null
            ]);
            return;
        }

        $idFacultad = $parametros['idFacultad'] ?? null;
        $pagina = $parametros['pagina'] ?? 1;
        
        if (!$idFacultad) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'ID de facultad no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        $pagina = intval($pagina);
        if ($pagina < 1) $pagina = 1;
        
        $asignaturas = D_Asignatura::obtenerAsignaturasPorFacultadPaginadas($idFacultad, $pagina);
        $resultado = [];
        
        foreach ($asignaturas as $asignatura) {
            $resultado[] = $asignatura->convertirAArray();
        }
        
        // Obtener total de páginas para esta facultad
        $totalAsignaturas = D_Asignatura::contarAsignaturasPorFacultad($idFacultad);
        $totalPaginas = ceil($totalAsignaturas / D_Asignatura::REGISTROS_POR_PAGINA);
        
        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Asignaturas por facultad paginadas obtenidas correctamente',
            'resultado' => [
                'pagina_actual' => $pagina,
                'total_paginas' => $totalPaginas,
                'total_asignaturas' => $totalAsignaturas,
                'asignaturas' => $resultado
            ]
        ]);
    }

    // Buscar asignaturas
    private static function buscarAsignaturas($parametros)
    {
        if (!self::verificarSesionActiva()) {
            echo json_encode([
                'estado' => 401,
                'exito' => false,
                'mensaje' => 'No hay sesión activa',
                'resultado' => null
            ]);
            return;
        }

        $termino = $parametros['termino'] ?? '';
        $pagina = $parametros['pagina'] ?? 1;

        if (empty($termino)) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'Término de búsqueda no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        $pagina = intval($pagina);
        if ($pagina < 1) $pagina = 1;

        // Determinar si es búsqueda paginada o no
        if (isset($parametros['paginada']) && $parametros['paginada'] === 'true') {
            $asignaturas = D_Asignatura::buscarAsignaturasPaginadas($termino, $pagina);
            $totalPaginas = D_Asignatura::contarResultadosBusqueda($termino);
        } else {
            $asignaturas = D_Asignatura::buscarAsignaturas($termino);
            $totalPaginas = 1;
        }

        $resultado = [];
        foreach ($asignaturas as $asignatura) {
            $resultado[] = $asignatura->convertirAArray();
        }

        $response = [
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Búsqueda realizada correctamente',
            'resultado' => $resultado
        ];

        // Si es búsqueda paginada, incluir información de paginación
        if (isset($parametros['paginada']) && $parametros['paginada'] === 'true') {
            $response['resultado'] = [
                'pagina_actual' => $pagina,
                'total_paginas' => $totalPaginas,
                'asignaturas' => $resultado
            ];
        }

        echo json_encode($response);
    }

    // Insertar nueva asignatura
    private static function insertarAsignatura($parametros)
    {
        /*if (!self::verificarSesionActiva()) {
            echo json_encode([
                'estado' => 401,
                'exito' => false,
                'mensaje' => 'No hay sesión activa',
                'resultado' => null
            ]);
            return;
        }*/

        // Validar campos obligatorios
        $codigoAsignatura = $parametros['codigoAsignatura'] ?? '';
        $nombreAsignatura = $parametros['nombreAsignatura'] ?? '';
        $descripcion = $parametros['descripcion'] ?? '';

        $errores = [];
        
        if (empty($codigoAsignatura)) {
            $errores[] = 'Código de asignatura es obligatorio';
        }
        
        if (empty($nombreAsignatura)) {
            $errores[] = 'Nombre de asignatura es obligatorio';
        }

        if (!empty($errores)) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'Errores de validación',
                'resultado' => ['errores' => $errores]
            ]);
            return;
        }

        // Verificar si ya existe la asignatura por código
        if (D_Asignatura::existeAsignaturaPorCodigo($codigoAsignatura)) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'Ya existe una asignatura con ese código',
                'resultado' => null
            ]);
            return;
        }

        // Verificar si ya existe la asignatura por nombre
        if (D_Asignatura::existeAsignaturaPorNombre($nombreAsignatura)) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'Ya existe una asignatura con ese nombre',
                'resultado' => null
            ]);
            return;
        }

        // Insertar asignatura
        $asignaturaId = D_Asignatura::insertarAsignatura($parametros);

        if (!$asignaturaId) {
            echo json_encode([
                'estado' => 500,
                'exito' => false,
                'mensaje' => 'Error al crear la asignatura',
                'resultado' => null
            ]);
            return;
        }

        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Asignatura creada exitosamente',
            'resultado' => ['id' => $asignaturaId]
        ]);
    }

    // Actualizar asignatura existente
    private static function actualizarAsignatura($parametros)
    {
        /*if (!self::verificarSesionActiva()) {
            echo json_encode([
                'estado' => 401,
                'exito' => false,
                'mensaje' => 'No hay sesión activa',
                'resultado' => null
            ]);
            return;
        }*/

        $id = $parametros['idAsignatura'] ?? null;
        
        if (!$id) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'ID de asignatura no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        // Verificar que la asignatura existe
        $asignaturaExistente = D_Asignatura::obtenerAsignaturaPorId($id);
        if (!$asignaturaExistente) {
            echo json_encode([
                'estado' => 404,
                'exito' => false,
                'mensaje' => 'Asignatura no encontrada',
                'resultado' => null
            ]);
            return;
        }

        // Datos a actualizar
        $codigoAsignatura = $parametros['codigoAsignatura'] ?? $asignaturaExistente->codigoAsignatura;
        $nombreAsignatura = $parametros['nombreAsignatura'] ?? $asignaturaExistente->nombreAsignatura;
        $descripcion = $parametros['descripcion'] ?? $asignaturaExistente->descripcion;

        // Validaciones
        $errores = [];
        
        if (empty($codigoAsignatura)) {
            $errores[] = 'Código de asignatura es obligatorio';
        }
        
        if (empty($nombreAsignatura)) {
            $errores[] = 'Nombre de asignatura es obligatorio';
        }

        // Verificar si ya existe otra asignatura con ese código
        if ($codigoAsignatura != $asignaturaExistente->codigoAsignatura && 
            D_Asignatura::existeAsignaturaPorCodigo($codigoAsignatura, $id)) {
            $errores[] = 'Ya existe otra asignatura con ese código';
        }

        // Verificar si ya existe otra asignatura con ese nombre
        if ($nombreAsignatura != $asignaturaExistente->nombreAsignatura && 
            D_Asignatura::existeAsignaturaPorNombre($nombreAsignatura, $id)) {
            $errores[] = 'Ya existe otra asignatura con ese nombre';
        }

        if (!empty($errores)) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'Errores de validación',
                'resultado' => ['errores' => $errores]
            ]);
            return;
        }

        // Actualizar asignatura
        $actualizado = D_Asignatura::actualizarAsignatura([
            'id' => $id,
            'codigoAsignatura' => $codigoAsignatura,
            'nombreAsignatura' => $nombreAsignatura,
            'descripcion' => $descripcion
        ]);

        if (!$actualizado) {
            echo json_encode([
                'estado' => 500,
                'exito' => false,
                'mensaje' => 'Error al actualizar la asignatura',
                'resultado' => null
            ]);
            return;
        }

        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Asignatura actualizada exitosamente',
            'resultado' => ['id' => $id]
        ]);
    }

    // Eliminar asignatura
    private static function eliminarAsignatura($parametros)
    {
        if (!self::verificarSesionActiva()) {
            echo json_encode([
                'estado' => 401,
                'exito' => false,
                'mensaje' => 'No hay sesión activa',
                'resultado' => null
            ]);
            return;
        }

        $id = $parametros['id'] ?? null;
        
        if (!$id) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'ID de asignatura no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        // Verificar que la asignatura existe
        $asignaturaExistente = D_Asignatura::obtenerAsignaturaPorId($id);
        if (!$asignaturaExistente) {
            echo json_encode([
                'estado' => 404,
                'exito' => false,
                'mensaje' => 'Asignatura no encontrada',
                'resultado' => null
            ]);
            return;
        }

        // Verificar si tiene horarios asociados
        if (D_Asignatura::tieneHorariosAsociados($id)) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'No se puede eliminar la asignatura porque tiene horarios asociados',
                'resultado' => null
            ]);
            return;
        }

        // Eliminar asignatura
        $eliminado = D_Asignatura::eliminarAsignatura($id);

        if (!$eliminado) {
            echo json_encode([
                'estado' => 500,
                'exito' => false,
                'mensaje' => 'Error al eliminar la asignatura',
                'resultado' => null
            ]);
            return;
        }

        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Asignatura eliminada exitosamente',
            'resultado' => ['id' => $id]
        ]);
    }
}
?>