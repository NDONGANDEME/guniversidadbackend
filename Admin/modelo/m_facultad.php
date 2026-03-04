<?php
class FacultadModel
{
    public $idFacultad;
    public $nombreFacultad;
    public $direccionFacultad;
    public $correo;
    public $telefono;

    public function __construct($nombreFacultad = null, $direccionFacultad = null, $correo = null, $telefono = null)
    {
        $this->nombreFacultad = $nombreFacultad;
        $this->direccionFacultad = $direccionFacultad;
        $this->correo = $correo;
        $this->telefono = $telefono;
    }

    // Hidratar modelo desde array de base de datos
    public function hidratarDesdeArray($data)
    {
        if (isset($data['idFacultad'])) $this->idFacultad = $data['idFacultad'];
        if (isset($data['nombreFacultad'])) $this->nombreFacultad = $data['nombreFacultad'];
        if (isset($data['direccionFacultad'])) $this->direccionFacultad = $data['direccionFacultad'];
        if (isset($data['correo'])) $this->correo = $data['correo'];
        if (isset($data['telefono'])) $this->telefono = $data['telefono'];
        
        return $this;
    }

    // Convertir modelo a array
    public function convertirAArray()
    {
        return [
            'idFacultad' => $this->idFacultad,
            'nombreFacultad' => $this->nombreFacultad,
            'direccionFacultad' => $this->direccionFacultad,
            'correo' => $this->correo,
            'telefono' => $this->telefono
        ];
    }
}
?>