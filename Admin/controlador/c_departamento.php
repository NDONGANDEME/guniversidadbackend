<?php
require_once __DIR__ . "/../../utilidades/u_verificaciones.php";
require_once __DIR__ . "/../dao/d_departamento.php";
require_once __DIR__ . "/../modelo/m_departamento.php";

class DepartamentoController
{
    public static function dispatch($accion, $parametros)
    {
        // Verificar que el actor sea admin para todas estas operaciones
        if (!isset($parametros['actor']) || $parametros['actor'] !== 'admin') {
            echo json_encode([
                'estado' => 403,
                'exito' => false,
                'mensaje' => 'Acceso denegado. Se requiere el rol de administrador.',
                'resultado' => null
            ]);
            return;
        }

        switch ($accion) {
            case "obtenerDepartamentos":
                self::obtenerDepartamentos();
                break;
                
            case "obtenerDepartamentosAPaginar":
                self::obtenerDepartamentosPaginados($parametros);
                break;
                
            case "obtenerTotalPaginasDepartamento":
                self::obtenerTotalPaginas();
                break;
                
            case "obtenerDepartamentosPorFacultad":
                self::obtenerDepartamentosPorFacultad($parametros);
                break;
                
            case "obtenerDepartamentosPorFacultadPaginados":
                self::obtenerDepartamentosPorFacultadPaginados($parametros);
                break;
                
            case "buscarDepartamentos":
                self::buscarDepartamentos($parametros);
                break;
                
            case "insertarDepartamento":
                self::insertarDepartamento($parametros);
                break;
                
            case "actualizarDepartamento":
                self::actualizarDepartamento($parametros);
                break;
                
            case "eliminarDepartamento":
                self::eliminarDepartamento($parametros);
                break;
                
            default:
                echo json_encode([
                    'estado' => 400,
                    'exito' => false,
                    'mensaje' => "Acción '$accion' no válida en el controlador de departamentos",
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

    // Obtener todos los departamentos
    private static function obtenerDepartamentos()
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

        $departamentos = D_Departamento::obtenerDepartamentos();
        
        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Departamentos obtenidos correctamente',
            'resultado' => $departamentos
        ]);
    }

    // Obtener departamentos paginados
    private static function obtenerDepartamentosPaginados($parametros)
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
        
        $departamentos = D_Departamento::obtenerDepartamentosAPaginar($pagina);
        $resultado = [];
        
        foreach ($departamentos as $depto) {
            $resultado[] = $depto->convertirAArray();
        }
        
        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Departamentos paginados obtenidos correctamente',
            'resultado' => [
                'pagina_actual' => $pagina,
                'departamentos' => $resultado
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

        $totalPaginas = D_Departamento::contarDepartamentos();
        
        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Total de páginas obtenido correctamente',
            'resultado' => [
                'total_paginas' => $totalPaginas,
                'registros_por_pagina' => D_Departamento::REGISTROS_POR_PAGINA
            ]
        ]);
    }

    // Obtener departamentos por facultad
    private static function obtenerDepartamentosPorFacultad($parametros)
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

        $departamentos = D_Departamento::obtenerDepartamentosPorFacultad($idFacultad);
        $resultado = [];
        
        foreach ($departamentos as $depto) {
            $resultado[] = $depto->convertirAArray();
        }
        
        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Departamentos por facultad obtenidos correctamente',
            'resultado' => $resultado
        ]);
    }

    // Obtener departamentos por facultad paginados
    private static function obtenerDepartamentosPorFacultadPaginados($parametros)
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
        
        $departamentos = D_Departamento::obtenerDepartamentosPorFacultadPaginadas($idFacultad, $pagina);
        $resultado = [];
        
        foreach ($departamentos as $depto) {
            $resultado[] = $depto->convertirAArray();
        }
        
        // Obtener total de páginas para esta facultad
        $totalDeptos = D_Departamento::contarDepartamentosPorFacultad($idFacultad);
        $totalPaginas = ceil($totalDeptos / D_Departamento::REGISTROS_POR_PAGINA);
        
        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Departamentos por facultad paginados obtenidos correctamente',
            'resultado' => [
                'pagina_actual' => $pagina,
                'total_paginas' => $totalPaginas,
                'total_departamentos' => $totalDeptos,
                'departamentos' => $resultado
            ]
        ]);
    }

    // Buscar departamentos
    private static function buscarDepartamentos($parametros)
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
            $departamentos = D_Departamento::buscarDepartamentosPaginados($termino, $pagina);
            $totalPaginas = D_Departamento::contarResultadosBusquedaDepartamentos($termino);
        } else {
            $departamentos = D_Departamento::buscarDepartamentos($termino);
            $totalPaginas = 1;
        }

        $resultado = [];
        foreach ($departamentos as $depto) {
            $resultado[] = $depto->convertirAArray();
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
                'departamentos' => $resultado
            ];
        }

        echo json_encode($response);
    }

    // Insertar nuevo departamento
    private static function insertarDepartamento($parametros)
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
        $nombreDepartamento = $parametros['nombreDepartamento'] ?? '';
        $idFacultad = $parametros['idFacultad'] ?? '';

        $errores = [];
        
        if (empty($nombreDepartamento)) {
            $errores[] = 'Nombre de departamento es obligatorio';
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

        // Verificar si ya existe el departamento
        if (D_Departamento::existeDepartamento($nombreDepartamento)) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'Ya existe un departamento con ese nombre',
                'resultado' => null
            ]);
            return;
        }

        // Insertar departamento
        $departamentoId = D_Departamento::insertarDepartamento([
            'nombreDepartamento' => $nombreDepartamento,
            'idFacultad' => $idFacultad
        ]);

        if (!$departamentoId) {
            echo json_encode([
                'estado' => 500,
                'exito' => false,
                'mensaje' => 'Error al crear el departamento',
                'resultado' => null
            ]);
            return;
        }

        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Departamento creado exitosamente',
            'resultado' => ['id' => $departamentoId]
        ]);
    }

    // Actualizar departamento existente
    private static function actualizarDepartamento($parametros)
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

        $id = $parametros['idDepartamento'] ?? null;
        
        if (!$id) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'ID de departamento no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        // Verificar que el departamento existe
        $departamentoExistente = D_Departamento::obtenerDepartamentoPorId($id);
        if (!$departamentoExistente) {
            echo json_encode([
                'estado' => 404,
                'exito' => false,
                'mensaje' => 'Departamento no encontrado',
                'resultado' => null
            ]);
            return;
        }

        // Datos a actualizar
        $nombreDepartamento = $parametros['nombreDepartamento'] ?? $departamentoExistente->nombreDepartamento;
        $idFacultad = $parametros['idFacultad'] ?? $departamentoExistente->idFacultad;

        // Validaciones
        $errores = [];
        
        if (empty($nombreDepartamento)) {
            $errores[] = 'Nombre de departamento es obligatorio';
        }

        // Verificar si ya existe otro departamento con ese nombre
        if ($nombreDepartamento != $departamentoExistente->nombreDepartamento && 
            D_Departamento::existeDepartamento($nombreDepartamento, $id)) {
            $errores[] = 'Ya existe otro departamento con ese nombre';
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

        // Actualizar departamento
        $actualizado = D_Departamento::actualizarDepartamento([
            'id' => $id,
            'nombreDepartamento' => $nombreDepartamento,
            'idFacultad' => $idFacultad
        ]);

        if (!$actualizado) {
            echo json_encode([
                'estado' => 500,
                'exito' => false,
                'mensaje' => 'Error al actualizar el departamento',
                'resultado' => null
            ]);
            return;
        }

        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Departamento actualizado exitosamente',
            'resultado' => ['id' => $id]
        ]);
    }

    // Eliminar departamento
    private static function eliminarDepartamento($parametros)
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

        $id = $parametros['idDepartamento'] ?? null;
        
        if (!$id) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'ID de departamento no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        // Verificar que el departamento existe
        $departamentoExistente = D_Departamento::obtenerDepartamentoPorId($id);
        if (!$departamentoExistente) {
            echo json_encode([
                'estado' => 404,
                'exito' => false,
                'mensaje' => 'Departamento no encontrado',
                'resultado' => null
            ]);
            return;
        }

        // Verificar si tiene carreras asociadas
        if (D_Departamento::tieneCarrerasAsociadas($id)) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'No se puede eliminar el departamento porque tiene carreras asociadas',
                'resultado' => null
            ]);
            return;
        }

        // Eliminar departamento
        $eliminado = D_Departamento::eliminarDepartamento($id);

        if (!$eliminado) {
            echo json_encode([
                'estado' => 500,
                'exito' => false,
                'mensaje' => 'Error al eliminar el departamento',
                'resultado' => null
            ]);
            return;
        }

        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Departamento eliminado exitosamente',
            'resultado' => ['id' => $id]
        ]);
    }
}
?>