<?php
require_once __DIR__ . "/../utilidades/u_conexion.php";

class NoticiasDao
{
    // FUNCIÓN PARA OBTENER EL NÚMERO DE PÁGINAS (20 noticias por página)
    public static function contarNoticias()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT COUNT(*) as total FROM noticia";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

            return (int) ceil($resultado['total'] / 20);
        } catch (PDOException $e) {
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

            $sql = "SELECT * FROM noticia ORDER BY fechaPublicacion DESC LIMIT :lote OFFSET :saltos";
            $stmt = $instanciaConexion->prepare($sql);

            $stmt->bindParam(':lote', $lote, PDO::PARAM_INT);
            $stmt->bindParam(':saltos', $saltos, PDO::PARAM_INT);
            $stmt->execute();

            $noticias = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Obtener fotos para cada noticia
            foreach ($noticias as &$noticia) {
                $noticia['fotos'] = self::obtenerFotosNoticia($noticia['idNoticia']);
            }

            return $noticias;
        } catch (PDOException $e) {
            return [];
        }
    }

    // FUNCIÓN PARA OBTENER TODAS LAS NOTICIAS
    public static function listarNoticias()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT * FROM noticia ORDER BY fechaPublicacion DESC";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();

            $noticias = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Obtener fotos para cada noticia
            foreach ($noticias as &$noticia) {
                $noticia['fotos'] = self::obtenerFotosNoticia($noticia['idNoticia']);
            }

            return $noticias;
        } catch (PDOException $e) {
            return [];
        }
    }

    // FUNCIÓN PARA OBTENER LAS 5 NOTICIAS MÁS RECIENTES
    public static function obtenerNoticiasRecientes()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT * FROM noticia ORDER BY fechaPublicacion DESC LIMIT 5";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();

            $noticias = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Obtener fotos para cada noticia
            foreach ($noticias as &$noticia) {
                $noticia['fotos'] = self::obtenerFotosNoticia($noticia['idNoticia']);
            }

            return $noticias;
        } catch (PDOException $e) {
            return [];
        }
    }

    // FUNCIÓN PARA OBTENER UNA NOTICIA POR ID
    public static function obtenerNoticiaPorId(int $id)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT * FROM noticia WHERE idNoticia = :id";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $noticia = $stmt->fetch(PDO::FETCH_ASSOC);

            // Si existe la noticia, obtener sus fotos
            if ($noticia) {
                $noticia['fotos'] = self::obtenerFotosNoticia($id);
            }

            return $noticia;
        } catch (PDOException $e) {
            return null;
        }
    }

    // FUNCIÓN: Crear noticia
    public static function crearNoticia($datos)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "INSERT INTO noticia (asunto, descripcion, tipo, fechaPublicacion) 
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
            return null;
        }
    }

    // FUNCIÓN: Actualizar noticia
    public static function actualizarNoticia($id, $datos)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "UPDATE noticia 
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
            $sql = "DELETE FROM noticia WHERE idNoticia = :id";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    // FUNCIÓN: Guardar fotos de una noticia (usando tabla foto)
    public static function guardarFotosNoticia($noticiaId, $fotos)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "INSERT INTO foto (url, idReferencia, tablaReferencia) 
                    VALUES (:url, :idReferencia, 'noticia')";
            
            $stmt = $instanciaConexion->prepare($sql);
            
            foreach ($fotos as $foto) {
                $stmt->bindParam(':url', $foto);
                $stmt->bindParam(':idReferencia', $noticiaId, PDO::PARAM_INT);
                $stmt->execute();
            }
            
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    // FUNCIÓN: Obtener fotos de una noticia (desde tabla foto)
    public static function obtenerFotosNoticia($noticiaId)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT idFoto, url FROM foto 
                    WHERE tablaReferencia = 'noticia' 
                    AND idReferencia = :noticiaId 
                    ORDER BY idFoto ASC";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':noticiaId', $noticiaId, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    // FUNCIÓN: Eliminar fotos de una noticia
    public static function eliminarFotosNoticia($noticiaId)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "DELETE FROM foto 
                    WHERE tablaReferencia = 'noticia' 
                    AND idReferencia = :noticiaId";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':noticiaId', $noticiaId, PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    // FUNCIÓN: Buscar noticias por término
    public static function buscarNoticias($termino)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $termino = "%$termino%";

            $sql = "SELECT * FROM noticia 
                    WHERE asunto LIKE :termino 
                       OR descripcion LIKE :termino 
                       OR tipo LIKE :termino 
                    ORDER BY fechaPublicacion DESC";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':termino', $termino);
            $stmt->execute();

            $noticias = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Obtener fotos para cada noticia
            foreach ($noticias as &$noticia) {
                $noticia['fotos'] = self::obtenerFotosNoticia($noticia['idNoticia']);
            }

            return $noticias;
        } catch (PDOException $e) {
            return [];
        }
    }
}
?>