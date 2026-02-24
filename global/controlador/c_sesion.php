<?php
require_once __DIR__ . "/../dao/d_sesion.php";
require_once __DIR__ . "/../utilidades/LimpiarDatos.php";
require_once __DIR__ . "/../../utilidades/u_verificaciones.php";

class SesionController
{
    public static function dispatch($accion, $parametros)
    {
        switch ($accion) {
            case "iniciarSesion":
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
                
            case "obtenerUsuarioByCorreo":
                self::getUsuarioByCorreo($parametros);
                break;
                
            default:
                echo json_encode([
                    'estado' => 400, 
                    'éxito' => false, 
                    'mensaje' => "La acción '$accion' no está disponible"
                ]);
        }
    }

    /**
     * Iniciar sesión con correo y contraseña
     */
    public static function iniciarSesion($parametros)
    {
        // Validar parámetros obligatorios
        if (!VerificacionesUtil::validarSesion('iniciarSesion', $parametros)) {
            echo json_encode([
                'estado' => 400,
                'éxito' => false,
                'mensaje' => 'Correo y contraseña son obligatorios'
            ]);
            return;
        }

        $correo = LimpiarDatos::limpiarParametro($parametros['correo']);
        $contrasena = $parametros['contrasena']; // No se limpia porque puede tener caracteres especiales

        // Buscar usuario por correo
        $usuario = D_Sesion::obtenerUsuarioByCorreo($correo);

        if (!$usuario) {
            echo json_encode([
                'estado' => 401,
                'éxito' => false,
                'mensaje' => 'Credenciales inválidas'
            ]);
            return;
        }

        // Verificar si el usuario está activo
        if ($usuario['estado'] != 1) {
            echo json_encode([
                'estado' => 403,
                'éxito' => false,
                'mensaje' => 'Usuario inactivo. Contacte al administrador.'
            ]);
            return;
        }

        // Verificar contraseña
        if (!VerificacionesUtil::verificarContrasenas($contrasena, $usuario['contrasena'])) {
            echo json_encode([
                'estado' => 401,
                'éxito' => false,
                'mensaje' => 'Credenciales inválidas'
            ]);
            return;
        }

        // Actualizar último acceso
        D_Sesion::actualizarUltimoAcceso($usuario['idUsuario']);

        // Iniciar sesión en PHP
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $_SESSION['usuario_id'] = $usuario['idUsuario'];
        $_SESSION['usuario_nombre'] = $usuario['nombreUsuario'];
        $_SESSION['usuario_correo'] = $usuario['correo'];
        $_SESSION['usuario_rol'] = $usuario['rol'];
        $_SESSION['usuario_foto'] = $usuario['foto'];
        $_SESSION['ultimo_acceso'] = time();

        // No enviar datos sensibles
        unset($usuario['contrasena']);
        unset($usuario['preguntaRecuperacion']);
        unset($usuario['RespuestaRecuperacion']);

        echo json_encode([
            'estado' => 200,
            'éxito' => true,
            'mensaje' => 'Sesión iniciada correctamente',
            'datos' => [
                'usuario' => $usuario,
                'sesion_id' => session_id()
            ]
        ]);
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
            'éxito' => true,
            'mensaje' => 'Sesión cerrada correctamente'
        ]);
    }

    /**
     * Validar si hay una sesión activa
     */
    public static function validarSesionActiva($parametros)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['usuario_id']) && isset($_SESSION['usuario_correo'])) {
            echo json_encode([
                'estado' => 200,
                'éxito' => true,
                'mensaje' => 'Sesión activa',
                'datos' => [
                    'id' => $_SESSION['usuario_id'],
                    'nombre' => $_SESSION['usuario_nombre'],
                    'correo' => $_SESSION['usuario_correo'],
                    'rol' => $_SESSION['usuario_rol'],
                    'foto' => $_SESSION['usuario_foto'] ?? null
                ]
            ]);
        } else {
            echo json_encode([
                'estado' => 401,
                'éxito' => false,
                'mensaje' => 'No hay sesión activa'
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
                'éxito' => false,
                'mensaje' => 'Correo electrónico es obligatorio'
            ]);
            return;
        }

        $correo = LimpiarDatos::limpiarParametro($correo);
        
        // Validar formato de correo
        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            echo json_encode([
                'estado' => 400,
                'éxito' => false,
                'mensaje' => 'Formato de correo electrónico no válido'
            ]);
            return;
        }

        $usuario = D_Sesion::obtenerUsuarioByCorreo($correo);

        if (!$usuario) {
            echo json_encode([
                'estado' => 404,
                'éxito' => false,
                'mensaje' => 'No se encontró un usuario con ese correo electrónico'
            ]);
            return;
        }

        echo json_encode([
            'estado' => 200,
            'éxito' => true,
            'datos' => [
                'id' => $usuario['idUsuario'],
                'pregunta' => $usuario['preguntaRecuperacion']
            ]
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
                'éxito' => false,
                'mensaje' => 'ID de usuario no proporcionado'
            ]);
            return;
        }

        if (empty($respuesta)) {
            echo json_encode([
                'estado' => 400,
                'éxito' => false,
                'mensaje' => 'Respuesta de recuperación es obligatoria'
            ]);
            return;
        }

        $id = intval($id);
        $respuestaLimpia = LimpiarDatos::limpiarParametro($respuesta);

        // Obtener usuario por ID
        require_once __DIR__ . "/../dao/d_usuarios.php";
        $usuario = UsuariosDao::obtenerUsuarioPorId($id);

        if (!$usuario) {
            echo json_encode([
                'estado' => 404,
                'éxito' => false,
                'mensaje' => 'Usuario no encontrado'
            ]);
            return;
        }

        // Verificar respuesta (case-insensitive)
        if (strtolower(trim($respuestaLimpia)) !== strtolower(trim($usuario['RespuestaRecuperacion']))) {
            echo json_encode([
                'estado' => 401,
                'éxito' => false,
                'mensaje' => 'Respuesta incorrecta'
            ]);
            return;
        }

        // Generar token temporal para cambio de contraseña
        $token = bin2hex(random_bytes(32));
        
        // Guardar token en sesión o BD (por simplicidad, lo devolvemos)
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['reset_token'] = $token;
        $_SESSION['reset_user_id'] = $id;
        $_SESSION['reset_expira'] = time() + 300; // 5 minutos

        echo json_encode([
            'estado' => 200,
            'éxito' => true,
            'mensaje' => 'Respuesta correcta',
            'datos' => [
                'token' => $token,
                'expira' => 300
            ]
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
                'éxito' => false,
                'mensaje' => 'ID de usuario no proporcionado'
            ]);
            return;
        }

        if (empty($nuevaContrasena)) {
            echo json_encode([
                'estado' => 400,
                'éxito' => false,
                'mensaje' => 'Nueva contraseña es obligatoria'
            ]);
            return;
        }

        if (strlen($nuevaContrasena) < 6) {
            echo json_encode([
                'estado' => 400,
                'éxito' => false,
                'mensaje' => 'La contraseña debe tener al menos 6 caracteres'
            ]);
            return;
        }

        if (empty($token)) {
            echo json_encode([
                'estado' => 400,
                'éxito' => false,
                'mensaje' => 'Token de recuperación no proporcionado'
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
                'éxito' => false,
                'mensaje' => 'Token inválido o expirado'
            ]);
            return;
        }

        // Encriptar nueva contraseña
        $contrasenaHash = password_hash($nuevaContrasena, PASSWORD_DEFAULT);

        // Actualizar contraseña
        require_once __DIR__ . "/../dao/d_usuarios.php";
        $actualizado = UsuariosDao::actualizarContrasena($id, $contrasenaHash);

        if ($actualizado) {
            // Limpiar datos de recuperación
            unset($_SESSION['reset_token']);
            unset($_SESSION['reset_user_id']);
            unset($_SESSION['reset_expira']);

            echo json_encode([
                'estado' => 200,
                'éxito' => true,
                'mensaje' => 'Contraseña actualizada correctamente'
            ]);
        } else {
            echo json_encode([
                'estado' => 500,
                'éxito' => false,
                'mensaje' => 'Error al actualizar la contraseña'
            ]);
        }
    }

    /**
     * Obtener usuario por correo (método legacy)
     */
    public static function getUsuarioByCorreo($parametros)
    {
        $correo = $parametros['correo'] ?? '';
        
        if (empty($correo)) {
            echo json_encode([
                'estado' => 400,
                'éxito' => false,
                'mensaje' => 'Correo electrónico es obligatorio'
            ]);
            return;
        }

        $correo = LimpiarDatos::limpiarParametro($correo);
        $usuario = D_Sesion::obtenerUsuarioByCorreo($correo);

        if ($usuario) {
            // No enviar datos sensibles
            unset($usuario['contrasena']);
            
            echo json_encode([
                'estado' => 200,
                'éxito' => true,
                'datos' => $usuario
            ]);
        } else {
            echo json_encode([
                'estado' => 404,
                'éxito' => false,
                'mensaje' => 'Usuario no encontrado'
            ]);
        }
    }
}
?>