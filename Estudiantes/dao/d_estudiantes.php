<?php
class EstudianteDAO
{
    // ================================================
    // FUNCIONES PARA INFORMACIÓN PERSONAL DEL ESTUDIANTE
    // ================================================

    public static function obtenerInformacionPersonal($idEstudiante)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT e.*, 
                    c.NombreCarrera, f.NombreFacultad,
                    cu.NombreCurso, s.NombreSemestre
                    FROM estudiante e
                    LEFT JOIN carrera c ON e.idCarrera = c.idCarrera
                    LEFT JOIN facultad f ON c.idFacultad = f.idfacultad
                    LEFT JOIN curso cu ON e.idCurso = cu.idCurso
                    LEFT JOIN semestre s ON e.idSemestre = s.idSemestre
                    WHERE e.idEstudiante = :idEstudiante";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idEstudiante', $idEstudiante, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function obtenerInformacionPersonalPorCodigo($codigoEstudiante)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT e.*, 
                    c.NombreCarrera, f.NombreFacultad,
                    cu.NombreCurso, s.NombreSemestre
                    FROM estudiante e
                    LEFT JOIN carrera c ON e.idCarrera = c.idCarrera
                    LEFT JOIN facultad f ON c.idFacultad = f.idfacultad
                    LEFT JOIN curso cu ON e.idCurso = cu.idCurso
                    LEFT JOIN semestre s ON e.idSemestre = s.idSemestre
                    WHERE e.CodigoEstudiante = :codigoEstudiante";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':codigoEstudiante', $codigoEstudiante, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function actualizarInformacionPersonal($datosEstudiante)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "UPDATE estudiante SET 
                    NombreEstudiante = :NombreEstudiante,
                    ApellidosEstudiante = :ApellidosEstudiante,
                    dipEstudiante = :dipEstudiante,
                    CorreoEstudiante = :CorreoEstudiante,
                    FechadeNacimiento = :FechadeNacimiento,
                    Sexo = :Sexo,
                    Nacionalidad = :Nacionalidad,
                    Foto = :Foto
                    WHERE idEstudiante = :idEstudiante";
            
            $stmt = $instanciaConexion->prepare($sql);
            
            $stmt->bindParam(':NombreEstudiante', $datosEstudiante['NombreEstudiante'], PDO::PARAM_STR);
            $stmt->bindParam(':ApellidosEstudiante', $datosEstudiante['ApellidosEstudiante'], PDO::PARAM_STR);
            $stmt->bindParam(':dipEstudiante', $datosEstudiante['dipEstudiante'], PDO::PARAM_INT);
            $stmt->bindParam(':CorreoEstudiante', $datosEstudiante['CorreoEstudiante'], PDO::PARAM_STR);
            $stmt->bindParam(':FechadeNacimiento', $datosEstudiante['FechadeNacimiento'], PDO::PARAM_STR);
            $stmt->bindParam(':Sexo', $datosEstudiante['Sexo'], PDO::PARAM_STR);
            $stmt->bindParam(':Nacionalidad', $datosEstudiante['Nacionalidad'], PDO::PARAM_STR);
            $stmt->bindParam(':Foto', $datosEstudiante['Foto'], PDO::PARAM_STR);
            $stmt->bindParam(':idEstudiante', $datosEstudiante['idEstudiante'], PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    // ================================================
    // FUNCIONES PARA PLAN DE ESTUDIOS
    // ================================================

    public static function obtenerPlanEstudios($idCarrera, $idSemestre = null)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT pe.*, 
                    cp.idCurso, c.NombreCurso, c.CreditosCurso,
                    s.NombreSemestre
                    FROM planestudio pe
                    LEFT JOIN curso_planestudio cp ON pe.idPlanEstudio = cp.idPlanEstudio
                    LEFT JOIN curso c ON cp.idCurso = c.idCurso
                    LEFT JOIN semestre s ON cp.idSemestre = s.idSemestre
                    WHERE pe.idCarrera = :idCarrera";
            
            if ($idSemestre) {
                $sql .= " AND cp.idSemestre = :idSemestre";
            }
            
            $sql .= " ORDER BY s.idSemestre, c.NombreCurso";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idCarrera', $idCarrera, PDO::PARAM_INT);
            
            if ($idSemestre) {
                $stmt->bindParam(':idSemestre', $idSemestre, PDO::PARAM_INT);
            }
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    // ================================================
    // FUNCIONES PARA HISTORIAL ACADÉMICO
    // ================================================

    public static function obtenerHistorialAcademico($idEstudiante)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT ea.*, 
                    a.NombreAsignatura, a.Creditos,
                    s.NombreSemestre,
                    GROUP_CONCAT(CONCAT(ev.tipo, ': ', ev.nota) ORDER BY ev.fecha SEPARATOR '; ') as evaluaciones
                    FROM estudiante_asignatura ea
                    LEFT JOIN asignatura a ON ea.idAsignatura = a.idAsignatura
                    LEFT JOIN examen ex ON a.idAsignatura = ex.idAsignatura AND ex.idEstudiante = ea.idEstudiante
                    LEFT JOIN evaluacion ev ON ex.idExamen = ev.idExamen
                    LEFT JOIN semestre s ON a.idSemestre = s.idSemestre
                    WHERE ea.idEstudiante = :idEstudiante
                    GROUP BY ea.idAsignatura, ea.convocatoria
                    ORDER BY s.idSemestre, a.NombreAsignatura";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idEstudiante', $idEstudiante, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    // ================================================
    // FUNCIONES PARA GESTIÓN DE PAGOS
    // ================================================

    public static function obtenerDatosPagos($idEstudiante)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT p.*, 
                    m.EstadoMatricula, m.AñoAcademico
                    FROM pago p
                    LEFT JOIN matricula m ON p.idMatricula = m.idMatricula
                    WHERE m.idEstudiante = :idEstudiante
                    ORDER BY p.fecha DESC";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idEstudiante', $idEstudiante, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function obtenerEstadoPago($idEstudiante)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT 
                    COUNT(p.idPago) as totalPagos,
                    SUM(p.monto) as totalPagado,
                    MAX(p.fecha) as ultimoPago,
                    m.EstadoMatricula
                    FROM pago p
                    LEFT JOIN matricula m ON p.idMatricula = m.idMatricula
                    WHERE m.idEstudiante = :idEstudiante
                    GROUP BY m.idMatricula";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idEstudiante', $idEstudiante, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    // ================================================
    // FUNCIONES PARA BECAS
    // ================================================

    public static function obtenerInfoBeca($idEstudiante)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT eb.*, 
                    b.institucionBeca,
                    CASE 
                        WHEN eb.fechaFinal >= CURDATE() THEN 'Activa'
                        ELSE 'Finalizada'
                    END as estado
                    FROM estudiante_beca eb
                    LEFT JOIN becario b ON eb.idBecario = b.idBecario
                    WHERE eb.Codigo = :idEstudiante
                    ORDER BY eb.fechaInicio DESC";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idEstudiante', $idEstudiante, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    // ================================================
    // FUNCIONES PARA CALIFICACIONES
    // ================================================

    public static function obtenerCalificaciones($idEstudiante)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT ev.*, 
                    a.NombreAsignatura,
                    CONCAT(p.NombreProfesor, ' ', p.ApellidosProfesor) as NombreProfesor,
                    ex.FechaExamen
                    FROM evaluacion ev
                    LEFT JOIN examen ex ON ev.idExamen = ex.idExamen
                    LEFT JOIN asignatura a ON ex.idAsignatura = a.idAsignatura
                    LEFT JOIN profesor p ON ex.idProfesor = p.idProfesor
                    WHERE ex.idEstudiante = :idEstudiante
                    ORDER BY ev.fecha DESC, a.NombreAsignatura";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idEstudiante', $idEstudiante, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function obtenerPromedioPorAsignatura($idEstudiante, $idAsignatura)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT 
                    AVG(ev.nota) as promedio,
                    COUNT(ev.idEvaluacion) as totalEvaluaciones
                    FROM evaluacion ev
                    LEFT JOIN examen ex ON ev.idExamen = ex.idExamen
                    WHERE ex.idEstudiante = :idEstudiante 
                    AND ex.idAsignatura = :idAsignatura";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idEstudiante', $idEstudiante, PDO::PARAM_INT);
            $stmt->bindParam(':idAsignatura', $idAsignatura, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    // ================================================
    // FUNCIONES PARA HORARIOS DE EXÁMENES
    // ================================================

    public static function obtenerHorariosExamenes($idEstudiante)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT ex.*, 
                    a.NombreAsignatura,
                    ev.tipo, ev.fecha, ev.Hora,
                    CONCAT(p.NombreProfesor, ' ', p.ApellidosProfesor) as NombreProfesor
                    FROM examen ex
                    LEFT JOIN asignatura a ON ex.idAsignatura = a.idAsignatura
                    LEFT JOIN evaluacion ev ON ex.idExamen = ev.idExamen
                    LEFT JOIN profesor p ON ex.idProfesor = p.idProfesor
                    WHERE ex.idEstudiante = :idEstudiante 
                    AND ev.fecha >= CURDATE()
                    ORDER BY ev.fecha, ev.Hora";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idEstudiante', $idEstudiante, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    // ================================================
    // FUNCIONES PARA HORARIO DE CLASES
    // ================================================

    public static function obtenerHorarioClases($idEstudiante)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT DISTINCT cl.*, 
                    a.NombreAsignatura,
                    au.nombre as nombreAula,
                    CONCAT(p.NombreProfesor, ' ', p.ApellidosProfesor) as NombreProfesor,
                    h.dia, h.HoraInicio, h.HoraFinal
                    FROM clase cl
                    LEFT JOIN asignatura a ON cl.idAsignatura = a.idAsignatura
                    LEFT JOIN estudiante_asignatura ea ON a.idAsignatura = ea.idAsignatura
                    LEFT JOIN aulas au ON cl.idAula = au.idAula
                    LEFT JOIN profesor p ON cl.idProfesor = p.idProfesor
                    LEFT JOIN horario h ON cl.idHorario = h.idHorario
                    WHERE ea.idEstudiante = :idEstudiante
                    ORDER BY h.dia, h.HoraInicio";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idEstudiante', $idEstudiante, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    // ================================================
    // FUNCIONES PARA PROFESORES DE ASIGNATURAS
    // ================================================

    public static function obtenerProfesoresAsignaturas($idEstudiante)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT DISTINCT p.*, 
                    a.NombreAsignatura,
                    d.NombreDepartamento,
                    f.NombreFacultad
                    FROM profesor p
                    LEFT JOIN clase cl ON p.idProfesor = cl.idProfesor
                    LEFT JOIN asignatura a ON cl.idAsignatura = a.idAsignatura
                    LEFT JOIN estudiante_asignatura ea ON a.idAsignatura = ea.idAsignatura
                    LEFT JOIN departamento d ON p.idDepartamento = d.idDepartamento
                    LEFT JOIN facultad f ON p.idFacultad = f.idfacultad
                    WHERE ea.idEstudiante = :idEstudiante
                    ORDER BY p.ApellidosProfesor, p.NombreProfesor";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idEstudiante', $idEstudiante, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    // ================================================
    // FUNCIONES PARA CONSULTAS
    // ================================================

    public static function obtenerConsultasRecibidas($idUsuarioDestinatario)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT c.*, 
                    u.NombreUsuario as emisor,
                    dc.estado
                    FROM consultas c
                    LEFT JOIN destinatarioconsulta dc ON c.idConsulta = dc.idConsulta
                    LEFT JOIN usuarios u ON c.idEmisor = u.idUsuario
                    WHERE dc.idUsuarioDestinatario = :idUsuarioDestinatario
                    ORDER BY c.idConsulta DESC";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idUsuarioDestinatario', $idUsuarioDestinatario, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function obtenerConsultasEnviadas($idEmisor)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT c.*, 
                    GROUP_CONCAT(DISTINCT u.NombreUsuario) as destinatarios,
                    GROUP_CONCAT(DISTINCT dc.estado) as estados
                    FROM consultas c
                    LEFT JOIN destinatarioconsulta dc ON c.idConsulta = dc.idConsulta
                    LEFT JOIN usuarios u ON dc.idUsuarioDestinatario = u.idUsuario
                    WHERE c.idEmisor = :idEmisor
                    GROUP BY c.idConsulta
                    ORDER BY c.idConsulta DESC";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idEmisor', $idEmisor, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    // ================================================
    // FUNCIONES PARA GUIAS DIDÁCTICAS
    // ================================================

    public static function obtenerGuiasDidacticas($idEstudiante)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT DISTINCT g.*, 
                    d.url,
                    a.NombreAsignatura
                    FROM guiasdidacticas g
                    LEFT JOIN documento d ON g.idGuia = d.idGuia
                    LEFT JOIN asignatura a ON g.idAsignatura = a.idAsignatura
                    LEFT JOIN estudiante_asignatura ea ON a.idAsignatura = ea.idAsignatura
                    WHERE ea.idEstudiante = :idEstudiante
                    ORDER BY a.NombreAsignatura";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idEstudiante', $idEstudiante, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    // ================================================
    // FUNCIONES PARA ASIGNATURAS MATRICULADAS
    // ================================================

    public static function obtenerAsignaturasMatriculadas($idEstudiante, $idSemestre = null)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT ea.*, 
                    a.*,
                    s.NombreSemestre,
                    c.NombreCurso,
                    ca.NombreCarrera
                    FROM estudiante_asignatura ea
                    LEFT JOIN asignatura a ON ea.idAsignatura = a.idAsignatura
                    LEFT JOIN semestre s ON a.idSemestre = s.idSemestre
                    LEFT JOIN curso c ON a.idCurso = c.idCurso
                    LEFT JOIN carrera ca ON a.idCarrera = ca.idCarrera
                    WHERE ea.idEstudiante = :idEstudiante";
            
            if ($idSemestre) {
                $sql .= " AND a.idSemestre = :idSemestre";
            }
            
            $sql .= " ORDER BY s.idSemestre, a.NombreAsignatura";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idEstudiante', $idEstudiante, PDO::PARAM_INT);
            
            if ($idSemestre) {
                $stmt->bindParam(':idSemestre', $idSemestre, PDO::PARAM_INT);
            }
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    // ================================================
    // FUNCIONES PARA INFORMACIÓN DE MATRÍCULA
    // ================================================

    public static function obtenerInfoMatricula($idEstudiante)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT m.*, 
                    f.NombreFacultad,
                    c.NombreCurso,
                    ca.NombreCarrera
                    FROM matricula m
                    LEFT JOIN facultad f ON m.idFacultad = f.idfacultad
                    LEFT JOIN curso c ON m.idCurso = c.idCurso
                    LEFT JOIN carrera ca ON c.idCarrera = ca.idCarrera
                    WHERE m.idEstudiante = :idEstudiante
                    ORDER BY m.FechaMatricula DESC";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idEstudiante', $idEstudiante, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    // ================================================
    // FUNCIONES PARA INFORMACIÓN DE FAMILIARES
    // ================================================

    public static function obtenerFamiliares($idEstudiante)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT * FROM familiar 
                    WHERE idEstudiante = :idEstudiante
                    ORDER BY NombreTutor";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idEstudiante', $idEstudiante, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    // ================================================
    // FUNCIONES PARA ESTADÍSTICAS ACADÉMICAS
    // ================================================

    public static function obtenerEstadisticasAcademicas($idEstudiante)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT 
                    COUNT(DISTINCT ea.idAsignatura) as totalAsignaturas,
                    COUNT(DISTINCT CASE WHEN ea.convocatoria = 1 THEN ea.idAsignatura END) as aprobadasPrimera,
                    COUNT(DISTINCT CASE WHEN ea.convocatoria > 1 THEN ea.idAsignatura END) as aprobadasOtras,
                    AVG(ev.nota) as promedioGeneral,
                    MIN(ev.nota) as notaMinima,
                    MAX(ev.nota) as notaMaxima
                    FROM estudiante_asignatura ea
                    LEFT JOIN asignatura a ON ea.idAsignatura = a.idAsignatura
                    LEFT JOIN examen ex ON a.idAsignatura = ex.idAsignatura AND ex.idEstudiante = ea.idEstudiante
                    LEFT JOIN evaluacion ev ON ex.idExamen = ev.idExamen
                    WHERE ea.idEstudiante = :idEstudiante";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idEstudiante', $idEstudiante, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    // ================================================
    // FUNCIONES PARA INFORMACIÓN DE CUENTA DE USUARIO
    // ================================================

    public static function obtenerInfoCuenta($idEstudiante)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT e.*, 
                    u.login, u.rol, u.estado as estadoUsuario
                    FROM estudiante e
                    LEFT JOIN usuarios u ON e.idEstudiante = u.idUsuario
                    WHERE e.idEstudiante = :idEstudiante";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idEstudiante', $idEstudiante, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    // ================================================
    // FUNCIONES PARA NOTAS POR ASIGNATURA
    // ================================================

    public static function obtenerNotasPorAsignatura($idEstudiante, $idAsignatura)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT ev.*, 
                    ex.FechaExamen,
                    ex.EstadoExamen
                    FROM evaluacion ev
                    LEFT JOIN examen ex ON ev.idExamen = ex.idExamen
                    WHERE ex.idEstudiante = :idEstudiante 
                    AND ex.idAsignatura = :idAsignatura
                    ORDER BY ev.fecha DESC";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idEstudiante', $idEstudiante, PDO::PARAM_INT);
            $stmt->bindParam(':idAsignatura', $idAsignatura, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    // ================================================
    // FUNCIONES PARA PRÓXIMOS EXÁMENES
    // ================================================

    public static function obtenerProximosExamenes($idEstudiante, $limite = 5)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT ev.*, 
                    a.NombreAsignatura,
                    ex.FechaExamen,
                    ex.HoraExamen,
                    DATEDIFF(ev.fecha, CURDATE()) as diasRestantes
                    FROM evaluacion ev
                    LEFT JOIN examen ex ON ev.idExamen = ex.idExamen
                    LEFT JOIN asignatura a ON ex.idAsignatura = a.idAsignatura
                    WHERE ex.idEstudiante = :idEstudiante 
                    AND ev.fecha >= CURDATE()
                    ORDER BY ev.fecha ASC
                    LIMIT :limite";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idEstudiante', $idEstudiante, PDO::PARAM_INT);
            $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    // ================================================
    // FUNCIONES PARA ASISTENCIA (ASUMIENDO TABLA ASISTENCIA)
    // ================================================

    public static function obtenerAsistencia($idEstudiante, $idAsignatura = null)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT 
                    COUNT(*) as totalClases,
                    COUNT(CASE WHEN a.estado = 'presente' THEN 1 END) as presentes,
                    COUNT(CASE WHEN a.estado = 'ausente' THEN 1 END) as ausentes,
                    COUNT(CASE WHEN a.estado = 'justificado' THEN 1 END) as justificados
                    FROM asistencia a
                    LEFT JOIN clase cl ON a.idClase = cl.idClase
                    WHERE a.idEstudiante = :idEstudiante";
            
            if ($idAsignatura) {
                $sql .= " AND cl.idAsignatura = :idAsignatura";
            }
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idEstudiante', $idEstudiante, PDO::PARAM_INT);
            
            if ($idAsignatura) {
                $stmt->bindParam(':idAsignatura', $idAsignatura, PDO::PARAM_INT);
            }
            
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }
}
?>