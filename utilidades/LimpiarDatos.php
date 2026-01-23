<?php
class LimpiarDatos
{
    /**
     * Validar rutas: solo letras, números y guiones bajos
     */
    public static function limpiarRuta($ruta)
    {
        $ruta = trim($ruta);
        if (preg_match('/^[a-zA-Z0-9_]+$/', $ruta)) {
            return $ruta;
        }
        return ''; // inválida → devolver vacío o lanzar excepción
    }

    /**
     * Sanitizar parámetros generales (texto libre)
     */
    public static function limpiarParametro($valor)
    {
        // Eliminar espacios y slashes
        $valor = trim($valor);
        $valor = stripslashes($valor);

        // Quitar etiquetas HTML y PHP
        $valor = strip_tags($valor);

        // Convertir caracteres especiales en entidades HTML
        $valor = htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');

        return $valor;
    }

    /**
     * Sanitizar todos los parámetros de un array (ej: $_GET, $_POST)
     */
    public static function limpiarArray($array)
    {
        return array_map([self::class, 'limpiarParametro'], $array);
    }
}
