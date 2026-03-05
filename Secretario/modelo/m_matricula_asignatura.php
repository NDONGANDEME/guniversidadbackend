<?php
class MatriculaAsignaturaModel
{
    public $idMatriculaAsignatura;
    public $idMatricula;
    public $idPlanCursoAsignatura;
    public $convocatoria;
    public $notaFinal;
    public $estado;
    public $numeroVecesMatriculado;
    public $nombreEstudiante;
    public $apellidosEstudiante;
    public $codigoAsignatura;
    public $nombreAsignatura;
    public $creditos;

    public function __construct($idMatricula = null, $idPlanCursoAsignatura = null)
    {
        $this->idMatricula = $idMatricula;
        $this->idPlanCursoAsignatura = $idPlanCursoAsignatura;
    }

    // Hidratar modelo desde array de base de datos
    public function hidratarDesdeArray($data)
    {
        if (isset($data['idMatriculaAsignatura'])) $this->idMatriculaAsignatura = $data['idMatriculaAsignatura'];
        if (isset($data['idMatricula'])) $this->idMatricula = $data['idMatricula'];
        if (isset($data['idPlanCursoAsignatura'])) $this->idPlanCursoAsignatura = $data['idPlanCursoAsignatura'];
        if (isset($data['convocatoria'])) $this->convocatoria = $data['convocatoria'];
        if (isset($data['notaFinal'])) $this->notaFinal = $data['notaFinal'];
        if (isset($data['estado'])) $this->estado = $data['estado'];
        if (isset($data['numeroVecesMatriculado'])) $this->numeroVecesMatriculado = $data['numeroVecesMatriculado'];
        
        return $this;
    }

    // Convertir modelo a array
    public function convertirAArray()
    {
        return [
            'idMatriculaAsignatura' => $this->idMatriculaAsignatura,
            'idMatricula' => $this->idMatricula,
            'idPlanCursoAsignatura' => $this->idPlanCursoAsignatura,
            'convocatoria' => $this->convocatoria,
            'notaFinal' => $this->notaFinal,
            'estado' => $this->estado,
            'numeroVecesMatriculado' => $this->numeroVecesMatriculado
        ];
    }

    // Verificar si está aprobada
    public function estaAprobada()
    {
        return $this->notaFinal !== null && $this->notaFinal >= 5.0;
    }

    // Verificar si está matriculado
    public function estaMatriculado()
    {
        return $this->estado === 'matriculado';
    }
}
?>