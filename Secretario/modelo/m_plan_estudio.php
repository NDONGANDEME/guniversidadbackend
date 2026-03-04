<?php
class PlanEstudioModel
{
    public $idPlanEstudio;
    public $nombre;
    public $idCarrera;
    public $fechaElaboracion;
    public $periodoPlanEstudio;
    public $vigente;
    public $nombreCarrera;

    public function __construct($nombre = null, $idCarrera = null, $vigente = 1)
    {
        $this->nombre = $nombre;
        $this->idCarrera = $idCarrera;
        $this->vigente = $vigente;
    }

    // Hidratar modelo desde array de base de datos
    public function hidratarDesdeArray($data)
    {
        if (isset($data['idPlanEstudio'])) $this->idPlanEstudio = $data['idPlanEstudio'];
        if (isset($data['nombre'])) $this->nombre = $data['nombre'];
        if (isset($data['idCarrera'])) $this->idCarrera = $data['idCarrera'];
        if (isset($data['fechaElaboracion'])) $this->fechaElaboracion = $data['fechaElaboracion'];
        if (isset($data['periodoPlanEstudio'])) $this->periodoPlanEstudio = $data['periodoPlanEstudio'];
        if (isset($data['vigente'])) $this->vigente = $data['vigente'];
        
        return $this;
    }

    // Convertir modelo a array
    public function convertirAArray()
    {
        return [
            'idPlanEstudio' => $this->idPlanEstudio,
            'nombre' => $this->nombre,
            'idCarrera' => $this->idCarrera,
            'fechaElaboracion' => $this->fechaElaboracion,
            'periodoPlanEstudio' => $this->periodoPlanEstudio,
            'vigente' => $this->vigente
        ];
    }

    // Verificar si está vigente
    public function estaVigente()
    {
        return $this->vigente == 1;
    }
}
?>