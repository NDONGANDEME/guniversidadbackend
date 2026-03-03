<?php
class LimpiarDatos
{
    // Constantes para carpetas (rutas relativas al proyecto)
    const CARPETA_IMAGENES = "../../../htdocs/guniversidadfrontend/public/img";
    const CARPETA_DOCUMENTOS = "../../../htdocs/guniversidadfrontend/public/docs";
    
    // Mapeo de extensiones a MIME types
    const MIME_TYPES = [
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif',
        'webp' => 'image/webp',
        'pdf' => 'application/pdf'
    ];

    /**
     * Validar rutas: solo letras, números y guiones bajos
     */
    public static function limpiarRuta($ruta)
    {
        $ruta = trim($ruta);
        if (preg_match('/^[a-zA-Z0-9_]+$/', $ruta)) {
            return $ruta;
        }
        return '';
    }

    /**
     * Sanitizar parámetros generales (texto libre)
     */
    public static function limpiarParametro($valor)
    {
        if ($valor === null) {
            return '';
        }
        
        $valor = trim($valor);
        $valor = stripslashes($valor);
        $valor = strip_tags($valor);
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
     * Procesar archivos subidos
     */
    public static function procesarArchivos($archivos)
    {
        $resultado = [];
        
        if (empty($archivos)) {
            return $resultado;
        }

        foreach ($archivos as $campo => $archivo) {
            $campoLimpio = self::limpiarParametro($campo);
            
            if (is_array($archivo['name'])) {
                $resultado[$campoLimpio] = [];
                for ($i = 0; $i < count($archivo['name']); $i++) {
                    $archivoOriginal = [
                        'name' => $archivo['name'][$i],
                        'type' => $archivo['type'][$i],
                        'tmp_name' => $archivo['tmp_name'][$i],
                        'error' => $archivo['error'][$i],
                        'size' => $archivo['size'][$i]
                    ];
                    
                    if (self::validarTipoBasico($archivoOriginal)) {
                        $resultado[$campoLimpio][] = $archivoOriginal;
                    }
                }
            } else {
                $archivoOriginal = [
                    'name' => $archivo['name'],
                    'type' => $archivo['type'],
                    'tmp_name' => $archivo['tmp_name'],
                    'error' => $archivo['error'],
                    'size' => $archivo['size']
                ];
                
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
        if ($archivo['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
        
        return array_key_exists($extension, self::MIME_TYPES);
    }

    /**
     * Validar archivo completo (tipo, tamaño, MIME)
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
        
        // Verificar que la extensión es válida
        if (!array_key_exists($extension, self::MIME_TYPES)) {
            return false;
        }

        // Verificar MIME type real del archivo
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $archivo['tmp_name']);
        finfo_close($finfo);

        $mimeEsperado = self::MIME_TYPES[$extension];

        if ($tipo === 'foto') {
            $extensionesPermitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            return in_array($extension, $extensionesPermitidas) && $mimeType === $mimeEsperado;
        } 
        
        if ($tipo === 'documento') {
            $extensionesPermitidas = ['pdf'];
            return in_array($extension, $extensionesPermitidas) && $mimeType === $mimeEsperado;
        }

        return false;
    }

    /**
     * Validar múltiples archivos
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

    /**
     * Obtener ruta completa de la carpeta destino
     */
    private static function getRutaCarpeta($tipo)
    {
        // Obtener la raíz del proyecto (subir 4 niveles desde /utilidades)
        $raiz = dirname(__DIR__, 4);
        
        if ($tipo === 'foto') {
            return $raiz . self::CARPETA_IMAGENES;
        }
        
        if ($tipo === 'documento') {
            return $raiz . self::CARPETA_DOCUMENTOS;
        }
        
        return null;
    }

    /**
     * Generar nombre único para archivo
     */
    public static function generarNombreUnico($prefijo, $registroId, $extension)
    {
        $timestamp = time();
        $random = bin2hex(random_bytes(8));
        return $prefijo . $registroId . '_' . $timestamp . '_' . $random . '.' . $extension;
    }

    /**
     * Guardar archivo (foto o documento)
     */
    public static function guardarArchivo($archivo, $tipo, $registroId)
    {
        if ($archivo['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $carpeta = self::getRutaCarpeta($tipo);
        if (!$carpeta) {
            return null;
        }

        // Crear carpeta si no existe
        if (!file_exists($carpeta)) {
            if (!mkdir($carpeta, 0777, true)) {
                return null;
            }
        }

        $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
        $prefijo = ($tipo === 'foto') ? 'Foto_' : 'Documento_';
        $nombreUnico = self::generarNombreUnico($prefijo, $registroId, $extension);
        
        $rutaCompleta = $carpeta . $nombreUnico;

        if (move_uploaded_file($archivo['tmp_name'], $rutaCompleta)) {
            return $nombreUnico;
        }

        return null;
    }

    /**
     * Guardar múltiples archivos
     */
    public static function guardarMultiplesArchivos($archivos, $tipo, $registroId)
    {
        $archivosGuardados = [];
        
        if (empty($archivos)) {
            return $archivosGuardados;
        }

        if (isset($archivos[0]) && is_array($archivos[0])) {
            foreach ($archivos as $archivo) {
                $nombreGuardado = self::guardarArchivo($archivo, $tipo, $registroId);
                if ($nombreGuardado) {
                    $archivosGuardados[] = $nombreGuardado;
                }
            }
        } else {
            $nombreGuardado = self::guardarArchivo($archivos, $tipo, $registroId);
            if ($nombreGuardado) {
                $archivosGuardados[] = $nombreGuardado;
            }
        }

        return $archivosGuardados;
    }

    /**
     * Obtener URL pública de un archivo
     */
    public static function obtenerUrlArchivo($nombreArchivo, $tipo)
    {
        if ($tipo === 'foto') {
            return '/public/imagenes/' . $nombreArchivo;
        }
        
        if ($tipo === 'documento') {
            return '/public/documentos/' . $nombreArchivo;
        }
        
        return null;
    }
}
?>