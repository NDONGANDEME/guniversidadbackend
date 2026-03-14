<?php
class RolModel
{
    public $idRol;
    public $nombreRol;
    
    // Propiedades adicionales
    public $permisos = [];

    public function __construct($nombreRol = null)
    {
        $this->nombreRol = $nombreRol;
    }

    // Hidratar modelo desde array de base de datos
    public function hidratarDesdeArray($data)
    {
        if (isset($data['idRol'])) $this->idRol = $data['idRol'];
        if (isset($data['nombreRol'])) $this->nombreRol = $data['nombreRol'];
        
        return $this;
    }

    // Convertir modelo a array
    public function convertirAArray($incluirPermisos = true)
    {
        $data = [
            'idRol' => $this->idRol,
            'nombreRol' => $this->nombreRol
        ];

        if ($incluirPermisos && !empty($this->permisos)) {
            $data['permisos'] = $this->permisos;
        }

        return $data;
    }

    // Establecer permisos del rol
    public function establecerPermisos($permisos)
    {
        $this->permisos = $permisos;
        return $this;
    }

    // Verificar si el rol tiene un permiso específico
    public function tienePermiso($nombrePermiso)
    {
        foreach ($this->permisos as $permiso) {
            if ($permiso['nombrePermiso'] === $nombrePermiso) {
                return true;
            }
        }
        return false;
    }
}
?>