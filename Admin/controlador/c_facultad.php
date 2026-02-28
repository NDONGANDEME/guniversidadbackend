<?php
require_once __DIR__ . "/../utilidades/u_verificaciones.php";
require_once __DIR__ . "/../dao/d_facultad.php";
require_once __DIR__ . "/../modelo/m_facultad.php";

class FacultadController
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
            case "obtenerFacultades":
                self::obtenerFacultades();
                break;
                
            case "insertarFacultad":
                self::insertarFacultad($parametros);
                break;
                
            case "actualizarFacultad":
                self::actualizarFacultad($parametros);
                break;
                
            case "deshabilitarFacultad":
                // Como facultad no tiene campo estado, esto elimina físicamente
                self::eliminarFacultad($parametros['valor'] ?? null);
                break;
                
            case "habilitarFacultad":
                // Para facultad, habilitar no aplica, se responde con error
                echo json_encode([
                    'estado' => 400,
                    'exito' => false,
                    'mensaje' => 'Las facultades no tienen estado que habilitar',
                    'resultado' => null
                ]);
                break;
                
            default:
                echo json_encode([
                    'estado' => 400,
                    'exito' => false,
                    'mensaje' => "Acción '$accion' no válida en el controlador de facultades",
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

    // Obtener todas las facultades
    private static function obtenerFacultades()
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

        $facultades = D_Facultad::obtenerFacultades();
        $resultado = [];
        
        foreach ($facultades as $facultad) {
            $resultado[] = $facultad->convertirAArray();
        }
        
        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Facultades obtenidas correctamente',
            'resultado' => $resultado
        ]);
    }

    // Insertar nueva facultad
    private static function insertarFacultad($parametros)
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
        $nombreFacultad = $parametros['nombreFacultad'] ?? '';
        $direccionFacultad = $parametros['direccionFacultad'] ?? '';
        $contacto = $parametros['contacto'] ?? '';

        $errores = [];
        
        if (empty($nombreFacultad)) {
            $errores[] = 'Nombre de facultad es obligatorio';
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

        // Verificar si ya existe una facultad con ese nombre
        if (D_Facultad::existeNombreFacultad($nombreFacultad)) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'Ya existe una facultad con ese nombre',
                'resultado' => null
            ]);
            return;
        }

        // Insertar facultad
        $facultadId = D_Facultad::insertarFacultad([
            'nombreFacultad' => $nombreFacultad,
            'direccionFacultad' => $direccionFacultad,
            'contacto' => $contacto
        ]);

        if (!$facultadId) {
            echo json_encode([
                'estado' => 500,
                'exito' => false,
                'mensaje' => 'Error al crear la facultad',
                'resultado' => null
            ]);
            return;
        }

        // Obtener facultad creada
        $facultadCreada = D_Facultad::obtenerFacultadPorId($facultadId);

        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Facultad creada exitosamente',
            'resultado' => $facultadCreada ? $facultadCreada->convertirAArray() : ['id' => $facultadId]
        ]);
    }

    // Actualizar facultad existente
    private static function actualizarFacultad($parametros)
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
                'mensaje' => 'ID de facultad no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        // Verificar que la facultad existe
        $facultadExistente = D_Facultad::obtenerFacultadPorId($id);
        if (!$facultadExistente) {
            echo json_encode([
                'estado' => 404,
                'exito' => false,
                'mensaje' => 'Facultad no encontrada',
                'resultado' => null
            ]);
            return;
        }

        // Datos a actualizar
        $nombreFacultad = $parametros['nombreFacultad'] ?? $facultadExistente->nombreFacultad;
        $direccionFacultad = $parametros['direccionFacultad'] ?? $facultadExistente->direccionFacultad;
        $contacto = $parametros['contacto'] ?? $facultadExistente->contacto;

        // Validaciones
        $errores = [];
        
        if (empty($nombreFacultad)) {
            $errores[] = 'Nombre de facultad es obligatorio';
        }

        // Verificar si el nombre ya existe (y no es la misma facultad)
        if ($nombreFacultad !== $facultadExistente->nombreFacultad && 
            D_Facultad::existeNombreFacultad($nombreFacultad, $id)) {
            $errores[] = 'Ya existe una facultad con ese nombre';
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

        // Actualizar facultad
        $actualizado = D_Facultad::actualizarFacultad([
            'id' => $id,
            'nombreFacultad' => $nombreFacultad,
            'direccionFacultad' => $direccionFacultad,
            'contacto' => $contacto
        ]);

        if (!$actualizado) {
            echo json_encode([
                'estado' => 500,
                'exito' => false,
                'mensaje' => 'Error al actualizar la facultad',
                'resultado' => null
            ]);
            return;
        }

        // Obtener facultad actualizada
        $facultadActualizada = D_Facultad::obtenerFacultadPorId($id);

        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Facultad actualizada exitosamente',
            'resultado' => $facultadActualizada->convertirAArray()
        ]);
    }

    // Eliminar facultad (físicamente)
    private static function eliminarFacultad($id)
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

        if (!$id) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'ID de facultad no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        // Verificar que la facultad existe
        $facultadExistente = D_Facultad::obtenerFacultadPorId($id);
        if (!$facultadExistente) {
            echo json_encode([
                'estado' => 404,
                'exito' => false,
                'mensaje' => 'Facultad no encontrada',
                'resultado' => null
            ]);
            return;
        }

        // Eliminar facultad
        $eliminado = D_Facultad::eliminarFacultad($id);

        if ($eliminado) {
            echo json_encode([
                'estado' => 'exito',
                'exito' => true,
                'mensaje' => 'Facultad eliminada exitosamente',
                'resultado' => ['id' => $id]
            ]);
        } else {
            echo json_encode([
                'estado' => 500,
                'exito' => false,
                'mensaje' => 'Error al eliminar la facultad',
                'resultado' => null
            ]);
        }
    }
}
?>