<?php
class FamiliarModel
{
    public $idFamiliar;
    public $nombre;
    public $apellidos;
    public $dipFamiliar;
    public $telefono;
    public $correoFamiliar;
    public $direccion;
    public $parentesco;
    public $esContactoIncidentes;
    public $esResponsablePago;
    public $idEstudiante;
    public $nombreEstudiante;

    public function __construct($nombre = null, $apellidos = null, $idEstudiante = null)
    {
        $this->nombre = $nombre;
        $this->apellidos = $apellidos;
        $this->idEstudiante = $idEstudiante;
    }

    // Hidratar modelo desde array de base de datos
    public function hidratarDesdeArray($data)
    {
        if (isset($data['idFamiliar'])) $this->idFamiliar = $data['idFamiliar'];
        if (isset($data['nombre'])) $this->nombre = $data['nombre'];
        if (isset($data['apellidos'])) $this->apellidos = $data['apellidos'];
        if (isset($data['dipFamiliar'])) $this->dipFamiliar = $data['dipFamiliar'];
        if (isset($data['telefono'])) $this->telefono = $data['telefono'];
        if (isset($data['correoFamiliar'])) $this->correoFamiliar = $data['correoFamiliar'];
        if (isset($data['direccion'])) $this->direccion = $data['direccion'];
        if (isset($data['parentesco'])) $this->parentesco = $data['parentesco'];
        if (isset($data['esContactoIncidentes'])) $this->esContactoIncidentes = $data['esContactoIncidentes'];
        if (isset($data['esResponsablePago'])) $this->esResponsablePago = $data['esResponsablePago'];
        if (isset($data['idEstudiante'])) $this->idEstudiante = $data['idEstudiante'];
        
        return $this;
    }

    // Convertir modelo a array
    public function convertirAArray()
    {
        return [
            'idFamiliar' => $this->idFamiliar,
            'nombre' => $this->nombre,
            'apellidos' => $this->apellidos,
            'dipFamiliar' => $this->dipFamiliar,
            'telefono' => $this->telefono,
            'correoFamiliar' => $this->correoFamiliar,
            'direccion' => $this->direccion,
            'parentesco' => $this->parentesco,
            'esContactoIncidentes' => $this->esContactoIncidentes,
            'esResponsablePago' => $this->esResponsablePago,
            'idEstudiante' => $this->idEstudiante
        ];
    }

    // Verificar si es responsable de pagos
    public function esResponsablePago()
    {
        return $this->esResponsablePago == 1;
    }

    // Verificar si es contacto para incidentes
    public function esContactoIncidentes()
    {
        return $this->esContactoIncidentes == 1;
    }
}
?>