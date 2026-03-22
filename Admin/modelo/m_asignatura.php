<?php
class AsignaturaModel
{
    public $idAsignatura;
    public $codigoAsignatura;
    public $nombreAsignatura;
    public $descripcion;
    public $idFacultad;
    public $nombreFacultad;
    public $prerrequisitos = []; // Para almacenar los prerrequisitos con sus datos

    public function __construct($codigoAsignatura = null, $nombreAsignatura = null, $descripcion = null, $idFacultad = null)
    {
        $this->codigoAsignatura = $codigoAsignatura;
        $this->nombreAsignatura = $nombreAsignatura;
        $this->descripcion = $descripcion;
        $this->idFacultad = $idFacultad;
    }

    // Hidratar modelo desde array de base de datos
    public function hidratarDesdeArray($data)
    {
        if (isset($data['idAsignatura'])) $this->idAsignatura = $data['idAsignatura'];
        if (isset($data['codigoAsignatura'])) $this->codigoAsignatura = $data['codigoAsignatura'];
        if (isset($data['nombreAsignatura'])) $this->nombreAsignatura = $data['nombreAsignatura'];
        if (isset($data['descripcion'])) $this->descripcion = $data['descripcion'];
        if (isset($data['idFacultad'])) $this->idFacultad = $data['idFacultad'];
        if (isset($data['nombreFacultad'])) $this->nombreFacultad = $data['nombreFacultad'];
        
        return $this;
    }

    // Convertir modelo a array
    public function convertirAArray($incluirPrerrequisitos = true)
    {
        $data = [
            'idAsignatura' => $this->idAsignatura,
            'codigoAsignatura' => $this->codigoAsignatura,
            'nombreAsignatura' => $this->nombreAsignatura,
            'descripcion' => $this->descripcion,
            'idFacultad' => $this->idFacultad,
            'nombreFacultad' => $this->nombreFacultad
        ];

        if ($incluirPrerrequisitos && !empty($this->prerrequisitos)) {
            $data['prerrequisitos'] = $this->prerrequisitos;
        }

        return $data;
    }

    // Establecer prerrequisitos
    public function establecerPrerrequisitos($prerrequisitos)
    {
        $this->prerrequisitos = $prerrequisitos;
        return $this;
    }

    // Verificar si tiene prerrequisitos
    public function tienePrerrequisitos()
    {
        return !empty($this->prerrequisitos);
    }

    // Obtener nombres de los prerrequisitos
    public function obtenerNombresPrerrequisitos()
    {
        $nombres = [];
        foreach ($this->prerrequisitos as $prerreq) {
            $nombres[] = $prerreq['nombreAsignatura'] ?? $prerreq['nombreAsignaturaRequerida'] ?? '';
        }
        return $nombres;
    }
}
?>