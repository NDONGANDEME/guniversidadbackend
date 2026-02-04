<?php
require_once __DIR__ . "/../utilidades/u_verificaciones.php";
require_once __DIR__ . "/../dao/d_estudiante.php";

class EstudianteController
{
    public static function dispatch($accion, $parametros)
    {
        if (VerificacionesUtil::validarDispatch($accion, $parametros)) {
            switch ($accion) {
                case "obtenerInformacionPersonal":
                    self::getInformacionPersonal($parametros['idEstudiante']);
                    break;
                case "obtenerInformacionPersonalPorCodigo":
                    self::getInformacionPersonalPorCodigo($parametros['codigoEstudiante']);
                    break;
                case "actualizarInformacionPersonal":
                    self::setInformacionPersonal($parametros);
                    break;
                case "obtenerPlanEstudios":
                    self::getPlanEstudios($parametros['idCarrera'], $parametros['idSemestre'] ?? null);
                    break;
                case "obtenerHistorialAcademico":
                    self::getHistorialAcademico($parametros['idEstudiante']);
                    break;
                case "obtenerDatosPagos":
                    self::getDatosPagos($parametros['idEstudiante']);
                    break;
                case "obtenerEstadoPago":
                    self::getEstadoPago($parametros['idEstudiante']);
                    break;
                case "obtenerInfoBeca":
                    self::getInfoBeca($parametros['idEstudiante']);
                    break;
                case "obtenerCalificaciones":
                    self::getCalificaciones($parametros['idEstudiante']);
                    break;
                case "obtenerPromedioPorAsignatura":
                    self::getPromedioPorAsignatura($parametros['idEstudiante'], $parametros['idAsignatura']);
                    break;
                case "obtenerHorariosExamenes":
                    self::getHorariosExamenes($parametros['idEstudiante']);
                    break;
                case "obtenerHorarioClases":
                    self::getHorarioClases($parametros['idEstudiante']);
                    break;
                case "obtenerProfesoresAsignaturas":
                    self::getProfesoresAsignaturas($parametros['idEstudiante']);
                    break;
                case "obtenerConsultasRecibidas":
                    self::getConsultasRecibidas($parametros['idUsuarioDestinatario']);
                    break;
                case "obtenerConsultasEnviadas":
                    self::getConsultasEnviadas($parametros['idEmisor']);
                    break;
                case "obtenerGuiasDidacticas":
                    self::getGuiasDidacticas($parametros['idEstudiante']);
                    break;
                case "obtenerAsignaturasMatriculadas":
                    self::getAsignaturasMatriculadas($parametros['idEstudiante'], $parametros['idSemestre'] ?? null);
                    break;
                case "obtenerInfoMatricula":
                    self::getInfoMatricula($parametros['idEstudiante']);
                    break;
                case "obtenerFamiliares":
                    self::getFamiliares($parametros['idEstudiante']);
                    break;
                case "obtenerEstadisticasAcademicas":
                    self::getEstadisticasAcademicas($parametros['idEstudiante']);
                    break;
                case "obtenerInfoCuenta":
                    self::getInfoCuenta($parametros['idEstudiante']);
                    break;
                case "obtenerNotasPorAsignatura":
                    self::getNotasPorAsignatura($parametros['idEstudiante'], $parametros['idAsignatura']);
                    break;
                case "obtenerProximosExamenes":
                    $limite = $parametros['limite'] ?? 5;
                    self::getProximosExamenes($parametros['idEstudiante'], $limite);
                    break;
                case "obtenerAsistencia":
                    self::getAsistencia($parametros['idEstudiante'], $parametros['idAsignatura'] ?? null);
                    break;
                default:
                    echo json_encode([
                        'estado' => 400,
                        'éxito' => false,
                        'mensaje' => "Acción '$accion' no válida en el controlador de estudiante"
                    ]);
            }
        }
    }

    private static function getInformacionPersonal($idEstudiante)
    {
        try {
            $resultado = EstudianteDAO::obtenerInformacionPersonal($idEstudiante);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function getInformacionPersonalPorCodigo($codigoEstudiante)
    {
        try {
            $resultado = EstudianteDAO::obtenerInformacionPersonalPorCodigo($codigoEstudiante);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function setInformacionPersonal($parametros)
    {
        try {
            $resultado = EstudianteDAO::actualizarInformacionPersonal($parametros);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function getPlanEstudios($idCarrera, $idSemestre = null)
    {
        try {
            $resultado = EstudianteDAO::obtenerPlanEstudios($idCarrera, $idSemestre);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function getHistorialAcademico($idEstudiante)
    {
        try {
            $resultado = EstudianteDAO::obtenerHistorialAcademico($idEstudiante);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function getDatosPagos($idEstudiante)
    {
        try {
            $resultado = EstudianteDAO::obtenerDatosPagos($idEstudiante);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function getEstadoPago($idEstudiante)
    {
        try {
            $resultado = EstudianteDAO::obtenerEstadoPago($idEstudiante);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function getInfoBeca($idEstudiante)
    {
        try {
            $resultado = EstudianteDAO::obtenerInfoBeca($idEstudiante);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function getCalificaciones($idEstudiante)
    {
        try {
            $resultado = EstudianteDAO::obtenerCalificaciones($idEstudiante);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function getPromedioPorAsignatura($idEstudiante, $idAsignatura)
    {
        try {
            $resultado = EstudianteDAO::obtenerPromedioPorAsignatura($idEstudiante, $idAsignatura);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function getHorariosExamenes($idEstudiante)
    {
        try {
            $resultado = EstudianteDAO::obtenerHorariosExamenes($idEstudiante);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function getHorarioClases($idEstudiante)
    {
        try {
            $resultado = EstudianteDAO::obtenerHorarioClases($idEstudiante);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function getProfesoresAsignaturas($idEstudiante)
    {
        try {
            $resultado = EstudianteDAO::obtenerProfesoresAsignaturas($idEstudiante);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function getConsultasRecibidas($idUsuarioDestinatario)
    {
        try {
            $resultado = EstudianteDAO::obtenerConsultasRecibidas($idUsuarioDestinatario);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function getConsultasEnviadas($idEmisor)
    {
        try {
            $resultado = EstudianteDAO::obtenerConsultasEnviadas($idEmisor);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function getGuiasDidacticas($idEstudiante)
    {
        try {
            $resultado = EstudianteDAO::obtenerGuiasDidacticas($idEstudiante);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function getAsignaturasMatriculadas($idEstudiante, $idSemestre = null)
    {
        try {
            $resultado = EstudianteDAO::obtenerAsignaturasMatriculadas($idEstudiante, $idSemestre);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function getInfoMatricula($idEstudiante)
    {
        try {
            $resultado = EstudianteDAO::obtenerInfoMatricula($idEstudiante);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function getFamiliares($idEstudiante)
    {
        try {
            $resultado = EstudianteDAO::obtenerFamiliares($idEstudiante);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function getEstadisticasAcademicas($idEstudiante)
    {
        try {
            $resultado = EstudianteDAO::obtenerEstadisticasAcademicas($idEstudiante);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function getInfoCuenta($idEstudiante)
    {
        try {
            $resultado = EstudianteDAO::obtenerInfoCuenta($idEstudiante);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function getNotasPorAsignatura($idEstudiante, $idAsignatura)
    {
        try {
            $resultado = EstudianteDAO::obtenerNotasPorAsignatura($idEstudiante, $idAsignatura);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function getProximosExamenes($idEstudiante, $limite = 5)
    {
        try {
            $resultado = EstudianteDAO::obtenerProximosExamenes($idEstudiante, $limite);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function getAsistencia($idEstudiante, $idAsignatura = null)
    {
        try {
            $resultado = EstudianteDAO::obtenerAsistencia($idEstudiante, $idAsignatura);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }
}