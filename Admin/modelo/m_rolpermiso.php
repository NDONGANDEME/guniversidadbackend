<?php
class RolPermisoModel
{
    public $idRolPermiso;
    public $idRol;
    public $idPermiso;
    
    // Propiedades adicionales para joins
    public $nombreRol;
    public $nombrePermiso;
    public $tabla;
    public $accion;

    public function __construct($idRol = null, $idPermiso = null)
    {
        $this->idRol = $idRol;
        $this->idPermiso = $idPermiso;
    }

    // Hidratar modelo desde array de base de datos
    public function hidratarDesdeArray($data)
    {
        if (isset($data['idRolPermiso'])) $this->idRolPermiso = $data['idRolPermiso'];
        if (isset($data['idRol'])) $this->idRol = $data['idRol'];
        if (isset($data['idPermiso'])) $this->idPermiso = $data['idPermiso'];
        
        return $this;
    }

    // Convertir modelo a array
    public function convertirAArray()
    {
        $data = [
            'idRolPermiso' => $this->idRolPermiso,
            'idRol' => $this->idRol,
            'idPermiso' => $this->idPermiso
        ];

        if (isset($this->nombreRol)) {
            $data['nombreRol'] = $this->nombreRol;
        }
        
        if (isset($this->nombrePermiso)) {
            $data['nombrePermiso'] = $this->nombrePermiso;
            $data['tabla'] = $this->tabla ?? '';
            $data['accion'] = $this->accion ?? '';
        }

        return $data;
    }
}
?>