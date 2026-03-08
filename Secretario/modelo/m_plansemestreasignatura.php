<?php
class PlanSemestreAsignaturaModel
{
    public $idPlanCursoAsignatura;
    public $idPlanEstudio;
    public $idSemestre;
    public $semestre;
    public $idAsignatura;
    public $asignatura;
    public $creditos;
    public $modalidad;
    public $nombrePlanEstudio;

    public function __construct($idPlanEstudio = null, $idSemestre = null, $idAsignatura = null)
    {
        $this->idPlanEstudio = $idPlanEstudio;
        $this->idSemestre = $idSemestre;
        $this->idAsignatura = $idAsignatura;
    }

    // Hidratar modelo desde array de base de datos
    public function hidratarDesdeArray($data)
    {
        if (isset($data['idPlanCursoAsignatura'])) $this->idPlanCursoAsignatura = $data['idPlanCursoAsignatura'];
        if (isset($data['idPlanEstudio'])) $this->idPlanEstudio = $data['idPlanEstudio'];
        if (isset($data['idSemestre'])) $this->idSemestre = $data['idSemestre'];
        if (isset($data['idAsignatura'])) $this->idAsignatura = $data['idAsignatura'];
        if (isset($data['creditos'])) $this->creditos = $data['creditos'];
        if (isset($data['modalidad'])) $this->modalidad = $data['modalidad'];
        
        return $this;
    }

    // Convertir modelo a array
    public function convertirAArray()
    {
        return [
            'idPlanCursoAsignatura' => $this->idPlanCursoAsignatura,
            'idPlanEstudio' => $this->idPlanEstudio,
            'idSemestre' => $this->idSemestre,
            'idAsignatura' => $this->idAsignatura,
            'creditos' => $this->creditos,
            'modalidad' => $this->modalidad
        ];
    }
}
?>