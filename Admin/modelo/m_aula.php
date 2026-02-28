<?php
class AulaModel
{
    public $idAula;
    public $nombreAula;
    public $capacidad;
    public $idFacultad;
    public $nombreFacultad;

    public function __construct($nombreAula = null, $capacidad = null, $idFacultad = null)
    {
        $this->nombreAula = $nombreAula;
        $this->capacidad = $capacidad;
        $this->idFacultad = $idFacultad;
    }

    // Hidratar modelo desde array de base de datos
    public function hidratarDesdeArray($data)
    {
        if (isset($data['idAula'])) $this->idAula = $data['idAula'];
        if (isset($data['nombreAula'])) $this->nombreAula = $data['nombreAula'];
        if (isset($data['capacidad'])) $this->capacidad = $data['capacidad'];
        if (isset($data['idFacultad'])) $this->idFacultad = $data['idFacultad'];
        
        
        return $this;
    }

    // Convertir modelo a array
    public function convertirAArray()
    {
        return [
            'idAula' => $this->idAula,
            'nombreAula' => $this->nombreAula,
            'capacidad' => $this->capacidad,
            'idFacultad' => $this->idFacultad,
        ];
    }
}
?>