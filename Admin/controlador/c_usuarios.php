<?php
require_once __DIR__ . "/../utilidades/u_verificaciones.php";
require_once __DIR__ . "/../dao/d_usuarios.php";
require_once __DIR__ . "/../utilidades/LimpiarDatos.php";

class UsuarioController
{

    public static function dispatch($accion, $parametros)
    {
        if (VerificacionesUtil::validarDispatch($accion, $parametros)) {
            switch ($accion) {
                // Operaciones de listado
                case "listarUsuarios":
                    self::getUsuarios();
                    break;
                case "obtenerUsuarioById":
                    self::getUsuarioById($parametros['id'] ?? null);
                    break;
                case "listarUsuariosPorRol":
                    self::getUsuariosPorRol($parametros['rol'] ?? '');
                    break;
                case "listarUsuariosActivos":
                    self::getUsuariosActivos();
                    break;
                
                // Operaciones de paginación
                case "paginacion":
                    self::getUsuariosPaginacion($parametros['pagina'] ?? 1);
                    break;
                case "obtenerCantidadPaginacion":
                    self::getCantidadPaginacion();
                    break;
                
                // Operaciones CRUD
                case "crearUsuario":
                    self::crearUsuario($parametros);
                    break;
                case "actualizarUsuario":
                    self::actualizarUsuario($parametros);
                    break;
                case "eliminarUsuario":
                    self::eliminarUsuario($parametros['id'] ?? null);
                    break;
                case "cambiarEstadoUsuario":
                    self::cambiarEstadoUsuario($parametros['id'] ?? null, $parametros['estado'] ?? null);
                    break;
                
                // Operaciones específicas
                case "login":
                    self::login($parametros);
                    break;
                case "actualizarUltimoAcceso":
                    self::actualizarUltimoAcceso($parametros['id'] ?? null);
                    break;
                case "cambiarContrasena":
                    self::cambiarContrasena($parametros);
                    break;
                case "recuperarContrasena":
                    self::recuperarContrasena($parametros);
                    break;
                case "verificarRespuestaRecuperacion":
                    self::verificarRespuestaRecuperacion($parametros);
                    break;
                    
                default:
                    echo json_encode([
                        'estado' => 400,
                        'éxito' => false,
                        'mensaje' => "Acción '$accion' no válida en el controlador de usuarios"
                    ]);
            }
        }
    }
    

    /**
     * VERIFICAR SI HAY UNA SESIÓN ACTIVA
     */
    private static function verificarSesionActiva()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['usuario_correo'])) {
            return false;
        }
        
        return true;
    }
    /**
     * Listar todos los usuarios
     */
    private static function getUsuarios()
    {
        if (!self::verificarSesionActiva()) {
            echo json_encode([
                'estado' => 401,
                'éxito' => false,
                'mensaje' => 'No hay sesión activa'
            ]);
            return;
        }

        $resultado = UsuariosDao::listarUsuarios();
        echo json_encode([
            'estado' => 200,
            'éxito' => true,
            'datos' => $resultado
        ]);
    }

    /**
     * Obtener usuario por ID
     */
    private static function getUsuarioById($id)
    {
        if (!self::verificarSesionActiva()) {
            echo json_encode([
                'estado' => 401,
                'éxito' => false,
                'mensaje' => 'No hay sesión activa'
            ]);
            return;
        }

        if (!$id) {
            echo json_encode([
                'estado' => 400,
                'éxito' => false,
                'mensaje' => 'ID de usuario no proporcionado'
            ]);
            return;
        }

        $resultado = UsuariosDao::obtenerUsuarioPorId($id);
        
        if ($resultado) {
            // No enviar la contraseña en la respuesta
            unset($resultado['contrasena']);
            unset($resultado['respuestaRecuperacion']);
            
            echo json_encode([
                'estado' => 200,
                'éxito' => true,
                'datos' => $resultado
            ]);
        } else {
            echo json_encode([
                'estado' => 404,
                'éxito' => false,
                'mensaje' => 'Usuario no encontrado'
            ]);
        }
    }

    /**
     * Listar usuarios por rol
     */
    private static function getUsuariosPorRol($rol)
    {
        if (!self::verificarSesionActiva()) {
            echo json_encode([
                'estado' => 401,
                'éxito' => false,
                'mensaje' => 'No hay sesión activa'
            ]);
            return;
        }

        if (empty($rol)) {
            echo json_encode([
                'estado' => 400,
                'éxito' => false,
                'mensaje' => 'Rol no proporcionado'
            ]);
            return;
        }

        $resultado = UsuariosDao::listarUsuariosPorRol($rol);
        echo json_encode([
            'estado' => 200,
            'éxito' => true,
            'datos' => $resultado
        ]);
    }

    /**
     * Listar usuarios activos
     */
    private static function getUsuariosActivos()
    {
        if (!self::verificarSesionActiva()) {
            echo json_encode([
                'estado' => 401,
                'éxito' => false,
                'mensaje' => 'No hay sesión activa'
            ]);
            return;
        }

        $resultado = UsuariosDao::listarUsuariosActivos();
        echo json_encode([
            'estado' => 200,
            'éxito' => true,
            'datos' => $resultado
        ]);
    }

    /**
     * Obtener usuarios paginados
     */
    private static function getUsuariosPaginacion($pagina)
    {
        if (!self::verificarSesionActiva()) {
            echo json_encode([
                'estado' => 401,
                'éxito' => false,
                'mensaje' => 'No hay sesión activa'
            ]);
            return;
        }

        $pagina = intval($pagina);
        if ($pagina < 1) $pagina = 1;
        
        $resultado = UsuariosDao::obtenerUsuariosPaginados($pagina);
        echo json_encode([
            'estado' => 200,
            'éxito' => true,
            'datos' => $resultado,
            'pagina_actual' => $pagina
        ]);
    }

    /**
     * Obtener cantidad de páginas para paginación
     */
    private static function getCantidadPaginacion()
    {
        if (!self::verificarSesionActiva()) {
            echo json_encode([
                'estado' => 401,
                'éxito' => false,
                'mensaje' => 'No hay sesión activa'
            ]);
            return;
        }

        $resultado = UsuariosDao::contarUsuarios();
        echo json_encode([
            'estado' => 200,
            'éxito' => true,
            'total_paginas' => $resultado
        ]);
    }

    /**
     * Crear nuevo usuario (con foto opcional)
     */
    private static function crearUsuario($parametros)
    {
        if (!self::verificarSesionActiva()) {
            echo json_encode([
                'estado' => 401,
                'éxito' => false,
                'mensaje' => 'No hay sesión activa'
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

        // Validaciones básicas
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
        
        if (empty($preguntaRecuperacion)) {
            $errores[] = 'Pregunta de recuperación es obligatoria';
        }
        
        if (empty($respuestaRecuperacion)) {
            $errores[] = 'Respuesta de recuperación es obligatoria';
        }

        if (!empty($errores)) {
            echo json_encode([
                'estado' => 400,
                'éxito' => false,
                'mensaje' => 'Errores de validación',
                'errores' => $errores
            ]);
            return;
        }

        // Verificar si el nombre de usuario ya existe
        if (UsuariosDao::existeNombreUsuario($nombreUsuario)) {
            echo json_encode([
                'estado' => 400,
                'éxito' => false,
                'mensaje' => 'El nombre de usuario ya está en uso'
            ]);
            return;
        }

        // Verificar si el correo ya existe
        if (UsuariosDao::existeCorreo($correo)) {
            echo json_encode([
                'estado' => 400,
                'éxito' => false,
                'mensaje' => 'El correo electrónico ya está registrado'
            ]);
            return;
        }

        // Obtener foto si existe
        $foto = $parametros['foto'] ?? null;

        // Validar foto si existe
        if ($foto && !LimpiarDatos::validarArchivo($foto, 'foto')) {
            echo json_encode([
                'estado' => 400,
                'éxito' => false,
                'mensaje' => 'La foto no es válida (solo imágenes JPG, PNG, GIF, WEBP - máx 10MB)'
            ]);
            return;
        }

        // Encriptar contraseña
        $contrasenaHash = password_hash($contrasena, PASSWORD_DEFAULT);

        // Insertar usuario en BD (con estado 'activo')
        $usuarioId = UsuariosDao::crearUsuario([
            'nombreUsuario' => $nombreUsuario,
            'contrasena' => $contrasenaHash,
            'correo' => $correo,
            'rol' => $rol,
            'estado' => 'activo',
            'preguntaRecuperacion' => $preguntaRecuperacion,
            'respuestaRecuperacion' => $respuestaRecuperacion
        ]);

        if (!$usuarioId) {
            echo json_encode([
                'estado' => 500,
                'éxito' => false,
                'mensaje' => 'Error al crear el usuario en la base de datos'
            ]);
            return;
        }

        // Procesar y guardar foto si existe
        if ($foto) {
            $fotosGuardadas = LimpiarDatos::guardarMultiplesArchivos($foto, 'foto', $usuarioId);
            
            if (!empty($fotosGuardadas)) {
                $fotoGuardada = $fotosGuardadas[0];
                UsuariosDao::actualizarFotoUsuario($usuarioId, $fotoGuardada);
            }
        }

        // Obtener usuario creado (sin contraseña)
        $usuarioCreado = UsuariosDao::obtenerUsuarioPorId($usuarioId);
        unset($usuarioCreado['contrasena']);
        unset($usuarioCreado['respuestaRecuperacion']);

        echo json_encode([
            'estado' => 201,
            'éxito' => true,
            'mensaje' => 'Usuario creado exitosamente',
            'datos' => $usuarioCreado
        ]);
    }

    /**
     * Actualizar usuario existente
     */
    private static function actualizarUsuario($parametros)
    {
        if (!self::verificarSesionActiva()) {
            echo json_encode([
                'estado' => 401,
                'éxito' => false,
                'mensaje' => 'No hay sesión activa'
            ]);
            return;
        }

        $id = $parametros['id'] ?? null;
        
        if (!$id) {
            echo json_encode([
                'estado' => 400,
                'éxito' => false,
                'mensaje' => 'ID de usuario no proporcionado'
            ]);
            return;
        }

        // Verificar que el usuario existe
        $usuarioExistente = UsuariosDao::obtenerUsuarioPorId($id);
        if (!$usuarioExistente) {
            echo json_encode([
                'estado' => 404,
                'éxito' => false,
                'mensaje' => 'Usuario no encontrado'
            ]);
            return;
        }

        // Datos a actualizar
        $nombreUsuario = $parametros['nombreUsuario'] ?? $usuarioExistente['nombreUsuario'];
        $correo = $parametros['correo'] ?? $usuarioExistente['correo'];
        $rol = $parametros['rol'] ?? $usuarioExistente['rol'];
        $preguntaRecuperacion = $parametros['preguntaRecuperacion'] ?? $usuarioExistente['preguntaRecuperacion'];
        $respuestaRecuperacion = $parametros['respuestaRecuperacion'] ?? $usuarioExistente['respuestaRecuperacion'];

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
        if ($nombreUsuario !== $usuarioExistente['nombreUsuario'] && UsuariosDao::existeNombreUsuario($nombreUsuario)) {
            $errores[] = 'El nombre de usuario ya está en uso';
        }

        // Verificar si el correo ya existe (y no es el mismo usuario)
        if ($correo !== $usuarioExistente['correo'] && UsuariosDao::existeCorreo($correo)) {
            $errores[] = 'El correo electrónico ya está registrado';
        }

        if (!empty($errores)) {
            echo json_encode([
                'estado' => 400,
                'éxito' => false,
                'mensaje' => 'Errores de validación',
                'errores' => $errores
            ]);
            return;
        }

        // Obtener nueva foto si existe
        $foto = $parametros['foto'] ?? null;

        // Validar foto si existe
        if ($foto && !LimpiarDatos::validarArchivo($foto, 'foto')) {
            echo json_encode([
                'estado' => 400,
                'éxito' => false,
                'mensaje' => 'La foto no es válida (solo imágenes JPG, PNG, GIF, WEBP - máx 10MB)'
            ]);
            return;
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

        // Procesar nueva foto si existe
        if ($foto) {
            $fotosGuardadas = LimpiarDatos::guardarMultiplesArchivos($foto, 'foto', $id);
            
            if (!empty($fotosGuardadas)) {
                $fotoGuardada = $fotosGuardadas[0];
                $datosActualizar['foto'] = $fotoGuardada;
            }
        }

        // Actualizar usuario
        $actualizado = UsuariosDao::actualizarUsuario($datosActualizar);

        if (!$actualizado) {
            echo json_encode([
                'estado' => 500,
                'éxito' => false,
                'mensaje' => 'Error al actualizar el usuario'
            ]);
            return;
        }

        // Obtener usuario actualizado (sin contraseña)
        $usuarioActualizado = UsuariosDao::obtenerUsuarioPorId($id);
        unset($usuarioActualizado['contrasena']);
        unset($usuarioActualizado['respuestaRecuperacion']);

        echo json_encode([
            'estado' => 200,
            'éxito' => true,
            'mensaje' => 'Usuario actualizado exitosamente',
            'datos' => $usuarioActualizado
        ]);
    }

    /**
     * Eliminar usuario (soft delete)
     */
    private static function eliminarUsuario($id)
    {
        if (!self::verificarSesionActiva()) {
            echo json_encode([
                'estado' => 401,
                'éxito' => false,
                'mensaje' => 'No hay sesión activa'
            ]);
            return;
        }

        if (!$id) {
            echo json_encode([
                'estado' => 400,
                'éxito' => false,
                'mensaje' => 'ID de usuario no proporcionado'
            ]);
            return;
        }

        // Verificar que el usuario existe
        $usuarioExistente = UsuariosDao::obtenerUsuarioPorId($id);
        if (!$usuarioExistente) {
            echo json_encode([
                'estado' => 404,
                'éxito' => false,
                'mensaje' => 'Usuario no encontrado'
            ]);
            return;
        }

        // Eliminar usuario (soft delete - cambiar estado a 'inactivo')
        $eliminado = UsuariosDao::eliminarUsuario($id);

        if ($eliminado) {
            echo json_encode([
                'estado' => 200,
                'éxito' => true,
                'mensaje' => 'Usuario eliminado exitosamente'
            ]);
        } else {
            echo json_encode([
                'estado' => 500,
                'éxito' => false,
                'mensaje' => 'Error al eliminar el usuario'
            ]);
        }
    }

    /**
     * Cambiar estado del usuario (activar/desactivar)
     */
    private static function cambiarEstadoUsuario($id, $estado)
    {
        if (!self::verificarSesionActiva()) {
            echo json_encode([
                'estado' => 401,
                'éxito' => false,
                'mensaje' => 'No hay sesión activa'
            ]);
            return;
        }

        if (!$id) {
            echo json_encode([
                'estado' => 400,
                'éxito' => false,
                'mensaje' => 'ID de usuario no proporcionado'
            ]);
            return;
        }

        if (!in_array($estado, ['activo', 'inactivo'])) {
            echo json_encode([
                'estado' => 400,
                'éxito' => false,
                'mensaje' => 'Estado no válido (debe ser "activo" o "inactivo")'
            ]);
            return;
        }

        $cambiado = UsuariosDao::cambiarEstadoUsuario($id, $estado);

        if ($cambiado) {
            $mensaje = $estado == 'activo' ? 'Usuario activado exitosamente' : 'Usuario desactivado exitosamente';
            echo json_encode([
                'estado' => 200,
                'éxito' => true,
                'mensaje' => $mensaje
            ]);
        } else {
            echo json_encode([
                'estado' => 500,
                'éxito' => false,
                'mensaje' => 'Error al cambiar el estado del usuario'
            ]);
        }
    }

    /**
     * Login de usuario
     */
    private static function login($parametros)
    {
        $nombreUsuario = $parametros['nombreUsuario'] ?? '';
        $contrasena = $parametros['contrasena'] ?? '';

        if (empty($nombreUsuario) || empty($contrasena)) {
            echo json_encode([
                'estado' => 400,
                'éxito' => false,
                'mensaje' => 'Nombre de usuario y contraseña son obligatorios'
            ]);
            return;
        }

        // Buscar usuario por nombre de usuario
        $usuario = UsuariosDao::obtenerUsuarioPorNombreUsuario($nombreUsuario);

        if (!$usuario) {
            echo json_encode([
                'estado' => 401,
                'éxito' => false,
                'mensaje' => 'Credenciales inválidas'
            ]);
            return;
        }

        // Verificar si el usuario está activo
        if ($usuario['estado'] != 'activo') {
            echo json_encode([
                'estado' => 403,
                'éxito' => false,
                'mensaje' => 'Usuario inactivo'
            ]);
            return;
        }

        // Verificar contraseña
        if (!password_verify($contrasena, $usuario['contrasena'])) {
            echo json_encode([
                'estado' => 401,
                'éxito' => false,
                'mensaje' => 'Credenciales inválidas'
            ]);
            return;
        }

        // Actualizar último acceso
        UsuariosDao::actualizarUltimoAcceso($usuario['idUsuario']);

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
        unset($usuario['respuestaRecuperacion']);

        echo json_encode([
            'estado' => 200,
            'éxito' => true,
            'mensaje' => 'Login exitoso',
            'datos' => $usuario
        ]);
    }

    /**
     * Actualizar último acceso
     */
    private static function actualizarUltimoAcceso($id)
    {
        if (!self::verificarSesionActiva()) {
            echo json_encode([
                'estado' => 401,
                'éxito' => false,
                'mensaje' => 'No hay sesión activa'
            ]);
            return;
        }

        if (!$id) {
            echo json_encode([
                'estado' => 400,
                'éxito' => false,
                'mensaje' => 'ID de usuario no proporcionado'
            ]);
            return;
        }

        $actualizado = UsuariosDao::actualizarUltimoAcceso($id);

        if ($actualizado) {
            echo json_encode([
                'estado' => 200,
                'éxito' => true,
                'mensaje' => 'Último acceso actualizado'
            ]);
        } else {
            echo json_encode([
                'estado' => 500,
                'éxito' => false,
                'mensaje' => 'Error al actualizar último acceso'
            ]);
        }
    }

    /**
     * Cambiar contraseña
     */
    private static function cambiarContrasena($parametros)
    {
        if (!self::verificarSesionActiva()) {
            echo json_encode([
                'estado' => 401,
                'éxito' => false,
                'mensaje' => 'No hay sesión activa'
            ]);
            return;
        }

        $id = $parametros['id'] ?? null;
        $contrasenaActual = $parametros['contrasenaActual'] ?? '';
        $nuevaContrasena = $parametros['nuevaContrasena'] ?? '';

        if (!$id) {
            echo json_encode([
                'estado' => 400,
                'éxito' => false,
                'mensaje' => 'ID de usuario no proporcionado'
            ]);
            return;
        }

        if (empty($contrasenaActual) || empty($nuevaContrasena)) {
            echo json_encode([
                'estado' => 400,
                'éxito' => false,
                'mensaje' => 'Contraseña actual y nueva son obligatorias'
            ]);
            return;
        }

        if (strlen($nuevaContrasena) < 6) {
            echo json_encode([
                'estado' => 400,
                'éxito' => false,
                'mensaje' => 'La nueva contraseña debe tener al menos 6 caracteres'
            ]);
            return;
        }

        // Obtener usuario
        $usuario = UsuariosDao::obtenerUsuarioPorId($id);
        if (!$usuario) {
            echo json_encode([
                'estado' => 404,
                'éxito' => false,
                'mensaje' => 'Usuario no encontrado'
            ]);
            return;
        }

        // Verificar contraseña actual
        if (!password_verify($contrasenaActual, $usuario['contrasena'])) {
            echo json_encode([
                'estado' => 401,
                'éxito' => false,
                'mensaje' => 'Contraseña actual incorrecta'
            ]);
            return;
        }

        // Encriptar nueva contraseña
        $nuevaContrasenaHash = password_hash($nuevaContrasena, PASSWORD_DEFAULT);

        // Actualizar contraseña
        $actualizado = UsuariosDao::actualizarContrasena($id, $nuevaContrasenaHash);

        if ($actualizado) {
            echo json_encode([
                'estado' => 200,
                'éxito' => true,
                'mensaje' => 'Contraseña actualizada exitosamente'
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
     * Recuperar contraseña (obtener pregunta de recuperación)
     */
    private static function recuperarContrasena($parametros)
    {
        $nombreUsuario = $parametros['nombreUsuario'] ?? '';

        if (empty($nombreUsuario)) {
            echo json_encode([
                'estado' => 400,
                'éxito' => false,
                'mensaje' => 'Nombre de usuario es obligatorio'
            ]);
            return;
        }

        $usuario = UsuariosDao::obtenerUsuarioPorNombreUsuario($nombreUsuario);

        if (!$usuario) {
            echo json_encode([
                'estado' => 404,
                'éxito' => false,
                'mensaje' => 'Usuario no encontrado'
            ]);
            return;
        }

        echo json_encode([
            'estado' => 200,
            'éxito' => true,
            'datos' => [
                'id' => $usuario['idUsuario'],
                'preguntaRecuperacion' => $usuario['preguntaRecuperacion']
            ]
        ]);
    }

    /**
     * Verificar respuesta de recuperación
     */
    private static function verificarRespuestaRecuperacion($parametros)
    {
        $id = $parametros['id'] ?? null;
        $respuesta = $parametros['respuesta'] ?? '';
        $nuevaContrasena = $parametros['nuevaContrasena'] ?? '';

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
                'mensaje' => 'La nueva contraseña debe tener al menos 6 caracteres'
            ]);
            return;
        }

        // Obtener usuario
        $usuario = UsuariosDao::obtenerUsuarioPorId($id);
        if (!$usuario) {
            echo json_encode([
                'estado' => 404,
                'éxito' => false,
                'mensaje' => 'Usuario no encontrado'
            ]);
            return;
        }

        // Verificar respuesta
        if ($respuesta !== $usuario['respuestaRecuperacion']) {
            echo json_encode([
                'estado' => 401,
                'éxito' => false,
                'mensaje' => 'Respuesta incorrecta'
            ]);
            return;
        }

        // Encriptar nueva contraseña
        $nuevaContrasenaHash = password_hash($nuevaContrasena, PASSWORD_DEFAULT);

        // Actualizar contraseña
        $actualizado = UsuariosDao::actualizarContrasena($id, $nuevaContrasenaHash);

        if ($actualizado) {
            echo json_encode([
                'estado' => 200,
                'éxito' => true,
                'mensaje' => 'Contraseña actualizada exitosamente'
            ]);
        } else {
            echo json_encode([
                'estado' => 500,
                'éxito' => false,
                'mensaje' => 'Error al actualizar la contraseña'
            ]);
        }
    }
}
?>