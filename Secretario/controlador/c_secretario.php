<?php

require_once __DIR__ . "/../utilidades/u_verificaciones.php";
require_once __DIR__ . "/../dao/d_secretario.php";

class SecretarioController
{
    public static function dispatch($accion, $parametros)
    {
        if (VerificacionesUtil::validarDispatch($accion, $parametros)) {

            switch ($accion) {
                case "insertarProfesor":
                    self::insertarProfesor($parametros);
                    break;
                case "actualizarProfesor":
                    self::setDatosProfesor($parametros);
                    break;
                case "desabilitarProfesor":
                    self::eliminarProfesor($parametros['idProfesor']);
                    break;
                case "obtenerTodosProfesores":
                    self::getProfesores();
                    break;
                case "obtenerProfesor":
                    self::getProfesorById($parametros['idProfesor']);
                    break;
                case "buscarProfesores":
                    self::buscarProfesores($parametros['criterioBusqueda']);
                    break;
                case "insertarFormacion":
                    self::insertarFormacion($parametros);
                    break;
                case "actualizarFormacion":
                    self::setDatosFormacion($parametros);
                    break;
                case "eliminarFormacion":
                    self::eliminarFormacion($parametros['idFormacion']);
                    break;
                case "obtenerFormacionesPorProfesor":
                    self::getFormacionesByProfesor($parametros['idProfesor']);
                    break;
                case "insertarEstudiante":
                    self::insertarEstudiante($parametros);
                    break;
                case "actualizarEstudiante":
                    self::setEstudiante($parametros);
                    break;
                case "desabilitarEstudiante":
                    self::eliminarEstudiante($parametros['idEstudiante']);
                    break;
                case "obtenerEstudiantePorCodigo":
                    self::getEstudianteByCodigo($parametros['CodigoEstudiante']);
                    break;
                case "obtenerEstudiantesPorCurso":
                    self::getEstudiantesByCurso($parametros['idCurso']);
                    break;
                case "buscarEstudiantes":
                    self::buscarEstudiante($parametros['criterioBusqueda']);
                    break;
                case "obtenerEstudiantesPorCarrera":
                    self::getEstudiantesByCarrera($parametros['idCarrera']);
                    break;
                case "obtenerEstudiantesPorAsignatura":
                    self::getEstudiantesByAsignatura($parametros['idAsignatura']);
                    break;
                case "obtenerEstudiantesPorFacultad":
                    self::getEstudiantesByFacultad($parametros['idFacultad']);
                    break;
                case "obtenerEstudiantes":
                    self::getEstudiantes();
                    break;
                case "obtenerFamiliaresPorEstudiante":
                    self::getFamiliaresByEstudiantes($parametros['idEstudiante']);
                    break;
                case "insertarFamiliar":
                    self::insertarFamiliar($parametros);
                    break;
                case "actualizarFamiliar":
                    self::setFamiliar($parametros);
                    break;
                case "eliminarFamiliar":
                    self::eliminarFamiliar($parametros['idFamiliar']);
                    break;
                case "insertarMatricula":
                    self::insertarMatricula($parametros);
                    break;
                case "obtenerMatriculasPorEstudiante":
                    self::getMatriculasByEstudiante($parametros['idEstudiante']);
                    break;
                case "buscarMatriculas":
                    self::buscarMatriculas($parametros['criterioBusqueda']);
                    break;
                case "asignarBeca":
                    self::asignarBeca($parametros);
                    break;
                case "obtenerBecas":
                    self::getBecas();
                    break;
                case "buscarBecas":
                    self::buscarBecas($parametros['criterioBusqueda']);
                    break;
                case "insertarBecario":
                    self::insertarBecario($parametros);
                    break;
                case "obtenerBecarios":
                    self::getBecarios();
                    break;
                case "matricularEstudianteAsignatura":
                    self::matricularEstudianteAsignatura($parametros['idEstudianteConvocatoria'], $parametros['idAsignatura'], $parametros['convocatoria']);
                    break;
                case "obtenerAsignaturasPorEstudiante":
                    self::getAsignaturasByEstudiante($parametros['idEstudiante']);
                    break;
                case "consultarHistorialAcademico":
                    self::consultarHistorialAcademico($parametros['idEstudiante']);
                    break;
                case "insertarPlanEstudio":
                    self::insertarPlanEstudio($parametros);
                    break;
                case "obtenerPlanesEstudio":
                    self::getPlanesEstudio();
                    break;
                case "buscarPlanesEstudio":
                    self::buscarPlanesEstudio($parametros['criterioBusqueda']);
                    break;
                case "obtenerCursosPorPlanEstudio":
                    self::getCursosByPlanEstudio($parametros['idPlanEstudio']);
                    break;
                case "insertarAsignatura":
                    self::insertarAsignatura($parametros);
                    break;
                case "obtenerAsignaturas":
                    self::getAsignaturas();
                    break;
                case "buscarAsignaturas":
                    self::buscarAsignaturas($parametros['criterioBusqueda']);
                    break;
                case "consultarOfertaAcademica":
                    self::consultarOfertaAcademica($parametros['idCarrera']);
                    break;
                case "insertarClase":
                    self::insertarClase($parametros);
                    break;
                case "obtenerClases":
                    self::getClases();
                    break;
                case "buscarClases":
                    self::buscarClases($parametros['criterioBusqueda']);
                    break;
                case "insertarPago":
                    self::insertarPago($parametros);
                    break;
                case "obtenerPagos":
                    self::getPagos();
                    break;
                case "buscarPagos":
                    self::buscarPagos($parametros['criterioBusqueda']);
                    break;
                case "consultarEstadoPago":
                    self::consultarEstadoPago($parametros['idEstudiante']);
                    break;
                case "insertarConsulta":
                    self::insertarConsulta($parametros);
                    break;
                case "obtenerConsultas":
                    self::getConsultas();
                    break;
                case "buscarConsultas":
                    self::buscarConsultas($parametros['criterioBusqueda']);
                    break;
                case "insertarGuiaDidactica":
                    self::insertarGuiaDidactica($parametros);
                    break;
                case "obtenerGuiasPorAsignatura":
                    self::getGuiasByAsignatura($parametros['idAsignatura']);
                    break;
                case "buscarGuias":
                    self::buscarGuias($parametros['criterioBusqueda']);
                    break;
                case "insertarInforme":
                    self::insertarInforme($parametros);
                    break;
                case "obtenerInformes":
                    self::getInformes();
                    break;
                case "buscarInformes":
                    self::buscarInformes($parametros['criterioBusqueda']);
                    break;
                case "consultarEstadisticasAcademicas":
                    self::consultarEstadisticasAcademicas($parametros['anoAcademico'] ?? null);
                    break;
                case "insertarFacultad":
                    self::insertarFacultad($parametros);
                    break;
                case "obtenerFacultades":
                    self::getFacultades();
                    break;
                case "buscarFacultades":
                    self::buscarFacultades($parametros['criterioBusqueda']);
                    break;
                case "insertarCarrera":
                    self::insertarCarrera($parametros);
                    break;
                case "obtenerCarreras":
                    self::getCarreras();
                    break;
                case "buscarCarreras":
                    self::buscarCarreras($parametros['criterioBusqueda']);
                    break;
                case "insertarAula":
                    self::insertarAula($parametros);
                    break;
                case "obtenerAulas":
                    self::getAulas();
                    break;
                case "buscarAulas":
                    self::buscarAulas($parametros['criterioBusqueda']);
                    break;
                case "insertarHorario":
                    self::insertarHorario($parametros);
                    break;
                case "obtenerHorarios":
                    self::getHorarios();
                    break;
                case "buscarHorarios":
                    self::buscarHorarios($parametros['criterioBusqueda']);
                    break;
                case "insertarCurso":
                    self::insertarCurso($parametros);
                    break;
                case "obtenerCursos":
                    self::getCursos();
                    break;
                case "buscarCursos":
                    self::buscarCursos($parametros['criterioBusqueda']);
                    break;
                case "insertarSemestre":
                    self::insertarSemestre($parametros);
                    break;
                case "obtenerSemestres":
                    self::getSemestres();
                    break;
                case "buscarSemestres":
                    self::buscarSemestres($parametros['criterioBusqueda']);
                    break;
                case "cargarProfesoresParaSelect":
                    self::cargarProfesoresParaSelect();
                    break;
                case "cargarEstudiantesParaSelect":
                    self::cargarEstudiantesParaSelect();
                    break;
                case "cargarBecariosParaSelect":
                    self::cargarBecariosParaSelect();
                    break;
                case "obtenerResumenEstadistico":
                    self::getResumenEstadistico();
                    break;
                case "obtenerUltimasNoticias":
                    self::getUltimasNoticias($parametros['limite'] ?? 5);
                    break;
                case "obtenerProfesoresPorDepartamento":
                    self::getProfesoresByDepartamento($parametros['idDepartamento']);
                    break;
                default:
                    echo json_encode([
                        'estado' => 400,
                        'éxito' => false,
                        'mensaje' => "Acción '$accion' no válida en el controlador de secretario"
                    ]);
            }
        }
    }

    private static function getProfesorById($idProfesor)
    {
        try {
            $resultado = SecretarioDao::obtenerProfesorPorId($idProfesor);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function insertarProfesor($parametros)
    {
        try {
            $resultado = SecretarioDao::insertarProfesor($parametros);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function eliminarProfesor($idProfesor)
    {
        try {
            $resultado = SecretarioDao::eliminarProfesor($idProfesor);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function getProfesores()
    {
        try {
            $resultado = SecretarioDao::obtenerProfesores();
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function setDatosProfesor($parametros)
    {
        try {
            $resultado = SecretarioDao::actualizarProfesor($parametros);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function buscarProfesores($criterioBusqueda)
    {
        try {
            $resultado = SecretarioDao::buscarProfesores($criterioBusqueda);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function insertarFormacion($parametros)
    {
        try {
            $resultado = SecretarioDao::insertarFormacion($parametros);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function eliminarFormacion($idFormacion)
    {
        try {
            $resultado = SecretarioDao::eliminarFormacion($idFormacion);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function getFormacionesByProfesor($idProfesor)
    {
        try {
            $resultado = SecretarioDao::obtenerFormacionesPorProfesor($idProfesor);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function setDatosFormacion($parametros)
    {
        try {
            $resultado = SecretarioDao::actualizarFormacion($parametros);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    // FUNCIONES PARA LA GESTIÓN DE ESTUDIANTES
    private static function insertarEstudiante($parametros)
    {
        try {
            $resultado = SecretarioDao::insertarEstudiante($parametros);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function eliminarEstudiante($idEstudiante)
    {
        try {
            $resultado = SecretarioDao::eliminarEstudiante($idEstudiante);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function getEstudianteByCodigo($codigoEstudiante)
    {
        try {
            $resultado = SecretarioDao::obtenerEstudiantePorCodigo($codigoEstudiante);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function getEstudiantesByCurso($idCurso)
    {
        try {
            $resultado = SecretarioDao::obtenerEstudiantesPorCurso($idCurso);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function getEstudiantesByCarrera($idCarrera)
    {
        try {
            $resultado = SecretarioDao::obtenerEstudiantesPorCarrera($idCarrera);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function getEstudiantesByFacultad($idFacultad)
    {
        try {
            $resultado = SecretarioDao::obtenerEstudiantesPorFacultad($idFacultad);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function getEstudiantesByAsignatura($idAsignatura)
    {
        try {
            $resultado = SecretarioDao::obtenerEstudiantesPorAsignatura($idAsignatura);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function setEstudiante($parametros)
    {
        try {
            $resultado = SecretarioDao::actualizarEstudiante($parametros);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function buscarEstudiante($criterioBusqueda)
    {
        try {
            $resultado = SecretarioDao::buscarEstudiantes($criterioBusqueda);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function getEstudiantes()
    {
        try {
            $resultado = SecretarioDao::obtenerEstudiantes();
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }


    private static function getFamiliaresByEstudiantes($idEstudiante)
    {
        try {
            $resultado = SecretarioDao::obtenerFamiliaresPorEstudiante($idEstudiante);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function insertarFamiliar($parametros)
    {
        try {
            $resultado = SecretarioDao::insertarFamiliar($parametros);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function setFamiliar($parametros)
    {
        try {
            $resultado = SecretarioDao::actualizarFamiliar($parametros);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function eliminarFamiliar($idFamiliar)
    {
        try {
            $resultado = SecretarioDao::eliminarFamiliar($idFamiliar);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }


    private static function insertarMatricula($parametros)
    {
        try {
            $resultado = SecretarioDao::insertarMatricula($parametros);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function getMatriculasByEstudiante($idEstudiante)
    {
        try {
            $resultado = SecretarioDao::obtenerMatriculasPorEstudiante($idEstudiante);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function buscarMatriculas($criterioBusqueda)
    {
        try {
            $resultado = SecretarioDao::buscarMatriculas($criterioBusqueda);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function asignarBeca($parametros)
    {
        try {
            $resultado = SecretarioDao::asignarBeca($parametros);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function getBecas()
    {
        try {
            $resultado = SecretarioDao::obtenerBecas();
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function buscarBecas($criterioBusqueda)
    {
        try {
            $resultado = SecretarioDao::buscarBecas($criterioBusqueda);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function insertarBecario($parametros)
    {
        try {
            $resultado = SecretarioDao::insertarBecario($parametros);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function getBecarios()
    {
        try {
            $resultado = SecretarioDao::obtenerBecarios();
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function matricularEstudianteAsignatura($idEstudianteConvocatoria, $idAsignatura, $convocatoria)
    {
        try {
            $resultado = SecretarioDao::matricularEstudianteAsignatura($idEstudianteConvocatoria, $idAsignatura, $convocatoria);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function getAsignaturasByEstudiante($idEstudiante)
    {
        try {
            $resultado = SecretarioDao::obtenerAsignaturasPorEstudiante($idEstudiante);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function consultarHistorialAcademico($idEstudiante)
    {
        try {
            $resultado = SecretarioDao::consultarHistorialAcademico($idEstudiante);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function insertarPlanEstudio($parametros)
    {
        try {
            $resultado = SecretarioDao::insertarPlanEstudio($parametros);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function getPlanesEstudio()
    {
        try {
            $resultado = SecretarioDao::obtenerPlanesEstudio();
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function buscarPlanesEstudio($criterioBusqueda)
    {
        try {
            $resultado = SecretarioDao::buscarPlanesEstudio($criterioBusqueda);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function getCursosByPlanEstudio($idPlanEstudio)
    {
        try {
            $resultado = SecretarioDao::obtenerCursosPorPlanEstudio($idPlanEstudio);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function insertarAsignatura($parametros)
    {
        try {
            $resultado = SecretarioDao::insertarAsignatura($parametros);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function getAsignaturas()
    {
        try {
            $resultado = SecretarioDao::obtenerAsignaturas();
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function buscarAsignaturas($criterioBusqueda)
    {
        try {
            $resultado = SecretarioDao::buscarAsignaturas($criterioBusqueda);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function consultarOfertaAcademica($idCarrera)
    {
        try {
            $resultado = SecretarioDao::consultarOfertaAcademica($idCarrera);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function insertarClase($parametros)
    {
        try {
            $resultado = SecretarioDao::insertarClase($parametros);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function getClases()
    {
        try {
            $resultado = SecretarioDao::obtenerClases();
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function buscarClases($criterioBusqueda)
    {
        try {
            $resultado = SecretarioDao::buscarClases($criterioBusqueda);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function insertarPago($parametros)
    {
        try {
            $resultado = SecretarioDao::insertarPago($parametros);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function getPagos()
    {
        try {
            $resultado = SecretarioDao::obtenerPagos();
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function buscarPagos($criterioBusqueda)
    {
        try {
            $resultado = SecretarioDao::buscarPagos($criterioBusqueda);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function consultarEstadoPago($idEstudiante)
    {
        try {
            $resultado = SecretarioDao::consultarEstadoPago($idEstudiante);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function insertarConsulta($parametros)
    {
        try {
            $resultado = SecretarioDao::insertarConsulta($parametros);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function getConsultas()
    {
        try {
            $resultado = SecretarioDao::obtenerConsultas();
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function buscarConsultas($criterioBusqueda)
    {
        try {
            $resultado = SecretarioDao::buscarConsultas($criterioBusqueda);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function insertarGuiaDidactica($parametros)
    {
        try {
            $resultado = SecretarioDao::insertarGuiaDidactica($parametros);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function getGuiasByAsignatura($idAsignatura)
    {
        try {
            $resultado = SecretarioDao::obtenerGuiasPorAsignatura($idAsignatura);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function buscarGuias($criterioBusqueda)
    {
        try {
            $resultado = SecretarioDao::buscarGuias($criterioBusqueda);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function insertarInforme($parametros)
    {
        try {
            $resultado = SecretarioDao::insertarInforme($parametros);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function getInformes()
    {
        try {
            $resultado = SecretarioDao::obtenerInformes();
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function buscarInformes($criterioBusqueda)
    {
        try {
            $resultado = SecretarioDao::buscarInformes($criterioBusqueda);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function consultarEstadisticasAcademicas($anoAcademico = null)
    {
        try {
            $resultado = SecretarioDao::consultarEstadisticasAcademicas($anoAcademico);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function insertarFacultad($parametros)
    {
        try {
            $resultado = SecretarioDao::insertarFacultad($parametros);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function getFacultades()
    {
        try {
            $resultado = SecretarioDao::obtenerFacultades();
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function buscarFacultades($criterioBusqueda)
    {
        try {
            $resultado = SecretarioDao::buscarFacultades($criterioBusqueda);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function insertarCarrera($parametros)
    {
        try {
            $resultado = SecretarioDao::insertarCarrera($parametros);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function getCarreras()
    {
        try {
            $resultado = SecretarioDao::obtenerCarreras();
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function buscarCarreras($criterioBusqueda)
    {
        try {
            $resultado = SecretarioDao::buscarCarreras($criterioBusqueda);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function insertarAula($parametros)
    {
        try {
            $resultado = SecretarioDao::insertarAula($parametros);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function getAulas()
    {
        try {
            $resultado = SecretarioDao::obtenerAulas();
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function buscarAulas($criterioBusqueda)
    {
        try {
            $resultado = SecretarioDao::buscarAulas($criterioBusqueda);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function insertarHorario($parametros)
    {
        try {
            $resultado = SecretarioDao::insertarHorario($parametros);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function getHorarios()
    {
        try {
            $resultado = SecretarioDao::obtenerHorarios();
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function buscarHorarios($criterioBusqueda)
    {
        try {
            $resultado = SecretarioDao::buscarHorarios($criterioBusqueda);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function insertarCurso($parametros)
    {
        try {
            $resultado = SecretarioDao::insertarCurso($parametros);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function getCursos()
    {
        try {
            $resultado = SecretarioDao::obtenerCursos();
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function buscarCursos($criterioBusqueda)
    {
        try {
            $resultado = SecretarioDao::buscarCursos($criterioBusqueda);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function insertarSemestre($parametros)
    {
        try {
            $resultado = SecretarioDao::insertarSemestre($parametros);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function getSemestres()
    {
        try {
            $resultado = SecretarioDao::obtenerSemestres();
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function buscarSemestres($criterioBusqueda)
    {
        try {
            $resultado = SecretarioDao::buscarSemestres($criterioBusqueda);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function cargarProfesoresParaSelect()
    {
        try {
            $resultado = SecretarioDao::cargarProfesoresParaSelect();
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function cargarEstudiantesParaSelect()
    {
        try {
            $resultado = SecretarioDao::cargarEstudiantesParaSelect();
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function cargarBecariosParaSelect()
    {
        try {
            $resultado = SecretarioDao::cargarBecariosParaSelect();
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function getResumenEstadistico()
    {
        try {
            $resultado = SecretarioDao::obtenerResumenEstadistico();
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function getUltimasNoticias($limite)
    {
        try {
            $resultado = SecretarioDao::obtenerUltimasNoticias($limite);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }

    private static function getProfesoresByDepartamento($idDepartamento)
    {
        try {
            $resultado = SecretarioDao::obtenerProfesoresPorDepartamento($idDepartamento);
            echo json_encode(['estado' => 'exito', 'resultado' => $resultado]);
        } catch (Exception $e) {
            echo json_encode(['estado' => 'fracaso', 'mensaje' => $e->getMessage()]);
        }
    }
}
