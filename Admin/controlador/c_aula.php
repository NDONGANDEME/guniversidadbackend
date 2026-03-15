<?php
require_once __DIR__ . "/../../utilidades/u_verificaciones.php";
require_once __DIR__ . "/../dao/d_aula.php";
require_once __DIR__ . "/../modelo/m_aula.php";

class AulaController
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
            case "obtenerAulas":
                self::obtenerAulas();
                break;
                
            case "obtenerAulasAPaginar":
                self::obtenerAulasPaginadas($parametros);
                break;
                
            case "obtenerTotalPaginasAula":
                self::obtenerTotalPaginas();
                break;
                
            case "obtenerAulasPorFacultad":
                self::obtenerAulasPorFacultad($parametros);
                break;
                
            case "obtenerAulasPorFacultadPaginadas":
                self::obtenerAulasPorFacultadPaginadas($parametros);
                break;
                
            case "buscarAulas":
                self::buscarAulas($parametros);
                break;
                
            case "insertarAula":
                self::insertarAula($parametros);
                break;
                
            case "actualizarAula":
                self::actualizarAula($parametros);
                break;
                
            case "eliminarAula":
                self::eliminarAula($parametros);
                break;
                
            default:
                echo json_encode([
                    'estado' => 400,
                    'exito' => false,
                    'mensaje' => "Acción '$accion' no válida en el controlador de aulas",
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

    // Obtener todas las aulas
    private static function obtenerAulas()
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

        $aulas = D_Aula::obtenerAulas();
        $resultado = [];
        
        foreach ($aulas as $aula) {
            $arr = $aula->convertirAArray();
            if (isset($aula->nombreFacultad)) {
                $arr['nombreFacultad'] = $aula->nombreFacultad;
            }
            $resultado[] = $arr;
        }
        
        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Aulas obtenidas correctamente',
            'resultado' => $resultado
        ]);
    }

    // Obtener aulas paginadas
    private static function obtenerAulasPaginadas($parametros)
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
        
        $aulas = D_Aula::obtenerAulasAPaginar($pagina);
        $resultado = [];
        
        foreach ($aulas as $aula) {
            $arr = $aula->convertirAArray();
            if (isset($aula->nombreFacultad)) {
                $arr['nombreFacultad'] = $aula->nombreFacultad;
            }
            $resultado[] = $arr;
        }
        
        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Aulas paginadas obtenidas correctamente',
            'resultado' => [
                'pagina_actual' => $pagina,
                'aulas' => $resultado
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

        $totalPaginas = D_Aula::contarAulas();
        
        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Total de páginas obtenido correctamente',
            'resultado' => [
                'total_paginas' => $totalPaginas,
                'registros_por_pagina' => D_Aula::REGISTROS_POR_PAGINA
            ]
        ]);
    }

    // Obtener aulas por facultad
    private static function obtenerAulasPorFacultad($parametros)
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
        
        if (!$idFacultad) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'ID de facultad no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        $aulas = D_Aula::obtenerAulasPorFacultad($idFacultad);
        $resultado = [];
        
        foreach ($aulas as $aula) {
            $resultado[] = $aula->convertirAArray();
        }
        
        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Aulas por facultad obtenidas correctamente',
            'resultado' => $resultado
        ]);
    }

    // Obtener aulas por facultad paginadas
    private static function obtenerAulasPorFacultadPaginadas($parametros)
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
        
        $aulas = D_Aula::obtenerAulasPorFacultadPaginadas($idFacultad, $pagina);
        $resultado = [];
        
        foreach ($aulas as $aula) {
            $resultado[] = $aula->convertirAArray();
        }
        
        // Obtener total de páginas para esta facultad
        $totalAulas = D_Aula::contarAulasPorFacultad($idFacultad);
        $totalPaginas = ceil($totalAulas / D_Aula::REGISTROS_POR_PAGINA);
        
        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Aulas por facultad paginadas obtenidas correctamente',
            'resultado' => [
                'pagina_actual' => $pagina,
                'total_paginas' => $totalPaginas,
                'total_aulas' => $totalAulas,
                'aulas' => $resultado
            ]
        ]);
    }

    // Buscar aulas
    private static function buscarAulas($parametros)
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
            $aulas = D_Aula::buscarAulasPaginadas($termino, $pagina);
            $totalPaginas = D_Aula::contarResultadosBusquedaAulas($termino);
        } else {
            $aulas = D_Aula::buscarAulas($termino);
            $totalPaginas = 1;
        }

        $resultado = [];
        foreach ($aulas as $aula) {
            $arr = $aula->convertirAArray();
            if (isset($aula->nombreFacultad)) {
                $arr['nombreFacultad'] = $aula->nombreFacultad;
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
                'aulas' => $resultado
            ];
        }

        echo json_encode($response);
    }

    // Insertar nueva aula
    private static function insertarAula($parametros)
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

        // Validar campos obligatorios
        $nombreAula = $parametros['nombreAula'] ?? '';
        $capacidad = $parametros['capacidad'] ?? '';
        $idFacultad = $parametros['idFacultad'] ?? '';
        $estado = $parametros['estado'] ?? '';

        $errores = [];
        
        if (empty($nombreAula)) {
            $errores[] = 'Nombre de aula es obligatorio';
        }
        
        if (empty($capacidad)) {
            $errores[] = 'Capacidad es obligatoria';
        } elseif (!is_numeric($capacidad) || $capacidad <= 0) {
            $errores[] = 'Capacidad debe ser un número positivo';
        }
        
        if (empty($idFacultad)) {
            $errores[] = 'Facultad es obligatoria';
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

        // Verificar si ya existe el aula en esa facultad
        if (D_Aula::existeAula($nombreAula, $idFacultad)) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'Ya existe un aula con ese nombre en esta facultad',
                'resultado' => null
            ]);
            return;
        }

        // Insertar aula
        $aulaId = D_Aula::insertarAula([
            'nombreAula' => $nombreAula,
            'capacidad' => $capacidad,
            'idFacultad' => $idFacultad,
            'estado' => $estado
        ]);

        if (!$aulaId) {
            echo json_encode([
                'estado' => 500,
                'exito' => false,
                'mensaje' => 'Error al crear el aula',
                'resultado' => null
            ]);
            return;
        }

        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Aula creada exitosamente',
            'resultado' => ['id' => $aulaId]
        ]);
    }

    // Actualizar aula existente
    private static function actualizarAula($parametros)
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

        $id = $parametros['idAula'] ?? null;
        
        if (!$id) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'ID de aula no proporcionado',
                'resultado' => $id
            ]);
            return;
        }

        // Verificar que el aula existe
        $aulaExistente = D_Aula::obtenerAulaPorId($id);
        if (!$aulaExistente) {
            echo json_encode([
                'estado' => 404,
                'exito' => false,
                'mensaje' => 'Aula no encontrada',
                'resultado' => null
            ]);
            return;
        }

        // Datos a actualizar
        $nombreAula = $parametros['nombreAula'] ?? $aulaExistente->nombreAula;
        $capacidad = $parametros['capacidad'] ?? $aulaExistente->capacidad;
        $idFacultad = $parametros['idFacultad'] ?? $aulaExistente->idFacultad;

        // Validaciones
        $errores = [];
        
        if (empty($nombreAula)) {
            $errores[] = 'Nombre de aula es obligatorio';
        }
        
        if (!is_numeric($capacidad) || $capacidad <= 0) {
            $errores[] = 'Capacidad debe ser un número positivo';
        }

        // Verificar si ya existe otro aula con ese nombre en la misma facultad
        if (($nombreAula != $aulaExistente->nombreAula || $idFacultad != $aulaExistente->idFacultad) && 
            D_Aula::existeAula($nombreAula, $idFacultad, $id)) {
            $errores[] = 'Ya existe otro aula con ese nombre en esta facultad';
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

        // Actualizar aula
        $actualizado = D_Aula::actualizarAula([
            'id' => $id,
            'nombreAula' => $nombreAula,
            'capacidad' => $capacidad,
            'idFacultad' => $idFacultad
        ]);

        if (!$actualizado) {
            echo json_encode([
                'estado' => 500,
                'exito' => false,
                'mensaje' => 'Error al actualizar el aula',
                'resultado' => null
            ]);
            return;
        }

        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Aula actualizada exitosamente',
            'resultado' => ['id' => $id]
        ]);
    }

    // Eliminar aula
    private static function eliminarAula($parametros)
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
                'mensaje' => 'ID de aula no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        // Verificar que el aula existe
        $aulaExistente = D_Aula::obtenerAulaPorId($id);
        if (!$aulaExistente) {
            echo json_encode([
                'estado' => 404,
                'exito' => false,
                'mensaje' => 'Aula no encontrada',
                'resultado' => null
            ]);
            return;
        }

        // Verificar si tiene horarios asociados
        if (D_Aula::tieneHorariosAsociados($id)) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'No se puede eliminar el aula porque tiene horarios asociados',
                'resultado' => null
            ]);
            return;
        }

        // Eliminar aula
        $eliminado = D_Aula::eliminarAula($id);

        if (!$eliminado) {
            echo json_encode([
                'estado' => 500,
                'exito' => false,
                'mensaje' => 'Error al eliminar el aula',
                'resultado' => null
            ]);
            return;
        }

        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Aula eliminada exitosamente',
            'resultado' => ['id' => $id]
        ]);
    }
}
?>