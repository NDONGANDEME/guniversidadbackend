<?php
require_once __DIR__ . "/../utilidades/u_verificaciones.php";
require_once __DIR__ . "/../dao/d_asignatura.php";
require_once __DIR__ . "/../modelo/m_asignatura.php";

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
                
            case "insertarAsignatura":
                self::insertarAsignatura($parametros);
                break;
                
            case "actualizarAsignatura":
                self::actualizarAsignatura($parametros);
                break;
                
            case "obtenerAsignaturasPorFacultad":
                self::obtenerAsignaturasPorFacultad($parametros);
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
        if (!self::verificarSesionActiva()) {
            echo json_encode([
                'estado' => 401,
                'exito' => false,
                'mensaje' => 'No hay sesión activa',
                'resultado' => null
            ]);
            return;
        }

        $asignaturas = D_Asignatura::obtenerAsignaturas();
        $resultado = [];
        
        foreach ($asignaturas as $asignatura) {
            $resultado[] = $asignatura->convertirAArray();
        }
        
        echo json_encode([
            'estado' => 200,
            'exito' => true,
            'mensaje' => 'Asignaturas obtenidas correctamente',
            'resultado' => $resultado
        ]);
    }

    // Obtener asignaturas por facultad
    private static function obtenerAsignaturasPorFacultad($parametros)
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

    // Insertar nueva asignatura
    private static function insertarAsignatura($parametros)
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
        $asignaturaId = D_Asignatura::insertarAsignatura([
            'codigoAsignatura' => $codigoAsignatura,
            'nombreAsignatura' => $nombreAsignatura,
            'descripcion' => $descripcion
        ]);

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
}
?>