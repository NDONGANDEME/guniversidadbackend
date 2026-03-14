<?php
class SesionModel
{
    public $idUsuario;
    public $idRol;  // Clave foránea a la tabla rol
    public $nombreUsuario;
    public $correo;
    public $contrasena;
    public $foto;
    public $estado;
    public $ultimoAcceso;
    public $preguntaRecuperacion;
    public $respuestaRecuperacion;
    
    // Propiedades del rol (se obtienen mediante JOIN)
    public $nombreRol;
    
    // Propiedad para permisos (ahora por usuario)
    public $permisos = [];

    public function __construct($correo = null, $contrasena = null)
    {
        $this->correo = $correo;
        $this->contrasena = $contrasena;
    }

    // Hidratar modelo desde array de base de datos
    public function hidratarDesdeArray($data)
    {
        if (isset($data['idUsuario'])) $this->idUsuario = $data['idUsuario'];
        if (isset($data['idRol'])) $this->idRol = $data['idRol'];
        if (isset($data['nombreUsuario'])) $this->nombreUsuario = $data['nombreUsuario'];
        if (isset($data['correo'])) $this->correo = $data['correo'];
        if (isset($data['contrasena'])) $this->contrasena = $data['contrasena'];
        if (isset($data['foto'])) $this->foto = $data['foto'];
        if (isset($data['estado'])) $this->estado = $data['estado'];
        if (isset($data['ultimoAcceso'])) $this->ultimoAcceso = $data['ultimoAcceso'];
        if (isset($data['preguntaRecuperacion'])) $this->preguntaRecuperacion = $data['preguntaRecuperacion'];
        if (isset($data['respuestaRecuperacion'])) $this->respuestaRecuperacion = $data['respuestaRecuperacion'];
        
        // Datos del rol
        if (isset($data['nombreRol'])) $this->nombreRol = $data['nombreRol'];
        
        return $this;
    }

    // Convertir modelo a array (excluyendo datos sensibles)
    public function convertirAArray($incluirSensibles = false)
    {
        $data = [
            'idUsuario' => $this->idUsuario,
            'idRol' => $this->idRol,
            'nombreUsuario' => $this->nombreUsuario,
            'correo' => $this->correo,
            'foto' => $this->foto,
            'estado' => $this->estado,
            'ultimoAcceso' => $this->ultimoAcceso,
            'rol' => $this->nombreRol,
            'permisos' => $this->permisos
        ];

        if ($incluirSensibles) {
            $data['preguntaRecuperacion'] = $this->preguntaRecuperacion;
            $data['respuestaRecuperacion'] = $this->respuestaRecuperacion;
        }

        return $data;
    }

    // Establecer permisos
    public function establecerPermisos($permisos)
    {
        $this->permisos = $permisos;
        return $this;
    }

    // Validar contraseña
    public function validarContrasena($password)
    {
        return password_verify($password, $this->contrasena);
    }

    // Verificar si el usuario está activo
    public function estaActivo()
    {
        return $this->estado === 'activo';
    }

    // Verificar si tiene un permiso específico
    public function tienePermiso($nombrePermiso)
    {
        foreach ($this->permisos as $permiso) {
            if ($permiso['nombrePermiso'] === $nombrePermiso) {
                return true;
            }
        }
        return false;
    }

    // Verificar si tiene permisos para una tabla/acción específica
    public function puede($tabla, $accion)
    {
        foreach ($this->permisos as $permiso) {
            if ($permiso['tabla'] === $tabla && $permiso['accion'] === $accion) {
                return true;
            }
        }
        return false;
    }

    // Verificar si tiene pregunta de recuperación
    public function tienePreguntaRecuperacion()
    {
        return !empty($this->preguntaRecuperacion) && !empty($this->respuestaRecuperacion);
    }

    // Verificar respuesta de recuperación
    public function verificarRespuestaRecuperacion($respuesta)
    {
        return strtolower(trim($respuesta)) === strtolower(trim($this->respuestaRecuperacion));
    }
}
?>