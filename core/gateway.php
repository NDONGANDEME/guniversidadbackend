<?php

require_once __DIR__. "/../utilidades/LimpiarDatos.php";
header('Content-Type: application/json; charset=utf-8');

// 1. Sanitizar ruta y acción (vienen por GET)
$ruta = LimpiarDatos::limpiarRuta($_GET['ruta'] ?? '');
$accion = LimpiarDatos::limpiarParametro($_GET['accion'] ?? 'index');
$actor = ucfirst(LimpiarDatos::limpiarParametro($_GET['actor'] ?? 'index'));

// 2. RECUPERAR TODOS LOS PARÁMETROS DE LA URL Y ALMACENARLOS EN $parametros
$parametros = [];

// Agregar TODOS los parámetros GET (incluyendo ruta y acción, pero ya los tenemos)
foreach ($_GET as $key => $value) {
    // No sobrescribir ruta y acción si ya existen, pero mantener el resto
    if ($key !== 'ruta' && $key !== 'accion' && $key !== 'actor') {
        $parametros[$key] = LimpiarDatos::limpiarParametro($value);
    }
}

// Si hay datos JSON en el body (para peticiones con Content-Type: application/json)
$contenidoJson = file_get_contents("php://input");
if (!empty($contenidoJson)) {
    $datosJson = json_decode($contenidoJson, true);
    if (is_array($datosJson)) {
        $parametrosJson = LimpiarDatos::limpiarArray($datosJson);
        $parametros = array_merge($parametros, $parametrosJson);
    }
}

// Si hay datos POST regulares (para formularios multipart)
if (!empty($_POST)) {
    $parametrosPost = LimpiarDatos::limpiarArray($_POST);
    $parametros = array_merge($parametros, $parametrosPost);
}

// Procesar archivos subidos (MANTIENE PROPIEDADES ORIGINALES)
if (!empty($_FILES)) {
    $archivosProcesados = LimpiarDatos::procesarArchivos($_FILES);
    
    // Agregar los archivos a los parámetros con el mismo nombre del campo
    foreach ($archivosProcesados as $campo => $archivo) {
        // Si el campo se llama 'fotos' o 'formaciones' (o cualquier otro nombre)
        // se guardará como un array en $parametros[$campo] con TODAS las propiedades
        $parametros[$campo] = $archivo;
    }
}

// 3. Validar que la ruta no esté vacía
if (empty($ruta)) {
    http_response_code(400);
    echo json_encode(['error' => 'Ruta inválida']);
    exit;
}

// 3. Validar que la ruta no esté vacía
if (empty($actor)) {
    http_response_code(400);
    echo json_encode(['error' => 'el parametro actor es necesario']);
    exit;
}

// 4. Construir archivo y clase

$archivo = "../{$actor}/controlador/c_{$ruta}.php";
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