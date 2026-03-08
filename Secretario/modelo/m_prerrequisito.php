<?php
class PrerrequisitoModel
{
    public $idPrerrequisito;
    public $idAsignatura;
    public $idAsignaturaRequerida;
    
    // Propiedades adicionales para mostrar nombres
    public $nombreAsignatura;
    public $codigoAsignatura;
    public $nombreAsignaturaRequerida;
    public $codigoAsignaturaRequerida;

    public function __construct($idAsignatura = null, $idAsignaturaRequerida = null)
    {
        $this->idAsignatura = $idAsignatura;
        $this->idAsignaturaRequerida = $idAsignaturaRequerida;
    }

    // Hidratar modelo desde array de base de datos
    public function hidratarDesdeArray($data)
    {
        if (isset($data['idPrerrequisito'])) $this->idPrerrequisito = $data['idPrerrequisito'];
        if (isset($data['idAsignatura'])) $this->idAsignatura = $data['idAsignatura'];
        if (isset($data['idAsignaturaRequerida'])) $this->idAsignaturaRequerida = $data['idAsignaturaRequerida'];
        
        return $this;
    }

    // Convertir modelo a array
    public function convertirAArray()
    {
        $data = [
            'idPrerrequisito' => $this->idPrerrequisito,
            'idAsignatura' => $this->idAsignatura,
            'idAsignaturaRequerida' => $this->idAsignaturaRequerida
        ];

        if (isset($this->nombreAsignatura)) {
            $data['nombreAsignatura'] = $this->nombreAsignatura;
            $data['codigoAsignatura'] = $this->codigoAsignatura;
        }
        if (isset($this->nombreAsignaturaRequerida)) {
            $data['nombreAsignaturaRequerida'] = $this->nombreAsignaturaRequerida;
            $data['codigoAsignaturaRequerida'] = $this->codigoAsignaturaRequerida;
        }

        return $data;
    }

    // Verificar si es el mismo prerrequisito
    public function esMismoPrerrequisito($idAsignatura, $idAsignaturaRequerida)
    {
        return $this->idAsignatura == $idAsignatura && $this->idAsignaturaRequerida == $idAsignaturaRequerida;
    }
}
?>