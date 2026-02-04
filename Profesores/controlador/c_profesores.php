<?php

require_once __DIR__ . "/../utilidades/u_verificaciones.php";
require_once __DIR__ . "/../dao/d_profesor.php";

class ProfesorController
{
    public static function dispatch($accion, $parametros)
    {
        if (VerificacionesUtil::validarDispatch($accion, $parametros)) {

            switch ($accion) {
                case "obtenerAsignaturasImpartidas":
                    self::getAsignaturasImpartidas($parametros['idProfesor']);
                    break;
                case "obtenerHorarioProfesor":
                    $idSemestre = isset($parametros['idSemestre']) ? $parametros['idSemestre'] : null;
                    self::getHorarioProfesor($parametros['idProfesor'], $idSemestre);
                    break;
                case "obtenerEstudiantesPorAsignatura":
                    self::getEstudiantesPorAsignatura($parametros['idAsignatura']);
                    break;
                case "obtenerEstudiantesPorClase":
                    self::getEstudiantesPorClase($parametros['idClase']);
                    break;
                case "obtenerEvaluacionesPorAsignatura":
                    self::getEvaluacionesPorAsignatura($parametros['idAsignatura']);
                    break;
                case "crearEvaluacion":
                    self::insertarEvaluacion($parametros);
                    break;
                case "actualizarNotaEvaluacion":
                    self::setNotaEvaluacion($parametros['idEvaluacion'], $parametros['nuevaNota']);
                    break;
                case "crearExamen":
                    self::insertarExamen($parametros);
                    break;
                case "obtenerExamenesPorProfesor":
                    self::getExamenesPorProfesor($parametros['idProfesor']);
                    break;
                case "obtenerFormacionProfesor":
                    self::getFormacionProfesor($parametros['idProfesor']);
                    break;
                case "agregarFormacion":
                    self::insertarFormacionProfesor($parametros);
                    break;
                case "obtenerConsultasRecibidas":
                    self::getConsultasRecibidas($parametros['idUsuarioDestinatario']);
                    break;
                case "crearConsulta":
                    self::insertarConsultaProfesor($parametros);
                    break;
                case "crearInforme":
                    self::insertarInformeProfesor($parametros);
                    break;
                case "obtenerInformesEnviados":
                    self::getInformesEnviados($parametros['idUsuario']);
                    break;
                case "obtenerGuiasPorAsignatura":
                    self::getGuiasPorAsignatura($parametros['idAsignatura']);
                    break;

                default:
                    echo json_encode([
                        'estado' => 400,
                        'éxito' => false,
                        'mensaje' => "Acción '$accion' no válida en el controlador de profesor"
                    ]);
            }
        }
    }


    private static function getAsignaturasImpartidas($idProfesor)
    {
        try {
            $resultado = ProfesorDao::obtenerAsignaturasImpartidas($idProfesor);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function getHorarioProfesor($idProfesor, $idSemestre = null)
    {
        try {
            $resultado = ProfesorDao::obtenerHorarioProfesor($idProfesor, $idSemestre);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }


    private static function getEstudiantesPorAsignatura($idAsignatura)
    {
        try {
            $resultado = ProfesorDao::obtenerEstudiantesPorAsignatura($idAsignatura);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function getEstudiantesPorClase($idClase)
    {
        try {
            $resultado = ProfesorDao::obtenerEstudiantesPorClase($idClase);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }


    private static function getEvaluacionesPorAsignatura($idAsignatura)
    {
        try {
            $resultado = ProfesorDao::obtenerEvaluacionesPorAsignatura($idAsignatura);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function insertarEvaluacion($parametros)
    {
        try {
            $resultado = ProfesorDao::crearEvaluacion($parametros);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function setNotaEvaluacion($idEvaluacion, $nuevaNota)
    {
        try {
            $resultado = ProfesorDao::actualizarNotaEvaluacion($idEvaluacion, $nuevaNota);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }


    private static function insertarExamen($parametros)
    {
        try {
            $resultado = ProfesorDao::crearExamen($parametros);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function getExamenesPorProfesor($idProfesor)
    {
        try {
            $resultado = ProfesorDao::obtenerExamenesPorProfesor($idProfesor);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function getFormacionProfesor($idProfesor)
    {
        try {
            $resultado = ProfesorDao::obtenerFormacionProfesor($idProfesor);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function insertarFormacionProfesor($parametros)
    {
        try {
            $resultado = ProfesorDao::agregarFormacion($parametros);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function getConsultasRecibidas($idUsuarioDestinatario)
    {
        try {
            $resultado = ProfesorDao::obtenerConsultasRecibidas($idUsuarioDestinatario);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function insertarConsultaProfesor($parametros)
    {
        try {
            $resultado = ProfesorDao::crearConsulta($parametros);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function insertarInformeProfesor($parametros)
    {
        try {
            $resultado = ProfesorDao::crearInforme($parametros);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function getInformesEnviados($idUsuario)
    {
        try {
            $resultado = ProfesorDao::obtenerInformesEnviados($idUsuario);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }


    private static function getGuiasPorAsignatura($idAsignatura)
    {
        try {
            $resultado = ProfesorDao::obtenerGuiasPorAsignatura($idAsignatura);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }
}
