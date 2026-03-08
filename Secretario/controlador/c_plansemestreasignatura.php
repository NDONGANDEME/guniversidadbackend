<?php
require_once __DIR__ . "/../dao/d_plansemestreasignatura.php";
require_once __DIR__ . "/../dao/d_planestudio.php";
require_once __DIR__ . "/../../Admin/dao/d_semestre.php";
require_once __DIR__ . "/../../Admin/dao/d_asignatura.php";
require_once __DIR__ . "/../../utilidades/u_verificaciones.php";
require_once __DIR__ . "/../modelo/m_plansemestreasignatura.php";

class PlanSemestreAsignaturaController
{
    public static function dispatch($accion, $parametros)
    {
        // Verificar que el actor sea admin
        if (!isset($parametros['actor']) || $parametros['actor'] !== 'secretario') {
            echo json_encode([
                'estado' => 403,
                'exito' => false,
                'mensaje' => 'Acceso denegado. Se requiere rol de administrador.',
                'resultado' => null
            ]);
            return;
        }

        // Verificar sesión activa
        if (!self::verificarSesionActiva()) {
            echo json_encode([
                'estado' => 401,
                'exito' => false,
                'mensaje' => 'No hay sesión activa',
                'resultado' => null
            ]);
            return;
        }

        switch ($accion) {
            case "obtenerPlanSemestreAsignaturas":
                self::obtenerPlanSemestreAsignaturas();
                break;
                
            case "obtenerPlanSemestreAsignaturaPorId":
                self::obtenerPlanSemestreAsignaturaPorId($parametros['id'] ?? null);
                break;
                
            case "obtenerPlanSemestreAsignaturaPorPlanEstudio":
                self::obtenerPlanSemestreAsignaturaPorPlanEstudio($parametros['idPlanEstudio'] ?? null);
                break;
                
            case "insertarPlanSemestreAsignatura":
                self::insertarPlanSemestreAsignatura($parametros);
                break;
                
            case "actualizarPlanSemestreAsignatura":
                self::actualizarPlanSemestreAsignatura($parametros);
                break;
                
            case "eliminarPlanSemestreAsignatura":
                self::eliminarPlanSemestreAsignatura($parametros['id'] ?? null);
                break;
                
            default:
                echo json_encode([
                    'estado' => 400,
                    'exito' => false,
                    'mensaje' => "Acción '$accion' no válida en controlador de plan semestre asignatura",
                    'resultado' => null
                ]);
        }
    }

    // Verificar sesión activa
    private static function verificarSesionActiva()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['usuario_id']) && isset($_SESSION['usuario_correo']);
    }

    // Obtener todos los registros
    private static function obtenerPlanSemestreAsignaturas()
    {
        $registros = D_PlanSemestreAsignatura::obtenerPlanSemestreAsignaturas();
        $resultado = [];
        
        foreach ($registros as $registro) {
            $arr = $registro->convertirAArray();
            if (isset($registro->nombrePlanEstudio)) {
                $arr['nombrePlanEstudio'] = $registro->nombrePlanEstudio;
            }
            if (isset($registro->semestre)) {
                $arr['semestre'] = $registro->semestre;
            }
            if (isset($registro->asignatura)) {
                $arr['asignatura'] = $registro->asignatura;
            }
            $resultado[] = $arr;
        }
        
        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Registros obtenidos correctamente',
            'resultado' => $resultado
        ]);
    }

    // Obtener por ID
    private static function obtenerPlanSemestreAsignaturaPorId($id)
    {
        if (!$id) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'ID no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        $registro = D_PlanSemestreAsignatura::obtenerPlanSemestreAsignaturaPorId($id);
        
        if ($registro) {
            $resultado = $registro->convertirAArray();
            
            echo json_encode([
                'estado' => 'exito',
                'exito' => true,
                'mensaje' => 'Registro obtenido correctamente',
                'resultado' => $resultado
            ]);
        } else {
            echo json_encode([
                'estado' => 404,
                'exito' => false,
                'mensaje' => 'Registro no encontrado',
                'resultado' => null
            ]);
        }
    }

    // Obtener por plan de estudio
    private static function obtenerPlanSemestreAsignaturaPorPlanEstudio($idPlanEstudio)
    {
        if (!$idPlanEstudio) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'ID de plan de estudio no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        $registros = D_PlanSemestreAsignatura::obtenerPlanSemestreAsignaturaPorPlanEstudio($idPlanEstudio);
        $resultado = [];
        
        foreach ($registros as $registro) {
            $arr = $registro->convertirAArray();
            if (isset($registro->semestre)) {
                $arr['semestre'] = $registro->semestre;
            }
            if (isset($registro->asignatura)) {
                $arr['asignatura'] = $registro->asignatura;
            }
            $resultado[] = $arr;
        }
        
        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Registros obtenidos correctamente',
            'resultado' => $resultado
        ]);
    }

    // Insertar
    private static function insertarPlanSemestreAsignatura($parametros)
    {
        // Validar campos obligatorios
        $idPlanEstudio = $parametros['idPlanEstudio'] ?? '';
        $idSemestre = $parametros['idSemestre'] ?? '';
        $idAsignatura = $parametros['idAsignatura'] ?? '';
        $creditos = $parametros['creditos'] ?? '';
        
        if (empty($idPlanEstudio) || empty($idSemestre) || empty($idAsignatura) || empty($creditos)) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'Todos los campos son obligatorios',
                'resultado' => null
            ]);
            return;
        }

        // Validar créditos
        if (!is_numeric($creditos) || $creditos <= 0) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'Los créditos deben ser un número positivo',
                'resultado' => null
            ]);
            return;
        }

        // Verificar si ya existe
        if (D_PlanSemestreAsignatura::existeAsignacion($idPlanEstudio, $idSemestre, $idAsignatura)) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'Esta asignación ya existe',
                'resultado' => null
            ]);
            return;
        }

        // Preparar datos
        $datos = [
            'idPlanEstudio' => $idPlanEstudio,
            'idSemestre' => $idSemestre,
            'idAsignatura' => $idAsignatura,
            'creditos' => $creditos,
            'modalidad' => $parametros['modalidad'] ?? ''
        ];

        // Insertar
        $registroId = D_PlanSemestreAsignatura::insertarPlanSemestreAsignatura($datos);

        if (!$registroId) {
            echo json_encode([
                'estado' => 500,
                'exito' => false,
                'mensaje' => 'Error al crear el registro',
                'resultado' => null
            ]);
            return;
        }

        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Registro creado exitosamente',
            'resultado' => ['id' => $registroId]
        ]);
    }

    // Actualizar
    private static function actualizarPlanSemestreAsignatura($parametros)
    {
        $id = $parametros['id'] ?? null;
        
        if (!$id) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'ID no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        // Verificar que existe
        $registroExistente = D_PlanSemestreAsignatura::obtenerPlanSemestreAsignaturaPorId($id);
        if (!$registroExistente) {
            echo json_encode([
                'estado' => 404,
                'exito' => false,
                'mensaje' => 'Registro no encontrado',
                'resultado' => null
            ]);
            return;
        }

        // Validar créditos
        $creditos = $parametros['creditos'] ?? $registroExistente->creditos;
        if (!is_numeric($creditos) || $creditos <= 0) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'Los créditos deben ser un número positivo',
                'resultado' => null
            ]);
            return;
        }

        // Verificar si ya existe otra asignación igual
        $idSemestre = $parametros['idSemestre'] ?? $registroExistente->idSemestre;
        $idAsignatura = $parametros['idAsignatura'] ?? $registroExistente->idAsignatura;
        
        if (($idSemestre != $registroExistente->idSemestre || $idAsignatura != $registroExistente->idAsignatura) &&
            D_PlanSemestreAsignatura::existeAsignacion($registroExistente->idPlanEstudio, $idSemestre, $idAsignatura, $id)) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'Ya existe otra asignación con esos datos',
                'resultado' => null
            ]);
            return;
        }

        // Preparar datos
        $datos = [
            'idSemestre' => $idSemestre,
            'idAsignatura' => $idAsignatura,
            'creditos' => $creditos,
            'modalidad' => $parametros['modalidad'] ?? $registroExistente->modalidad
        ];

        // Actualizar
        $actualizado = D_PlanSemestreAsignatura::actualizarPlanSemestreAsignatura($id, $datos);

        if ($actualizado) {
            echo json_encode([
                'estado' => 'exito',
                'exito' => true,
                'mensaje' => 'Registro actualizado exitosamente',
                'resultado' => ['id' => $id]
            ]);
        } else {
            echo json_encode([
                'estado' => 500,
                'exito' => false,
                'mensaje' => 'Error al actualizar el registro',
                'resultado' => null
            ]);
        }
    }

    // Eliminar
    private static function eliminarPlanSemestreAsignatura($id)
    {
        if (!$id) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'ID no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        // Verificar que existe
        $registroExistente = D_PlanSemestreAsignatura::obtenerPlanSemestreAsignaturaPorId($id);
        if (!$registroExistente) {
            echo json_encode([
                'estado' => 404,
                'exito' => false,
                'mensaje' => 'Registro no encontrado',
                'resultado' => null
            ]);
            return;
        }

        // Eliminar (usando la función que añadimos al DAO)
        $eliminado = D_PlanSemestreAsignatura::eliminarPlanSemestreAsignatura($id);

        if ($eliminado) {
            echo json_encode([
                'estado' => 'exito',
                'exito' => true,
                'mensaje' => 'Registro eliminado exitosamente',
                'resultado' => ['id' => $id]
            ]);
        } else {
            echo json_encode([
                'estado' => 500,
                'exito' => false,
                'mensaje' => 'Error al eliminar el registro',
                'resultado' => null
            ]);
        }
    }
}
?>