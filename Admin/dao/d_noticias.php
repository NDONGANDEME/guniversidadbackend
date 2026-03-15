<?php
require_once __DIR__ . "/../../utilidades/u_conexion.php";
require_once __DIR__ . "/../modelo/m_noticia.php";

class NoticiasDao
{
    // CONSTANTE PARA EL NÚMERO DE REGISTROS POR PÁGINA
    const REGISTROS_POR_PAGINA = 20;

    // FUNCIÓN PARA OBTENER EL NÚMERO DE PÁGINAS (solo lectura)
    public static function contarNoticias($tipo = null)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT COUNT(*) as total FROM noticias";
            
            if ($tipo !== null) {
                $sql .= " WHERE tipo = :tipo";
            }
            
            $stmt = $instanciaConexion->prepare($sql);
            
            if ($tipo !== null) {
                $stmt->bindParam(':tipo', $tipo);
            }
            
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

            return (int) ceil($resultado['total'] / self::REGISTROS_POR_PAGINA);
        } catch (PDOException $e) {
            error_log("Error en contarNoticias: " . $e->getMessage());
            return 0;
        }
    }

    // FUNCIÓN PARA OBTENER EL NÚMERO DE PÁGINAS POR TIPO (solo lectura)
    public static function contarNoticiasPorTipo($tipo)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT COUNT(*) as total FROM noticias WHERE tipo = :tipo";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':tipo', $tipo);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Retorna el total de páginas para ese tipo específico
            return (int) ceil($resultado['total'] / self::REGISTROS_POR_PAGINA);
        } catch (PDOException $e) {
            error_log("Error en contarNoticiasPorTipo: " . $e->getMessage());
            return 0;
        }
    }

    // FUNCIÓN PARA OBTENER NOTICIAS A PAGINAR (solo lectura)
    public static function obtenerNoticiasAPaginar($pagina, $tipo = null)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $saltos = ($pagina - 1) * self::REGISTROS_POR_PAGINA;
            $lote = self::REGISTROS_POR_PAGINA;

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
            
            foreach ($resultados as $fila) {
                $model = new NoticiaModel();
                $model->hidratarDesdeArray($fila);
                
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

    // FUNCIÓN PARA OBTENER TODAS LAS NOTICIAS (solo lectura)
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
            
            foreach ($resultados as $fila) {
                $model = new NoticiaModel();
                $model->hidratarDesdeArray($fila);
                
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

    // FUNCIÓN PARA OBTENER LAS 5 NOTICIAS MÁS RECIENTES (solo lectura)
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
            
            foreach ($resultados as $fila) {
                $model = new NoticiaModel();
                $model->hidratarDesdeArray($fila);
                
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

    // FUNCIÓN: Obtener noticia por ID (solo lectura)
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

    // BUSCAR NOTICIAS POR TÉRMINO CON PAGINACIÓN
    public static function buscarNoticiasPaginadas($termino, $pagina, $tipo = null)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $terminoBusqueda = "%$termino%";
            
            $saltos = ($pagina - 1) * self::REGISTROS_POR_PAGINA;
            $lote = self::REGISTROS_POR_PAGINA;

            $sql = "SELECT * FROM noticias 
                    WHERE asunto LIKE :termino 
                       OR descripcion LIKE :termino";
            
            if ($tipo !== null) {
                $sql .= " AND tipo = :tipo";
            }
            
            $sql .= " ORDER BY fechaPublicacion DESC
                      LIMIT :lote OFFSET :saltos";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':termino', $terminoBusqueda);
            
            if ($tipo !== null) {
                $stmt->bindParam(':tipo', $tipo);
            }
            
            $stmt->bindParam(':lote', $lote, PDO::PARAM_INT);
            $stmt->bindParam(':saltos', $saltos, PDO::PARAM_INT);
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
            error_log("Error en buscarNoticiasPaginadas: " . $e->getMessage());
            return [];
        }
    }

    // CONTAR RESULTADOS DE BÚSQUEDA
    public static function contarResultadosBusquedaNoticias($termino, $tipo = null)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $terminoBusqueda = "%$termino%";

            $sql = "SELECT COUNT(*) as total 
                    FROM noticias 
                    WHERE asunto LIKE :termino 
                       OR descripcion LIKE :termino";
            
            if ($tipo !== null) {
                $sql .= " AND tipo = :tipo";
            }
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':termino', $terminoBusqueda);
            
            if ($tipo !== null) {
                $stmt->bindParam(':tipo', $tipo);
            }
            
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int) ceil($resultado['total'] / self::REGISTROS_POR_PAGINA);
        } catch (PDOException $e) {
            error_log("Error en contarResultadosBusquedaNoticias: " . $e->getMessage());
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

    // CONTAR TOTAL DE NOTICIAS POR TIPO (solo lectura)
    public static function contarTotalNoticiasPorTipo($tipo)
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
            error_log("Error en contarTotalNoticiasPorTipo: " . $e->getMessage());
            return 0;
        }
    }

    // OBTENER NOTICIAS POR TIPO CON PAGINACIÓN
    public static function obtenerNoticiasPorTipoPaginadas($tipo, $pagina)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $saltos = ($pagina - 1) * self::REGISTROS_POR_PAGINA;
            $lote = self::REGISTROS_POR_PAGINA;

            $sql = "SELECT * FROM noticias 
                    WHERE tipo = :tipo 
                    ORDER BY fechaPublicacion DESC 
                    LIMIT :lote OFFSET :saltos";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':tipo', $tipo);
            $stmt->bindParam(':lote', $lote, PDO::PARAM_INT);
            $stmt->bindParam(':saltos', $saltos, PDO::PARAM_INT);
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
            error_log("Error en obtenerNoticiasPorTipoPaginadas: " . $e->getMessage());
            return [];
        }
    }

    // FUNCIÓN: Crear noticia CON TRANSACCIÓN
    public static function crearNoticia($datos)
    {
        $pdo = null;
        try {
            $pdo = ConexionUtil::conectar();
            $pdo->beginTransaction();

            $sql = "INSERT INTO noticias (asunto, descripcion, tipo, fechaPublicacion) 
                    VALUES (:asunto, :descripcion, :tipo, :fechaPublicacion)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':asunto', $datos['asunto']);
            $stmt->bindParam(':descripcion', $datos['descripcion']);
            $stmt->bindParam(':tipo', $datos['tipo']);
            $stmt->bindParam(':fechaPublicacion', $datos['fechaPublicacion']);
            
            if ($stmt->execute()) {
                $id = $pdo->lastInsertId();
                $pdo->commit();
                error_log("Noticia creada con ID: " . $id);
                return $id;
            } else {
                $pdo->rollBack();
                return null;
            }
            
        } catch (PDOException $e) {
            if ($pdo) {
                $pdo->rollBack();
            }
            error_log("Error en crearNoticia: " . $e->getMessage());
            return null;
        }
    }

    // FUNCIÓN: Actualizar noticia CON TRANSACCIÓN
    public static function actualizarNoticia($id, $datos)
    {
        $pdo = null;
        try {
            $pdo = ConexionUtil::conectar();
            $pdo->beginTransaction();

            $sql = "UPDATE noticias 
                    SET asunto = :asunto, 
                        descripcion = :descripcion, 
                        tipo = :tipo
                    WHERE idNoticia = :id";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':asunto', $datos['asunto']);
            $stmt->bindParam(':descripcion', $datos['descripcion']);
            $stmt->bindParam(':tipo', $datos['tipo']);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            $resultado = $stmt->execute();
            
            if ($resultado) {
                $pdo->commit();
                error_log("Noticia actualizada ID: " . $id . " - Resultado: éxito");
                return true;
            } else {
                $pdo->rollBack();
                error_log("Noticia actualizada ID: " . $id . " - Resultado: fallo");
                return false;
            }
            
        } catch (PDOException $e) {
            if ($pdo) {
                $pdo->rollBack();
            }
            error_log("Error en actualizarNoticia: " . $e->getMessage());
            return false;
        }
    }

    // FUNCIÓN: Eliminar noticia CON TRANSACCIÓN
    public static function eliminarNoticia($id)
    {
        $pdo = null;
        try {
            $pdo = ConexionUtil::conectar();
            $pdo->beginTransaction();

            // Primero eliminar las fotos asociadas (los registros de la BD)
            self::eliminarFotosNoticiaConTransaccion($pdo, $id);

            // Luego eliminar la noticia
            $sql = "DELETE FROM noticias WHERE idNoticia = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            $resultado = $stmt->execute();
            
            if ($resultado) {
                $pdo->commit();
                error_log("Noticia eliminada ID: " . $id . " - Resultado: éxito");
                return true;
            } else {
                $pdo->rollBack();
                error_log("Noticia eliminada ID: " . $id . " - Resultado: fallo");
                return false;
            }
            
        } catch (PDOException $e) {
            if ($pdo) {
                $pdo->rollBack();
            }
            error_log("Error en eliminarNoticia: " . $e->getMessage());
            return false;
        }
    }

    // FUNCIÓN: Guardar fotos de una noticia CON TRANSACCIÓN (usar dentro de otra transacción)
    public static function guardarFotosNoticiaConTransaccion($pdo, $noticiaId, $fotos)
    {
        try {
            $sql = "INSERT INTO archivos (url, tipoArchivo, idReferencia, tablaReferencia) 
                    VALUES (:url, :tipoArchivo, :idReferencia, 'noticia')";
            
            $stmt = $pdo->prepare($sql);
            $contador = 0;
            
            foreach ($fotos as $foto) {
                $stmt->bindParam(':url', $foto);
                $stmt->bindValue(':tipoArchivo', 'foto');
                $stmt->bindParam(':idReferencia', $noticiaId, PDO::PARAM_INT);
                
                if ($stmt->execute()) {
                    $contador++;
                } else {
                    error_log("Error al insertar foto: " . $foto);
                }
            }
            
            error_log("Guardadas $contador fotos para noticia ID: " . $noticiaId);
            return $contador > 0;
        } catch (PDOException $e) {
            error_log("Error en guardarFotosNoticiaConTransaccion: " . $e->getMessage());
            throw $e; // Re-lanzar para que la transacción principal pueda hacer rollback
        }
    }

    // FUNCIÓN: Guardar fotos de una noticia (versión sin transacción, para compatibilidad)
    public static function guardarFotosNoticia($noticiaId, $fotos)
    {
        $pdo = null;
        try {
            $pdo = ConexionUtil::conectar();
            $pdo->beginTransaction();
            
            $resultado = self::guardarFotosNoticiaConTransaccion($pdo, $noticiaId, $fotos);
            
            if ($resultado) {
                $pdo->commit();
                return true;
            } else {
                $pdo->rollBack();
                return false;
            }
            
        } catch (PDOException $e) {
            if ($pdo) {
                $pdo->rollBack();
            }
            error_log("Error en guardarFotosNoticia: " . $e->getMessage());
            return false;
        }
    }

    // FUNCIÓN: Obtener fotos de una noticia (solo lectura)
    public static function obtenerFotosNoticia($noticiaId)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT idArchivo, url FROM archivos 
                    WHERE tablaReferencia = 'noticia'
                    AND idReferencia = :noticiaId 
                    ORDER BY idArchivo ASC";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':noticiaId', $noticiaId, PDO::PARAM_INT);
            $stmt->execute();

            $fotos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            error_log("Obtenidas " . count($fotos) . " fotos para noticia ID: " . $noticiaId);
            return $fotos;
        } catch (PDOException $e) {
            error_log("Error en obtenerFotosNoticia: " . $e->getMessage());
            return [];
        }
    }

    // FUNCIÓN: Eliminar fotos de una noticia (para usar dentro de transacción)
    public static function eliminarFotosNoticiaConTransaccion($pdo, $noticiaId)
    {
        try {
            $sql = "DELETE FROM archivos 
                    WHERE tablaReferencia = 'noticia' 
                    AND idReferencia = :noticiaId";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':noticiaId', $noticiaId, PDO::PARAM_INT);
            
            $resultado = $stmt->execute();
            error_log("Eliminados registros de fotos para noticia ID: " . $noticiaId);
            return $resultado;
        } catch (PDOException $e) {
            error_log("Error en eliminarFotosNoticiaConTransaccion: " . $e->getMessage());
            throw $e; // Re-lanzar para que la transacción principal pueda hacer rollback
        }
    }

    // FUNCIÓN: Eliminar fotos de una noticia (versión sin transacción)
    public static function eliminarFotosNoticia($noticiaId)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "DELETE FROM archivos 
                    WHERE tablaReferencia = 'noticia' 
                    AND idReferencia = :noticiaId";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':noticiaId', $noticiaId, PDO::PARAM_INT);
            
            $resultado = $stmt->execute();
            error_log("Eliminados registros de fotos para noticia ID: " . $noticiaId);
            return $resultado;
        } catch (PDOException $e) {
            error_log("Error en eliminarFotosNoticia: " . $e->getMessage());
            return false;
        }
    }
}
?>