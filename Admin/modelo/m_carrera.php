<?php
class CarreraModel
{
    public $idCarrera;
    public $nombreCarrera;
    public $idDepartamento;
    public $nombreDepartamento;
    public $estado;
    public $nombreFacultad; // Nueva propiedad para el nombre de la facultad
    public $idFacultad; // Nueva propiedad para el ID de la facultad

    public function __construct($nombreCarrera = null, $idDepartamento = null, $estado = null)
    {
        $this->nombreCarrera = $nombreCarrera;
        $this->idDepartamento = $idDepartamento;
        $this->estado = $estado;
    }

    // Hidratar modelo desde array de base de datos
    public function hidratarDesdeArray($data)
    {
        if (isset($data['idCarrera'])) $this->idCarrera = $data['idCarrera'];
        if (isset($data['nombreCarrera'])) $this->nombreCarrera = $data['nombreCarrera'];
        if (isset($data['idDepartamento'])) $this->idDepartamento = $data['idDepartamento'];
        if (isset($data['nombreDepartamento'])) $this->nombreDepartamento = $data['nombreDepartamento'];
        if (isset($data['estado'])) $this->estado = $data['estado'];
        if (isset($data['nombreFacultad'])) $this->nombreFacultad = $data['nombreFacultad'];
        if (isset($data['idFacultad'])) $this->idFacultad = $data['idFacultad'];
        
        return $this;
    }

    // Convertir modelo a array
    public function convertirAArray()
    {
        $data = [
            'idCarrera' => $this->idCarrera,
            'nombreCarrera' => $this->nombreCarrera,
            'idDepartamento' => $this->idDepartamento,
            'nombreDepartamento' => $this->nombreDepartamento,
            'estado' => $this->estado
        ];

        if (isset($this->nombreFacultad)) {
            $data['nombreFacultad'] = $this->nombreFacultad;
        }
        if (isset($this->idFacultad)) {
            $data['idFacultad'] = $this->idFacultad;
        }

        return $data;
    }
}
?>