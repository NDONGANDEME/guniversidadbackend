<?php
class FormacionModel
{
    public $idFormacion;
    public $institucion;
    public $tipoFormacion;
    public $titulo;
    public $nivel;
    public $idProfesor;
    public $apellidosProfesor;
    public $nombreProfesor;

    public function __construct($institucion = null, $titulo = null, $idProfesor = null)
    {
        $this->institucion = $institucion;
        $this->titulo = $titulo;
        $this->idProfesor = $idProfesor;
    }

    // Hidratar modelo desde array de base de datos
    public function hidratarDesdeArray($data)
    {
        if (isset($data['idFormacion'])) $this->idFormacion = $data['idFormacion'];
        if (isset($data['institucion'])) $this->institucion = $data['institucion'];
        if (isset($data['tipoFormacion'])) $this->tipoFormacion = $data['tipoFormacion'];
        if (isset($data['titulo'])) $this->titulo = $data['titulo'];
        if (isset($data['nivel'])) $this->nivel = $data['nivel'];
        if (isset($data['idProfesor'])) $this->idProfesor = $data['idProfesor'];
        
        return $this;
    }

    // Convertir modelo a array
    public function convertirAArray()
    {
        return [
            'idFormacion' => $this->idFormacion,
            'institucion' => $this->institucion,
            'tipoFormacion' => $this->tipoFormacion,
            'titulo' => $this->titulo,
            'nivel' => $this->nivel,
            'idProfesor' => $this->idProfesor
        ];
    }
}
?>