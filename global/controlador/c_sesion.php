<?php
require_once __DIR__ . "/../dao/d_sesion.php";
require_once __DIR__ . "/../../Secretario/dao/d_estudiante.php";
require_once __DIR__ . "/../../Secretario/dao/d_profesor.php";
require_once __DIR__ . "/../../Admin/dao/d_administrativo.php";
require_once __DIR__ . "/../../Admin/dao/d_facultad.php";
require_once __DIR__ . "/../modelo/m_sesion.php";
require_once __DIR__ . "/../../utilidades/LimpiarDatos.php";
require_once __DIR__ . "/../../utilidades/u_verificaciones.php";

class SesionController
{
    public static function dispatch($accion, $parametros)
    {
        switch ($accion) {
            case "verificarCredenciales":
                self::iniciarSesion($parametros);
                break;
                
            case "cerrarSesion":
                self::cerrarSesion($parametros);
                break;
                
            case "validarSesion":
                self::validarSesionActiva($parametros);
                break;
                
            case "obtenerPreguntaRecuperacion":
                self::obtenerPreguntaRecuperacion($parametros);
                break;
                
            case "verificarRespuesta":
                self::verificarRespuestaRecuperacion($parametros);
                break;
                
            case "cambiarContrasenaRecuperacion":
                self::cambiarContrasenaRecuperacion($parametros);
                break;
                
            case "obtenerUsuarioPorCorreo":
                self::obtenerUsuarioPorCorreo($parametros);
                break;
                
            default:
                echo json_encode([
                    'estado' => 400, 
                    'exito' => false, 
                    'mensaje' => "La acción '$accion' no está disponible",
                    'resultado' => null
                ]);
        }
    }

    /**
     * Iniciar sesión con nombre/correo y contraseña
     * TODO EN resultado (usuario + rol + permisos + datos específicos)
     */
    public static function iniciarSesion($parametros)
    {
        // Validar parámetros obligatorios
        if (!isset($parametros['nombreOCorreo']) || !isset($parametros['contraseña'])) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'Correo/Usuario y contraseña son obligatorios',
                'resultado' => null
            ]);
            return;
        }

        $correoONombre = LimpiarDatos::limpiarParametro($parametros['nombreOCorreo']);
        $contrasena = $parametros['contraseña'];

        // Buscar usuario completo con rol y permisos (directos por usuario)
        //$usuarioModel = new SesionModel();
        $usuarioModel = D_Sesion::obtenerUsuarioCompleto($correoONombre);

        if (!$usuarioModel) {
            echo json_encode([
                'estado' => 401,
                'exito' => false,
                'mensaje' => 'Credenciales inválidas (nombre)',
                'resultado' => $usuarioModel
            ]);
            return;
        }

        // Verificar si el usuario está activo
        if (!$usuarioModel->estaActivo()) {
            echo json_encode([
                'estado' => 403,
                'exito' => false,
                'mensaje' => 'Usuario inactivo. Contacte al administrador.',
                'resultado' => null
            ]);
            return;
        }

        // Verificar contraseña
        /*if (!$usuarioModel->validarContrasena($contrasena)) {
            echo json_encode([
                'estado' => 401,
                'exito' => false,
                'mensaje' => 'Credenciales inválidas (contraseña)',
                'resultado' => $contrasena
            ]);
            return;
        }*/

        // Actualizar último acceso (con transacción)
        $actualizado = D_Sesion::actualizarUltimoAcceso($usuarioModel->idUsuario);
        if (!$actualizado) {
            error_log("No se pudo actualizar el último acceso del usuario: " . $usuarioModel->idUsuario);
        }

        // Iniciar sesión en PHP
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $_SESSION['usuario_id'] = $usuarioModel->idUsuario;
        $_SESSION['usuario_nombre'] = $usuarioModel->nombreUsuario;
        $_SESSION['usuario_correo'] = $usuarioModel->correo;
        $_SESSION['usuario_rol'] = $usuarioModel->nombreRol;
        $_SESSION['usuario_idRol'] = $usuarioModel->idRol;
        $_SESSION['usuario_foto'] = $usuarioModel->foto;
        $_SESSION['usuario_permisos'] = $usuarioModel->permisos;
        $_SESSION['ultimo_acceso'] = time();

        // Preparar resultado UNIFICADO
        $resultado = $usuarioModel->convertirAArray();
        
        // AÑADIR sesion_id al mismo objeto resultado
        $resultado['sesion_id'] = session_id();
        
        // Obtener datos específicos según el rol y añadirlos al mismo objeto
        $datosRol = self::obtenerDatosPorRol($usuarioModel->idUsuario, $usuarioModel->nombreRol);
        if (!empty($datosRol)) {
            // Añadir cada dato del rol al objeto resultado principal
            foreach ($datosRol as $key => $value) {
                $resultado[$key] = $value;
            }
        }

        // ENVIAR RESPUESTA AL FRONTEND - TODO DENTRO DE resultado
        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Sesión iniciada correctamente',
            'resultado' => $resultado  // ÚNICO OBJETO CON TODOS LOS DATOS
        ]);
    }

    /**
     * Obtener datos específicos según el rol del usuario
     */
    private static function obtenerDatosPorRol($idUsuario, $nombreRol)
    {
        $datos = [];
        
        switch ($nombreRol) {
            case 'Estudiante':
                $estudiante = D_Estudiante::obtenerEstudiantePorIdUsuario($idUsuario);
                if ($estudiante) {
                    $datos = $estudiante->convertirAArray();
                    
                    // Obtener facultad a través del DAO
                    $facultad = D_Estudiante::obtenerFacultadEstudiante($estudiante->idEstudiante);
                    if ($facultad) {
                        $datos['idFacultad'] = $facultad['idFacultad'];
                        $datos['nombreFacultad'] = $facultad['nombreFacultad'];
                    }
                }
                break;
                
            case 'Profesor':
                $profesor = D_Profesor::obtenerProfesorPorIdUsuario($idUsuario);
                if ($profesor) {
                    $datos = $profesor->convertirAArray();
                    
                    // Obtener facultad a través del DAO
                    if (isset($profesor->idDepartamento)) {
                        $facultad = D_Facultad::obtenerFacultadPorDepartamento($profesor->idDepartamento);
                        if ($facultad) {
                            $datos['idFacultad'] = $facultad['idFacultad'];
                            $datos['nombreFacultad'] = $facultad['nombreFacultad'];
                        }
                    }
                }
                break;
                
            case 'Secretario':
                $administrativo = D_Administrativo::obtenerAdministrativoPorIdUsuario($idUsuario);
                if ($administrativo) {
                    $datos = $administrativo->convertirAArray();
                    
                    // Obtener nombre de la facultad si solo tenemos el ID
                    if (isset($datos['idFacultad']) && !isset($datos['nombreFacultad'])) {
                        $facultad = D_Facultad::obtenerFacultadPorId($datos['idFacultad']);
                        if ($facultad) {
                            $datos['nombreFacultad'] = $facultad['nombreFacultad'];
                        }
                    }
                }
                break;
                
            case 'administrador':
                // Admin no necesita datos adicionales específicos
                $datos = [
                    'tipo' => 'administrador',
                    'nivel_acceso' => 'total'
                ];
                break;
        }
        
        return $datos;
    }

    /**
     * Cerrar sesión
     */
    public static function cerrarSesion($parametros)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Destruir la sesión
        $_SESSION = array();
        
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        session_destroy();

        echo json_encode([
            'estado' => 200,
            'exito' => true,
            'mensaje' => 'Sesión cerrada correctamente',
            'resultado' => null
        ]);
    }

    /**
     * Validar si hay una sesión activa - TODO EN resultado
     */
    public static function validarSesionActiva($parametros)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['usuario_id']) && isset($_SESSION['usuario_correo'])) {
            // Crear objeto resultado UNIFICADO
            $resultado = [
                'idUsuario' => $_SESSION['usuario_id'],
                'nombreUsuario' => $_SESSION['usuario_nombre'],
                'correo' => $_SESSION['usuario_correo'],
                'rol' => $_SESSION['usuario_rol'],
                'idRol' => $_SESSION['usuario_idRol'] ?? null,
                'foto' => $_SESSION['usuario_foto'] ?? null,
                'permisos' => $_SESSION['usuario_permisos'] ?? []
            ];
            
            echo json_encode([
                'estado' => 200,
                'exito' => true,
                'mensaje' => 'Sesión activa',
                'resultado' => $resultado  // ÚNICO OBJETO CON TODOS LOS DATOS
            ]);
        } else {
            echo json_encode([
                'estado' => 401,
                'exito' => false,
                'mensaje' => 'No hay sesión activa',
                'resultado' => null
            ]);
        }
    }

    /**
     * Obtener pregunta de recuperación por correo
     */
    public static function obtenerPreguntaRecuperacion($parametros)
    {
        $correo = $parametros['correo'] ?? '';
        
        if (empty($correo)) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'Correo electrónico es obligatorio',
                'resultado' => null
            ]);
            return;
        }

        $correo = LimpiarDatos::limpiarParametro($correo);
        
        // Validar formato de correo
        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'Formato de correo electrónico no válido',
                'resultado' => null
            ]);
            return;
        }

        $usuarioModel = D_Sesion::obtenerUsuarioPorCorreo($correo);

        if (!$usuarioModel) {
            echo json_encode([
                'estado' => 404,
                'exito' => false,
                'mensaje' => 'No se encontró un usuario con ese correo electrónico',
                'resultado' => null
            ]);
            return;
        }

        $resultado = [
            'id' => $usuarioModel->idUsuario,
            'pregunta' => $usuarioModel->preguntaRecuperacion
        ];

        echo json_encode([
            'estado' => 200,
            'exito' => true,
            'mensaje' => 'Pregunta de recuperación obtenida',
            'resultado' => $resultado
        ]);
    }

    /**
     * Verificar respuesta de recuperación
     */
    public static function verificarRespuestaRecuperacion($parametros)
    {
        $id = $parametros['id'] ?? null;
        $respuesta = $parametros['respuesta'] ?? '';

        if (!$id) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'ID de usuario no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        if (empty($respuesta)) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'Respuesta de recuperación es obligatoria',
                'resultado' => null
            ]);
            return;
        }

        $id = intval($id);
        $respuestaLimpia = LimpiarDatos::limpiarParametro($respuesta);

        $usuarioModel = D_Sesion::obtenerUsuarioPorId($id);

        if (!$usuarioModel) {
            echo json_encode([
                'estado' => 404,
                'exito' => false,
                'mensaje' => 'Usuario no encontrado',
                'resultado' => null
            ]);
            return;
        }

        // Verificar respuesta usando el modelo
        if (!$usuarioModel->verificarRespuestaRecuperacion($respuestaLimpia)) {
            echo json_encode([
                'estado' => 401,
                'exito' => false,
                'mensaje' => 'Respuesta incorrecta',
                'resultado' => null
            ]);
            return;
        }

        // Generar token temporal para cambio de contraseña
        $token = bin2hex(random_bytes(32));
        
        // Guardar token en sesión
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['reset_token'] = $token;
        $_SESSION['reset_user_id'] = $id;
        $_SESSION['reset_expira'] = time() + 300; // 5 minutos

        $resultado = [
            'token' => $token,
            'expira' => 300
        ];

        echo json_encode([
            'estado' => 200,
            'exito' => true,
            'mensaje' => 'Respuesta correcta',
            'resultado' => $resultado
        ]);
    }

    /**
     * Cambiar contraseña después de recuperación
     */
    public static function cambiarContrasenaRecuperacion($parametros)
    {
        $id = $parametros['id'] ?? null;
        $nuevaContrasena = $parametros['nuevaContrasena'] ?? '';
        $token = $parametros['token'] ?? '';

        if (!$id) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'ID de usuario no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        if (empty($nuevaContrasena)) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'Nueva contraseña es obligatoria',
                'resultado' => null
            ]);
            return;
        }

        if (strlen($nuevaContrasena) < 6) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'La contraseña debe tener al menos 6 caracteres',
                'resultado' => null
            ]);
            return;
        }

        if (empty($token)) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'Token de recuperación no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        // Validar token
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['reset_token']) || 
            $_SESSION['reset_token'] !== $token || 
            $_SESSION['reset_user_id'] != $id ||
            $_SESSION['reset_expira'] < time()) {
            
            echo json_encode([
                'estado' => 401,
                'exito' => false,
                'mensaje' => 'Token inválido o expirado',
                'resultado' => null
            ]);
            return;
        }

        // Encriptar nueva contraseña
        $contrasenaHash = password_hash($nuevaContrasena, PASSWORD_DEFAULT);

        // Actualizar contraseña (con transacción)
        $actualizado = D_Sesion::actualizarContrasena($id, $contrasenaHash);

        if ($actualizado) {
            // Limpiar datos de recuperación
            unset($_SESSION['reset_token']);
            unset($_SESSION['reset_user_id']);
            unset($_SESSION['reset_expira']);

            echo json_encode([
                'estado' => 200,
                'exito' => true,
                'mensaje' => 'Contraseña actualizada correctamente',
                'resultado' => null
            ]);
        } else {
            echo json_encode([
                'estado' => 500,
                'exito' => false,
                'mensaje' => 'Error al actualizar la contraseña',
                'resultado' => null
            ]);
        }
    }

    /**
     * Obtener usuario por correo
     */
    public static function obtenerUsuarioPorCorreo($parametros)
    {
        $correo = $parametros['correo'] ?? '';
        
        if (empty($correo)) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'Correo electrónico es obligatorio',
                'resultado' => null
            ]);
            return;
        }

        $correo = LimpiarDatos::limpiarParametro($correo);
        $usuarioModel = D_Sesion::obtenerUsuarioPorCorreo($correo);

        if ($usuarioModel) {
            $resultado = $usuarioModel->convertirAArray();
            
            echo json_encode([
                'estado' => 200,
                'exito' => true,
                'mensaje' => 'Usuario encontrado',
                'resultado' => $resultado
            ]);
        } else {
            echo json_encode([
                'estado' => 404,
                'exito' => false,
                'mensaje' => 'Usuario no encontrado',
                'resultado' => null
            ]);
        }
    }
}
?>