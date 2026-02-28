<?php
require_once __DIR__ . "/../utilidades/u_conexion.php";

class NoticiasDao
{
    // FUNCIÓN: Obtener noticia por ID
    public static function obtenerNoticiaPorId($id)
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

    // FUNCIÓN: Obtener fotos de una noticia
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
}
?>