<?php
require_once __DIR__ . "/../utilidades/u_verificaciones.php";
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
                
            case "insertarAula":
                self::insertarAula($parametros);
                break;
                
            case "obtenerAulasPorFacultad":
                self::obtenerAulasPorFacultad($parametros);
                break;
                
            case "actualizarAula":
                self::actualizarAula($parametros);
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
            'idFacultad' => $idFacultad
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
}
?>