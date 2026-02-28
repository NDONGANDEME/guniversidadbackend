<?php
class AsignaturaModel
{
    public $idAsignatura;
    public $codigoAsignatura;
    public $nombreAsignatura;
    public $descripcion;

    public function __construct($codigoAsignatura = null, $nombreAsignatura = null, $descripcion = null)
    {
        $this->codigoAsignatura = $codigoAsignatura;
        $this->nombreAsignatura = $nombreAsignatura;
        $this->descripcion = $descripcion;
    }

    // Hidratar modelo desde array de base de datos
    public function hidratarDesdeArray($data)
    {
        if (isset($data['idAsignatura'])) $this->idAsignatura = $data['idAsignatura'];
        if (isset($data['codigoAsignatura'])) $this->codigoAsignatura = $data['codigoAsignatura'];
        if (isset($data['nombreAsignatura'])) $this->nombreAsignatura = $data['nombreAsignatura'];
        if (isset($data['descripcion'])) $this->descripcion = $data['descripcion'];
        
        return $this;
    }

    // Convertir modelo a array
    public function convertirAArray()
    {
        return [
            'idAsignatura' => $this->idAsignatura,
            'codigoAsignatura' => $this->codigoAsignatura,
            'nombreAsignatura' => $this->nombreAsignatura,
            'descripcion' => $this->descripcion
        ];
    }
}
?>