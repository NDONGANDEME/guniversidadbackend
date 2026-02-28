<?php
require_once __DIR__ . "/../utilidades/u_verificaciones.php";
require_once __DIR__ . "/../dao/d_departamento.php";
require_once __DIR__ . "/../modelo/m_departamento.php";

class DepartamentoController
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

        switch ($accion) {
            case "obtenerDepartamentos":
                self::obtenerDepartamentos();
                break;
                
            case "insertarDepartamento":
                self::insertarDepartamento($parametros);
                break;
                
            case "actualizarDepartamento":
                self::actualizarDepartamento($parametros);
                break;
                
            default:
                echo json_encode([
                    'estado' => 400,
                    'exito' => false,
                    'mensaje' => "Acción '$accion' no válida en el controlador de departamentos",
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

    // Obtener todos los departamentos
    private static function obtenerDepartamentos()
    {
        if (!self::verificarSesionActiva()) {
            echo json_encode([
                'estado' => 401,
                'exito' => false,
                'mensaje' => 'No hay sesión activa',
                'resultado' => null
            ]);
            return;
        }

        $departamentos = D_Departamento::obtenerDepartamentos();
        $resultado = [];
        
        foreach ($departamentos as $departamento) {
            $arr = $departamento->convertirAArray();
            if (isset($departamento->nombreFacultad)) {
                $arr['nombreFacultad'] = $departamento->nombreFacultad;
            }
            $resultado[] = $arr;
        }
        
        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Departamentos obtenidos correctamente',
            'resultado' => $resultado
        ]);
    }

    // Insertar nuevo departamento
    private static function insertarDepartamento($parametros)
    {
        if (!self::verificarSesionActiva()) {
            echo json_encode([
                'estado' => 401,
                'exito' => false,
                'mensaje' => 'No hay sesión activa',
                'resultado' => null
            ]);
            return;
        }

        // Validar campos obligatorios
        $nombreDepartamento = $parametros['nombreDepartamento'] ?? '';
        $idFacultad = $parametros['idFacultad'] ?? '';

        $errores = [];
        
        if (empty($nombreDepartamento)) {
            $errores[] = 'Nombre de departamento es obligatorio';
        }
        
        if (empty($idFacultad)) {
            $errores[] = 'Facultad es obligatoria';
        }

        if (!empty($errores)) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'Errores de validación',
                'resultado' => ['errores' => $errores]
            ]);
            return;
        }

        // Verificar si ya existe el departamento
        if (D_Departamento::existeDepartamento($nombreDepartamento)) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'Ya existe un departamento con ese nombre',
                'resultado' => null
            ]);
            return;
        }

        // Insertar departamento
        $departamentoId = D_Departamento::insertarDepartamento([
            'nombreDepartamento' => $nombreDepartamento,
            'idFacultad' => $idFacultad
        ]);

        if (!$departamentoId) {
            echo json_encode([
                'estado' => 500,
                'exito' => false,
                'mensaje' => 'Error al crear el departamento',
                'resultado' => null
            ]);
            return;
        }

        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Departamento creado exitosamente',
            'resultado' => ['id' => $departamentoId]
        ]);
    }

    // Actualizar departamento existente
    private static function actualizarDepartamento($parametros)
    {
        if (!self::verificarSesionActiva()) {
            echo json_encode([
                'estado' => 401,
                'exito' => false,
                'mensaje' => 'No hay sesión activa',
                'resultado' => null
            ]);
            return;
        }

        $id = $parametros['id'] ?? null;
        
        if (!$id) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'ID de departamento no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        // Verificar que el departamento existe
        $departamentoExistente = D_Departamento::obtenerDepartamentoPorId($id);
        if (!$departamentoExistente) {
            echo json_encode([
                'estado' => 404,
                'exito' => false,
                'mensaje' => 'Departamento no encontrado',
                'resultado' => null
            ]);
            return;
        }

        // Datos a actualizar
        $nombreDepartamento = $parametros['nombreDepartamento'] ?? $departamentoExistente->nombreDepartamento;
        $idFacultad = $parametros['idFacultad'] ?? $departamentoExistente->idFacultad;

        // Validaciones
        $errores = [];
        
        if (empty($nombreDepartamento)) {
            $errores[] = 'Nombre de departamento es obligatorio';
        }

        // Verificar si ya existe otro departamento con ese nombre
        if ($nombreDepartamento != $departamentoExistente->nombreDepartamento && 
            D_Departamento::existeDepartamento($nombreDepartamento, $id)) {
            $errores[] = 'Ya existe otro departamento con ese nombre';
        }

        if (!empty($errores)) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'Errores de validación',
                'resultado' => ['errores' => $errores]
            ]);
            return;
        }

        // Actualizar departamento
        $actualizado = D_Departamento::actualizarDepartamento([
            'id' => $id,
            'nombreDepartamento' => $nombreDepartamento,
            'idFacultad' => $idFacultad
        ]);

        if (!$actualizado) {
            echo json_encode([
                'estado' => 500,
                'exito' => false,
                'mensaje' => 'Error al actualizar el departamento',
                'resultado' => null
            ]);
            return;
        }

        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Departamento actualizado exitosamente',
            'resultado' => ['id' => $id]
        ]);
    }
}
?>