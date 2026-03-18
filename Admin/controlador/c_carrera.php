<?php
require_once __DIR__ . "/../../utilidades/u_verificaciones.php";
require_once __DIR__ . "/../dao/d_carrera.php";
require_once __DIR__ . "/../modelo/m_carrera.php";

class CarreraController
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
            case "obtenerCarreras":
                self::obtenerCarreras();
                break;
                
            case "obtenerCarrerasAPaginar":
                self::obtenerCarrerasPaginadas($parametros);
                break;
                
            case "obtenerTotalPaginasCarrera":
                self::obtenerTotalPaginas();
                break;
                
            case "obtenerCarrerasPorDepartamento":
                self::obtenerCarrerasPorDepartamento($parametros);
                break;
                
            case "obtenerCarrerasPorDepartamentoPaginadas":
                self::obtenerCarrerasPorDepartamentoPaginadas($parametros);
                break;
                
            case "buscarCarreras":
                self::buscarCarreras($parametros);
                break;
                
            case "insertarCarrera":
                self::insertarCarrera($parametros);
                break;
                
            case "actualizarCarrera":
                self::actualizarCarrera($parametros);
                break;
                
            case "cambioEstadoCarrera":
                self::cambiarEstadoCarrera($parametros);
                break;
                
            case "eliminarCarrera":
                self::eliminarCarrera($parametros);
                break;
                
            default:
                echo json_encode([
                    'estado' => 400,
                    'exito' => false,
                    'mensaje' => "Acción '$accion' no válida en el controlador de carreras",
                    'resultado' => null
                ]);
        }
    }

    // Verificar si hay sesión activa
    /*private static function verificarSesionActiva()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        return isset($_SESSION['usuario_id']) && isset($_SESSION['usuario_correo']);
    }*/

    // Obtener todas las carreras
    private static function obtenerCarreras()
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

        $carreras = D_Carrera::obtenerCarreras();
        $resultado = [];
        
        foreach ($carreras as $carrera) {
            $arr = $carrera->convertirAArray();
            if (isset($carrera->nombreDepartamento)) {
                $arr['nombreDepartamento'] = $carrera->nombreDepartamento;
            }
            $resultado[] = $arr;
        }
        
        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Carreras obtenidas correctamente',
            'resultado' => $resultado
        ]);
    }

    // Obtener carreras paginadas
    private static function obtenerCarrerasPaginadas($parametros)
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

        $pagina = $parametros['pagina'] ?? 1;
        $pagina = intval($pagina);
        if ($pagina < 1) $pagina = 1;
        
        $carreras = D_Carrera::obtenerCarrerasAPaginar($pagina);
        $resultado = [];
        
        foreach ($carreras as $carrera) {
            $arr = $carrera->convertirAArray();
            if (isset($carrera->nombreDepartamento)) {
                $arr['nombreDepartamento'] = $carrera->nombreDepartamento;
            }
            $resultado[] = $arr;
        }
        
        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Carreras paginadas obtenidas correctamente',
            'resultado' => [
                'pagina_actual' => $pagina,
                'carreras' => $resultado
            ]
        ]);
    }

    // Obtener total de páginas
    private static function obtenerTotalPaginas()
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

        $totalPaginas = D_Carrera::contarCarreras();
        
        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Total de páginas obtenido correctamente',
            'resultado' => [
                'total_paginas' => $totalPaginas,
                'registros_por_pagina' => D_Carrera::REGISTROS_POR_PAGINA
            ]
        ]);
    }

    // Obtener carreras por departamento
    private static function obtenerCarrerasPorDepartamento($parametros)
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

        $idDepartamento = $parametros['idDepartamento'] ?? null;
        
        if (!$idDepartamento) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'ID de departamento no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        $carreras = D_Carrera::obtenerCarrerasPorDepartamento($idDepartamento);
        $resultado = [];
        
        foreach ($carreras as $carrera) {
            $resultado[] = $carrera->convertirAArray();
        }
        
        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Carreras por departamento obtenidas correctamente',
            'resultado' => $resultado
        ]);
    }

    // Obtener carreras por departamento paginadas
    private static function obtenerCarrerasPorDepartamentoPaginadas($parametros)
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

        $idDepartamento = $parametros['idDepartamento'] ?? null;
        $pagina = $parametros['pagina'] ?? 1;
        
        if (!$idDepartamento) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'ID de departamento no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        $pagina = intval($pagina);
        if ($pagina < 1) $pagina = 1;
        
        $carreras = D_Carrera::obtenerCarrerasPorDepartamentoPaginadas($idDepartamento, $pagina);
        $resultado = [];
        
        foreach ($carreras as $carrera) {
            $arr = $carrera->convertirAArray();
            if (isset($carrera->nombreDepartamento)) {
                $arr['nombreDepartamento'] = $carrera->nombreDepartamento;
            }
            $resultado[] = $arr;
        }
        
        // Obtener total de páginas para este departamento
        $totalCarreras = D_Carrera::contarCarrerasPorDepartamento($idDepartamento);
        $totalPaginas = ceil($totalCarreras / D_Carrera::REGISTROS_POR_PAGINA);
        
        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Carreras por departamento paginadas obtenidas correctamente',
            'resultado' => [
                'pagina_actual' => $pagina,
                'total_paginas' => $totalPaginas,
                'total_carreras' => $totalCarreras,
                'carreras' => $resultado
            ]
        ]);
    }

    // Buscar carreras
    private static function buscarCarreras($parametros)
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
            $carreras = D_Carrera::buscarCarrerasPaginadas($termino, $pagina);
            $totalPaginas = D_Carrera::contarResultadosBusquedaCarreras($termino);
        } else {
            $carreras = D_Carrera::buscarCarreras($termino);
            $totalPaginas = 1;
        }

        $resultado = [];
        foreach ($carreras as $carrera) {
            $arr = $carrera->convertirAArray();
            if (isset($carrera->nombreDepartamento)) {
                $arr['nombreDepartamento'] = $carrera->nombreDepartamento;
            }
            $resultado[] = $arr;
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
                'carreras' => $resultado
            ];
        }

        echo json_encode($response);
    }

    // Insertar nueva carrera
    private static function insertarCarrera($parametros)
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
        $nombreCarrera = $parametros['nombreCarrera'] ?? '';
        $idDepartamento = $parametros['idDepartamento'] ?? '';

        $errores = [];
        
        if (empty($nombreCarrera)) {
            $errores[] = 'Nombre de carrera es obligatorio';
        }
        
        if (empty($idDepartamento)) {
            $errores[] = 'Departamento es obligatorio';
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

        // Verificar si ya existe la carrera
        if (D_Carrera::existeCarrera($nombreCarrera)) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'Ya existe una carrera con ese nombre',
                'resultado' => null
            ]);
            return;
        }

        // Insertar carrera
        $carreraId = D_Carrera::insertarCarrera($parametros);

        if (!$carreraId) {
            echo json_encode([
                'estado' => 500,
                'exito' => false,
                'mensaje' => 'Error al crear la carrera',
                'resultado' => null
            ]);
            return;
        }

        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Carrera creada exitosamente',
            'resultado' => ['id' => $carreraId]
        ]);
    }

    // Actualizar carrera existente
    private static function actualizarCarrera($parametros)
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

        $id = $parametros['idCarrera'] ?? null;
        
        if (!$id) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'ID de carrera no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        // Verificar que la carrera existe
        $carreraExistente = D_Carrera::obtenerCarreraPorId($id);
        if (!$carreraExistente) {
            echo json_encode([
                'estado' => 404,
                'exito' => false,
                'mensaje' => 'Carrera no encontrada',
                'resultado' => null
            ]);
            return;
        }

        // Datos a actualizar
        $nombreCarrera = $parametros['nombreCarrera'] ?? $carreraExistente->nombreCarrera;
        $idDepartamento = $parametros['idDepartamento'] ?? $carreraExistente->idDepartamento;

        // Validaciones
        $errores = [];
        
        if (empty($nombreCarrera)) {
            $errores[] = 'Nombre de carrera es obligatorio';
        }

        // Verificar si ya existe otra carrera con ese nombre
        if ($nombreCarrera != $carreraExistente->nombreCarrera && 
            D_Carrera::existeCarrera($nombreCarrera, $id)) {
            $errores[] = 'Ya existe otra carrera con ese nombre';
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

        // Actualizar carrera
        $actualizado = D_Carrera::actualizarCarrera($parametros);

        if (!$actualizado) {
            echo json_encode([
                'estado' => 500,
                'exito' => false,
                'mensaje' => 'Error al actualizar la carrera',
                'resultado' => null
            ]);
            return;
        }

        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Carrera actualizada exitosamente',
            'resultado' => ['id' => $id]
        ]);
    }

    // Cambiar estado de carrera
    private static function cambiarEstadoCarrera($parametros)
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

        $id = $parametros['id'] ?? null;
        $nuevoEstado = $parametros['nuevoEstado'] ?? null;
        
        if (!$id) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'ID de carrera no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        if ($nuevoEstado === null) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'Nuevo estado no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        // Validar que el estado sea válido (1: activo, 0: inactivo)
        if (!in_array($nuevoEstado, ['activo', 'inactivo'])) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'Estado no válido. Debe ser 1 (activo) o 0 (inactivo)',
                'resultado' => null
            ]);
            return;
        }

        // Verificar que la carrera existe
        $carreraExistente = D_Carrera::obtenerCarreraPorId($id);
        if (!$carreraExistente) {
            echo json_encode([
                'estado' => 404,
                'exito' => false,
                'mensaje' => 'Carrera no encontrada',
                'resultado' => null
            ]);
            return;
        }

        // Cambiar estado
        $cambiado = D_Carrera::cambiarEstadoCarrera($id, $nuevoEstado);

        if (!$cambiado) {
            echo json_encode([
                'estado' => 500,
                'exito' => false,
                'mensaje' => 'Error al cambiar el estado de la carrera',
                'resultado' => null
            ]);
            return;
        }

        $estadoTexto = $nuevoEstado == 'activo' ? 'activada' : 'desactivada';
        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => "Carrera $estadoTexto exitosamente",
            'resultado' => ['id' => $id, 'nuevoEstado' => $nuevoEstado]
        ]);
    }

    // Eliminar carrera
    private static function eliminarCarrera($parametros)
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

        $id = $parametros['id'] ?? null;
        
        if (!$id) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'ID de carrera no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        // Verificar que la carrera existe
        $carreraExistente = D_Carrera::obtenerCarreraPorId($id);
        if (!$carreraExistente) {
            echo json_encode([
                'estado' => 404,
                'exito' => false,
                'mensaje' => 'Carrera no encontrada',
                'resultado' => null
            ]);
            return;
        }

        // Verificar si tiene asignaturas asociadas
        if (D_Carrera::tieneAsignaturasAsociadas($id)) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'No se puede eliminar la carrera porque tiene asignaturas asociadas',
                'resultado' => null
            ]);
            return;
        }

        // Eliminar carrera
        $eliminado = D_Carrera::eliminarCarrera($id);

        if (!$eliminado) {
            echo json_encode([
                'estado' => 500,
                'exito' => false,
                'mensaje' => 'Error al eliminar la carrera',
                'resultado' => null
            ]);
            return;
        }

        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Carrera eliminada exitosamente',
            'resultado' => ['id' => $id]
        ]);
    }
}
?>