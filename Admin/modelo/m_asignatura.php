<?php
class AsignaturaModel
{
    public $idAsignatura;
    public $codigoAsignatura;
    public $nombreAsignatura;
    public $descripcion;
    public $idFacultad;
    public $nombreFacultad;

    public function __construct($codigoAsignatura = null, $nombreAsignatura = null, $descripcion = null, $idFacultad = null)
    {
        $this->codigoAsignatura = $codigoAsignatura;
        $this->nombreAsignatura = $nombreAsignatura;
        $this->descripcion = $descripcion;
        $this->idFacultad = $idFacultad;
    }

    // Hidratar modelo desde array de base de datos
    public function hidratarDesdeArray($data)
    {
        if (isset($data['idAsignatura'])) $this->idAsignatura = $data['idAsignatura'];
        if (isset($data['codigoAsignatura'])) $this->codigoAsignatura = $data['codigoAsignatura'];
        if (isset($data['nombreAsignatura'])) $this->nombreAsignatura = $data['nombreAsignatura'];
        if (isset($data['descripcion'])) $this->descripcion = $data['descripcion'];
        if (isset($data['idFacultad'])) $this->idFacultad = $data['idFacultad'];
        if (isset($data['nombreFacultad'])) $this->nombreFacultad = $data['nombreFacultad'];
        
        return $this;
    }

    // Convertir modelo a array
    public function convertirAArray()
    {
        return [
            'idAsignatura' => $this->idAsignatura,
            'codigoAsignatura' => $this->codigoAsignatura,
            'nombreAsignatura' => $this->nombreAsignatura,
            'descripcion' => $this->descripcion,
            'idFacultad' => $this->idFacultad,
            'nombreFacultad' => $this->nombreFacultad
        ];
    }
}
?>