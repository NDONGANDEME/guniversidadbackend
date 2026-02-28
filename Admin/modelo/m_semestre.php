<?php
class SemestreModel
{
    public $idSemestre;
    public $numeroSemestre;
    public $tipoSemestre;

    public function __construct($numeroSemestre = null, $tipoSemestre = null)
    {
        $this->numeroSemestre = $numeroSemestre;
        $this->tipoSemestre = $tipoSemestre;
    }

    // Hidratar modelo desde array de base de datos
    public function hidratarDesdeArray($data)
    {
        if (isset($data['idSemestre'])) $this->idSemestre = $data['idSemestre'];
        if (isset($data['numeroSemestre'])) $this->numeroSemestre = $data['numeroSemestre'];
        if (isset($data['tipoSemestre'])) $this->tipoSemestre = $data['tipoSemestre'];
        
        return $this;
    }

    // Convertir modelo a array
    public function convertirAArray()
    {
        return [
            'idSemestre' => $this->idSemestre,
            'numeroSemestre' => $this->numeroSemestre,
            'tipoSemestre' => $this->tipoSemestre
        ];
    }
}
?>