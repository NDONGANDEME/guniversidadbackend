<?php
class UsuarioModel
{
    public $idUsuario;
    public $nombreUsuario;
    public $contrasena;
    public $correo;
    public $rol;
    public $foto;
    public $estado;
    public $ultimoAcceso;
    public $preguntaRecuperacion;
    public $respuestaRecuperacion;

    public function __construct($nombreUsuario = null, $correo = null, $contrasena = null)
    {
        $this->nombreUsuario = $nombreUsuario;
        $this->correo = $correo;
        $this->contrasena = $contrasena;
    }

    // Hidratar modelo desde array de base de datos
    public function hidratarDesdeArray($data)
    {
        if (isset($data['idUsuario'])) $this->idUsuario = $data['idUsuario'];
        if (isset($data['nombreUsuario'])) $this->nombreUsuario = $data['nombreUsuario'];
        if (isset($data['contrasena'])) $this->contrasena = $data['contrasena'];
        if (isset($data['correo'])) $this->correo = $data['correo'];
        if (isset($data['rol'])) $this->rol = $data['rol'];
        if (isset($data['foto'])) $this->foto = $data['foto'];
        if (isset($data['estado'])) $this->estado = $data['estado'];
        if (isset($data['ultimoAcceso'])) $this->ultimoAcceso = $data['ultimoAcceso'];
        if (isset($data['preguntaRecuperacion'])) $this->preguntaRecuperacion = $data['preguntaRecuperacion'];
        if (isset($data['respuestaRecuperacion'])) $this->respuestaRecuperacion = $data['respuestaRecuperacion'];
        
        return $this;
    }

    // Convertir modelo a array (excluyendo datos sensibles)
    public function convertirAArray($incluirSensibles = false)
    {
        $data = [
            'idUsuario' => $this->idUsuario,
            'nombreUsuario' => $this->nombreUsuario,
            'correo' => $this->correo,
            'rol' => $this->rol,
            'foto' => $this->foto,
            'estado' => $this->estado,
            'ultimoAcceso' => $this->ultimoAcceso
        ];

        if ($incluirSensibles) {
            $data['preguntaRecuperacion'] = $this->preguntaRecuperacion;
            $data['respuestaRecuperacion'] = $this->respuestaRecuperacion;
        }

        return $data;
    }

    // Validar contraseña
    public function validarContrasena($contrasena)
    {
        return password_verify($contrasena, $this->contrasena);
    }

    // Verificar si el usuario está activo
    public function estaActivo()
    {
        return $this->estado === 'activo';
    }

    // Cambiar estado
    public function cambiarEstado($nuevoEstado)
    {
        $this->estado = $nuevoEstado;
        return $this;
    }
}
?>