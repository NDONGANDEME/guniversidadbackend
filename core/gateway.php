<?php

require_once "utilidades/LimpiarDatos.php";
header('Content-Type: application/json; charset=utf-8');

// 1. Sanitizar ruta y acción
$ruta = LimpiarDatos::limpiarRuta($_GET['ruta'] ?? '');
$accion = LimpiarDatos::limpiarParametro($_GET['accion'] ?? 'index');

// 2. Sanitizar todos los parámetros
$datos = json_decode(file_get_contents("php://input"), true);
$parametros = LimpiarDatos::limpiarArray($datos);
// 3. Validar que la ruta no esté vacía
if (empty($ruta)) {
    http_response_code(400);
    echo json_encode(['error' => 'Ruta inválida']);
    exit;
}
// 4. Construir archivo y clase
$archivo = "controlador/c_{$ruta}.php";
if (!file_exists($archivo)) {
    http_response_code(404);
    echo json_encode(['error' => 'Controlador no encontrado']);
    exit;
}
require_once $archivo;
$className = ucfirst($ruta).'Controller';
if (!class_exists($className)) {
    http_response_code(500);
    echo json_encode(['error' => "Clase $className no encontrada"]);
    exit;
}

$ctrl = new $className();

// 5. Verificar que exista el método dispatch
if (!method_exists($ctrl, 'dispatch')) {
    http_response_code(500);
    echo json_encode(['error' => "Método dispatch no definido en $className"]);
    exit;
}
// 6. Ejecutar acción con manejo de errores
try {
    $ctrl->dispatch($accion, $parametros);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Error interno',
        'message' => $e->getMessage()
    ]);
}
?>