<?php
session_start();

require_once "utilidades/LimpiarDatos.php";

// Verificar si el usuario está logueado y tiene rol
if (!isset($_SESSION['usuario']) || !isset($_SESSION['rol'])) {
    header("Location: ../index.html");
    exit;
}
// Limpiar el parámetro 'ruta' que llega por GET
$ruta = LimpiarDatos::limpiarRuta($_GET['ruta'] ?? '');
// Definir la ruta base del proyecto
define("BASE_PATH", realpath(__DIR__ . "/../../..") . "/guniversidad/");

// Array de rutas permitidas por rol
$rutas = [
    'admin' => BASE_PATH . 'admin/index.php',
    'secretario' => BASE_PATH . 'secretario/index.php',
    'profesor' => BASE_PATH . 'profesor/index.php',
    'estudiante' => BASE_PATH . 'estudiante/index.php',
    'global' => BASE_PATH . 'frontend/global/index.php'
];

// Verificar si la ruta solicitada existe
if (!isset($rutas[$ruta])) {
    header("Location: ../index.html");
    exit;
}

// Control de acceso según rol
$rolUsuario = $_SESSION['rol'];

// Definir roles permitidos para cada ruta
$rolesPermitidos = [
    'admin' => ['admin'],
    'secretario' => ['secretario'],
    'profesor' => ['profesor'],
    'estudiante' => ['estudiante'],
    'global' => ['admin','secretario','profesor','estudiante'] // todos pueden acceder
];

// Verificar si el rol del usuario tiene permiso
if (!in_array($rolUsuario, $rolesPermitidos[$ruta])) {
   // header("HTTP/1.1 403 Forbidden");
    header("Location: ../error.html");
    exit;
}

// Incluir la ruta correspondiente de forma segura
require_once $rutas[$ruta];