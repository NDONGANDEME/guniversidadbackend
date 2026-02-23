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
     * Sanitizar todos los parámetros de un array
     */
    public static function limpiarArray($array)
    {
        if (!is_array($array)) {
            return [];
        }
        
        $limpio = [];
        foreach ($array as $key => $value) {
            $keyLimpio = self::limpiarParametro($key);
            if (is_array($value)) {
                $limpio[$keyLimpio] = self::limpiarArray($value);
            } else {
                $limpio[$keyLimpio] = self::limpiarParametro($value);
            }
        }
        return $limpio;
    }

    /**
     * Procesar archivos subidos - MANTIENE TODAS LAS PROPIEDADES ORIGINALES
     * @param array $archivos Array $_FILES
     * @return array Array con los archivos procesados organizados por campo
     */
    public static function procesarArchivos($archivos)
    {
        $resultado = [];
        
        if (empty($archivos)) {
            return $resultado;
        }

        foreach ($archivos as $campo => $archivo) {
            // Limpiar el nombre del campo
            $campoLimpio = self::limpiarParametro($campo);
            
            // Si es un array de archivos (múltiples)
            if (is_array($archivo['name'])) {
                $resultado[$campoLimpio] = [];
                for ($i = 0; $i < count($archivo['name']); $i++) {
                    // Crear array con TODAS las propiedades originales
                    $archivoOriginal = [
                        'name' => $archivo['name'][$i],
                        'type' => $archivo['type'][$i],
                        'tmp_name' => $archivo['tmp_name'][$i],
                        'error' => $archivo['error'][$i],
                        'size' => $archivo['size'][$i]
                    ];
                    
                    // Validar tipo (pero mantener propiedades originales)
                    if (self::validarTipoArchivo($archivoOriginal)) {
                        $resultado[$campoLimpio][] = $archivoOriginal;
                    }
                }
            } else {
                // Archivo único - mantener TODAS las propiedades originales
                $archivoOriginal = [
                    'name' => $archivo['name'],
                    'type' => $archivo['type'],
                    'tmp_name' => $archivo['tmp_name'],
                    'error' => $archivo['error'],
                    'size' => $archivo['size']
                ];
                
                // Validar tipo (pero mantener propiedades originales)
                if (self::validarTipoArchivo($archivoOriginal)) {
                    $resultado[$campoLimpio] = $archivoOriginal;
                }
            }
        }
        
        return $resultado;
    }

    /**
     * Validar tipo de archivo permitido (AHORA INCLUYE WORD)
     */
    private static function validarTipoArchivo($archivo)
    {
        // Si hay error, no validar tipo (el error ya indica problema)
        if ($archivo['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
        $tipo = $archivo['type'];
        
        // Tipos permitidos para imágenes
        $tiposImagen = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $extensionesImagen = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        // Tipos permitidos para documentos (PDF y WORD)
        $tiposDocumento = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-word',
            'application/word',
            'application/octet-stream' // Algunos navegadores envían este para .docx
        ];
        $extensionesDocumento = ['pdf', 'doc', 'docx'];
        
        // Validar por extensión primero (más fiable)
        if (in_array($extension, $extensionesImagen)) {
            return true;
        }
        
        if (in_array($extension, $extensionesDocumento)) {
            return true;
        }
        
        // Si la extensión no coincide, validar por tipo MIME
        if (in_array($tipo, $tiposImagen) || in_array($tipo, $tiposDocumento)) {
            return true;
        }
        
        return false;
    }
}
?>