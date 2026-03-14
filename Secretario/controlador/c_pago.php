<?php
require_once __DIR__ . "/../../utilidades/u_verificaciones.php";
require_once __DIR__ . "/../../utilidades/u_permisos_controlador.php";
require_once __DIR__ . "/../dao/d_pago.php";
require_once __DIR__ . "/../dao/d_matricula.php";
require_once __DIR__ . "/../dao/d_familiar.php";
require_once __DIR__ . "/../modelo/m_pago.php";

class PagoController
{
    public static function dispatch($accion, $parametros)
    {
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
            case "obtenerPagos":
                self::obtenerPagos();
                break;
                
            case "obtenerPagoPorId":
                self::obtenerPagoPorId($parametros['id'] ?? null);
                break;
                
            case "obtenerPagosPorMatricula":
                self::obtenerPagosPorMatricula($parametros['idMatricula'] ?? null);
                break;
                
            case "obtenerPagosPorFamiliar":
                self::obtenerPagosPorFamiliar($parametros['idFamiliar'] ?? null);
                break;
                
            case "insertarPago":
                self::insertarPago($parametros);
                break;
                
            case "actualizarPago":
                self::actualizarPago($parametros);
                break;
                
            case "eliminarPago":
                self::eliminarPago($parametros['id'] ?? null);
                break;
                
            case "obtenerTotalPagado":
                self::obtenerTotalPagado($parametros['idMatricula'] ?? null);
                break;
                
            default:
                echo json_encode([
                    'estado' => 400,
                    'exito' => false,
                    'mensaje' => "Acción '$accion' no válida en controlador de pagos",
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

    // Obtener todos los pagos
    private static function obtenerPagos()
    {
        $pagos = D_Pago::obtenerPagos();
        $resultado = [];
        
        foreach ($pagos as $pago) {
            $resultado[] = $pago->convertirAArray();
        }
        
        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Pagos obtenidos correctamente',
            'resultado' => $resultado
        ]);
    }

    // Obtener pago por ID
    private static function obtenerPagoPorId($id)
    {
        if (!$id) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'ID de pago no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        $pago = D_Pago::obtenerPagoPorId($id);
        
        if ($pago) {
            echo json_encode([
                'estado' => 'exito',
                'exito' => true,
                'mensaje' => 'Pago obtenido correctamente',
                'resultado' => $pago->convertirAArray()
            ]);
        } else {
            echo json_encode([
                'estado' => 404,
                'exito' => false,
                'mensaje' => 'Pago no encontrado',
                'resultado' => null
            ]);
        }
    }

    // Obtener pagos por matrícula
    private static function obtenerPagosPorMatricula($idMatricula)
    {
        if (!$idMatricula) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'ID de matrícula no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        $pagos = D_Pago::obtenerPagosPorMatricula($idMatricula);
        $resultado = [];
        
        foreach ($pagos as $pago) {
            $resultado[] = $pago->convertirAArray();
        }
        
        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Pagos de la matrícula obtenidos correctamente',
            'resultado' => $resultado
        ]);
    }

    // Obtener pagos por familiar
    private static function obtenerPagosPorFamiliar($idFamiliar)
    {
        if (!$idFamiliar) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'ID de familiar no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        $pagos = D_Pago::obtenerPagosPorFamiliar($idFamiliar);
        $resultado = [];
        
        foreach ($pagos as $pago) {
            $resultado[] = $pago->convertirAArray();
        }
        
        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Pagos del familiar obtenidos correctamente',
            'resultado' => $resultado
        ]);
    }

    // Insertar pago
    private static function insertarPago($parametros)
    {
        // Validar campos obligatorios
        $idMatricula = $parametros['idMatricula'] ?? '';
        $monto = $parametros['monto'] ?? '';
        
        if (empty($idMatricula) || empty($monto)) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'Matrícula y monto son obligatorios',
                'resultado' => null
            ]);
            return;
        }

        // Validar que la matrícula existe
        $matricula = D_Matricula::obtenerMatriculaPorId($idMatricula);
        if (!$matricula) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'La matrícula no existe',
                'resultado' => null
            ]);
            return;
        }

        // Validar monto
        if (!is_numeric($monto) || $monto <= 0) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'El monto debe ser un número positivo',
                'resultado' => null
            ]);
            return;
        }

        // Validar familiar si se proporciona
        $idFamiliar = $parametros['idFamiliar'] ?? null;
        if ($idFamiliar) {
            $familiar = D_Familiar::obtenerFamiliarPorId($idFamiliar);
            if (!$familiar) {
                echo json_encode([
                    'estado' => 400,
                    'exito' => false,
                    'mensaje' => 'El familiar no existe',
                    'resultado' => null
                ]);
                return;
            }
        }

        // Preparar datos
        $datos = [
            'idMatricula' => $idMatricula,
            'idFamiliar' => $idFamiliar,
            'cuota' => $parametros['cuota'] ?? 1,
            'monto' => $monto,
            'fechaPago' => $parametros['fechaPago'] ?? date('Y-m-d H:i:s')
        ];

        // Insertar pago
        $pagoId = D_Pago::insertarPago($datos);

        if (!$pagoId) {
            echo json_encode([
                'estado' => 500,
                'exito' => false,
                'mensaje' => 'Error al registrar el pago',
                'resultado' => null
            ]);
            return;
        }

        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Pago registrado exitosamente',
            'resultado' => ['id' => $pagoId]
        ]);
    }

    // Actualizar pago
    private static function actualizarPago($parametros)
    {
        $id = $parametros['idPago'] ?? null;
        
        if (!$id) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'ID de pago no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        // Verificar que el pago existe
        $pagoExistente = D_Pago::obtenerPagoPorId($id);
        if (!$pagoExistente) {
            echo json_encode([
                'estado' => 404,
                'exito' => false,
                'mensaje' => 'Pago no encontrado',
                'resultado' => null
            ]);
            return;
        }

        // Validar monto si se proporciona
        $monto = $parametros['monto'] ?? $pagoExistente->monto;
        if (!is_numeric($monto) || $monto <= 0) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'El monto debe ser un número positivo',
                'resultado' => null
            ]);
            return;
        }

        // Validar familiar si se proporciona
        $idFamiliar = $parametros['idFamiliar'] ?? $pagoExistente->idFamiliar;
        if ($idFamiliar) {
            $familiar = D_Familiar::obtenerFamiliarPorId($idFamiliar);
            if (!$familiar) {
                echo json_encode([
                    'estado' => 400,
                    'exito' => false,
                    'mensaje' => 'El familiar no existe',
                    'resultado' => null
                ]);
                return;
            }
        }

        // Preparar datos
        $datos = [
            'idFamiliar' => $idFamiliar,
            'cuota' => $parametros['cuota'] ?? $pagoExistente->cuota,
            'monto' => $monto
        ];

        // Actualizar pago
        $actualizado = D_Pago::actualizarPago($id, $datos);

        if ($actualizado) {
            echo json_encode([
                'estado' => 'exito',
                'exito' => true,
                'mensaje' => 'Pago actualizado exitosamente',
                'resultado' => ['id' => $id]
            ]);
        } else {
            echo json_encode([
                'estado' => 500,
                'exito' => false,
                'mensaje' => 'Error al actualizar el pago',
                'resultado' => null
            ]);
        }
    }

    // Eliminar pago
    private static function eliminarPago($id)
    {
        if (!$id) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'ID de pago no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        // Verificar que el pago existe
        $pagoExistente = D_Pago::obtenerPagoPorId($id);
        if (!$pagoExistente) {
            echo json_encode([
                'estado' => 404,
                'exito' => false,
                'mensaje' => 'Pago no encontrado',
                'resultado' => null
            ]);
            return;
        }

        // Eliminar pago
        $eliminado = D_Pago::eliminarPago($id);

        if ($eliminado) {
            echo json_encode([
                'estado' => 'exito',
                'exito' => true,
                'mensaje' => 'Pago eliminado exitosamente',
                'resultado' => ['id' => $id]
            ]);
        } else {
            echo json_encode([
                'estado' => 500,
                'exito' => false,
                'mensaje' => 'Error al eliminar el pago',
                'resultado' => null
            ]);
        }
    }

    // Obtener total pagado por matrícula
    private static function obtenerTotalPagado($idMatricula)
    {
        if (!$idMatricula) {
            echo json_encode([
                'estado' => 400,
                'exito' => false,
                'mensaje' => 'ID de matrícula no proporcionado',
                'resultado' => null
            ]);
            return;
        }

        $total = D_Pago::obtenerTotalPagadoPorMatricula($idMatricula);
        
        echo json_encode([
            'estado' => 'exito',
            'exito' => true,
            'mensaje' => 'Total pagado obtenido correctamente',
            'resultado' => [
                'idMatricula' => $idMatricula,
                'totalPagado' => $total
            ]
        ]);
    }
}
?>