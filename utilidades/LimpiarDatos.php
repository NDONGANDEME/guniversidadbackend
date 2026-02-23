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

    // ... (tus funciones existentes) ...

    /**
     * NUEVO: Generar ID único para archivos
     * @param string $prefijo Prefijo para el archivo (Foto_, Documento_, etc)
     * @return string Nombre único con prefijo
     */
    public static function generarIdUnicoArchivo($prefijo = '')
    {
        // Generar ID único: prefijo + timestamp + bin2hex + extension
        $timestamp = time();
        $random = bin2hex(random_bytes(8));
        return $prefijo . $timestamp . '_' . $random;
    }

    /**
     * NUEVO: Procesar y guardar archivos de noticias
     * @param array $archivos Array de archivos del campo 'fotos' o 'documentos'
     * @param string $tipo 'fotos' o 'documentos'
     * @param int $noticiaId ID de la noticia para referencia
     * @return array Array con los nombres de los archivos guardados
     */
    public static function procesarArchivosNoticia($archivos, $tipo, $noticiaId)
    {
        $archivosGuardados = [];
        
        if (empty($archivos)) {
            return $archivosGuardados;
        }

        // Determinar carpeta y prefijo según tipo
        $carpeta = ($tipo === 'fotos') ? 'imagenes' : 'documentos';
        $prefijo = ($tipo === 'fotos') ? 'Foto_' : 'Documento_';
        
        $rutaBase = __DIR__ . "/../../uploads/{$carpeta}/";
        
        // Crear carpeta si no existe
        if (!file_exists($rutaBase)) {
            mkdir($rutaBase, 0777, true);
        }

        // Si es un array de archivos (múltiples)
        if (isset($archivos[0]) && is_array($archivos[0])) {
            foreach ($archivos as $archivo) {
                $nombreGuardado = self::guardarArchivoIndividual(
                    $archivo, 
                    $rutaBase, 
                    $prefijo,
                    $noticiaId
                );
                if ($nombreGuardado) {
                    $archivosGuardados[] = $nombreGuardado;
                }
            }
        } else {
            // Archivo único
            $nombreGuardado = self::guardarArchivoIndividual(
                $archivos, 
                $rutaBase, 
                $prefijo,
                $noticiaId
            );
            if ($nombreGuardado) {
                $archivosGuardados[] = $nombreGuardado;
            }
        }

        return $archivosGuardados;
    }

    /**
     * NUEVO: Guardar archivo individual
     */
    private static function guardarArchivoIndividual($archivo, $rutaBase, $prefijo, $noticiaId)
    {
        // Validar que el archivo se subió correctamente
        if ($archivo['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        // Obtener extensión
        $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
        
        // Generar nombre único: prefijo + noticiaId + timestamp + random
        $timestamp = time();
        $random = bin2hex(random_bytes(8));
        $nombreUnico = $prefijo . $noticiaId . '_' . $timestamp . '_' . $random . '.' . $extension;
        
        $rutaCompleta = $rutaBase . $nombreUnico;

        // Mover el archivo usando tmp_name original
        if (move_uploaded_file($archivo['tmp_name'], $rutaCompleta)) {
            return $nombreUnico;
        }

        return null;
    }

    /**
     * NUEVO: Validar que los archivos sean del tipo correcto
     */
    public static function validarArchivosNoticia($archivos, $tipo)
    {
        if (empty($archivos)) {
            return true; // No hay archivos, válido
        }

        $tiposPermitidos = ($tipo === 'fotos') 
            ? ['image/jpeg', 'image/png', 'image/gif', 'image/webp']
            : ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];

        $extensionesPermitidas = ($tipo === 'fotos')
            ? ['jpg', 'jpeg', 'png', 'gif', 'webp']
            : ['pdf', 'doc', 'docx'];

        // Si es array de archivos
        if (isset($archivos[0]) && is_array($archivos[0])) {
            foreach ($archivos as $archivo) {
                if (!self::validarArchivoIndividual($archivo, $tiposPermitidos, $extensionesPermitidas)) {
                    return false;
                }
            }
        } else {
            // Archivo único
            return self::validarArchivoIndividual($archivos, $tiposPermitidos, $extensionesPermitidas);
        }

        return true;
    }

    /**
     * NUEVO: Validar archivo individual
     */
    private static function validarArchivoIndividual($archivo, $tiposPermitidos, $extensionesPermitidas)
    {
        // Validar que no haya error
        if ($archivo['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        // Validar tamaño (10MB máximo)
        if ($archivo['size'] > 10 * 1024 * 1024) {
            return false;
        }

        $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
        
        // Validar por extensión
        if (!in_array($extension, $extensionesPermitidas)) {
            return false;
        }

        // Validar por tipo MIME (si es necesario)
        if (!in_array($archivo['type'], $tiposPermitidos)) {
            // Algunos navegadores envían application/octet-stream para .docx
            if ($extension === 'docx' && $archivo['type'] !== 'application/octet-stream') {
                return false;
            }
        }

        return true;
    }

}
?>