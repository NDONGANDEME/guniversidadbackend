<?php
require_once __DIR__ . "/../dao/d_planestudio.php";
require_once __DIR__ . "/../../utilidades/u_verificaciones.php";
require_once __DIR__ . "/../modelo/m_planestudio.php";

class PlanEstudioController
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
            case "obtenerPlanesEstudios":
                self::obtenerPlanesEstudios();
                break;
                
            case "obtenerPlanesEstudioPorCarrera":
                self::obtenerPlanesEstudioPorCarrera($parametros['idCarrera'] ?? null);
                break;
                
            case "insertarPlanEstudio":
                self::insertarPlanEstudio($parametros);
                break;
                
            case "actualizarPlanEstudio":
                self::actualizarPlanEstudio($parametros);
                break;
                
            case "deshabilitarPlanEstudio":
                self::cambiarVigenciaPlanEstudio($parametros['id'] ?? null, 0);
                break;
                
            case "habilitarPlanEstudio":
                self::cambiarVigenciaPlanEstudio($parametros['id'] ?? null, 1);
                break;
                
            default:
                echo json_encode([
                    'estado' => 400,
                    'exito' => false,
                    'mensaje' => "Acción '$accion' no válida en controlador de planes de estudio",
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

    // Obtener todos los planes de estudio
    private static function obtenerPlanesEstudios()
    {
        $planes = D_PlanEstudio::obtenerPlanesEstudios();
        $resultado = [];
        
        foreach ($planes as $plan) {
            $arr = $plan->convertirAArray();
            if (isset($plan->nombreCarrera)) {
                $arr['nombreCarrera'] = $plan->nombreCarrera;
            }
            $resultado[] = $arr;
        }
        
        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Planes de estudio obtenidos correctamente',
            'resultado' => $resultado
        ]);
    }

    // Obtener planes de estudio por carrera
    private static function obtenerPlanesEstudioPorCarrera($idCarrera)
    {
        if (!$idCarrera) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'ID de carrera no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        $planes = D_PlanEstudio::obtenerPlanesEstudioPorCarrera($idCarrera);
        $resultado = [];
        
        foreach ($planes as $plan) {
            $resultado[] = $plan->convertirAArray();
        }
        
        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Planes de estudio por carrera obtenidos correctamente',
            'resultado' => $resultado
        ]);
    }

    // Insertar plan de estudio
    private static function insertarPlanEstudio($parametros)
    {
        // Validar campos obligatorios
        $nombre = $parametros['nombre'] ?? '';
        $idCarrera = $parametros['idCarrera'] ?? '';
        $periodoPlanEstudio = $parametros['periodoPlanEstudio'] ?? '';
        
        if (empty($nombre) || empty($idCarrera) || empty($periodoPlanEstudio)) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'Nombre, carrera y período son obligatorios',
                'resultado' => null
            ]);
            return;
        }

        // Verificar si ya existe
        if (D_PlanEstudio::existePlanEstudio($nombre, $idCarrera)) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'Ya existe un plan de estudio con ese nombre para esta carrera',
                'resultado' => null
            ]);
            return;
        }

        // Preparar datos
        $datos = [
            'nombre' => $nombre,
            'idCarrera' => $idCarrera,
            'fechaElaboracion' => $parametros['fechaElaboracion'] ?? date('Y-m-d'),
            'periodoPlanEstudio' => $periodoPlanEstudio,
            'vigente' => $parametros['vigente']
        ];

        // Insertar
        $planId = D_PlanEstudio::insertarPlanEstudio($datos);

        if (!$planId) {
            echo json_encode([
                'estado' => 500,
                'exito' => false,
                'mensaje' => 'Error al crear el plan de estudio',
                'resultado' => null
            ]);
            return;
        }

        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Plan de estudio creado exitosamente',
            'resultado' => ['id' => $planId]
        ]);
    }

    // Actualizar plan de estudio
    private static function actualizarPlanEstudio($parametros)
    {
        $id = $parametros['idPlanEstudio'] ?? null;
        
        if (!$id) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'ID de plan de estudio no proporcionado',
                'resultado' => $parametros
            ]);
            return;
        }

        // Verificar que existe
        $planExistente = D_PlanEstudio::obtenerPlanEstudioPorId($id);
        if (!$planExistente) {
            echo json_encode([
                'estado' => 404,
                'exito' => false,
                'mensaje' => 'Plan de estudio no encontrado',
                'resultado' => null
            ]);
            return;
        }

        // Datos a actualizar
        $nombre = $parametros['nombre'] ?? $planExistente->nombre;
        $idCarrera = $parametros['idCarrera'] ?? $planExistente->idCarrera;

        // Verificar si ya existe otro con mismo nombre y carrera
        if (($nombre != $planExistente->nombre || $idCarrera != $planExistente->idCarrera) &&
            D_PlanEstudio::existePlanEstudio($nombre, $idCarrera, $id)) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'Ya existe otro plan de estudio con ese nombre para esta carrera',
                'resultado' => null
            ]);
            return;
        }

        // Preparar datos
        $datos = [
            'nombre' => $nombre,
            'idCarrera' => $idCarrera,
            'fechaElaboracion' => $parametros['fechaElaboracion'] ?? $planExistente->fechaElaboracion,
            'periodoPlanEstudio' => $parametros['periodoPlanEstudio'] ?? $planExistente->periodoPlanEstudio
        ];

        // Actualizar
        $actualizado = D_PlanEstudio::actualizarPlanEstudio($id, $datos);

        if (!$actualizado) {
            echo json_encode([
                'estado' => 500,
                'exito' => false,
                'mensaje' => 'Error al actualizar el plan de estudio',
                'resultado' => null
            ]);
            return;
        }

        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Plan de estudio actualizado exitosamente',
            'resultado' => ['id' => $id]
        ]);
    }

    // Cambiar vigencia del plan de estudio
    private static function cambiarVigenciaPlanEstudio($id, $vigente)
    {
        if (!$id) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'ID de plan de estudio no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        // Verificar que existe
        $planExistente = D_PlanEstudio::obtenerPlanEstudioPorId($id);
        if (!$planExistente) {
            echo json_encode([
                'estado' => 404,
                'exito' => false,
                'mensaje' => 'Plan de estudio no encontrado',
                'resultado' => null
            ]);
            return;
        }

        // Cambiar vigencia
        $cambiado = D_PlanEstudio::cambiarVigenciaPlanEstudio($id, $vigente);

        if ($cambiado) {
            $mensaje = $vigente ? 'Plan de estudio habilitado' : 'Plan de estudio deshabilitado';
            echo json_encode([
                'estado' => 'exito',
                'exito' => true,
                'mensaje' => $mensaje,
                'resultado' => ['id' => $id, 'vigente' => $vigente]
            ]);
        } else {
            echo json_encode([
                'estado' => 500,
                'exito' => false,
                'mensaje' => 'Error al cambiar la vigencia del plan de estudio',
                'resultado' => null
            ]);
        }
    }
}
?>