<?php
require_once __DIR__ . "/../utilidades/u_verificaciones.php";
require_once __DIR__ . "/../dao/d_semestre.php";
require_once __DIR__ . "/../modelo/m_semestre.php";

class SemestreController
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
            case "obtenerSemestres":
                self::obtenerSemestres();
                break;
                
            case "insertarSemestre":
                self::insertarSemestre($parametros);
                break;
                
            case "actualizarSemestre":
                self::actualizarSemestre($parametros);
                break;
                
            default:
                echo json_encode([
                    'estado' => 400,
                    'exito' => false,
                    'mensaje' => "Acción '$accion' no válida en el controlador de semestres",
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

    // Obtener todos los semestres
    private static function obtenerSemestres()
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

        $semestres = D_Semestre::obtenerSemestres();
        $resultado = [];
        
        foreach ($semestres as $semestre) {
            $resultado[] = $semestre->convertirAArray();
        }
        
        echo json_encode([
            'estado' => 200,
            'exito' => true,
            'mensaje' => 'Semestres obtenidos correctamente',
            'resultado' => $resultado
        ]);
    }

    // Insertar nuevo semestre
    private static function insertarSemestre($parametros)
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
        $numeroSemestre = $parametros['numeroSemestre'] ?? '';
        $tipoSemestre = $parametros['tipoSemestre'] ?? '';

        $errores = [];
        
        if (empty($numeroSemestre)) {
            $errores[] = 'Número de semestre es obligatorio';
        }
        
        if (empty($tipoSemestre)) {
            $errores[] = 'Tipo de semestre es obligatorio';
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

        // Verificar si ya existe el semestre
        if (D_Semestre::existeSemestre($numeroSemestre, $tipoSemestre)) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'Ya existe un semestre con ese número y tipo',
                'resultado' => null
            ]);
            return;
        }

        // Insertar semestre
        $semestreId = D_Semestre::insertarSemestre([
            'numeroSemestre' => $numeroSemestre,
            'tipoSemestre' => $tipoSemestre
        ]);

        if (!$semestreId) {
            echo json_encode([
                'estado' => 500,
                'exito' => false,
                'mensaje' => 'Error al crear el semestre',
                'resultado' => null
            ]);
            return;
        }

        echo json_encode([
            'estado' => 200,
            'exito' => true,
            'mensaje' => 'Semestre creado exitosamente',
            'resultado' => ['id' => $semestreId]
        ]);
    }

    // Actualizar semestre existente
    private static function actualizarSemestre($parametros)
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
                'mensaje' => 'ID de semestre no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        // Verificar que el semestre existe
        $semestreExistente = D_Semestre::obtenerSemestrePorId($id);
        if (!$semestreExistente) {
            echo json_encode([
                'estado' => 404,
                'exito' => false,
                'mensaje' => 'Semestre no encontrado',
                'resultado' => null
            ]);
            return;
        }

        // Datos a actualizar
        $numeroSemestre = $parametros['numeroSemestre'] ?? $semestreExistente->numeroSemestre;
        $tipoSemestre = $parametros['tipoSemestre'] ?? $semestreExistente->tipoSemestre;

        // Validaciones
        $errores = [];
        
        if (empty($numeroSemestre)) {
            $errores[] = 'Número de semestre es obligatorio';
        }
        
        if (empty($tipoSemestre)) {
            $errores[] = 'Tipo de semestre es obligatorio';
        }

        // Verificar si ya existe otro semestre con esos datos
        if (($numeroSemestre != $semestreExistente->numeroSemestre || 
             $tipoSemestre != $semestreExistente->tipoSemestre) && 
            D_Semestre::existeSemestre($numeroSemestre, $tipoSemestre, $id)) {
            $errores[] = 'Ya existe otro semestre con ese número y tipo';
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

        // Actualizar semestre
        $actualizado = D_Semestre::actualizarSemestre([
            'id' => $id,
            'numeroSemestre' => $numeroSemestre,
            'tipoSemestre' => $tipoSemestre
        ]);

        if (!$actualizado) {
            echo json_encode([
                'estado' => 500,
                'exito' => false,
                'mensaje' => 'Error al actualizar el semestre',
                'resultado' => null
            ]);
            return;
        }

        echo json_encode([
            'estado' => 200,
            'exito' => true,
            'mensaje' => 'Semestre actualizado exitosamente',
            'resultado' => ['id' => $id]
        ]);
    }
}
?>