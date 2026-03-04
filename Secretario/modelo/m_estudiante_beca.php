<?php
class EstudianteBecaModel
{
    public $idEstudianteBecario;
    public $idEstudiante;
    public $idBeca;
    public $fechaInicio;
    public $fechaFinal;
    public $estado;
    public $observaciones;
    public $nombreEstudiante;
    public $institucionBeca;
    public $tipoBeca;

    public function __construct($idEstudiante = null, $idBeca = null, $estado = 'activo')
    {
        $this->idEstudiante = $idEstudiante;
        $this->idBeca = $idBeca;
        $this->estado = $estado;
    }

    // Hidratar modelo desde array de base de datos
    public function hidratarDesdeArray($data)
    {
        if (isset($data['idEstudianteBecario'])) $this->idEstudianteBecario = $data['idEstudianteBecario'];
        if (isset($data['idEstudiante'])) $this->idEstudiante = $data['idEstudiante'];
        if (isset($data['idBeca'])) $this->idBeca = $data['idBeca'];
        if (isset($data['fechaInicio'])) $this->fechaInicio = $data['fechaInicio'];
        if (isset($data['fechaFinal'])) $this->fechaFinal = $data['fechaFinal'];
        if (isset($data['estado'])) $this->estado = $data['estado'];
        if (isset($data['observaciones'])) $this->observaciones = $data['observaciones'];
        
        return $this;
    }

    // Convertir modelo a array
    public function convertirAArray()
    {
        return [
            'idEstudianteBecario' => $this->idEstudianteBecario,
            'idEstudiante' => $this->idEstudiante,
            'idBeca' => $this->idBeca,
            'fechaInicio' => $this->fechaInicio,
            'fechaFinal' => $this->fechaFinal,
            'estado' => $this->estado,
            'observaciones' => $this->observaciones
        ];
    }

    // Verificar si está activo
    public function estaActivo()
    {
        return $this->estado === 'activo';
    }

    // Verificar si está vigente
    public function estaVigente()
    {
        $hoy = date('Y-m-d');
        return $this->estaActivo() && 
               $this->fechaInicio <= $hoy && 
               ($this->fechaFinal === null || $this->fechaFinal >= $hoy);
    }
}
?>