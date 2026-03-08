<?php
require_once __DIR__ . "/../dao/d_prerrequisito.php";
require_once __DIR__ . "/../../Admin/dao/d_asignatura.php";
require_once __DIR__ . "/../../utilidades/u_verificaciones.php";
require_once __DIR__ . "/../modelo/m_prerrequisito.php";

class PrerrequisitoController
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
            case "obtenerPrerrequisitos":
                self::obtenerPrerrequisitos();
                break;
                
            case "obtenerPrerrequisitosPorAsignatura":
                self::obtenerPrerrequisitosPorAsignatura($parametros['idAsignatura'] ?? null);
                break;
                
            case "obtenerPrerrequisitoPorId":
                self::obtenerPrerrequisitoPorId($parametros['id'] ?? null);
                break;
                
            case "insertarPrerrequisito":
                self::insertarPrerrequisito($parametros);
                break;
                
            case "eliminarPrerrequisito":
                self::eliminarPrerrequisito($parametros['id'] ?? null);
                break;
                
            case "eliminarPrerrequisitosPorAsignatura":
                self::eliminarPrerrequisitosPorAsignatura($parametros['idAsignatura'] ?? null);
                break;
                
            default:
                echo json_encode([
                    'estado' => 400,
                    'exito' => false,
                    'mensaje' => "Acción '$accion' no válida en controlador de prerrequisitos",
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

    // Obtener todos los prerrequisitos
    private static function obtenerPrerrequisitos()
    {
        $prerrequisitos = D_Prerrequisito::obtenerPrerrequisitos();
        $resultado = [];
        
        foreach ($prerrequisitos as $prerrequisito) {
            $resultado[] = $prerrequisito->convertirAArray();
        }
        
        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Prerrequisitos obtenidos correctamente',
            'resultado' => $resultado
        ]);
    }

    // Obtener prerrequisitos por asignatura
    private static function obtenerPrerrequisitosPorAsignatura($idAsignatura)
    {
        if (!$idAsignatura) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'ID de asignatura no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        // Verificar que la asignatura existe
        $asignatura = D_Asignatura::obtenerAsignaturaPorId($idAsignatura);
        if (!$asignatura) {
            echo json_encode([
                'estado' => 404,
                'exito' => false,
                'mensaje' => 'Asignatura no encontrada',
                'resultado' => null
            ]);
            return;
        }

        $prerrequisitos = D_Prerrequisito::obtenerPrerrequisitosPorAsignatura($idAsignatura);
        $resultado = [];
        
        foreach ($prerrequisitos as $prerrequisito) {
            $resultado[] = $prerrequisito->convertirAArray();
        }
        
        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Prerrequisitos de la asignatura obtenidos correctamente',
            'resultado' => $resultado
        ]);
    }

    // Obtener prerrequisito por ID
    private static function obtenerPrerrequisitoPorId($id)
    {
        if (!$id) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'ID de prerrequisito no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        $prerrequisito = D_Prerrequisito::obtenerPrerrequisitoPorId($id);
        
        if ($prerrequisito) {
            // Obtener nombres de las asignaturas
            $asignatura = D_Asignatura::obtenerAsignaturaPorId($prerrequisito->idAsignatura);
            $asignaturaRequerida = D_Asignatura::obtenerAsignaturaPorId($prerrequisito->idAsignaturaRequerida);
            
            $resultado = $prerrequisito->convertirAArray();
            if ($asignatura) {
                $resultado['nombreAsignatura'] = $asignatura->nombreAsignatura;
                $resultado['codigoAsignatura'] = $asignatura->codigoAsignatura;
            }
            if ($asignaturaRequerida) {
                $resultado['nombreAsignaturaRequerida'] = $asignaturaRequerida->nombreAsignatura;
                $resultado['codigoAsignaturaRequerida'] = $asignaturaRequerida->codigoAsignatura;
            }
            
            echo json_encode([
                'estado' => 'exito',
                'exito' => true,
                'mensaje' => 'Prerrequisito obtenido correctamente',
                'resultado' => $resultado
            ]);
        } else {
            echo json_encode([
                'estado' => 404,
                'exito' => false,
                'mensaje' => 'Prerrequisito no encontrado',
                'resultado' => null
            ]);
        }
    }

    // Insertar prerrequisito
    private static function insertarPrerrequisito($parametros)
    {
        // Validar campos obligatorios
        $idAsignatura = $parametros['idAsignatura'] ?? '';
        $idAsignaturaRequerida = $parametros['idAsignaturaRequerida'] ?? '';
        
        if (empty($idAsignatura) || empty($idAsignaturaRequerida)) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'Asignatura y asignatura requerida son obligatorias',
                'resultado' => null
            ]);
            return;
        }

        // Validar que las asignaturas existan
        $asignatura = D_Asignatura::obtenerAsignaturaPorId($idAsignatura);
        if (!$asignatura) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'La asignatura no existe',
                'resultado' => null
            ]);
            return;
        }

        $asignaturaRequerida = D_Asignatura::obtenerAsignaturaPorId($idAsignaturaRequerida);
        if (!$asignaturaRequerida) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'La asignatura requerida no existe',
                'resultado' => null
            ]);
            return;
        }

        // Verificar que no sea la misma asignatura
        if ($idAsignatura == $idAsignaturaRequerida) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'Una asignatura no puede ser prerrequisito de sí misma',
                'resultado' => null
            ]);
            return;
        }

        // Verificar si ya existe
        if (D_Prerrequisito::existePrerrequisito($idAsignatura, $idAsignaturaRequerida)) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'Este prerrequisito ya existe',
                'resultado' => null
            ]);
            return;
        }

        // Verificar prerrequisitos circulares
        if (D_Prerrequisito::existePrerrequisitoCircular($idAsignatura, $idAsignaturaRequerida)) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'No se puede crear un prerrequisito circular',
                'resultado' => null
            ]);
            return;
        }

        // Preparar datos
        $datos = [
            'idAsignatura' => $idAsignatura,
            'idAsignaturaRequerida' => $idAsignaturaRequerida
        ];

        // Insertar
        $prerrequisitoId = D_Prerrequisito::insertarPrerrequisito($datos);

        if (!$prerrequisitoId) {
            echo json_encode([
                'estado' => 500,
                'exito' => false,
                'mensaje' => 'Error al crear el prerrequisito',
                'resultado' => null
            ]);
            return;
        }

        // Obtener el prerrequisito creado
        $nuevoPrerrequisito = D_Prerrequisito::obtenerPrerrequisitoPorId($prerrequisitoId);
        $resultado = $nuevoPrerrequisito->convertirAArray();
        $resultado['nombreAsignatura'] = $asignatura->nombreAsignatura;
        $resultado['codigoAsignatura'] = $asignatura->codigoAsignatura;
        $resultado['nombreAsignaturaRequerida'] = $asignaturaRequerida->nombreAsignatura;
        $resultado['codigoAsignaturaRequerida'] = $asignaturaRequerida->codigoAsignatura;

        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Prerrequisito creado exitosamente',
            'resultado' => $resultado
        ]);
    }

    // Eliminar prerrequisito
    private static function eliminarPrerrequisito($id)
    {
        if (!$id) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'ID de prerrequisito no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        // Verificar que existe
        $prerrequisitoExistente = D_Prerrequisito::obtenerPrerrequisitoPorId($id);
        if (!$prerrequisitoExistente) {
            echo json_encode([
                'estado' => 404,
                'exito' => false,
                'mensaje' => 'Prerrequisito no encontrado',
                'resultado' => null
            ]);
            return;
        }

        // Eliminar
        $eliminado = D_Prerrequisito::eliminarPrerrequisito($id);

        if ($eliminado) {
            echo json_encode([
                'estado' => 'exito',
                'exito' => true,
                'mensaje' => 'Prerrequisito eliminado exitosamente',
                'resultado' => ['id' => $id]
            ]);
        } else {
            echo json_encode([
                'estado' => 500,
                'exito' => false,
                'mensaje' => 'Error al eliminar el prerrequisito',
                'resultado' => null
            ]);
        }
    }

    // Eliminar todos los prerrequisitos de una asignatura
    private static function eliminarPrerrequisitosPorAsignatura($idAsignatura)
    {
        if (!$idAsignatura) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'ID de asignatura no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        // Verificar que la asignatura existe
        $asignatura = D_Asignatura::obtenerAsignaturaPorId($idAsignatura);
        if (!$asignatura) {
            echo json_encode([
                'estado' => 404,
                'exito' => false,
                'mensaje' => 'Asignatura no encontrada',
                'resultado' => null
            ]);
            return;
        }

        // Eliminar
        $eliminado = D_Prerrequisito::eliminarPrerrequisitosPorAsignatura($idAsignatura);

        if ($eliminado) {
            echo json_encode([
                'estado' => 'exito',
                'exito' => true,
                'mensaje' => 'Prerrequisitos eliminados exitosamente',
                'resultado' => ['idAsignatura' => $idAsignatura]
            ]);
        } else {
            echo json_encode([
                'estado' => 500,
                'exito' => false,
                'mensaje' => 'Error al eliminar los prerrequisitos',
                'resultado' => null
            ]);
        }
    }
}
?>
