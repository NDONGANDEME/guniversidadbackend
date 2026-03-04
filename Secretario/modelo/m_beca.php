<?php
class BecaModel
{
    public $idBeca;
    public $institucionBeca;
    public $tipoBeca;
    public $estado;

    public function __construct($institucionBeca = null, $tipoBeca = null, $estado = 'activo')
    {
        $this->institucionBeca = $institucionBeca;
        $this->tipoBeca = $tipoBeca;
        $this->estado = $estado;
    }

    // Hidratar modelo desde array de base de datos
    public function hidratarDesdeArray($data)
    {
        if (isset($data['idBeca'])) $this->idBeca = $data['idBeca'];
        if (isset($data['institucionBeca'])) $this->institucionBeca = $data['institucionBeca'];
        if (isset($data['tipoBeca'])) $this->tipoBeca = $data['tipoBeca'];
        if (isset($data['estado'])) $this->estado = $data['estado'];
        
        return $this;
    }

    // Convertir modelo a array
    public function convertirAArray()
    {
        return [
            'idBeca' => $this->idBeca,
            'institucionBeca' => $this->institucionBeca,
            'tipoBeca' => $this->tipoBeca,
            'estado' => $this->estado
        ];
    }

    // Verificar si está activa
    public function estaActiva()
    {
        return $this->estado === 'activo';
    }
}
?>