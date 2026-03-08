<?php
class HorarioModel
{
    public $idHorario;
    public $nombre;

    public function __construct($nombre = null)
    {
        $this->nombre = $nombre;
    }

    // Hidratar modelo desde array de base de datos
    public function hidratarDesdeArray($data)
    {
        if (isset($data['idHorario'])) $this->idHorario = $data['idHorario'];
        if (isset($data['nombre'])) $this->nombre = $data['nombre'];
        
        return $this;
    }

    // Convertir modelo a array
    public function convertirAArray()
    {
        return [
            'idHorario' => $this->idHorario,
            'nombre' => $this->nombre
        ];
    }
}
?>