<?php
class EstudianteModel
{
    public $idEstudiante;
    public $idUsuario;
    public $codigoEstudiante;
    public $nombre;
    public $apellidos;
    public $dipEstudiante;
    public $fechaNacimiento;
    public $sexo;
    public $nacionalidad;
    public $direccion;
    public $localidad;
    public $provincia;
    public $pais;
    public $telefono;
    public $correoEstudiante;
    public $centroProcedencia;
    public $universidadProcedencia;
    public $esBecado;
    public $nombreUsuario;

    public function __construct($nombre = null, $apellidos = null, $codigoEstudiante = null)
    {
        $this->nombre = $nombre;
        $this->apellidos = $apellidos;
        $this->codigoEstudiante = $codigoEstudiante;
    }

    // Hidratar modelo desde array de base de datos
    public function hidratarDesdeArray($data)
    {
        if (isset($data['idEstudiante'])) $this->idEstudiante = $data['idEstudiante'];
        if (isset($data['idUsuario'])) $this->idUsuario = $data['idUsuario'];
        if (isset($data['codigoEstudiante'])) $this->codigoEstudiante = $data['codigoEstudiante'];
        if (isset($data['nombre'])) $this->nombre = $data['nombre'];
        if (isset($data['apellidos'])) $this->apellidos = $data['apellidos'];
        if (isset($data['dipEstudiante'])) $this->dipEstudiante = $data['dipEstudiante'];
        if (isset($data['fechaNacimiento'])) $this->fechaNacimiento = $data['fechaNacimiento'];
        if (isset($data['sexo'])) $this->sexo = $data['sexo'];
        if (isset($data['nacionalidad'])) $this->nacionalidad = $data['nacionalidad'];
        if (isset($data['direccion'])) $this->direccion = $data['direccion'];
        if (isset($data['localidad'])) $this->localidad = $data['localidad'];
        if (isset($data['provincia'])) $this->provincia = $data['provincia'];
        if (isset($data['pais'])) $this->pais = $data['pais'];
        if (isset($data['telefono'])) $this->telefono = $data['telefono'];
        if (isset($data['correoEstudiante'])) $this->correoEstudiante = $data['correoEstudiante'];
        if (isset($data['centroProcedencia'])) $this->centroProcedencia = $data['centroProcedencia'];
        if (isset($data['universidadProcedencia'])) $this->universidadProcedencia = $data['universidadProcedencia'];
        if (isset($data['esBecado'])) $this->esBecado = $data['esBecado'];
        
        return $this;
    }

    // Convertir modelo a array
    public function convertirAArray()
    {
        return [
            'idEstudiante' => $this->idEstudiante,
            'idUsuario' => $this->idUsuario,
            'codigoEstudiante' => $this->codigoEstudiante,
            'nombre' => $this->nombre,
            'apellidos' => $this->apellidos,
            'dipEstudiante' => $this->dipEstudiante,
            'fechaNacimiento' => $this->fechaNacimiento,
            'sexo' => $this->sexo,
            'nacionalidad' => $this->nacionalidad,
            'direccion' => $this->direccion,
            'localidad' => $this->localidad,
            'provincia' => $this->provincia,
            'pais' => $this->pais,
            'telefono' => $this->telefono,
            'correoEstudiante' => $this->correoEstudiante,
            'centroProcedencia' => $this->centroProcedencia,
            'universidadProcedencia' => $this->universidadProcedencia,
            'esBecado' => $this->esBecado
        ];
    }

    // Obtener nombre completo
    public function obtenerNombreCompleto()
    {
        return $this->nombre . ' ' . $this->apellidos;
    }

    // Verificar si es becado
    public function esBecado()
    {
        return $this->esBecado == 1;
    }
}
?>