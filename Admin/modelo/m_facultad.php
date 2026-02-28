<?php
class FacultadModel
{
    public $idFacultad;
    public $nombreFacultad;
    public $direccionFacultad;
    public $contacto;

    public function __construct($nombreFacultad = null, $direccionFacultad = null, $contacto = null)
    {
        $this->nombreFacultad = $nombreFacultad;
        $this->direccionFacultad = $direccionFacultad;
        $this->contacto = $contacto;
    }

    // Hidratar modelo desde array de base de datos
    public function hidratarDesdeArray($data)
    {
        if (isset($data['idFacultad'])) $this->idFacultad = $data['idFacultad'];
        if (isset($data['nombreFacultad'])) $this->nombreFacultad = $data['nombreFacultad'];
        if (isset($data['direccionFacultad'])) $this->direccionFacultad = $data['direccionFacultad'];
        if (isset($data['contacto'])) $this->contacto = $data['contacto'];
        
        return $this;
    }

    // Convertir modelo a array
    public function convertirAArray()
    {
        return [
            'idFacultad' => $this->idFacultad,
            'nombreFacultad' => $this->nombreFacultad,
            'direccionFacultad' => $this->direccionFacultad,
            'contacto' => $this->contacto
        ];
    }
}
?>