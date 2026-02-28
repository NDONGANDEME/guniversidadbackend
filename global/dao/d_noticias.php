<?php
require_once __DIR__ . "/../utilidades/u_conexion.php";
require_once __DIR__ . "/../modelo/m_noticia.php";

class D_Noticias
{
    // OBTENER EL NÚMERO DE PÁGINAS (20 noticias por página)
    public static function contarNoticias($tipo = null)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT COUNT(*) as total FROM noticias";
            
            if ($tipo !== null) {
                $sql .= " WHERE tipo = :tipo";
                $stmt = $instanciaConexion->prepare($sql);
                $stmt->bindParam(':tipo', $tipo);
            } else {
                $stmt = $instanciaConexion->prepare($sql);
            }
            
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

            return (int) ceil($resultado['total'] / 20);
        } catch (PDOException $e) {
            error_log("Error en contarNoticias: " . $e->getMessage());
            return 0;
        }
    }

    // OBTENER NOTICIAS A PAGINAR
    public static function obtenerNoticiasAPaginar($pagina, $tipo = null)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $saltos = ($pagina - 1) * 20;
            $lote = 20;

            $sql = "SELECT * FROM noticias";
            
            if ($tipo !== null) {
                $sql .= " WHERE tipo = :tipo";
            }
            
            $sql .= " ORDER BY fechaPublicacion DESC LIMIT :lote OFFSET :saltos";
            
            $stmt = $instanciaConexion->prepare($sql);
            
            if ($tipo !== null) {
                $stmt->bindParam(':tipo', $tipo);
            }
            
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

    // LISTAR TODAS LAS NOTICIAS
    public static function listarNoticias($tipo = null)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT * FROM noticias";
            
            if ($tipo !== null) {
                $sql .= " WHERE tipo = :tipo";
            }
            
            $sql .= " ORDER BY fechaPublicacion DESC";
            
            $stmt = $instanciaConexion->prepare($sql);
            
            if ($tipo !== null) {
                $stmt->bindParam(':tipo', $tipo);
            }
            
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

    // OBTENER LAS 5 NOTICIAS MÁS RECIENTES
    public static function obtenerNoticiasRecientes($tipo = null)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT * FROM noticias";
            
            if ($tipo !== null) {
                $sql .= " WHERE tipo = :tipo";
            }
            
            $sql .= " ORDER BY fechaPublicacion DESC LIMIT 5";
            
            $stmt = $instanciaConexion->prepare($sql);
            
            if ($tipo !== null) {
                $stmt->bindParam(':tipo', $tipo);
            }
            
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

    // OBTENER UNA NOTICIA POR ID
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

    // BUSCAR NOTICIAS POR TÉRMINO
    public static function buscarNoticias($termino, $tipo = null)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $terminoBusqueda = "%$termino%";

            $sql = "SELECT * FROM noticias 
                    WHERE asunto LIKE :termino 
                       OR descripcion LIKE :termino";
            
            if ($tipo !== null) {
                $sql .= " AND tipo = :tipo";
            }
            
            $sql .= " ORDER BY fechaPublicacion DESC";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':termino', $terminoBusqueda);
            
            if ($tipo !== null) {
                $stmt->bindParam(':tipo', $tipo);
            }
            
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
            error_log("Error en buscarNoticias: " . $e->getMessage());
            return [];
        }
    }

    // OBTENER FOTOS DE UNA NOTICIA (desde tabla archivos, filtrando por 'foto')
    public static function obtenerFotosNoticia($noticiaId)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT idArchivo, url, tipoArchivo 
                    FROM archivos 
                    WHERE tablaReferencia = 'noticias' 
                    AND idReferencia = :noticiaId 
                    AND tipoArchivo = 'foto'
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

    // CONTAR NOTICIAS POR TIPO
    public static function contarNoticiasPorTipo($tipo)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT COUNT(*) as total FROM noticias WHERE tipo = :tipo";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':tipo', $tipo);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int) $resultado['total'];
        } catch (PDOException $e) {
            error_log("Error en contarNoticiasPorTipo: " . $e->getMessage());
            return 0;
        }
    }

    // OBTENER NOTICIAS POR TIPO
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