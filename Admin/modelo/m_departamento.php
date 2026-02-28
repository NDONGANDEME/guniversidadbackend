<?php
class DepartamentoModel
{
    public $idDepartamento;
    public $nombreDepartamento;
    public $idFacultad;
    public $nombreFacultad;

    public function __construct($nombreDepartamento = null, $idFacultad = null)
    {
        $this->nombreDepartamento = $nombreDepartamento;
        $this->idFacultad = $idFacultad;
    }

    // Hidratar modelo desde array de base de datos
    public function hidratarDesdeArray($data)
    {
        if (isset($data['idDepartamento'])) $this->idDepartamento = $data['idDepartamento'];
        if (isset($data['nombreDepartamento'])) $this->nombreDepartamento = $data['nombreDepartamento'];
        if (isset($data['idFacultad'])) $this->idFacultad = $data['idFacultad'];
        if (isset($data['nombreFacultad'])) $this->nombreDepartamento = $data['nombreFacultad'];
        
        return $this;
    }

    // Convertir modelo a array
    public function convertirAArray()
    {
        return [
            'idDepartamento' => $this->idDepartamento,
            'nombreDepartamento' => $this->nombreDepartamento,
            'idFacultad' => $this->idFacultad,
            'nombreFacultad' => $this->nombreFacultad
        ];
    }
}
?>