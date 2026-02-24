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
        return ''; // inválida → devolver vacío
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
                    
                    // Validar tipo básico (imagen o pdf)
                    if (self::validarTipoBasico($archivoOriginal)) {
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
                
                // Validar tipo básico (imagen o pdf)
                if (self::validarTipoBasico($archivoOriginal)) {
                    $resultado[$campoLimpio] = $archivoOriginal;
                }
            }
        }
        
        return $resultado;
    }

    /**
     * Validación básica: solo imágenes y PDFs
     */
    private static function validarTipoBasico($archivo)
    {
        // Si hay error, no validar tipo
        if ($archivo['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
        
        // Extensiones permitidas
        $extensionesPermitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf'];
        
        return in_array($extension, $extensionesPermitidas);
    }

    /**
     * CONSTANTES PARA CARPETAS
     */
    const CARPETA_IMAGENES = "/../../htdocs/guniversidadfrontend/public/imagenes/";
    const CARPETA_DOCUMENTOS = "/../../htdocs/guniversidadfrontend/public/documentos/";

    /**
     * Generar nombre único para archivo
     * @param string $prefijo Prefijo (ej: 'Foto_', 'Documento_')
     * @param int $registroId ID del registro
     * @param string $extension Extensión del archivo
     * @return string Nombre único
     */
    public static function generarNombreUnico($prefijo, $registroId, $extension)
    {
        $timestamp = time();
        $random = bin2hex(random_bytes(8));
        return $prefijo . $registroId . '_' . $timestamp . '_' . $random . '.' . $extension;
    }

    /**
     * Guardar archivo (foto o documento)
     * @param array $archivo Datos del archivo (de $_FILES)
     * @param string $tipo 'foto' o 'documento'
     * @param int $registroId ID del registro asociado
     * @return string|null Nombre del archivo guardado o null si falla
     */
    public static function guardarArchivo($archivo, $tipo, $registroId)
    {
        // Validar que el archivo se subió correctamente
        if ($archivo['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        // Determinar carpeta y prefijo según tipo
        if ($tipo === 'foto') {
            $carpeta = __DIR__ . self::CARPETA_IMAGENES;
            $prefijo = 'Foto_';
        } else if ($tipo === 'documento') {
            $carpeta = __DIR__ . self::CARPETA_DOCUMENTOS;
            $prefijo = 'Documento_';
        } else {
            return null;
        }

        // Crear carpeta si no existe
        if (!file_exists($carpeta)) {
            mkdir($carpeta, 0777, true);
        }

        // Obtener extensión
        $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
        
        // Generar nombre único
        $nombreUnico = self::generarNombreUnico($prefijo, $registroId, $extension);
        
        $rutaCompleta = $carpeta . $nombreUnico;

        // Mover el archivo
        if (move_uploaded_file($archivo['tmp_name'], $rutaCompleta)) {
            return $nombreUnico;
        }

        return null;
    }

    /**
     * Guardar múltiples archivos
     * @param array $archivos Array de archivos
     * @param string $tipo 'foto' o 'documento'
     * @param int $registroId ID del registro asociado
     * @return array Nombres de los archivos guardados
     */
    public static function guardarMultiplesArchivos($archivos, $tipo, $registroId)
    {
        $archivosGuardados = [];
        
        if (empty($archivos)) {
            return $archivosGuardados;
        }

        // Si es un array de archivos (múltiples)
        if (isset($archivos[0]) && is_array($archivos[0])) {
            foreach ($archivos as $archivo) {
                $nombreGuardado = self::guardarArchivo($archivo, $tipo, $registroId);
                if ($nombreGuardado) {
                    $archivosGuardados[] = $nombreGuardado;
                }
            }
        } else {
            // Archivo único
            $nombreGuardado = self::guardarArchivo($archivos, $tipo, $registroId);
            if ($nombreGuardado) {
                $archivosGuardados[] = $nombreGuardado;
            }
        }

        return $archivosGuardados;
    }

    /**
     * Validar archivo (foto o documento)
     * @param array $archivo Datos del archivo
     * @param string $tipo 'foto' o 'documento'
     * @return bool True si es válido
     */
    public static function validarArchivo($archivo, $tipo)
    {
        if ($archivo['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        // Tamaño máximo 10MB
        if ($archivo['size'] > 10 * 1024 * 1024) {
            return false;
        }

        $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
        
        if ($tipo === 'foto') {
            $extensionesPermitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            return in_array($extension, $extensionesPermitidas);
        } else if ($tipo === 'documento') {
            $extensionesPermitidas = ['pdf'];
            return in_array($extension, $extensionesPermitidas);
        }

        return false;
    }

    /**
     * Validar múltiples archivos
     * @param array $archivos Array de archivos
     * @param string $tipo 'foto' o 'documento'
     * @return bool True si todos son válidos
     */
    public static function validarMultiplesArchivos($archivos, $tipo)
    {
        if (empty($archivos)) {
            return true;
        }

        if (isset($archivos[0]) && is_array($archivos[0])) {
            foreach ($archivos as $archivo) {
                if (!self::validarArchivo($archivo, $tipo)) {
                    return false;
                }
            }
        } else {
            return self::validarArchivo($archivos, $tipo);
        }

        return true;
    }
}
?>