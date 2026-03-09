<?php
require_once __DIR__ . "/../dao/d_familiar.php";
require_once __DIR__ . "/../../utilidades/u_verificaciones.php";
require_once __DIR__ . "/../modelo/m_familiar.php";

class FamiliarController
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
            case "obtenerFamiliares":
                self::obtenerFamiliares();
                break;
                
            case "obtenerFamiliarResponsablePorEstudiante":
                self::obtenerFamiliarResponsablePorEstudiante($parametros['idEstudiante'] ?? null);
                break;
                
            case "obtenerFamiliaresPorEstudiante":
                self::obtenerFamiliaresPorEstudiante($parametros['idEstudiante'] ?? null);
                break;
                
            case "insertarFamiliar":
                self::insertarFamiliar($parametros);
                break;
                
            case "actualizarFamiliar":
                self::actualizarFamiliar($parametros);
                break;
                
            case "eliminarFamiliar":
                self::eliminarFamiliar($parametros['id'] ?? null);
                break;
                
            default:
                echo json_encode([
                    'estado' => 400,
                    'exito' => false,
                    'mensaje' => "Acción '$accion' no válida en controlador de familiares",
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

    // Obtener todos los familiares
    private static function obtenerFamiliares()
    {
        $familiares = D_Familiar::obtenerFamiliares();
        $resultado = [];
        
        foreach ($familiares as $familiar) {
            $arr = $familiar->convertirAArray();
            if (isset($familiar->nombreEstudiante)) {
                $arr['nombreEstudiante'] = $familiar->nombreEstudiante;
            }
            $resultado[] = $arr;
        }
        
        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Familiares obtenidos correctamente',
            'resultado' => $resultado
        ]);
    }

    // Obtener familiares por estudiante
    private static function obtenerFamiliaresPorEstudiante($idEstudiante)
    {
        if (!$idEstudiante) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'ID de estudiante no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        $familiares = D_Familiar::obtenerFamiliaresPorEstudiante($idEstudiante);
        $resultado = [];
        
        foreach ($familiares as $familiar) {
            $resultado[] = $familiar->convertirAArray();
        }
        
        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Familiares del estudiante obtenidos correctamente',
            'resultado' => $resultado
        ]);
    }

    // Obtener familiar responsable por estudiante
    private static function obtenerFamiliarResponsablePorEstudiante($idEstudiante)
    {
        if (!$idEstudiante) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'ID de estudiante no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        $familiar = D_Familiar::obtenerFamiliarResponsablePorEstudiante($idEstudiante);
        
        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => $familiar ? 'Familiar responsable obtenido' : 'No hay familiar responsable',
            'resultado' => $familiar ? $familiar->convertirAArray() : null
        ]);
    }

    // Insertar familiar
    private static function insertarFamiliar($parametros)
    {
        // Validar campos obligatorios
        $nombre = $parametros['nombre'] ?? '';
        $apellidos = $parametros['apellidos'] ?? '';
        $idEstudiante = $parametros['idEstudiante'] ?? '';
        
        if (empty($nombre) || empty($apellidos) || empty($idEstudiante)) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'Nombre, apellidos e ID de estudiante son obligatorios',
                'resultado' => null
            ]);
            return;
        }

        // Verificar DIP si se proporciona
        $dipFamiliar = $parametros['dipFamiliar'] ?? '';
        if (!empty($dipFamiliar) && D_Familiar::existeFamiliarPorDip($dipFamiliar)) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'Ya existe un familiar con ese DIP',
                'resultado' => null
            ]);
            return;
        }

        // Preparar datos
        $datos = [
            'nombre' => $nombre,
            'apellidos' => $apellidos,
            'dipFamiliar' => $dipFamiliar,
            'telefono' => $parametros['telefono'] ?? '',
            'correoFamiliar' => $parametros['correoFamiliar'] ?? '',
            'direccion' => $parametros['direccion'] ?? '',
            'parentesco' => $parametros['parentesco'] ?? '',
            'esContactoIncidentes' => $parametros['esContactoIncidentes'],
            'esResponsablePago' => $parametros['esResponsablePago'],
            'idEstudiante' => $idEstudiante
        ];

        // Insertar
        $familiarId = D_Familiar::insertarFamiliar($datos);

        if (!$familiarId) {
            echo json_encode([
                'estado' => 500,
                'exito' => false,
                'mensaje' => 'Error al crear el familiar',
                'resultado' => null
            ]);
            return;
        }

        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Familiar creado exitosamente',
            'resultado' => ['id' => $familiarId]
        ]);
    }

    // Actualizar familiar
    private static function actualizarFamiliar($parametros)
    {
        $id = $parametros['id'] ?? null;
        
        if (!$id) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'ID de familiar no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        // Verificar que existe
        $familiarExistente = D_Familiar::obtenerFamiliarPorId($id);
        if (!$familiarExistente) {
            echo json_encode([
                'estado' => 404,
                'exito' => false,
                'mensaje' => 'Familiar no encontrado',
                'resultado' => null
            ]);
            return;
        }

        // Datos a actualizar
        $dipFamiliar = $parametros['dipFamiliar'] ?? $familiarExistente->dipFamiliar;
        
        // Verificar DIP si cambió
        if (!empty($dipFamiliar) && $dipFamiliar != $familiarExistente->dipFamiliar) {
            if (D_Familiar::existeFamiliarPorDip($dipFamiliar, $id)) {
                echo json_encode([
                    'estado' => 400,
                    'exito' => false,
                    'mensaje' => 'Ya existe otro familiar con ese DIP',
                    'resultado' => null
                ]);
                return;
            }
        }

        // Preparar datos
        $datos = [
            'nombre' => $parametros['nombre'] ?? $familiarExistente->nombre,
            'apellidos' => $parametros['apellidos'] ?? $familiarExistente->apellidos,
            'dipFamiliar' => $dipFamiliar,
            'telefono' => $parametros['telefono'] ?? $familiarExistente->telefono,
            'correoFamiliar' => $parametros['correoFamiliar'] ?? $familiarExistente->correoFamiliar,
            'direccion' => $parametros['direccion'] ?? $familiarExistente->direccion,
            'parentesco' => $parametros['parentesco'] ?? $familiarExistente->parentesco,
            'esContactoIncidentes' => isset($parametros['esContactoIncidentes']) ? (int)$parametros['esContactoIncidentes'] : $familiarExistente->esContactoIncidentes,
            'esResponsablePago' => isset($parametros['esResponsablePago']) ? (int)$parametros['esResponsablePago'] : $familiarExistente->esResponsablePago,
            'idEstudiante' => $parametros['idEstudiante'] ?? $familiarExistente->idEstudiante
        ];

        // Actualizar
        $actualizado = D_Familiar::actualizarFamiliar($id, $datos);

        if (!$actualizado) {
            echo json_encode([
                'estado' => 500,
                'exito' => false,
                'mensaje' => 'Error al actualizar el familiar',
                'resultado' => null
            ]);
            return;
        }

        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Familiar actualizado exitosamente',
            'resultado' => ['id' => $id]
        ]);
    }

    // Eliminar familiar
    private static function eliminarFamiliar($id)
    {
        if (!$id) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'ID de familiar no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        // Verificar que existe
        $familiarExistente = D_Familiar::obtenerFamiliarPorId($id);
        if (!$familiarExistente) {
            echo json_encode([
                'estado' => 404,
                'exito' => false,
                'mensaje' => 'Familiar no encontrado',
                'resultado' => null
            ]);
            return;
        }

        // Eliminar
        $eliminado = D_Familiar::eliminarFamiliar($id);

        if ($eliminado) {
            echo json_encode([
                'estado' => 'exito',
                'exito' => true,
                'mensaje' => 'Familiar eliminado exitosamente',
                'resultado' => ['id' => $id]
            ]);
        } else {
            echo json_encode([
                'estado' => 500,
                'exito' => false,
                'mensaje' => 'Error al eliminar el familiar',
                'resultado' => null
            ]);
        }
    }
}
?>