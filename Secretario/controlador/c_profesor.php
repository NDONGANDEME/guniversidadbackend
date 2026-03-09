<?php
require_once __DIR__ . "/../dao/d_profesor.php";
require_once __DIR__ . "../../../Admin/dao/d_usuario.php";
require_once __DIR__ . "/../../utilidades/LimpiarDatos.php";
require_once __DIR__ . "/../../utilidades/u_verificaciones.php";
require_once __DIR__ . "/../modelo/m_profesor.php";

class ProfesorController
{
    public static function dispatch($accion, $parametros)
    {
        // Verificar que el actor sea admin
        if (!isset($parametros['actor']) || $parametros['actor'] !== 'secretario') {
            echo json_encode([
                'estado' => 403,
                'exito' => false,
                'mensaje' => 'Acceso denegado. Se requiere rol de administrador.',
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
            case "obtenerProfesores":
                self::obtenerProfesores();
                break;
                
            case "obtenerProfesoresPorFacultad":
                self::obtenerProfesoresPorFacultad($parametros['idFacultad'] ?? null);
                break;
                
            case "obtenerProfesoresPorDepartamento":
                self::obtenerProfesoresPorDepartamento($parametros['idDepartamento'] ?? null);
                break;
                
            case "insertarProfesor":
                self::insertarProfesor($parametros);
                break;
                
            case "actualizarProfesor":
                self::actualizarProfesor($parametros);
                break;
                
            default:
                echo json_encode([
                    'estado' => 400,
                    'exito' => false,
                    'mensaje' => "Acción '$accion' no válida en controlador de profesores",
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

    // Obtener todos los profesores
    private static function obtenerProfesores()
    {
        $profesores = D_Profesor::obtenerProfesores();
        $resultado = [];
        
        foreach ($profesores as $profesor) {
            $arr = $profesor->convertirAArray();
            if (isset($profesor->nombreDepartamento)) {
                $arr['nombreDepartamento'] = $profesor->nombreDepartamento;
            }
            $resultado[] = $arr;
        }
        
        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Profesores obtenidos correctamente',
            'resultado' => $resultado
        ]);
    }

    // Obtener profesores por facultad
    private static function obtenerProfesoresPorFacultad($idFacultad)
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

        $profesores = D_Profesor::obtenerProfesoresPorFacultad($idFacultad);
        $resultado = [];
        
        foreach ($profesores as $profesor) {
            $arr = $profesor->convertirAArray();
            if (isset($profesor->nombreDepartamento)) {
                $arr['nombreDepartamento'] = $profesor->nombreDepartamento;
            }
            $resultado[] = $arr;
        }
        
        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Profesores por facultad obtenidos correctamente',
            'resultado' => $resultado
        ]);
    }

    // Obtener profesores por departamento
    private static function obtenerProfesoresPorDepartamento($idDepartamento)
    {
        if (!$idDepartamento) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'ID de departamento no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        $profesores = D_Profesor::obtenerProfesoresPorDepartamento($idDepartamento);
        $resultado = [];
        
        foreach ($profesores as $profesor) {
            $resultado[] = $profesor->convertirAArray();
        }
        
        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Profesores por departamento obtenidos correctamente',
            'resultado' => $resultado
        ]);
    }

    // Insertar profesor
    private static function insertarProfesor($parametros)
    {
        // Validar campos obligatorios
        $nombreProfesor = $parametros['nombreProfesor'] ?? '';
        $apellidosProfesor = $parametros['apellidosProfesor'] ?? '';
        $correoProfesor = $parametros['correoProfesor'] ?? '';
        $idDepartamento = $parametros['idDepartamento'] ?? '';
        
        if (empty($nombreProfesor) || empty($apellidosProfesor) || empty($correoProfesor) || empty($idDepartamento)) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'Nombre, apellidos, correo y departamento son obligatorios',
                'resultado' => null
            ]);
            return;
        }

        // Verificar DIP si se proporciona
        $dipProfesor = $parametros['dipProfesor'] ?? '';
        if (!empty($dipProfesor) && D_Profesor::existeProfesorPorDip($dipProfesor)) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'Ya existe un profesor con ese DIP',
                'resultado' => null
            ]);
            return;
        }

        // Verificar correo
        if (D_Profesor::existeProfesorPorCorreo($correoProfesor)) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'Ya existe un profesor con ese correo',
                'resultado' => null
            ]);
            return;
        }

        // Primero crear usuario asociado si se proporcionan datos de usuario
        $idUsuario = null;
        if (!empty($parametros['nombreUsuario']) && !empty($parametros['contrasena'])) {
            // Verificar si el usuario ya existe
            if (D_Usuario::existeNombreUsuario($parametros['nombreUsuario'])) {
                echo json_encode([
                    'estado' => 400,
                    'exito' => false,
                    'mensaje' => 'El nombre de usuario ya está en uso',
                    'resultado' => null
                ]);
                return;
            }

            // Crear usuario
            $parametros['contrasena'] = password_hash($parametros['contrasena'], PASSWORD_DEFAULT);
            $idUsuario = D_Usuario::insertarUsuario($parametros);

            if (!$idUsuario) {
                echo json_encode([
                    'estado' => 500,
                    'exito' => false,
                    'mensaje' => 'Error al crear el usuario asociado',
                    'resultado' => null
                ]);
                return;
            }
        }

        // Preparar datos del profesor
        $datos = [
            'nombreProfesor' => $nombreProfesor,
            'apellidosProfesor' => $apellidosProfesor,
            'dipProfesor' => $dipProfesor,
            'especialidad' => $parametros['especialidad'] ?? '',
            'gradoEstudio' => $parametros['gradoEstudio'] ?? '',
            'idDepartamento' => $idDepartamento,
            'idUsuario' => $idUsuario,
            'genero' => $parametros['genero'] ?? '',
            'nacionalidad' => $parametros['nacionalidad'] ?? '',
            'responsabilidad' => $parametros['responsabilidad'] ?? '',
            'correoProfesor' => $correoProfesor,
            'contactoProfesor' => $parametros['contactoProfesor'] ?? ''
        ];

        // Insertar profesor
        $profesorId = D_Profesor::insertarProfesor($datos);

        if (!$profesorId) {
            echo json_encode([
                'estado' => 500,
                'exito' => false,
                'mensaje' => 'Error al crear el profesor',
                'resultado' => null
            ]);
            return;
        }

        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Profesor creado exitosamente',
            'resultado' => ['id' => $profesorId]
        ]);
    }

    // Actualizar profesor
    private static function actualizarProfesor($parametros)
    {
        $id = $parametros['id'] ?? null;
        
        if (!$id) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'ID de profesor no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        // Verificar que existe
        $profesorExistente = D_Profesor::obtenerProfesorPorId($id);
        if (!$profesorExistente) {
            echo json_encode([
                'estado' => 404,
                'exito' => false,
                'mensaje' => 'Profesor no encontrado',
                'resultado' => null
            ]);
            return;
        }

        // Datos a actualizar
        $dipProfesor = $parametros['dipProfesor'] ?? $profesorExistente->dipProfesor;
        $correoProfesor = $parametros['correoProfesor'] ?? $profesorExistente->correoProfesor;

        // Verificar DIP si cambió
        if (!empty($dipProfesor) && $dipProfesor != $profesorExistente->dipProfesor) {
            if (D_Profesor::existeProfesorPorDip($dipProfesor, $id)) {
                echo json_encode([
                    'estado' => 400,
                    'exito' => false,
                    'mensaje' => 'Ya existe otro profesor con ese DIP',
                    'resultado' => null
                ]);
                return;
            }
        }

        // Verificar correo si cambió
        if ($correoProfesor != $profesorExistente->correoProfesor) {
            if (D_Profesor::existeProfesorPorCorreo($correoProfesor, $id)) {
                echo json_encode([
                    'estado' => 400,
                    'exito' => false,
                    'mensaje' => 'Ya existe otro profesor con ese correo',
                    'resultado' => null
                ]);
                return;
            }
        }

        // Preparar datos
        $datos = [
            'nombreProfesor' => $parametros['nombreProfesor'] ?? $profesorExistente->nombreProfesor,
            'apellidosProfesor' => $parametros['apellidosProfesor'] ?? $profesorExistente->apellidosProfesor,
            'dipProfesor' => $dipProfesor,
            'especialidad' => $parametros['especialidad'] ?? $profesorExistente->especialidad,
            'gradoEstudio' => $parametros['gradoEstudio'] ?? $profesorExistente->gradoEstudio,
            'idDepartamento' => $parametros['idDepartamento'] ?? $profesorExistente->idDepartamento,
            'genero' => $parametros['genero'] ?? $profesorExistente->genero,
            'nacionalidad' => $parametros['nacionalidad'] ?? $profesorExistente->nacionalidad,
            'responsabilidad' => $parametros['responsabilidad'] ?? $profesorExistente->responsabilidad,
            'correoProfesor' => $correoProfesor,
            'contactoProfesor' => $parametros['contactoProfesor'] ?? $profesorExistente->contactoProfesor
        ];

        // Actualizar
        $actualizado = D_Profesor::actualizarProfesor($id, $datos);

        if (!$actualizado) {
            echo json_encode([
                'estado' => 500,
                'exito' => false,
                'mensaje' => 'Error al actualizar el profesor',
                'resultado' => null
            ]);
            return;
        }

        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Profesor actualizado exitosamente',
            'resultado' => ['id' => $id]
        ]);
    }
}
?>