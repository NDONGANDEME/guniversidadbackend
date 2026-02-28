<?php
require_once __DIR__ . "/../utilidades/u_verificaciones.php";
require_once __DIR__ . "/../dao/d_curso.php";
require_once __DIR__ . "/../modelo/m_curso.php";

class CursoController
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
            case "obtenerCursos":
                self::obtenerCursos();
                break;
                
            case "insertarCurso":
                self::insertarCurso($parametros);
                break;
                
            case "actualizarCurso":
                self::actualizarCurso($parametros);
                break;
                
            default:
                echo json_encode([
                    'estado' => 400,
                    'exito' => false,
                    'mensaje' => "Acción '$accion' no válida en el controlador de cursos",
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

    // Obtener todos los cursos
    private static function obtenerCursos()
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

        $cursos = D_Curso::obtenerCursos();
        $resultado = [];
        
        foreach ($cursos as $curso) {
            $resultado[] = $curso->convertirAArray();
        }
        
        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Cursos obtenidos correctamente',
            'resultado' => $resultado
        ]);
    }

    // Insertar nuevo curso
    private static function insertarCurso($parametros)
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
        $nombreCurso = $parametros['nombreCurso'] ?? '';
        $nivel = $parametros['nivel'] ?? '';

        $errores = [];
        
        if (empty($nombreCurso)) {
            $errores[] = 'Nombre de curso es obligatorio';
        }
        
        if (empty($nivel)) {
            $errores[] = 'Nivel es obligatorio';
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

        // Verificar si ya existe el curso
        if (D_Curso::existeCurso($nombreCurso)) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'Ya existe un curso con ese nombre',
                'resultado' => null
            ]);
            return;
        }

        // Insertar curso
        $cursoId = D_Curso::insertarCurso([
            'nombreCurso' => $nombreCurso,
            'nivel' => $nivel
        ]);

        if (!$cursoId) {
            echo json_encode([
                'estado' => 500,
                'exito' => false,
                'mensaje' => 'Error al crear el curso',
                'resultado' => null
            ]);
            return;
        }

        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Curso creado exitosamente',
            'resultado' => ['id' => $cursoId]
        ]);
    }

    // Actualizar curso existente
    private static function actualizarCurso($parametros)
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
                'mensaje' => 'ID de curso no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        // Verificar que el curso existe
        $cursoExistente = D_Curso::obtenerCursoPorId($id);
        if (!$cursoExistente) {
            echo json_encode([
                'estado' => 404,
                'exito' => false,
                'mensaje' => 'Curso no encontrado',
                'resultado' => null
            ]);
            return;
        }

        // Datos a actualizar
        $nombreCurso = $parametros['nombreCurso'] ?? $cursoExistente->nombreCurso;
        $nivel = $parametros['nivel'] ?? $cursoExistente->nivel;

        // Validaciones
        $errores = [];
        
        if (empty($nombreCurso)) {
            $errores[] = 'Nombre de curso es obligatorio';
        }

        // Verificar si ya existe otro curso con ese nombre
        if ($nombreCurso != $cursoExistente->nombreCurso && 
            D_Curso::existeCurso($nombreCurso, $id)) {
            $errores[] = 'Ya existe otro curso con ese nombre';
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

        // Actualizar curso
        $actualizado = D_Curso::actualizarCurso([
            'id' => $id,
            'nombreCurso' => $nombreCurso,
            'nivel' => $nivel
        ]);

        if (!$actualizado) {
            echo json_encode([
                'estado' => 500,
                'exito' => false,
                'mensaje' => 'Error al actualizar el curso',
                'resultado' => null
            ]);
            return;
        }

        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Curso actualizado exitosamente',
            'resultado' => ['id' => $id]
        ]);
    }
}
?>