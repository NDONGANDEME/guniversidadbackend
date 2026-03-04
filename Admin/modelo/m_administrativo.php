<?php
class AdministrativoModel
{
    public $idAdministrativos;
    public $idUsuario;
    public $nombreAdministrativo;
    public $apellidosAdministrativo;
    public $idFacultad;
    public $nombreFacultad; // Para joins
    public $telefono;
    public $correo;

    public function __construct(
        $nombreAdministrativo = null, 
        $apellidosAdministrativo = null, 
        $idFacultad = null,
        $telefono = null,
        $correo = null
    ) {
        $this->nombreAdministrativo = $nombreAdministrativo;
        $this->apellidosAdministrativo = $apellidosAdministrativo;
        $this->idFacultad = $idFacultad;
        $this->telefono = $telefono;
        $this->correo = $correo;
    }

    // Hidratar modelo desde array de base de datos
    public function hidratarDesdeArray($data)
    {
        if (isset($data['idAdministrativos'])) $this->idAdministrativos = $data['idAdministrativos'];
        if (isset($data['idUsuario'])) $this->idUsuario = $data['idUsuario'];
        if (isset($data['nombreAdministrativo'])) $this->nombreAdministrativo = $data['nombreAdministrativo'];
        if (isset($data['apellidosAdministrativo'])) $this->apellidosAdministrativo = $data['apellidosAdministrativo'];
        if (isset($data['idFacultad'])) $this->idFacultad = $data['idFacultad'];
        if (isset($data['nombreFacultad'])) $this->nombreFacultad = $data['nombreFacultad'];
        if (isset($data['telefono'])) $this->telefono = $data['telefono'];
        if (isset($data['correo'])) $this->correo = $data['correo'];
        
        return $this;
    }

    // Convertir modelo a array
    public function convertirAArray()
    {
        return [
            'idAdministrativos' => $this->idAdministrativos,
            'idUsuario' => $this->idUsuario,
            'nombreAdministrativo' => $this->nombreAdministrativo,
            'apellidosAdministrativo' => $this->apellidosAdministrativo,
            'idFacultad' => $this->idFacultad,
            'nombreFacultad' => $this->nombreFacultad,
            'telefono' => $this->telefono,
            'correo' => $this->correo
        ];
    }

    // Obtener nombre completo
    public function obtenerNombreCompleto()
    {
        return $this->nombreAdministrativo . ' ' . $this->apellidosAdministrativo;
    }
}
?>