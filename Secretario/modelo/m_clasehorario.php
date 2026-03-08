<?php
class ClaseHorarioModel
{
    public $idClaseHorario;
    public $idClase;
    public $idHorario;
    
    // Propiedades adicionales
    public $nombreHorario;

    public function __construct($idClase = null, $idHorario = null)
    {
        $this->idClase = $idClase;
        $this->idHorario = $idHorario;
    }

    // Hidratar modelo desde array de base de datos
    public function hidratarDesdeArray($data)
    {
        if (isset($data['idClaseHorario'])) $this->idClaseHorario = $data['idClaseHorario'];
        if (isset($data['idClase'])) $this->idClase = $data['idClase'];
        if (isset($data['idHorario'])) $this->idHorario = $data['idHorario'];
        
        return $this;
    }

    // Convertir modelo a array
    public function convertirAArray()
    {
        $data = [
            'idClaseHorario' => $this->idClaseHorario,
            'idClase' => $this->idClase,
            'idHorario' => $this->idHorario
        ];

        if (isset($this->nombreHorario)) {
            $data['nombreHorario'] = $this->nombreHorario;
        }

        return $data;
    }
}
?>