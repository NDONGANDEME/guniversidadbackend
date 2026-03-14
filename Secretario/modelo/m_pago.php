<?php
class PagoModel
{
    public $idPago;
    public $idMatricula;
    public $idFamiliar;
    public $cuota;
    public $monto;
    public $fechaPago;
    
    // Propiedades adicionales para joins
    public $nombreEstudiante;
    public $apellidosEstudiante;
    public $codigoEstudiante;
    public $nombreFamiliar;
    public $apellidosFamiliar;
    public $conceptoMatricula;

    public function __construct($idMatricula = null, $idFamiliar = null, $monto = null)
    {
        $this->idMatricula = $idMatricula;
        $this->idFamiliar = $idFamiliar;
        $this->monto = $monto;
    }

    // Hidratar modelo desde array de base de datos
    public function hidratarDesdeArray($data)
    {
        if (isset($data['idPago'])) $this->idPago = $data['idPago'];
        if (isset($data['idMatricula'])) $this->idMatricula = $data['idMatricula'];
        if (isset($data['idFamiliar'])) $this->idFamiliar = $data['idFamiliar'];
        if (isset($data['cuota'])) $this->cuota = $data['cuota'];
        if (isset($data['monto'])) $this->monto = $data['monto'];
        if (isset($data['fechaPago'])) $this->fechaPago = $data['fechaPago'];
        
        return $this;
    }

    // Convertir modelo a array
    public function convertirAArray()
    {
        $data = [
            'idPago' => $this->idPago,
            'idMatricula' => $this->idMatricula,
            'idFamiliar' => $this->idFamiliar,
            'cuota' => $this->cuota,
            'monto' => $this->monto,
            'fechaPago' => $this->fechaPago
        ];

        if (isset($this->nombreEstudiante)) {
            $data['nombreEstudiante'] = $this->nombreEstudiante;
            $data['apellidosEstudiante'] = $this->apellidosEstudiante;
            $data['codigoEstudiante'] = $this->codigoEstudiante;
            $data['nombreCompletoEstudiante'] = trim(($this->nombreEstudiante ?? '') . ' ' . ($this->apellidosEstudiante ?? ''));
        }
        
        if (isset($this->nombreFamiliar)) {
            $data['nombreFamiliar'] = $this->nombreFamiliar;
            $data['apellidosFamiliar'] = $this->apellidosFamiliar;
            $data['nombreCompletoFamiliar'] = trim(($this->nombreFamiliar ?? '') . ' ' . ($this->apellidosFamiliar ?? ''));
        }
        
        if (isset($this->conceptoMatricula)) {
            $data['conceptoMatricula'] = $this->conceptoMatricula;
        }

        return $data;
    }
}
?>