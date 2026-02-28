<?php
class CarreraModel
{
    public $idCarrera;
    public $nombreCarrera;
    public $idDepartamento;
    public $nombreDepartamento;

    public function __construct($nombreCarrera = null, $idDepartamento = null)
    {
        $this->nombreCarrera = $nombreCarrera;
        $this->idDepartamento = $idDepartamento;
    }

    // Hidratar modelo desde array de base de datos
    public function hidratarDesdeArray($data)
    {
        if (isset($data['idCarrera'])) $this->idCarrera = $data['idCarrera'];
        if (isset($data['nombreCarrera'])) $this->nombreCarrera = $data['nombreCarrera'];
        if (isset($data['idDepartamento'])) $this->idDepartamento = $data['idDepartamento'];
        if (isset($data['nombreDepartamento'])) $this->idDepartamento = $data['nombreDepartamento'];
        
        return $this;
    }

    // Convertir modelo a array
    public function convertirAArray()
    {
        return [
            'idCarrera' => $this->idCarrera,
            'nombreCarrera' => $this->nombreCarrera,
            'idDepartamento' => $this->idDepartamento,
            'nombreDepartamento' => $this->nombreDepartamento

        ];
    }
}
?>