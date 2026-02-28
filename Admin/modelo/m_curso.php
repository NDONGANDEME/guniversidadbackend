<?php
class CursoModel
{
    public $idCurso;
    public $nombreCurso;
    public $nivel;

    public function __construct($nombreCurso = null, $nivel = null)
    {
        $this->nombreCurso = $nombreCurso;
        $this->nivel = $nivel;
    }

    // Hidratar modelo desde array de base de datos
    public function hidratarDesdeArray($data)
    {
        if (isset($data['idCurso'])) $this->idCurso = $data['idCurso'];
        if (isset($data['nombreCurso'])) $this->nombreCurso = $data['nombreCurso'];
        if (isset($data['nivel'])) $this->nivel = $data['nivel'];
        
        return $this;
    }

    // Convertir modelo a array
    public function convertirAArray()
    {
        return [
            'idCurso' => $this->idCurso,
            'nombreCurso' => $this->nombreCurso,
            'nivel' => $this->nivel
        ];
    }
}
?>