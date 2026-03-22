<?php
require_once __DIR__ . "/../../utilidades/u_conexion.php";
require_once __DIR__ . "/../modelo/m_asignatura.php";

class D_Asignatura
{
    // CONSTANTE PARA EL NÚMERO DE REGISTROS POR PÁGINA
    const REGISTROS_POR_PAGINA = 8;

    // OBTENER TODAS LAS ASIGNATURAS (solo lectura)
    public static function obtenerAsignaturas()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT a.*, f.nombreFacultad
                    FROM asignaturas a
                    LEFT JOIN facultad f ON a.idFacultad = f.idFacultad
                    ORDER BY a.nombreAsignatura ASC";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $asignaturas = [];
            
            foreach ($resultados as $fila) {
                $model = new AsignaturaModel();
                $model->hidratarDesdeArray($fila);
                $asignaturas[] = $model;
            }

            return $asignaturas;
        } catch (PDOException $e) {
            error_log("Error en obtenerAsignaturas: " . $e->getMessage());
            return [];
        }
    }

    // OBTENER ASIGNATURA POR ID (solo lectura)
    public static function obtenerAsignaturaPorId($id)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT * FROM asignaturas WHERE idAsignatura = :id";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($resultado) {
                $model = new AsignaturaModel();
                return $model->hidratarDesdeArray($resultado);
            }
            
            return null;
        } catch (PDOException $e) {
            error_log("Error en obtenerAsignaturaPorId: " . $e->getMessage());
            return null;
        }
    }

    // OBTENER ASIGNATURAS POR FACULTAD (solo lectura)
    public static function obtenerAsignaturasPorFacultad($idFacultad)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT DISTINCT a.* FROM asignaturas a WHERE a.idFacultad = :idFacultad ORDER BY a.nombreAsignatura ASC";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idFacultad', $idFacultad, PDO::PARAM_INT);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $asignaturas = [];
            
            foreach ($resultados as $fila) {
                $model = new AsignaturaModel();
                $model->hidratarDesdeArray($fila);
                $asignaturas[] = $model;
            }

            return $asignaturas;
        } catch (PDOException $e) {
            error_log("Error en obtenerAsignaturasPorFacultad: " . $e->getMessage());
            return [];
        }
    }

    /**
     * OBTENER ASIGNATURAS DEL ÚLTIMO SEMESTRE EN EL QUE SE MATRICULÓ EL ESTUDIANTE
     * @param int $idEstudiante ID del estudiante
     * @param int $numeroSemestre Número del semestre (opcional)
     * @return array Lista de asignaturas
     */
    public static function obtenerAsignaturasPorSemestre($idEstudiante, $numeroSemestre = null)
    {
        try {
            $pdo = ConexionUtil::conectar();

            // Si no se especifica número de semestre, obtener el último semestre matriculado
            if ($numeroSemestre === null) {
                $sqlUltimoSemestre = "SELECT s.numeroSemestre 
                                    FROM matriculas m
                                    INNER JOIN semestre s ON m.idSemestre = s.idSemestre
                                    WHERE m.idEstudiante = :idEstudiante 
                                    ORDER BY m.cursoAcademico DESC, s.numeroSemestre DESC
                                    LIMIT 1";
                $stmt = $pdo->prepare($sqlUltimoSemestre);
                $stmt->bindParam(':idEstudiante', $idEstudiante, PDO::PARAM_INT);
                $stmt->execute();
                $ultimoSemestre = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$ultimoSemestre) {
                    return [];
                }
                $numeroSemestre = $ultimoSemestre['numeroSemestre'];
            }

            // Obtener las asignaturas del semestre
            $sql = "SELECT DISTINCT a.idAsignatura, a.codigoAsignatura, a.nombreAsignatura, a.descripcion, a.idFacultad, f.nombreFacultad
                    FROM plan_semestre_asignatura psa
                    INNER JOIN asignaturas a ON psa.idAsignatura = a.idAsignatura
                    LEFT JOIN facultad f ON a.idFacultad = f.idFacultad
                    INNER JOIN semestre s ON psa.idSemestre = s.idSemestre
                    INNER JOIN planestudio pe ON psa.idPlanEstudio = pe.idPlanEstudio
                    INNER JOIN matriculas m ON pe.idPlanEstudio = m.idPlanEstudio
                    WHERE m.idEstudiante = :idEstudiante 
                    AND s.numeroSemestre = :numeroSemestre
                    ORDER BY a.nombreAsignatura ASC";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':idEstudiante', $idEstudiante, PDO::PARAM_INT);
            $stmt->bindParam(':numeroSemestre', $numeroSemestre, PDO::PARAM_INT);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $asignaturas = [];
            
            foreach ($resultados as $fila) {
                $model = new AsignaturaModel();
                $model->hidratarDesdeArray($fila);
                $asignaturas[] = $model;
            }

            return $asignaturas;
        } catch (PDOException $e) {
            error_log("Error en obtenerAsignaturasPorSemestre: " . $e->getMessage());
            return [];
        }
    }

    /**
     * OBTENER ASIGNATURAS PENDIENTES Y BLOQUEADAS DEL ESTUDIANTE
     * Teniendo en cuenta las prerrequisitos con nombres completos
     * @param int $idEstudiante ID del estudiante
     * @param int $numeroSemestre Número del semestre actual
     * @return array Lista con asignaturas pendientes y bloqueadas
     */
    public static function obtenerAsignaturasPendientesYBloqueadas($idEstudiante, $numeroSemestre)
    {
        try {
            $pdo = ConexionUtil::conectar();

            // 1. Obtener todas las asignaturas que el estudiante ha aprobado (nota >= 5)
            $sqlAprobadas = "SELECT DISTINCT psa.idAsignatura
                            FROM matricula_asignatura ma
                            INNER JOIN plan_semestre_asignatura psa ON ma.idPlanCursoAsignatura = psa.idPlanCursoAsignatura
                            INNER JOIN matriculas m ON ma.idMatricula = m.idMatricula
                            WHERE m.idEstudiante = :idEstudiante 
                            AND ma.notaFinal >= 5";
            
            $stmt = $pdo->prepare($sqlAprobadas);
            $stmt->bindParam(':idEstudiante', $idEstudiante, PDO::PARAM_INT);
            $stmt->execute();
            $asignaturasAprobadas = $stmt->fetchAll(PDO::FETCH_COLUMN);

            // 2. Obtener todas las asignaturas del plan de estudio del estudiante (para los semestres anteriores)
            $sqlAsignaturasPlan = "SELECT DISTINCT a.idAsignatura, a.codigoAsignatura, a.nombreAsignatura, a.descripcion, a.idFacultad, f.nombreFacultad, s.numeroSemestre
                                FROM plan_semestre_asignatura psa
                                INNER JOIN asignaturas a ON psa.idAsignatura = a.idAsignatura
                                LEFT JOIN facultad f ON a.idFacultad = f.idFacultad
                                INNER JOIN semestre s ON psa.idSemestre = s.idSemestre
                                INNER JOIN planestudio pe ON psa.idPlanEstudio = pe.idPlanEstudio
                                INNER JOIN matriculas m ON pe.idPlanEstudio = m.idPlanEstudio
                                WHERE m.idEstudiante = :idEstudiante 
                                    AND s.numeroSemestre < :numeroSemestre
                                ORDER BY s.numeroSemestre ASC, a.nombreAsignatura ASC";
            
            $stmt = $pdo->prepare($sqlAsignaturasPlan);
            $stmt->bindParam(':idEstudiante', $idEstudiante, PDO::PARAM_INT);
            $stmt->bindParam(':numeroSemestre', $numeroSemestre, PDO::PARAM_INT);
            $stmt->execute();
            $asignaturasPlan = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // 3. Obtener todos los prerrequisitos con nombres completos
            $sqlPrerrequisitos = "SELECT p.idAsignatura, p.idAsignaturaRequerida,
                                        a1.codigoAsignatura as codigoAsignatura, a1.nombreAsignatura as nombreAsignatura,
                                        a2.codigoAsignatura as codigoAsignaturaRequerida, a2.nombreAsignatura as nombreAsignaturaRequerida
                                FROM prerrequisitos p
                                INNER JOIN asignaturas a1 ON p.idAsignatura = a1.idAsignatura
                                INNER JOIN asignaturas a2 ON p.idAsignaturaRequerida = a2.idAsignatura";
            $stmt = $pdo->prepare($sqlPrerrequisitos);
            $stmt->execute();
            $prerrequisitos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Crear mapa de prerrequisitos con información completa
            $mapaPrerrequisitos = [];
            foreach ($prerrequisitos as $prerreq) {
                if (!isset($mapaPrerrequisitos[$prerreq['idAsignatura']])) {
                    $mapaPrerrequisitos[$prerreq['idAsignatura']] = [];
                }
                $mapaPrerrequisitos[$prerreq['idAsignatura']][] = [
                    'id' => $prerreq['idAsignaturaRequerida'],
                    'codigo' => $prerreq['codigoAsignaturaRequerida'],
                    'nombre' => $prerreq['nombreAsignaturaRequerida']
                ];
            }

            // 4. Clasificar asignaturas
            $pendientes = [];
            $bloqueadas = [];

            foreach ($asignaturasPlan as $asignatura) {
                $idAsig = $asignatura['idAsignatura'];
                
                // Crear modelo de asignatura
                $model = new AsignaturaModel();
                $model->hidratarDesdeArray($asignatura);
                
                // Si ya está aprobada, saltar
                if (in_array($idAsig, $asignaturasAprobadas)) {
                    continue;
                }

                // Verificar si tiene prerrequisitos
                $prerreqs = $mapaPrerrequisitos[$idAsig] ?? [];
                
                if (empty($prerreqs)) {
                    // No tiene prerrequisitos, es pendiente
                    $model->establecerPrerrequisitos([]);
                    $pendientes[] = $model;
                } else {
                    // Tiene prerrequisitos, verificar si están aprobados
                    $todosAprobados = true;
                    $prerreqsFaltantes = [];
                    
                    foreach ($prerreqs as $prerreq) {
                        if (!in_array($prerreq['id'], $asignaturasAprobadas)) {
                            $todosAprobados = false;
                            $prerreqsFaltantes[] = $prerreq;
                        }
                    }
                    
                    $model->establecerPrerrequisitos($prerreqs);
                    
                    if ($todosAprobados) {
                        $pendientes[] = $model;
                    } else {
                        $bloqueadas[] = [
                            'asignatura' => $model,
                            'prerrequisitos_faltantes' => $prerreqsFaltantes
                        ];
                    }
                }
            }

            return [
                'pendientes' => $pendientes,
                'bloqueadas' => $bloqueadas
            ];
        } catch (PDOException $e) {
            error_log("Error en obtenerAsignaturasPendientesYBloqueadas: " . $e->getMessage());
            return ['pendientes' => [], 'bloqueadas' => []];
        }
    }


    // OBTENER EL NÚMERO DE PÁGINAS (30 asignaturas por página)
    public static function contarAsignaturas()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT COUNT(*) as total FROM asignaturas";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

            return (int) ceil($resultado['total'] / self::REGISTROS_POR_PAGINA);
        } catch (PDOException $e) {
            error_log("Error en contarAsignaturas: " . $e->getMessage());
            return 0;
        }
    }

    // OBTENER ASIGNATURAS A PAGINAR
    public static function obtenerAsignaturasAPaginar($pagina)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $saltos = ($pagina - 1) * self::REGISTROS_POR_PAGINA;
            $lote = self::REGISTROS_POR_PAGINA;

            $sql = "SELECT a.*, f.nombreFacultad
                    FROM asignaturas a
                    LEFT JOIN facultad f ON a.idFacultad = f.idFacultad
                    ORDER BY a.nombreAsignatura ASC 
                    LIMIT :lote OFFSET :saltos";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':lote', $lote, PDO::PARAM_INT);
            $stmt->bindParam(':saltos', $saltos, PDO::PARAM_INT);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $asignaturas = [];
            
            foreach ($resultados as $fila) {
                $model = new AsignaturaModel();
                $model->hidratarDesdeArray($fila);
                $asignaturas[] = $model;
            }

            return $asignaturas;
        } catch (PDOException $e) {
            error_log("Error en obtenerAsignaturasAPaginar: " . $e->getMessage());
            return [];
        }
    }

    // CONTAR ASIGNATURAS POR FACULTAD
    public static function contarAsignaturasPorFacultad($idFacultad)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT COUNT(*) as total FROM asignaturas WHERE idFacultad = :idFacultad";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idFacultad', $idFacultad, PDO::PARAM_INT);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int) $resultado['total'];
        } catch (PDOException $e) {
            error_log("Error en contarAsignaturasPorFacultad: " . $e->getMessage());
            return 0;
        }
    }

    // OBTENER ASIGNATURAS POR FACULTAD CON PAGINACIÓN
    public static function obtenerAsignaturasPorFacultadPaginadas($idFacultad, $pagina)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $saltos = ($pagina - 1) * self::REGISTROS_POR_PAGINA;
            $lote = self::REGISTROS_POR_PAGINA;

            $sql = "SELECT a.*, f.nombreFacultad
                    FROM asignaturas a
                    LEFT JOIN facultad f ON a.idFacultad = f.idFacultad
                    WHERE a.idFacultad = :idFacultad 
                    ORDER BY a.nombreAsignatura ASC 
                    LIMIT :lote OFFSET :saltos";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idFacultad', $idFacultad, PDO::PARAM_INT);
            $stmt->bindParam(':lote', $lote, PDO::PARAM_INT);
            $stmt->bindParam(':saltos', $saltos, PDO::PARAM_INT);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $asignaturas = [];
            
            foreach ($resultados as $fila) {
                $model = new AsignaturaModel();
                $model->hidratarDesdeArray($fila);
                $asignaturas[] = $model;
            }

            return $asignaturas;
        } catch (PDOException $e) {
            error_log("Error en obtenerAsignaturasPorFacultadPaginadas: " . $e->getMessage());
            return [];
        }
    }

    // BUSCAR ASIGNATURAS POR TÉRMINO
    public static function buscarAsignaturas($termino)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $terminoBusqueda = "%$termino%";

            $sql = "SELECT a.*, f.nombreFacultad
                    FROM asignaturas a
                    LEFT JOIN facultad f ON a.idFacultad = f.idFacultad
                    WHERE a.codigoAsignatura LIKE :termino 
                       OR a.nombreAsignatura LIKE :termino
                       OR a.descripcion LIKE :termino
                    ORDER BY a.nombreAsignatura ASC";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':termino', $terminoBusqueda);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $asignaturas = [];
            
            foreach ($resultados as $fila) {
                $model = new AsignaturaModel();
                $model->hidratarDesdeArray($fila);
                $asignaturas[] = $model;
            }

            return $asignaturas;
        } catch (PDOException $e) {
            error_log("Error en buscarAsignaturas: " . $e->getMessage());
            return [];
        }
    }

    // BUSCAR ASIGNATURAS POR TÉRMINO CON PAGINACIÓN
    public static function buscarAsignaturasPaginadas($termino, $pagina)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $terminoBusqueda = "%$termino%";
            
            $saltos = ($pagina - 1) * self::REGISTROS_POR_PAGINA;
            $lote = self::REGISTROS_POR_PAGINA;

            $sql = "SELECT a.*, f.nombreFacultad
                    FROM asignaturas a
                    LEFT JOIN facultad f ON a.idFacultad = f.idFacultad
                    WHERE a.codigoAsignatura LIKE :termino 
                       OR a.nombreAsignatura LIKE :termino
                       OR a.descripcion LIKE :termino
                    ORDER BY a.nombreAsignatura ASC
                    LIMIT :lote OFFSET :saltos";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':termino', $terminoBusqueda);
            $stmt->bindParam(':lote', $lote, PDO::PARAM_INT);
            $stmt->bindParam(':saltos', $saltos, PDO::PARAM_INT);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $asignaturas = [];
            
            foreach ($resultados as $fila) {
                $model = new AsignaturaModel();
                $model->hidratarDesdeArray($fila);
                $asignaturas[] = $model;
            }

            return $asignaturas;
        } catch (PDOException $e) {
            error_log("Error en buscarAsignaturasPaginadas: " . $e->getMessage());
            return [];
        }
    }

    // CONTAR RESULTADOS DE BÚSQUEDA
    public static function contarResultadosBusqueda($termino)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $terminoBusqueda = "%$termino%";

            $sql = "SELECT COUNT(*) as total 
                    FROM asignaturas 
                    WHERE codigoAsignatura LIKE :termino 
                       OR nombreAsignatura LIKE :termino
                       OR descripcion LIKE :termino";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':termino', $terminoBusqueda);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int) ceil($resultado['total'] / self::REGISTROS_POR_PAGINA);
        } catch (PDOException $e) {
            error_log("Error en contarResultadosBusqueda: " . $e->getMessage());
            return 0;
        }
    }

    // INSERTAR ASIGNATURA CON TRANSACCIÓN
    public static function insertarAsignatura($datos)
    {
        $pdo = null;
        try {
            $pdo = ConexionUtil::conectar();
            $pdo->beginTransaction();

            $sql = "INSERT INTO asignaturas (codigoAsignatura, nombreAsignatura, descripcion, idFacultad) 
                    VALUES (:codigoAsignatura, :nombreAsignatura, :descripcion, :idFacultad)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':codigoAsignatura', $datos['codigoAsignatura']);
            $stmt->bindParam(':nombreAsignatura', $datos['nombreAsignatura']);
            $stmt->bindParam(':descripcion', $datos['descripcion']);
            $stmt->bindParam(':idFacultad', $datos['idFacultad']);
            
            if ($stmt->execute()) {
                $id = $pdo->lastInsertId();
                $pdo->commit();
                return $id;
            } else {
                $pdo->rollBack();
                return null;
            }
            
        } catch (PDOException $e) {
            if ($pdo) {
                $pdo->rollBack();
            }
            error_log("Error en insertarAsignatura: " . $e->getMessage());
            return null;
        }
    }

    // ACTUALIZAR ASIGNATURA CON TRANSACCIÓN
    public static function actualizarAsignatura($datos)
    {
        $pdo = null;
        try {
            $pdo = ConexionUtil::conectar();
            $pdo->beginTransaction();

            $sql = "UPDATE asignaturas SET 
                        codigoAsignatura = :codigoAsignatura,
                        nombreAsignatura = :nombreAsignatura,
                        descripcion = :descripcion
                    WHERE idAsignatura = :id";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $datos['id'], PDO::PARAM_INT);
            $stmt->bindParam(':codigoAsignatura', $datos['codigoAsignatura']);
            $stmt->bindParam(':nombreAsignatura', $datos['nombreAsignatura']);
            $stmt->bindParam(':descripcion', $datos['descripcion']);
            
            $resultado = $stmt->execute();
            
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
            error_log("Error en actualizarAsignatura: " . $e->getMessage());
            return false;
        }
    }

    // ELIMINAR ASIGNATURA CON TRANSACCIÓN
    public static function eliminarAsignatura($id)
    {
        $pdo = null;
        try {
            $pdo = ConexionUtil::conectar();
            $pdo->beginTransaction();

            // Verificar si la asignatura tiene relaciones en plan_semestre_asignatura
            $sqlVerificar = "SELECT COUNT(*) as total FROM plan_semestre_asignatura WHERE idAsignatura = :id";
            $stmtVerificar = $pdo->prepare($sqlVerificar);
            $stmtVerificar->bindParam(':id', $id, PDO::PARAM_INT);
            $stmtVerificar->execute();
            $resultado = $stmtVerificar->fetch(PDO::FETCH_ASSOC);
            
            if ($resultado['total'] > 0) {
                $pdo->rollBack();
                return false; // No se puede eliminar porque tiene relaciones
            }

            // Si no tiene relaciones, proceder a eliminar
            $sql = "DELETE FROM asignaturas WHERE idAsignatura = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                if ($stmt->rowCount() > 0) {
                    $pdo->commit();
                    return true;
                } else {
                    $pdo->rollBack();
                    return false;
                }
            } else {
                $pdo->rollBack();
                return false;
            }
            
        } catch (PDOException $e) {
            if ($pdo) {
                $pdo->rollBack();
            }
            error_log("Error en eliminarAsignatura: " . $e->getMessage());
            return false;
        }
    }

    // VERIFICAR SI EXISTE ASIGNATURA POR CÓDIGO (solo lectura)
    public static function existeAsignaturaPorCodigo($codigoAsignatura, $excluirId = null)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT COUNT(*) as total FROM asignaturas WHERE codigoAsignatura = :codigoAsignatura";
            
            if ($excluirId !== null) {
                $sql .= " AND idAsignatura != :excluirId";
            }
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':codigoAsignatura', $codigoAsignatura);
            
            if ($excluirId !== null) {
                $stmt->bindParam(':excluirId', $excluirId, PDO::PARAM_INT);
            }
            
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['total'] > 0;
        } catch (PDOException $e) {
            error_log("Error en existeAsignaturaPorCodigo: " . $e->getMessage());
            return false;
        }
    }

    // VERIFICAR SI EXISTE ASIGNATURA POR NOMBRE (solo lectura)
    public static function existeAsignaturaPorNombre($nombreAsignatura, $excluirId = null)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT COUNT(*) as total FROM asignaturas WHERE nombreAsignatura = :nombreAsignatura";
            
            if ($excluirId !== null) {
                $sql .= " AND idAsignatura != :excluirId";
            }
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':nombreAsignatura', $nombreAsignatura);
            
            if ($excluirId !== null) {
                $stmt->bindParam(':excluirId', $excluirId, PDO::PARAM_INT);
            }
            
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['total'] > 0;
        } catch (PDOException $e) {
            error_log("Error en existeAsignaturaPorNombre: " . $e->getMessage());
            return false;
        }
    }
}
?>