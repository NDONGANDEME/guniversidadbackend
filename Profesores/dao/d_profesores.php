<?php
class ProfesorDao
{
    // FUNCIONES PARA LA GESTIÓN DE ASIGNATURAS
    public static function obtenerAsignaturasImpartidas($idProfesor)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT a.* FROM asignatura a 
                    INNER JOIN clase c ON a.idAsignatura = c.idAsignatura 
                    WHERE c.idProfesor = :idProfesor 
                    GROUP BY a.idAsignatura";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idProfesor', $idProfesor, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    // FUNCIONES PARA LA GESTIÓN DE CLASES/HORARIOS
    public static function obtenerHorarioProfesor($idProfesor, $idSemestre = null)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT c.*, a.NombreAsignatura, au.nombre as nombreAula, 
                    h.HoraInicio, h.HoraFinal, h.dia
                    FROM clase c
                    INNER JOIN asignatura a ON c.idAsignatura = a.idAsignatura
                    LEFT JOIN aulas au ON c.idAula = au.idAula
                    LEFT JOIN horario h ON c.idHorario = h.idHorario
                    WHERE c.idProfesor = :idProfesor";

            if ($idSemestre) {
                $sql .= " AND a.idSemestre = :idSemestre";
            }

            $sql .= " ORDER BY h.dia, h.HoraInicio";

            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idProfesor', $idProfesor, PDO::PARAM_INT);
            if ($idSemestre) {
                $stmt->bindParam(':idSemestre', $idSemestre, PDO::PARAM_INT);
            }
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    // FUNCIONES PARA LA GESTIÓN DE ESTUDIANTES
    public static function obtenerEstudiantesPorAsignatura($idAsignatura)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT e.* FROM estudiante e
                    INNER JOIN estudiante_asignatura ea ON e.idEstudiante = ea.idEstudiante
                    WHERE ea.idAsignatura = :idAsignatura";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idAsignatura', $idAsignatura, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function obtenerEstudiantesPorClase($idClase)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT e.* FROM estudiante e
                    INNER JOIN clase c ON c.idCurso = e.idCurso
                    WHERE c.idClase = :idClase";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idClase', $idClase, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    // FUNCIONES PARA LA GESTIÓN DE EVALUACIONES
    public static function obtenerEvaluacionesPorAsignatura($idAsignatura)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT ev.*, e.NombreEstudiante, e.ApellidosEstudiante 
                    FROM evaluacion ev
                    INNER JOIN examen ex ON ev.idExamen = ex.idExamen
                    INNER JOIN estudiante e ON ex.idEstudiante = e.idEstudiante
                    WHERE ex.idAsignatura = :idAsignatura
                    ORDER BY ev.fecha DESC";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idAsignatura', $idAsignatura, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function crearEvaluacion($datosEvaluacion)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "INSERT INTO evaluacion (idExamen, tipo, numero, nota, fecha, Hora) 
                    VALUES (:idExamen, :tipo, :numero, :nota, :fecha, :Hora)";
            $stmt = $instanciaConexion->prepare($sql);

            $stmt->bindParam(':idExamen', $datosEvaluacion['idExamen'], PDO::PARAM_INT);
            $stmt->bindParam(':tipo', $datosEvaluacion['tipo'], PDO::PARAM_STR);
            $stmt->bindParam(':numero', $datosEvaluacion['numero'], PDO::PARAM_INT);
            $stmt->bindParam(':nota', $datosEvaluacion['nota'], PDO::PARAM_STR);
            $stmt->bindParam(':fecha', $datosEvaluacion['fecha'], PDO::PARAM_STR);
            $stmt->bindParam(':Hora', $datosEvaluacion['Hora'], PDO::PARAM_STR);

            $stmt->execute();
            return $instanciaConexion->lastInsertId();
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function actualizarNotaEvaluacion($idEvaluacion, $nuevaNota)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "UPDATE evaluacion SET nota = :nota WHERE idEvaluacion = :idEvaluacion";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':nota', $nuevaNota, PDO::PARAM_STR);
            $stmt->bindParam(':idEvaluacion', $idEvaluacion, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    // FUNCIONES PARA LA GESTIÓN DE EXAMENES
    public static function crearExamen($datosExamen)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "INSERT INTO examen (idProfesor, idEstudiante, idAsignatura, EstadoExamen, 
                    NotaExamen, NumeroExamen, FechaExamen, HoraExamen, idSemestre) 
                    VALUES (:idProfesor, :idEstudiante, :idAsignatura, :EstadoExamen, 
                    :NotaExamen, :NumeroExamen, :FechaExamen, :HoraExamen, :idSemestre)";
            $stmt = $instanciaConexion->prepare($sql);

            $stmt->bindParam(':idProfesor', $datosExamen['idProfesor'], PDO::PARAM_INT);
            $stmt->bindParam(':idEstudiante', $datosExamen['idEstudiante'], PDO::PARAM_INT);
            $stmt->bindParam(':idAsignatura', $datosExamen['idAsignatura'], PDO::PARAM_INT);
            $stmt->bindParam(':EstadoExamen', $datosExamen['EstadoExamen'], PDO::PARAM_STR);
            $stmt->bindParam(':NotaExamen', $datosExamen['NotaExamen'], PDO::PARAM_STR);
            $stmt->bindParam(':NumeroExamen', $datosExamen['NumeroExamen'], PDO::PARAM_INT);
            $stmt->bindParam(':FechaExamen', $datosExamen['FechaExamen'], PDO::PARAM_STR);
            $stmt->bindParam(':HoraExamen', $datosExamen['HoraExamen'], PDO::PARAM_STR);
            $stmt->bindParam(':idSemestre', $datosExamen['idSemestre'], PDO::PARAM_INT);

            $stmt->execute();
            return $instanciaConexion->lastInsertId();
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function obtenerExamenesPorProfesor($idProfesor)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT ex.*, e.NombreEstudiante, e.ApellidosEstudiante, a.NombreAsignatura
                    FROM examen ex
                    INNER JOIN estudiante e ON ex.idEstudiante = e.idEstudiante
                    INNER JOIN asignatura a ON ex.idAsignatura = a.idAsignatura
                    WHERE ex.idProfesor = :idProfesor
                    ORDER BY ex.FechaExamen DESC";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idProfesor', $idProfesor, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function obtenerFormacionProfesor($idProfesor)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT * FROM formacion WHERE idProfesor = :idProfesor ORDER BY nivel DESC";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idProfesor', $idProfesor, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function agregarFormacion($datosFormacion)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "INSERT INTO formacion (institution, tipo, titulo, nivel, idProfesor) 
                    VALUES (:institution, :tipo, :titulo, :nivel, :idProfesor)";
            $stmt = $instanciaConexion->prepare($sql);

            $stmt->bindParam(':institution', $datosFormacion['institution'], PDO::PARAM_STR);
            $stmt->bindParam(':tipo', $datosFormacion['tipo'], PDO::PARAM_STR);
            $stmt->bindParam(':titulo', $datosFormacion['titulo'], PDO::PARAM_STR);
            $stmt->bindParam(':nivel', $datosFormacion['nivel'], PDO::PARAM_INT);
            $stmt->bindParam(':idProfesor', $datosFormacion['idProfesor'], PDO::PARAM_INT);

            $stmt->execute();
            return $instanciaConexion->lastInsertId();
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    // FUNCIONES PARA LA GESTIÓN DE CONSULTAS
    public static function obtenerConsultasRecibidas($idUsuarioDestinatario)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT c.*, u.NombreUsuario as emisor 
                    FROM consultas c
                    INNER JOIN destinatarioconsulta dc ON c.idConsulta = dc.idConsulta
                    INNER JOIN usuarios u ON c.idEmisor = u.idUsuario
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

    public static function crearConsulta($datosConsulta)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "INSERT INTO consultas (tipo, motivo, contenido, idEmisor) 
                    VALUES (:tipo, :motivo, :contenido, :idEmisor)";
            $stmt = $instanciaConexion->prepare($sql);

            $stmt->bindParam(':tipo', $datosConsulta['tipo'], PDO::PARAM_STR);
            $stmt->bindParam(':motivo', $datosConsulta['motivo'], PDO::PARAM_STR);
            $stmt->bindParam(':contenido', $datosConsulta['contenido'], PDO::PARAM_STR);
            $stmt->bindParam(':idEmisor', $datosConsulta['idEmisor'], PDO::PARAM_INT);

            $stmt->execute();
            $idConsulta = $instanciaConexion->lastInsertId();

            // Registrar destinatario
            self::registrarDestinatarioConsulta($idConsulta, $datosConsulta['idDestinatario'], $datosConsulta['rolDestino']);

            return $idConsulta;
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    private static function registrarDestinatarioConsulta($idConsulta, $idDestinatario, $rolDestino)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "INSERT INTO destinatarioconsulta (idConsulta, idUsuarioDestinatario, rolDestinado, estado) 
                    VALUES (:idConsulta, :idUsuarioDestinatario, :rolDestinado, 'pendiente')";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idConsulta', $idConsulta, PDO::PARAM_INT);
            $stmt->bindParam(':idUsuarioDestinatario', $idDestinatario, PDO::PARAM_INT);
            $stmt->bindParam(':rolDestinado', $rolDestino, PDO::PARAM_STR);
            return $stmt->execute();
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    // ================================================
    // FUNCIONES PARA LA GESTIÓN DE INFORMES
    // ================================================

    public static function crearInforme($datosInforme)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "INSERT INTO informes (asunto, contenido, idUsuario) 
                    VALUES (:asunto, :contenido, :idUsuario)";
            $stmt = $instanciaConexion->prepare($sql);

            $stmt->bindParam(':asunto', $datosInforme['asunto'], PDO::PARAM_STR);
            $stmt->bindParam(':contenido', $datosInforme['contenido'], PDO::PARAM_STR);
            $stmt->bindParam(':idUsuario', $datosInforme['idUsuario'], PDO::PARAM_INT);

            $stmt->execute();
            return $instanciaConexion->lastInsertId();
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function obtenerInformesEnviados($idUsuario)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT * FROM informes WHERE idUsuario = :idUsuario ORDER BY idInforme DESC";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function obtenerGuiasPorAsignatura($idAsignatura)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT g.*, d.url FROM guiasdidacticas g
                    LEFT JOIN documento d ON g.idGuia = d.idGuia
                    WHERE g.idAsignatura = :idAsignatura";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idAsignatura', $idAsignatura, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }
}
