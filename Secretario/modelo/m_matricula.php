<?php
class MatriculaModel
{
    public $idMatricula;
    public $idEstudiante;
    public $idPlanEstudio;
    public $idSemestre;
    public $cursoAcademico;
    public $fechaMatricula;
    public $modalidadMatricula;
    public $totalCreditos;
    public $estado;
    public $nombreEstudiante;
    public $nombrePlanEstudio;
    public $numeroSemestre;
    public $codigoEstudiante;


    public function __construct($idEstudiante = null, $idPlanEstudio = null, $idSemestre = null)
    {
        $this->idEstudiante = $idEstudiante;
        $this->idPlanEstudio = $idPlanEstudio;
        $this->idSemestre = $idSemestre;
    }

    // Hidratar modelo desde array de base de datos
    public function hidratarDesdeArray($data)
    {
        if (isset($data['idMatricula'])) $this->idMatricula = $data['idMatricula'];
        if (isset($data['idEstudiante'])) $this->idEstudiante = $data['idEstudiante'];
        if (isset($data['idPlanEstudio'])) $this->idPlanEstudio = $data['idPlanEstudio'];
        if (isset($data['idSemestre'])) $this->idSemestre = $data['idSemestre'];
        if (isset($data['cursoAcademico'])) $this->cursoAcademico = $data['cursoAcademico'];
        if (isset($data['fechaMatricula'])) $this->fechaMatricula = $data['fechaMatricula'];
        if (isset($data['modalidadMatricula'])) $this->modalidadMatricula = $data['modalidadMatricula'];
        if (isset($data['totalCreditos'])) $this->totalCreditos = $data['totalCreditos'];
        if (isset($data['estado'])) $this->estado = $data['estado'];
        if (isset($data['nombreEstudiante'])) $this->nombreEstudiante = $data['nombreEstudiante'];
        if (isset($data['nombrePlanEstudio'])) $this->nombrePlanEstudio = $data['nombrePlanEstudio'];
        if (isset($data['numeroSemestre'])) $this->numeroSemestre = $data['numeroSemestre'];
        
        return $this;
    }

    // Convertir modelo a array
    public function convertirAArray()
    {
        return [
            'idMatricula' => $this->idMatricula,
            'idEstudiante' => $this->idEstudiante,
            'idPlanEstudio' => $this->idPlanEstudio,
            'idSemestre' => $this->idSemestre,
            'cursoAcademico' => $this->cursoAcademico,
            'fechaMatricula' => $this->fechaMatricula,
            'modalidadMatricula' => $this->modalidadMatricula,
            'totalCreditos' => $this->totalCreditos,
            'estado' => $this->estado
        ];
    }

    // Verificar si la matrícula está activa
    public function estaActiva()
    {
        return $this->estado === 'activa';
    }
}
?>