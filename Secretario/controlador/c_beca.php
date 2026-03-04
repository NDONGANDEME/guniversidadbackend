<?php
require_once __DIR__ . "/../dao/d_beca.php";
require_once __DIR__ . "/../../utilidades/u_verificaciones.php";
require_once __DIR__ . "/../modelo/m_beca.php";

class BecaController
{
    public static function dispatch($accion, $parametros)
    {
        // Verificar que el actor sea admin para todas estas operaciones
        if (!isset($parametros['actor']) || $parametros['actor'] !== 'admin') {
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
            case "obtenerBecasActivas":
                self::obtenerBecasActivas();
                break;
                
            case "obtenerBecas":
                self::obtenerBecas();
                break;
                
            case "insertarBeca":
                self::insertarBeca($parametros);
                break;
                
            case "eliminarBeca":
                self::eliminarBeca($parametros['id'] ?? null);
                break;
                
            case "actualizarBeca":
                self::actualizarBeca($parametros);
                break;
                
            default:
                echo json_encode([
                    'estado' => 400,
                    'exito' => false,
                    'mensaje' => "Acción '$accion' no válida en controlador de becas",
                    'resultado' => null
                ]);
        }
    }

    // Verificar si hay sesión activa
    private static function verificarSesionActiva()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['usuario_id']) && isset($_SESSION['usuario_correo']);
    }

    // Obtener todas las becas (incluyendo inactivas)
    private static function obtenerBecas()
    {
        $becas = D_Beca::obtenerBecas();
        $resultado = [];
        
        foreach ($becas as $beca) {
            $resultado[] = $beca->convertirAArray();
        }
        
        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Becas obtenidas correctamente',
            'resultado' => $resultado
        ]);
    }

    // Obtener solo becas activas
    private static function obtenerBecasActivas()
    {
        $becas = D_Beca::obtenerBecasActivas();
        $resultado = [];
        
        foreach ($becas as $beca) {
            $resultado[] = $beca->convertirAArray();
        }
        
        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Becas activas obtenidas correctamente',
            'resultado' => $resultado
        ]);
    }

    // Insertar nueva beca
    private static function insertarBeca($parametros)
    {
        // Validar campos obligatorios
        $institucionBeca = $parametros['institucionBeca'] ?? '';
        $tipoBeca = $parametros['tipoBeca'] ?? '';
        $estado = $parametros['estado'] ?? 'activo';
        
        if (empty($institucionBeca) || empty($tipoBeca)) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'Institución y tipo de beca son obligatorios',
                'resultado' => null
            ]);
            return;
        }

        // Verificar si ya existe
        if (D_Beca::existeBeca($institucionBeca, $tipoBeca)) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'Ya existe una beca con esa institución y tipo',
                'resultado' => null
            ]);
            return;
        }

        // Insertar beca
        $becaId = D_Beca::insertarBeca([
            'institucionBeca' => $institucionBeca,
            'tipoBeca' => $tipoBeca,
            'estado' => $estado
        ]);

        if (!$becaId) {
            echo json_encode([
                'estado' => 500,
                'exito' => false,
                'mensaje' => 'Error al crear la beca',
                'resultado' => null
            ]);
            return;
        }

        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Beca creada exitosamente',
            'resultado' => ['id' => $becaId]
        ]);
    }

    // Actualizar beca
    private static function actualizarBeca($parametros)
    {
        $id = $parametros['id'] ?? null;
        
        if (!$id) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'ID de beca no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        // Verificar que existe
        $becaExistente = D_Beca::obtenerBecaPorId($id);
        if (!$becaExistente) {
            echo json_encode([
                'estado' => 404,
                'exito' => false,
                'mensaje' => 'Beca no encontrada',
                'resultado' => null
            ]);
            return;
        }

        // Datos a actualizar
        $institucionBeca = $parametros['institucionBeca'] ?? $becaExistente->institucionBeca;
        $tipoBeca = $parametros['tipoBeca'] ?? $becaExistente->tipoBeca;

        // Verificar si ya existe otra con mismos datos
        if (($institucionBeca != $becaExistente->institucionBeca || $tipoBeca != $becaExistente->tipoBeca) &&
            D_Beca::existeBeca($institucionBeca, $tipoBeca, $id)) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'Ya existe otra beca con esa institución y tipo',
                'resultado' => null
            ]);
            return;
        }

        // Actualizar
        $actualizado = D_Beca::actualizarBeca($id, [
            'institucionBeca' => $institucionBeca,
            'tipoBeca' => $tipoBeca
        ]);

        if (!$actualizado) {
            echo json_encode([
                'estado' => 500,
                'exito' => false,
                'mensaje' => 'Error al actualizar la beca',
                'resultado' => null
            ]);
            return;
        }

        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Beca actualizada exitosamente',
            'resultado' => ['id' => $id]
        ]);
    }

    // Eliminar beca (soft delete)
    private static function eliminarBeca($id)
    {
        if (!$id) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'ID de beca no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        // Verificar que existe
        $becaExistente = D_Beca::obtenerBecaPorId($id);
        if (!$becaExistente) {
            echo json_encode([
                'estado' => 404,
                'exito' => false,
                'mensaje' => 'Beca no encontrada',
                'resultado' => null
            ]);
            return;
        }

        // Eliminar
        $eliminado = D_Beca::eliminarBeca($id);

        if ($eliminado) {
            echo json_encode([
                'estado' => 'exito',
                'exito' => true,
                'mensaje' => 'Beca eliminada exitosamente',
                'resultado' => ['id' => $id]
            ]);
        } else {
            echo json_encode([
                'estado' => 500,
                'exito' => false,
                'mensaje' => 'Error al eliminar la beca',
                'resultado' => null
            ]);
        }
    }
}
?>