<?php
require_once __DIR__ . "/../utilidades/u_verificaciones.php";
require_once __DIR__ . "/../dao/d_usuario.php";
require_once __DIR__ . "/../utilidades/LimpiarDatos.php";
require_once __DIR__ . "/../modelo/m_usuario.php";

class UsuarioController
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
            case "insertarUsuario":
                self::insertarUsuario($parametros);
                break;
                
            case "actualizarUsuario":
                self::actualizarUsuario($parametros);
                break;
                
            case "deshabilitarUsuario":
                self::cambiarEstadoUsuario($parametros['valor'] ?? null, 'inactivo');
                break;
                
            case "habilitarUsuario":
                self::cambiarEstadoUsuario($parametros['valor'] ?? null, 'activo');
                break;
                
            case "verificarContraseñaExistente":
                self::verificarContraseñaExistente($parametros);
                break;
                
            case "obtenerUsuarios":
                self::obtenerUsuarios();
                break;
                
            default:
                echo json_encode([
                    'estado' => 400,
                    'exito' => false,
                    'mensaje' => "Acción '$accion' no válida en el controlador de usuarios",
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

    // Obtener todos los usuarios
    private static function obtenerUsuarios()
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

        $usuarios = D_Usuario::obtenerUsuarios();
        $resultado = [];
        
        foreach ($usuarios as $usuario) {
            $resultado[] = $usuario->convertirAArray();
        }
        
        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Usuarios obtenidos correctamente',
            'resultado' => $resultado
        ]);
    }

    // Insertar nuevo usuario
    private static function insertarUsuario($parametros)
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
        $nombreUsuario = $parametros['nombreUsuario'] ?? '';
        $contrasena = $parametros['contrasena'] ?? '';
        $correo = $parametros['correo'] ?? '';
        $rol = $parametros['rol'] ?? 'usuario';
        $preguntaRecuperacion = $parametros['preguntaRecuperacion'] ?? '';
        $respuestaRecuperacion = $parametros['respuestaRecuperacion'] ?? '';

        $errores = [];
        
        if (empty($nombreUsuario)) {
            $errores[] = 'Nombre de usuario es obligatorio';
        }
        
        if (empty($contrasena)) {
            $errores[] = 'Contraseña es obligatoria';
        } elseif (strlen($contrasena) < 6) {
            $errores[] = 'La contraseña debe tener al menos 6 caracteres';
        }
        
        if (empty($correo)) {
            $errores[] = 'Correo es obligatorio';
        } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'Correo electrónico no válido';
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

        // Verificar si el nombre de usuario ya existe
        if (D_Usuario::existeNombreUsuario($nombreUsuario)) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'El nombre de usuario ya está en uso',
                'resultado' => null
            ]);
            return;
        }

        // Verificar si el correo ya existe
        if (D_Usuario::existeCorreo($correo)) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'El correo electrónico ya está registrado',
                'resultado' => null
            ]);
            return;
        }

        // Procesar foto si existe
        $foto = null;
        if (isset($parametros['foto']) && !empty($parametros['foto'])) {
            if (!LimpiarDatos::validarArchivo($parametros['foto'], 'foto')) {
                echo json_encode([
                    'estado' => 400,
                    'exito' => false,
                    'mensaje' => 'La foto no es válida',
                    'resultado' => null
                ]);
                return;
            }
            
            // Aquí iría la lógica para guardar la foto y obtener la URL
            $foto = $parametros['foto']; // Simplificado
        }

        // Encriptar contraseña
        $contrasenaHash = password_hash($contrasena, PASSWORD_DEFAULT);

        // Insertar usuario
        $usuarioId = D_Usuario::insertarUsuario([
            'nombreUsuario' => $nombreUsuario,
            'contrasena' => $contrasenaHash,
            'correo' => $correo,
            'rol' => $rol,
            'estado' => 'activo',
            'preguntaRecuperacion' => $preguntaRecuperacion,
            'respuestaRecuperacion' => $respuestaRecuperacion,
            'foto' => $foto
        ]);

        if (!$usuarioId) {
            echo json_encode([
                'estado' => 500,
                'exito' => false,
                'mensaje' => 'Error al crear el usuario',
                'resultado' => null
            ]);
            return;
        }

        // Obtener usuario creado
        $usuarioCreado = D_Usuario::obtenerUsuarioPorId($usuarioId);

        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Usuario creado exitosamente',
            'resultado' => $usuarioCreado ? $usuarioCreado->convertirAArray() : ['id' => $usuarioId]
        ]);
    }

    // Actualizar usuario existente
    private static function actualizarUsuario($parametros)
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
                'mensaje' => 'ID de usuario no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        // Verificar que el usuario existe
        $usuarioExistente = D_Usuario::obtenerUsuarioPorId($id);
        if (!$usuarioExistente) {
            echo json_encode([
                'estado' => 404,
                'exito' => false,
                'mensaje' => 'Usuario no encontrado',
                'resultado' => null
            ]);
            return;
        }

        // Datos a actualizar
        $nombreUsuario = $parametros['nombreUsuario'] ?? $usuarioExistente->nombreUsuario;
        $correo = $parametros['correo'] ?? $usuarioExistente->correo;
        $rol = $parametros['rol'] ?? $usuarioExistente->rol;
        $preguntaRecuperacion = $parametros['preguntaRecuperacion'] ?? $usuarioExistente->preguntaRecuperacion;
        $respuestaRecuperacion = $parametros['respuestaRecuperacion'] ?? $usuarioExistente->respuestaRecuperacion;

        // Validaciones
        $errores = [];
        
        if (empty($nombreUsuario)) {
            $errores[] = 'Nombre de usuario es obligatorio';
        }
        
        if (empty($correo)) {
            $errores[] = 'Correo es obligatorio';
        } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'Correo electrónico no válido';
        }

        // Verificar si el nombre de usuario ya existe (y no es el mismo usuario)
        if ($nombreUsuario !== $usuarioExistente->nombreUsuario && 
            D_Usuario::existeNombreUsuario($nombreUsuario, $id)) {
            $errores[] = 'El nombre de usuario ya está en uso';
        }

        // Verificar si el correo ya existe (y no es el mismo usuario)
        if ($correo !== $usuarioExistente->correo && 
            D_Usuario::existeCorreo($correo, $id)) {
            $errores[] = 'El correo electrónico ya está registrado';
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

        // Procesar nueva foto si existe
        $foto = null;
        if (isset($parametros['foto']) && !empty($parametros['foto'])) {
            if (!LimpiarDatos::validarArchivo($parametros['foto'], 'foto')) {
                echo json_encode([
                    'estado' => 400,
                    'exito' => false,
                    'mensaje' => 'La foto no es válida',
                    'resultado' => null
                ]);
                return;
            }
            $foto = $parametros['foto']; // Simplificado
        }

        // Preparar datos para actualizar
        $datosActualizar = [
            'id' => $id,
            'nombreUsuario' => $nombreUsuario,
            'correo' => $correo,
            'rol' => $rol,
            'preguntaRecuperacion' => $preguntaRecuperacion,
            'respuestaRecuperacion' => $respuestaRecuperacion
        ];

        if ($foto !== null) {
            $datosActualizar['foto'] = $foto;
        }

        // Actualizar usuario
        $actualizado = D_Usuario::actualizarUsuario($datosActualizar);

        if (!$actualizado) {
            echo json_encode([
                'estado' => 500,
                'exito' => false,
                'mensaje' => 'Error al actualizar el usuario',
                'resultado' => null
            ]);
            return;
        }

        // Obtener usuario actualizado
        $usuarioActualizado = D_Usuario::obtenerUsuarioPorId($id);

        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Usuario actualizado exitosamente',
            'resultado' => $usuarioActualizado->convertirAArray()
        ]);
    }

    // Cambiar estado del usuario (habilitar/deshabilitar)
    private static function cambiarEstadoUsuario($id, $estado)
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
                'mensaje' => 'ID de usuario no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        // Verificar que el usuario existe
        $usuarioExistente = D_Usuario::obtenerUsuarioPorId($id);
        if (!$usuarioExistente) {
            echo json_encode([
                'estado' => 404,
                'exito' => false,
                'mensaje' => 'Usuario no encontrado',
                'resultado' => null
            ]);
            return;
        }

        // Cambiar estado
        $cambiado = D_Usuario::cambiarEstadoUsuario($id, $estado);

        if ($cambiado) {
            $mensaje = $estado == 'activo' ? 'Usuario habilitado exitosamente' : 'Usuario deshabilitado exitosamente';
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
                'mensaje' => 'Error al cambiar el estado del usuario',
                'resultado' => null
            ]);
        }
    }

    // Verificar contraseña existente
    private static function verificarContraseñaExistente($parametros)
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
        $contrasena = $parametros['contrasena'] ?? '';

        if (!$id) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'ID de usuario no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        if (empty($contrasena)) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'Contraseña no proporcionada',
                'resultado' => null
            ]);
            return;
        }

        $esValida = D_Usuario::verificarContrasenaExistente($id, $contrasena);

        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => $esValida ? 'Contraseña válida' : 'Contraseña incorrecta',
            'resultado' => ['valida' => $esValida]
        ]);
    }
}
?>