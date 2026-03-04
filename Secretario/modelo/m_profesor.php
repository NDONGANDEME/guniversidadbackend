<?php
class ProfesorModel
{
    public $idProfesor;
    public $nombreProfesor;
    public $apellidosProfesor;
    public $dipProfesor;
    public $especialidad;
    public $gradoEstudio;
    public $idDepartamento;
    public $nombreDepartamento;
    public $idUsuario;
    public $genero;
    public $nacionalidad;
    public $responsabilidad;
    public $correoProfesor;
    public $contactoProfesor;

    public function __construct($nombreProfesor = null, $apellidosProfesor = null, $idDepartamento = null)
    {
        $this->nombreProfesor = $nombreProfesor;
        $this->apellidosProfesor = $apellidosProfesor;
        $this->idDepartamento = $idDepartamento;
    }

    // Hidratar modelo desde array de base de datos
    public function hidratarDesdeArray($data)
    {
        if (isset($data['idProfesor'])) $this->idProfesor = $data['idProfesor'];
        if (isset($data['nombreProfesor'])) $this->nombreProfesor = $data['nombreProfesor'];
        if (isset($data['apellidosProfesor'])) $this->apellidosProfesor = $data['apellidosProfesor'];
        if (isset($data['dipProfesor'])) $this->dipProfesor = $data['dipProfesor'];
        if (isset($data['especialidad'])) $this->especialidad = $data['especialidad'];
        if (isset($data['gradoEstudio'])) $this->gradoEstudio = $data['gradoEstudio'];
        if (isset($data['idDepartamento'])) $this->idDepartamento = $data['idDepartamento'];
        if (isset($data['idUsuario'])) $this->idUsuario = $data['idUsuario'];
        if (isset($data['genero'])) $this->genero = $data['genero'];
        if (isset($data['nacionalidad'])) $this->nacionalidad = $data['nacionalidad'];
        if (isset($data['responsabilidad'])) $this->responsabilidad = $data['responsabilidad'];
        if (isset($data['correoProfesor'])) $this->correoProfesor = $data['correoProfesor'];
        if (isset($data['contactoProfesor'])) $this->contactoProfesor = $data['contactoProfesor'];
        
        return $this;
    }

    // Convertir modelo a array
    public function convertirAArray()
    {
        return [
            'idProfesor' => $this->idProfesor,
            'nombreProfesor' => $this->nombreProfesor,
            'apellidosProfesor' => $this->apellidosProfesor,
            'dipProfesor' => $this->dipProfesor,
            'especialidad' => $this->especialidad,
            'gradoEstudio' => $this->gradoEstudio,
            'idDepartamento' => $this->idDepartamento,
            'idUsuario' => $this->idUsuario,
            'genero' => $this->genero,
            'nacionalidad' => $this->nacionalidad,
            'responsabilidad' => $this->responsabilidad,
            'correoProfesor' => $this->correoProfesor,
            'contactoProfesor' => $this->contactoProfesor
        ];
    }

    // Obtener nombre completo
    public function obtenerNombreCompleto()
    {
        return $this->nombreProfesor . ' ' . $this->apellidosProfesor;
    }
}
?>