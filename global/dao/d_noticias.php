<?php
require_once __DIR__ . "/../utilidades/u_conexion.php";

class NoticiasDao
{
    // FUNCIÓN PARA OBTENER EL NÚMERO DE PÁGINAS
    public static function contarNoticias()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT COUNT(*) as total FROM noticias";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

            return (int) ceil($resultado['total'] / 2);
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

            $sql = "SELECT * FROM noticias ORDER BY fecha_creacion DESC LIMIT :lote OFFSET :saltos";
            $stmt = $instanciaConexion->prepare($sql);

            $stmt->bindParam(':lote', $lote, PDO::PARAM_INT);
            $stmt->bindParam(':saltos', $saltos, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    // FUNCIÓN PARA OBTENER TODAS LAS NOTICIAS
    public static function listarNoticias()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT * FROM noticias ORDER BY fecha_creacion DESC";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    // FUNCIÓN PARA OBTENER LAS 5 NOTICIAS MÁS RECIENTES
    public static function obtenerNoticiasRecientes()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT * FROM noticias ORDER BY fecha_creacion DESC LIMIT 5";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    // FUNCIÓN PARA OBTENER UNA NOTICIA POR ID
    public static function obtenerNoticiaPorId(int $id)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT * FROM noticias WHERE id = :id";
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

    // NUEVA FUNCIÓN: Crear noticia
    public static function crearNoticia($datos)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "INSERT INTO noticias (titulo, contenido, fecha_creacion) 
                    VALUES (:titulo, :contenido, :fecha_creacion)";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':titulo', $datos['titulo']);
            $stmt->bindParam(':contenido', $datos['contenido']);
            $stmt->bindParam(':fecha_creacion', $datos['fecha_creacion']);
            
            if ($stmt->execute()) {
                return $instanciaConexion->lastInsertId();
            }
            
            return null;
        } catch (PDOException $e) {
            return null;
        }
    }

    // NUEVA FUNCIÓN: Guardar fotos de una noticia
    public static function guardarFotosNoticia($noticiaId, $fotos)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "INSERT INTO noticias_fotos (noticia_id, nombre_archivo) 
                    VALUES (:noticia_id, :nombre_archivo)";
            
            $stmt = $instanciaConexion->prepare($sql);
            
            foreach ($fotos as $foto) {
                $stmt->bindParam(':noticia_id', $noticiaId);
                $stmt->bindParam(':nombre_archivo', $foto);
                $stmt->execute();
            }
            
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    // NUEVA FUNCIÓN: Obtener fotos de una noticia
    public static function obtenerFotosNoticia($noticiaId)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT id, nombre_archivo FROM noticias_fotos 
                    WHERE noticia_id = :noticia_id 
                    ORDER BY id ASC";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':noticia_id', $noticiaId, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
}
?>