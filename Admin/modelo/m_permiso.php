<?php
class PermisoModel
{
    public $idPermiso;
    public $nombrePermiso;
    public $tabla;
    public $accionPermiso;

    public function __construct($nombrePermiso = null, $tabla = null, $accion = null)
    {
        $this->nombrePermiso = $nombrePermiso;
        $this->tabla = $tabla;
        $this->accionPermiso = $accion;
    }

    // Hidratar modelo desde array de base de datos
    public function hidratarDesdeArray($data)
    {
        if (isset($data['idPermiso'])) $this->idPermiso = $data['idPermiso'];
        if (isset($data['nombrePermiso'])) $this->nombrePermiso = $data['nombrePermiso'];
        if (isset($data['tabla'])) $this->tabla = $data['tabla'];
        if (isset($data['accion'])) $this->accionPermiso = $data['accion'];
        
        return $this;
    }

    // Convertir modelo a array
    public function convertirAArray()
    {
        return [
            'idPermiso' => $this->idPermiso,
            'nombrePermiso' => $this->nombrePermiso,
            'tabla' => $this->tabla,
            'accionPermiso' => $this->accionPermiso
        ];
    }

    // Verificar si el permiso corresponde a una tabla y acción específicas
    public function correspondeA($tabla, $accion)
    {
        return $this->tabla === $tabla && $this->accionPermiso === $accion;
    }
}
?>