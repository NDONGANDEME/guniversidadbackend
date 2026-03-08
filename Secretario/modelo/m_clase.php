<?php
class ClaseModel
{
    public $idClase;
    public $idPlanCursoAsignatura;
    public $idAula;
    public $idProfesor;
    public $diaSemanal;
    public $horaInicio;
    public $horaFinal;
    public $tipoSesion;
    public $observaciones;
    
    // Propiedades adicionales para mostrar información relacionada
    public $nombreAula;
    public $nombreProfesor;
    public $apellidosProfesor;
    public $asignatura;
    public $codigoAsignatura;
    public $nombreAsignatura;
    public $planEstudio;
    public $horarios = []; // Para los horarios asociados

    public function __construct($idPlanCursoAsignatura = null, $idAula = null, $idProfesor = null)
    {
        $this->idPlanCursoAsignatura = $idPlanCursoAsignatura;
        $this->idAula = $idAula;
        $this->idProfesor = $idProfesor;
    }

    // Hidratar modelo desde array de base de datos
    public function hidratarDesdeArray($data)
    {
        if (isset($data['idClase'])) $this->idClase = $data['idClase'];
        if (isset($data['idPlanCursoAsignatura'])) $this->idPlanCursoAsignatura = $data['idPlanCursoAsignatura'];
        if (isset($data['idAula'])) $this->idAula = $data['idAula'];
        if (isset($data['idProfesor'])) $this->idProfesor = $data['idProfesor'];
        if (isset($data['diaSemanal'])) $this->diaSemanal = $data['diaSemanal'];
        if (isset($data['horaInicio'])) $this->horaInicio = $data['horaInicio'];
        if (isset($data['horaFinal'])) $this->horaFinal = $data['horaFinal'];
        if (isset($data['tipoSesion'])) $this->tipoSesion = $data['tipoSesion'];
        if (isset($data['observaciones'])) $this->observaciones = $data['observaciones'];
        
        return $this;
    }

    // Convertir modelo a array
    public function convertirAArray($incluirHorarios = true)
    {
        $data = [
            'idClase' => $this->idClase,
            'idPlanCursoAsignatura' => $this->idPlanCursoAsignatura,
            'idAula' => $this->idAula,
            'idProfesor' => $this->idProfesor,
            'diaSemanal' => $this->diaSemanal,
            'horaInicio' => $this->horaInicio,
            'horaFinal' => $this->horaFinal,
            'tipoSesion' => $this->tipoSesion,
            'observaciones' => $this->observaciones
        ];

        if (isset($this->nombreAula)) {
            $data['nombreAula'] = $this->nombreAula;
        }
        if (isset($this->nombreProfesor) || isset($this->apellidosProfesor)) {
            $data['nombreProfesor'] = $this->nombreProfesor ?? '';
            $data['apellidosProfesor'] = $this->apellidosProfesor ?? '';
           
        }
        if (isset($this->asignatura)) {
            $data['asignatura'] = $this->asignatura;
        }
        if (isset($this->codigoAsignatura)) {
            $data['codigoAsignatura'] = $this->codigoAsignatura;
            $data['nombreAsignatura'] = $this->nombreAsignatura ?? '';
        }
        if (isset($this->planEstudio)) {
            $data['planEstudio'] = $this->planEstudio;
        }
        if ($incluirHorarios && !empty($this->horarios)) {
            $data['horarios'] = $this->horarios;
        }

        return $data;
    }

    // Establecer horarios asociados
    public function establecerHorarios($horarios)
    {
        $this->horarios = $horarios;
        return $this;
    }
}
?>