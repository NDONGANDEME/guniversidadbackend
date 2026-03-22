<?php
require_once __DIR__ . "/../dao/d_estudiante.php";
require_once __DIR__ . "/../../Admin/dao/d_usuario.php";
require_once __DIR__ . "/../../utilidades/LimpiarDatos.php";
require_once __DIR__ . "/../../utilidades/u_verificaciones.php";
require_once __DIR__ . "/../modelo/m_estudiante.php";

class EstudianteController
{
    public static function dispatch($accion, $parametros)
    {
        // Verificar que el actor sea secretario
        if (!isset($parametros['actor']) || $parametros['actor'] !== 'secretario') {
            echo json_encode([
                'estado' => 403,
                'exito' => false,
                'mensaje' => 'Acceso denegado. Se requiere rol de secretario.',
                'resultado' => null
            ]);
            return;
        }

        // Verificar sesión activa
        if (!self::verificarSesionActiva()) {
            echo json_encode([
                'estado' => 401,
                'exito' => false,
                'mensaje' => 'No hay sesión activa',
                'resultado' => null
            ]);
            return;
        }

        switch ($accion) {
            case "obtenerEstudiantes":
                self::obtenerEstudiantes();
                break;
                
            case "obtenerEstudiantesPorAsignatura":
                self::obtenerEstudiantesPorAsignatura($parametros['idAsignatura'] ?? null);
                break;
                
            case "obtenerEstudiantesPorFacultad":
                self::obtenerEstudiantesPorFacultad($parametros['idFacultad'] ?? null);
                break;
                
            case "obtenerEstudiantesAPaginarPorFacultad":
                self::obtenerEstudiantesPaginadosPorFacultad($parametros);
                break;
                
            case "obtenerDatosEspecificosEstudiantes":
                self::obtenerDatosEspecificosEstudiantes($parametros['anioAcademico'] ?? null);
                break;
                
            case "insertarEstudiante":
                self::insertarEstudiante($parametros);
                break;
                
            case "actualizarEstudiante":
                self::actualizarEstudiante($parametros);
                break;
                
            case "deshabilitarEstudiante":
                self::cambiarEstadoEstudiante($parametros['id'] ?? null, 'inactivo');
                break;
                
            case "habilitarEstudiante":
                self::cambiarEstadoEstudiante($parametros['id'] ?? null, 'activo');
                break;
                
            default:
                echo json_encode([
                    'estado' => 400,
                    'exito' => false,
                    'mensaje' => "Acción '$accion' no válida en controlador de estudiantes",
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

    // Obtener todos los estudiantes
    private static function obtenerEstudiantes()
    {
        $estudiantes = D_Estudiante::obtenerEstudiantes();
        $resultado = [];
        
        foreach ($estudiantes as $estudiante) {
            $arr = $estudiante->convertirAArray();
            if (isset($estudiante->nombreUsuario)) {
                $arr['nombreUsuario'] = $estudiante->nombreUsuario;
            }
            $resultado[] = $arr;
        }
        
        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Estudiantes obtenidos correctamente',
            'resultado' => $resultado
        ]);
    }

    // Obtener estudiantes por asignatura
    private static function obtenerEstudiantesPorAsignatura($idAsignatura)
    {
        if (!$idAsignatura) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'ID de asignatura no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        $estudiantes = D_Estudiante::obtenerEstudiantesPorAsignatura($idAsignatura);
        $resultado = [];
        
        foreach ($estudiantes as $estudiante) {
            $resultado[] = $estudiante->convertirAArray();
        }
        
        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Estudiantes por asignatura obtenidos correctamente',
            'resultado' => $resultado
        ]);
    }

    // Obtener estudiantes por facultad
    private static function obtenerEstudiantesPorFacultad($idFacultad)
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

        $estudiantes = D_Estudiante::obtenerEstudiantesPorFacultad($idFacultad);
        $resultado = [];
        
        foreach ($estudiantes as $estudiante) {
            $resultado[] = $estudiante->convertirAArray();
        }
        
        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Estudiantes por facultad obtenidos correctamente',
            'resultado' => $resultado
        ]);
    }

    // Obtener estudiantes paginados por facultad (NUEVA FUNCIÓN)
    private static function obtenerEstudiantesPaginadosPorFacultad($parametros)
    {
        $idFacultad = $parametros['id'] ?? null;
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

        // Obtener total de páginas
        $totalPaginas = D_Estudiante::contarEstudiantesPorFacultad($idFacultad);
        
        // Obtener estudiantes para la página solicitada
        $estudiantes = D_Estudiante::obtenerEstudiantesPaginadosPorFacultad($pagina, $idFacultad);
        $resultado = [];
        
        foreach ($estudiantes as $estudiante) {
            $resultado[] = $estudiante->convertirAArray();
        }
        
        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Estudiantes paginados por facultad obtenidos correctamente',
            'resultado' => [
                'pagina_actual' => $pagina,
                'total_paginas' => $totalPaginas,
                'registros_por_pagina' => D_Estudiante::REGISTROS_POR_PAGINA,
                'estudiantes' => $resultado
            ]
        ]);
    }

    // Obtener datos específicos de estudiantes matriculados
    private static function obtenerDatosEspecificosEstudiantes($anioAcademico)
    {
        $datos = D_Estudiante::obtenerDatosEspecificosEstudiantes($anioAcademico);
        
        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Datos específicos de estudiantes obtenidos correctamente',
            'resultado' => $datos
        ]);
    }

    // Insertar estudiante
    private static function insertarEstudiante($parametros)
    {
        // Validar campos obligatorios
        $nombre = $parametros['nombre'] ?? '';
        $apellidos = $parametros['apellidos'] ?? '';
        $codigoEstudiante = $parametros['codigoEstudiante'] ?? '';
        $correoEstudiante = $parametros['correoEstudiante'] ?? '';
        
        if (empty($nombre) || empty($apellidos) || empty($codigoEstudiante) || empty($correoEstudiante)) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'Nombre, apellidos, código y correo son obligatorios',
                'resultado' => null
            ]);
            return;
        }

        // Verificar si ya existe el código
        if (D_Estudiante::existeCodigoEstudiante($codigoEstudiante)) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'Ya existe un estudiante con ese código',
                'resultado' => null
            ]);
            return;
        }

        // Verificar DIP si se proporciona
        $dipEstudiante = $parametros['dipEstudiante'] ?? '';
        if (!empty($dipEstudiante) && D_Estudiante::existeDipEstudiante($dipEstudiante)) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'Ya existe un estudiante con ese DIP',
                'resultado' => null
            ]);
            return;
        }

        // Preparar datos del estudiante
        $datos = [
            'idUsuario' => $parametros['idUsuario'] ?? null,
            'codigoEstudiante' => $codigoEstudiante,
            'nombre' => $nombre,
            'apellidos' => $apellidos,
            'dipEstudiante' => $dipEstudiante,
            'fechaNacimiento' => $parametros['fechaNacimiento'] ?? null,
            'sexo' => $parametros['sexo'] ?? '',
            'nacionalidad' => $parametros['nacionalidad'] ?? '',
            'direccion' => $parametros['direccion'] ?? '',
            'localidad' => $parametros['localidad'] ?? '',
            'provincia' => $parametros['provincia'] ?? '',
            'pais' => $parametros['pais'] ?? '',
            'telefono' => $parametros['telefono'] ?? '',
            'correoEstudiante' => $correoEstudiante,
            'centroProcedencia' => $parametros['centroProcedencia'] ?? '',
            'universidadProcedencia' => $parametros['universidadProcedencia'] ?? '',
            'esBecado' => $parametros['esBecado'] ?? 0
        ];

        // Insertar estudiante
        $estudianteId = D_Estudiante::insertarEstudiante($datos);

        if (!$estudianteId) {
            echo json_encode([
                'estado' => 500,
                'exito' => false,
                'mensaje' => 'Error al crear el estudiante',
                'resultado' => null
            ]);
            return;
        }

        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Estudiante creado exitosamente',
            'resultado' => ['id' => $estudianteId]
        ]);
    }

    // Actualizar estudiante
    private static function actualizarEstudiante($parametros)
    {
        $id = $parametros['id'] ?? null;
        
        if (!$id) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'ID de estudiante no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        // Verificar que existe
        $estudianteExistente = D_Estudiante::obtenerEstudiantePorId($id);
        if (!$estudianteExistente) {
            echo json_encode([
                'estado' => 404,
                'exito' => false,
                'mensaje' => 'Estudiante no encontrado',
                'resultado' => null
            ]);
            return;
        }

        // Datos a actualizar
        $codigoEstudiante = $parametros['codigoEstudiante'] ?? $estudianteExistente->codigoEstudiante;
        $dipEstudiante = $parametros['dipEstudiante'] ?? $estudianteExistente->dipEstudiante;

        // Verificar código si cambió
        if ($codigoEstudiante != $estudianteExistente->codigoEstudiante) {
            if (D_Estudiante::existeCodigoEstudiante($codigoEstudiante, $id)) {
                echo json_encode([
                    'estado' => 400,
                    'exito' => false,
                    'mensaje' => 'Ya existe otro estudiante con ese código',
                    'resultado' => null
                ]);
                return;
            }
        }

        // Verificar DIP si cambió
        if (!empty($dipEstudiante) && $dipEstudiante != $estudianteExistente->dipEstudiante) {
            if (D_Estudiante::existeDipEstudiante($dipEstudiante, $id)) {
                echo json_encode([
                    'estado' => 400,
                    'exito' => false,
                    'mensaje' => 'Ya existe otro estudiante con ese DIP',
                    'resultado' => null
                ]);
                return;
            }
        }

        // Preparar datos
        $datos = [
            'codigoEstudiante' => $codigoEstudiante,
            'nombre' => $parametros['nombre'] ?? $estudianteExistente->nombre,
            'apellidos' => $parametros['apellidos'] ?? $estudianteExistente->apellidos,
            'dipEstudiante' => $dipEstudiante,
            'fechaNacimiento' => $parametros['fechaNacimiento'] ?? $estudianteExistente->fechaNacimiento,
            'sexo' => $parametros['sexo'] ?? $estudianteExistente->sexo,
            'nacionalidad' => $parametros['nacionalidad'] ?? $estudianteExistente->nacionalidad,
            'direccion' => $parametros['direccion'] ?? $estudianteExistente->direccion,
            'localidad' => $parametros['localidad'] ?? $estudianteExistente->localidad,
            'provincia' => $parametros['provincia'] ?? $estudianteExistente->provincia,
            'pais' => $parametros['pais'] ?? $estudianteExistente->pais,
            'telefono' => $parametros['telefono'] ?? $estudianteExistente->telefono,
            'correoEstudiante' => $parametros['correoEstudiante'] ?? $estudianteExistente->correoEstudiante,
            'centroProcedencia' => $parametros['centroProcedencia'] ?? $estudianteExistente->centroProcedencia,
            'universidadProcedencia' => $parametros['universidadProcedencia'] ?? $estudianteExistente->universidadProcedencia,
            'esBecado' => isset($parametros['esBecado']) ? (int)$parametros['esBecado'] : $estudianteExistente->esBecado
        ];

        // Actualizar
        $actualizado = D_Estudiante::actualizarEstudiante($id, $datos);

        if (!$actualizado) {
            echo json_encode([
                'estado' => 500,
                'exito' => false,
                'mensaje' => 'Error al actualizar el estudiante',
                'resultado' => null
            ]);
            return;
        }

        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Estudiante actualizado exitosamente',
            'resultado' => ['id' => $id]
        ]);
    }

    // Cambiar estado del estudiante
    private static function cambiarEstadoEstudiante($id, $estado)
    {
        if (!$id) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'ID de estudiante no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        // Verificar que existe
        $estudianteExistente = D_Estudiante::obtenerEstudiantePorId($id);
        if (!$estudianteExistente) {
            echo json_encode([
                'estado' => 404,
                'exito' => false,
                'mensaje' => 'Estudiante no encontrado',
                'resultado' => null
            ]);
            return;
        }

        // Cambiar estado
        $cambiado = D_Estudiante::cambiarEstadoEstudiante($id, $estado);

        if ($cambiado) {
            $mensaje = $estado == 'activo' ? 'Estudiante habilitado exitosamente' : 'Estudiante deshabilitado exitosamente';
            echo json_encode([
                'estado' => 'exito',
                'exito' => true,
                'mensaje' => $mensaje,
                'resultado' => ['id' => $id, 'nuevoEstado' => $estado]
            ]);
        } else {
            echo json_encode([
                'estado' => 500,
                'exito' => false,
                'mensaje' => 'Error al cambiar el estado del estudiante',
                'resultado' => null
            ]);
        }
    }
}
?>