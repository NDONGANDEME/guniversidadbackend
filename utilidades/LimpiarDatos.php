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
            
            // Verificar si es un array de archivos (múltiples)
            if (is_array($archivo['name'])) {
                $resultado[$campoLimpio] = [];
                for ($i = 0; $i < count($archivo['name']); $i++) {
                    // Solo procesar si no hay error
                    if ($archivo['error'][$i] === UPLOAD_ERR_OK) {
                        $archivoOriginal = [
                            'name' => $archivo['name'][$i],
                            'type' => $archivo['type'][$i],
                            'tmp_name' => $archivo['tmp_name'][$i],
                            'error' => $archivo['error'][$i],
                            'size' => $archivo['size'][$i]
                        ];
                        
                        $resultado[$campoLimpio][] = $archivoOriginal;
                    }
                }
            } else {
                // Archivo único - solo procesar si no hay error
                if ($archivo['error'] === UPLOAD_ERR_OK) {
                    $archivoOriginal = [
                        'name' => $archivo['name'],
                        'type' => $archivo['type'],
                        'tmp_name' => $archivo['tmp_name'],
                        'error' => $archivo['error'],
                        'size' => $archivo['size']
                    ];
                    
                    $resultado[$campoLimpio] = $archivoOriginal;
                }
            }
        }
        
        return $resultado;
    }

    /**
     * CONSTANTES PARA CARPETAS
     */
    const CARPETA_IMAGENES = "/../../htdocs/guniversidadfrontend/public/img/";
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
            error_log("Error en archivo: código " . $archivo['error']);
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
            error_log("Tipo de archivo no válido: " . $tipo);
            return null;
        }

        // Crear carpeta si no existe
        if (!file_exists($carpeta)) {
            if (!mkdir($carpeta, 0777, true)) {
                error_log("No se pudo crear la carpeta: " . $carpeta);
                return null;
            }
        }

        // Obtener extensión
        $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
        
        // Generar nombre único
        $nombreUnico = self::generarNombreUnico($prefijo, $registroId, $extension);
        
        $rutaCompleta = $carpeta . $nombreUnico;

        // Mover el archivo
        if (move_uploaded_file($archivo['tmp_name'], $rutaCompleta)) {
            error_log("Archivo guardado exitosamente: " . $nombreUnico);
            return $nombreUnico;
        } else {
            error_log("Error al mover el archivo a: " . $rutaCompleta);
        }

        return null;
    }

    /**
     * Guardar múltiples archivos
     * @param array $archivos Array de archivos (puede ser un archivo único o múltiples)
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

        error_log("Iniciando guardarMultiplesArchivos - Tipo: " . $tipo . ", Registro ID: " . $registroId);

        // Verificar la estructura de los archivos
        if (isset($archivos['name']) && is_array($archivos['name'])) {
            // Es un array de archivos múltiples en formato $_FILES
            error_log("Formato: Múltiples archivos en estructura $_FILES");
            for ($i = 0; $i < count($archivos['name']); $i++) {
                if ($archivos['error'][$i] === UPLOAD_ERR_OK) {
                    $archivoIndividual = [
                        'name' => $archivos['name'][$i],
                        'type' => $archivos['type'][$i],
                        'tmp_name' => $archivos['tmp_name'][$i],
                        'error' => $archivos['error'][$i],
                        'size' => $archivos['size'][$i]
                    ];
                    
                    $nombreGuardado = self::guardarArchivo($archivoIndividual, $tipo, $registroId);
                    if ($nombreGuardado) {
                        $archivosGuardados[] = $nombreGuardado;
                    }
                }
            }
        } else if (isset($archivos['name']) && !is_array($archivos['name'])) {
            // Es un archivo único
            error_log("Formato: Archivo único");
            $nombreGuardado = self::guardarArchivo($archivos, $tipo, $registroId);
            if ($nombreGuardado) {
                $archivosGuardados[] = $nombreGuardado;
            }
        } else if (is_array($archivos) && isset($archivos[0])) {
            // Ya es un array de archivos procesados individualmente
            error_log("Formato: Array de archivos individuales");
            foreach ($archivos as $index => $archivo) {
                $nombreGuardado = self::guardarArchivo($archivo, $tipo, $registroId);
                if ($nombreGuardado) {
                    $archivosGuardados[] = $nombreGuardado;
                }
            }
        } else {
            error_log("Formato no reconocido: " . print_r($archivos, true));
        }

        error_log("Archivos guardados: " . count($archivosGuardados));
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
            error_log("Archivo con error: " . $archivo['error']);
            return false;
        }

        // Tamaño máximo 10MB
        if ($archivo['size'] > 10 * 1024 * 1024) {
            error_log("Archivo demasiado grande: " . $archivo['size'] . " bytes");
            return false;
        }

        $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
        
        if ($tipo === 'foto') {
            $extensionesPermitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $esValida = in_array($extension, $extensionesPermitidas);
            if (!$esValida) {
                error_log("Extensión no válida para foto: " . $extension);
            }
            return $esValida;
        } else if ($tipo === 'documento') {
            $extensionesPermitidas = ['pdf'];
            $esValida = in_array($extension, $extensionesPermitidas);
            if (!$esValida) {
                error_log("Extensión no válida para documento: " . $extension);
            }
            return $esValida;
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

        error_log("Iniciando validación de múltiples archivos - Tipo: " . $tipo);

        // Verificar la estructura de los archivos
        if (isset($archivos['name']) && is_array($archivos['name'])) {
            // Es un array de archivos múltiples en formato $_FILES
            error_log("Validando formato: Múltiples archivos en estructura $_FILES");
            for ($i = 0; $i < count($archivos['name']); $i++) {
                if ($archivos['error'][$i] === UPLOAD_ERR_OK) {
                    $archivoIndividual = [
                        'name' => $archivos['name'][$i],
                        'type' => $archivos['type'][$i],
                        'tmp_name' => $archivos['tmp_name'][$i],
                        'error' => $archivos['error'][$i],
                        'size' => $archivos['size'][$i]
                    ];
                    
                    if (!self::validarArchivo($archivoIndividual, $tipo)) {
                        error_log("Archivo no válido en posición: " . $i);
                        return false;
                    }
                }
            }
            return true;
        } else if (isset($archivos['name']) && !is_array($archivos['name'])) {
            // Es un archivo único
            error_log("Validando formato: Archivo único");
            return self::validarArchivo($archivos, $tipo);
        } else if (is_array($archivos)) {
            // Ya es un array de archivos procesados individualmente
            error_log("Validando formato: Array de archivos individuales");
            foreach ($archivos as $index => $archivo) {
                if (!self::validarArchivo($archivo, $tipo)) {
                    error_log("Archivo no válido en índice: " . $index);
                    return false;
                }
            }
            return true;
        }

        error_log("Formato no reconocido en validación");
        return false;
    }
}
?>