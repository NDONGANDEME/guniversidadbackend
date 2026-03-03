<?php
require_once __DIR__ . "/../../utilidades/u_conexion.php";
require_once __DIR__ . "/../modelo/m_noticia.php";

class NoticiasDao
{
    // FUNCIÓN PARA OBTENER EL NÚMERO DE PÁGINAS (20 noticias por página)
    public static function contarNoticias()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT COUNT(*) as total FROM noticias";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

            return (int) ceil($resultado['total'] / 20);
        } catch (PDOException $e) {
            error_log("Error en contarNoticias: " . $e->getMessage());
            return 0;
        }
    }

    // FUNCIÓN PARA OBTENER NOTICIAS A PAGINAR
    public static function obtenerNoticiasAPaginar($pagina)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $saltos = ($pagina - 1) * 20;
            $lote = 20;

            $sql = "SELECT * FROM noticias ORDER BY fechaPublicacion DESC LIMIT :lote OFFSET :saltos";
            $stmt = $instanciaConexion->prepare($sql);

            $stmt->bindParam(':lote', $lote, PDO::PARAM_INT);
            $stmt->bindParam(':saltos', $saltos, PDO::PARAM_INT);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $noticias = [];
            
            // Crear modelos y obtener fotos para cada noticia
            foreach ($resultados as $fila) {
                $model = new NoticiaModel();
                $model->hidratarDesdeArray($fila);
                
                // Obtener fotos asociadas
                $fotos = self::obtenerFotosNoticia($model->idNoticia);
                $model->establecerFotos($fotos);
                
                $noticias[] = $model;
            }

            return $noticias;
        } catch (PDOException $e) {
            error_log("Error en obtenerNoticiasAPaginar: " . $e->getMessage());
            return [];
        }
    }

    // FUNCIÓN PARA OBTENER TODAS LAS NOTICIAS
    public static function listarNoticias()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT * FROM noticias ORDER BY fechaPublicacion DESC";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $noticias = [];
            
            // Crear modelos y obtener fotos para cada noticia
            foreach ($resultados as $fila) {
                $model = new NoticiaModel();
                $model->hidratarDesdeArray($fila);
                
                // Obtener fotos asociadas
                $fotos = self::obtenerFotosNoticia($model->idNoticia);
                $model->establecerFotos($fotos);
                
                $noticias[] = $model;
            }

            return $noticias;
        } catch (PDOException $e) {
            error_log("Error en listarNoticias: " . $e->getMessage());
            return [];
        }
    }

    // FUNCIÓN PARA OBTENER LAS 5 NOTICIAS MÁS RECIENTES
    public static function obtenerNoticiasRecientes()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT * FROM noticias ORDER BY fechaPublicacion DESC LIMIT 5";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $noticias = [];
            
            // Crear modelos y obtener fotos para cada noticia
            foreach ($resultados as $fila) {
                $model = new NoticiaModel();
                $model->hidratarDesdeArray($fila);
                
                // Obtener fotos asociadas
                $fotos = self::obtenerFotosNoticia($model->idNoticia);
                $model->establecerFotos($fotos);
                
                $noticias[] = $model;
            }

            return $noticias;
        } catch (PDOException $e) {
            error_log("Error en obtenerNoticiasRecientes: " . $e->getMessage());
            return [];
        }
    }

    // FUNCIÓN: Obtener noticia por ID (devuelve modelo)
    public static function obtenerNoticiaPorId($id)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT * FROM noticias WHERE idNoticia = :id";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($resultado) {
                $model = new NoticiaModel();
                $model->hidratarDesdeArray($resultado);
                
                // Obtener fotos asociadas
                $fotos = self::obtenerFotosNoticia($id);
                $model->establecerFotos($fotos);
                
                return $model;
            }

            return null;
        } catch (PDOException $e) {
            error_log("Error en obtenerNoticiaPorId: " . $e->getMessage());
            return null;
        }
    }

    // FUNCIÓN: Crear noticia (devuelve ID)
    public static function crearNoticia($datos)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "INSERT INTO noticias (asunto, descripcion, tipo, fechaPublicacion) 
                    VALUES (:asunto, :descripcion, :tipo, :fechaPublicacion)";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':asunto', $datos['asunto']);
            $stmt->bindParam(':descripcion', $datos['descripcion']);
            $stmt->bindParam(':tipo', $datos['tipo']);
            $stmt->bindParam(':fechaPublicacion', $datos['fechaPublicacion']);
            
            if ($stmt->execute()) {
                return $instanciaConexion->lastInsertId();
            }
            
            return null;
        } catch (PDOException $e) {
            error_log("Error en crearNoticia: " . $e->getMessage());
            return null;
        }
    }

    // FUNCIÓN: Actualizar noticia
    public static function actualizarNoticia($id, $datos)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "UPDATE noticias 
                    SET asunto = :asunto, 
                        descripcion = :descripcion, 
                        tipo = :tipo
                    WHERE idNoticia = :id";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':asunto', $datos['asunto']);
            $stmt->bindParam(':descripcion', $datos['descripcion']);
            $stmt->bindParam(':tipo', $datos['tipo']);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en actualizarNoticia: " . $e->getMessage());
            return false;
        }
    }

    // FUNCIÓN: Eliminar noticia
    public static function eliminarNoticia($id)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            // Primero eliminar las fotos asociadas
            self::eliminarFotosNoticia($id);

            // Luego eliminar la noticia
            $sql = "DELETE FROM noticias WHERE idNoticia = :id";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en eliminarNoticia: " . $e->getMessage());
            return false;
        }
    }

    // FUNCIÓN: Guardar fotos de una noticia
    public static function guardarFotosNoticia($noticiaId, $fotos)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "INSERT INTO archivos (url, tipoArchivo, idReferencia, tablaReferencia) 
                    VALUES (:url, :tipoArchivo, :idReferencia, 'noticias')";
            
            $stmt = $instanciaConexion->prepare($sql);
            
            foreach ($fotos as $foto) {
                $tipoArchivo = 'foto';
                $stmt->bindParam(':url', $foto);
                $stmt->bindParam(':tipoArchivo', $tipoArchivo);
                $stmt->bindParam(':idReferencia', $noticiaId, PDO::PARAM_INT);
                $stmt->execute();
            }
            
            return true;
        } catch (PDOException $e) {
            error_log("Error en guardarFotosNoticia: " . $e->getMessage());
            return false;
        }
    }

    // FUNCIÓN: Obtener fotos de una noticia
    public static function obtenerFotosNoticia($noticiaId)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT idArchivo, url FROM archivos 
                    WHERE tablaReferencia = 'noticias' 
                    AND idReferencia = :noticiaId 
                    ORDER BY idArchivo ASC";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':noticiaId', $noticiaId, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerFotosNoticia: " . $e->getMessage());
            return [];
        }
    }

    // FUNCIÓN: Eliminar fotos de una noticia
    public static function eliminarFotosNoticia($noticiaId)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "DELETE FROM archivos 
                    WHERE tablaReferencia = 'noticias' 
                    AND idReferencia = :noticiaId";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':noticiaId', $noticiaId, PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en eliminarFotosNoticia: " . $e->getMessage());
            return false;
        }
    }

    // FUNCIÓN: Buscar noticias por término
    public static function buscarNoticias($termino)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $termino = "%$termino%";

            $sql = "SELECT * FROM noticias 
                    WHERE asunto LIKE :termino 
                       OR descripcion LIKE :termino 
                       OR tipo LIKE :termino 
                    ORDER BY fechaPublicacion DESC";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':termino', $termino);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $noticias = [];
            
            foreach ($resultados as $fila) {
                $model = new NoticiaModel();
                $model->hidratarDesdeArray($fila);
                
                $fotos = self::obtenerFotosNoticia($model->idNoticia);
                $model->establecerFotos($fotos);
                
                $noticias[] = $model;
            }

            return $noticias;
        } catch (PDOException $e) {
            error_log("Error en buscarNoticias: " . $e->getMessage());
            return [];
        }
    }

    // FUNCIÓN: Obtener noticias por tipo
    public static function obtenerNoticiasPorTipo($tipo, $limite = null)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT * FROM noticias WHERE tipo = :tipo ORDER BY fechaPublicacion DESC";
            
            if ($limite !== null) {
                $sql .= " LIMIT :limite";
            }
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':tipo', $tipo);
            
            if ($limite !== null) {
                $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
            }
            
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $noticias = [];
            
            foreach ($resultados as $fila) {
                $model = new NoticiaModel();
                $model->hidratarDesdeArray($fila);
                
                $fotos = self::obtenerFotosNoticia($model->idNoticia);
                $model->establecerFotos($fotos);
                
                $noticias[] = $model;
            }

            return $noticias;
        } catch (PDOException $e) {
            error_log("Error en obtenerNoticiasPorTipo: " . $e->getMessage());
            return [];
        }
    }
}
?>