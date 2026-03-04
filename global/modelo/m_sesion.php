<?php
class SesionModel
{
    public $idUsuario;
    public $nombreUsuario;
    public $correo;
    public $contrasena;
    public $foto;
    public $rol;
    public $estado;
    public $ultimoAcceso;
    public $preguntaRecuperacion;
    public $respuestaRecuperacion;

    public function __construct($correo = null, $contrasena = null)
    {
        $this->correo = $correo;
        $this->contrasena = $contrasena;
    }

    // Hidratar modelo desde array de base de datos
    public function hidratarDesdeArray($data)
    {
        if (isset($data['idUsuario'])) $this->idUsuario = $data['idUsuario'];
        if (isset($data['nombreUsuario'])) $this->nombreUsuario = $data['nombreUsuario'];
        if (isset($data['correo'])) $this->correo = $data['correo'];
        if (isset($data['contrasena'])) $this->contrasena = $data['contrasena'];  //cambiado
        if (isset($data['foto'])) $this->foto = $data['foto'];
        if (isset($data['rol'])) $this->rol = $data['rol'];
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
            'foto' => $this->foto,
            'rol' => $this->rol,
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
    public function validarContrasena($password)
    {
        if ($password==$this->contrasena){
            return true;
        }else return false;
        //return password_verify($password, $this->contrasena);
    }

    // Verificar si el usuario está activo
    public function estaActivo()
    {
        return $this->estado === 'activo';
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