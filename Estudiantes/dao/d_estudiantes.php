
<?php

class EstudianteDAO
{

    // OBTENER INFORMACIÓN  DEL UN ESTUDIANTE
    public static function obtenerInformacionPersonal($codigo)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT 
                    e.codigo, e.Nombre, e.Apellidos, e.genero, 
                    e.fechaNacimiento, e.Nacionalidad, e.centroProcedencia,
                    c.nombre as carrera, f.Nombre as facultad
                FROM Estudiante e
                JOIN Inscripcion i ON e.codigo = i.codigo
                JOIN Carrera c ON i.idCarrera = c.idCarrera
                JOIN Facultad f ON c.idFacultad = f.idFacultad
                WHERE e.codigo = :codigo
                ORDER BY i.fecha DESC
            ";

            $stmt = $instanciaConexion->prepare($sql);

            $stmt->bindParam(':codigo', $codigo, PDO::PARAM_STR);

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$resultado) {
                throw new Exception('No se pudo obtener la información personal');
            }

            return $resultado;
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    // FUNCION PARA RECUPERAR PLAN SEGUN AÑO, CURSO, Y SEMESTRE

    public static function obtenerPlanEstudios($filtros = [])
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = " SELECT 
                    a.idAsignatura, a.Nombre, a.creditos, a.modalidad,
                    c.Nombre as curso, c.Nivel,
                    s.Nombre as semestre, s.Año, s.periodo,
                    pe.nombre as plan_estudio
                FROM Inscripcion ins
                JOIN Carrera ca ON ins.idCarrera = ca.idCarrera
                JOIN PlanEstudio pe ON ca.idCarrera = pe.idCarrera
                JOIN Curso_PlanEstudio cpe ON pe.periodoPlanEstudio = cpe.idPlanEstudio
                JOIN Curso c ON cpe.idCurso = c.idCurso
                JOIN Asignatura a ON a.idCurso = c.idCurso
                JOIN Semestre s ON cpe.idSemestre = s.idSemestre
                WHERE  
            ";



            if (!empty($filtros['codigo'])) {

                $sql .= " ins.codigo = :codigo";
            }

            if (!empty($filtros['anoAcademico'])) {

                $sql .= " AND s.Año = :ano";
            }

            if (!empty($filtros['idCurso'])) {

                $sql .= " AND c.idCurso = :idCurso";
            }

            if (!empty($filtros['idSemestre'])) {
                $sql .= " AND s.idSemestre = :idSemestre";
            }

            $sql .= " ORDER BY s.Año, c.Nivel, a.Nombre";

            $stmt = $instanciaConexion->prepare($sql);

            $stmt->bindParam(':codigo', $filtros['codigo']);
            $stmt->bindParam(':ano', $filtros['anoAcademico']);
            $stmt->bindParam('idCurso', $filtros['idCurso']);
            $stmt->bindParam('idSemestre', $filtros['idSemestre']);
            $resultado = $stmt->execute();

            return $resultado->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {

            return ['error' => $e->getMessage()];
        }
    }

    //FUNCION PARA VER HISTORIAL ACADÉMICO
    public static function obtenerHistorialAcademico($codigo)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT 
                    ea.idAsignatura, a.Nombre as asignatura,
                    ea.convocatoria,
                    GROUP_CONCAT(ev.nota ORDER BY ev.fecha) as notas
                FROM Estudiante_Asignatura ea
                JOIN Asignatura a ON ea.idAsignatura = a.idAsignatura
                JOIN Examen ex ON a.idAsignatura = ex.idAsignatura
                JOIN Evaluacion ev ON ex.idExamen = ev.idExamen
                WHERE ea.Codigo = :codigo
                GROUP BY ea.idAsignatura, ea.convocatoria
                ORDER BY a.Nombre, ea.convocatoria
            ";

            $stmt = $instanciaConexion->prepare($sql);

            $stmt->bindParam(':codigo', $codigo);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }



    //FUNCION PARA VER LOS DATOS DE LOS PAGOS REALIZADOS
    public static function obtenerDatosPagos($codigo)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT 
                    p.idPago, p.fechaPago, p.numeroCuota,
                    dp.cuota, dp.monto, dp.fecha as fecha_detalle,
                    i.anoAcademico, i.periodo
                FROM Inscripcion i
                JOIN Pago p ON i.idPago = p.idPago
                JOIN detallePago dp ON p.idPago = dp.idPago
                WHERE i.codigo = :codigo
                ORDER BY p.fechaPago DESC
            ";

            $stmt = $instanciaConexion->prepare($sql);

            $stmt->bindParam(':codigo', $codigo);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {

            return ['error' => $e->getMessage()];
        }
    }

    // FUNCION PARA OBTENER INFORMACION SOBRE LAS BECAS
    public static function obtenerInfoBeca($codigo)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT 
                    eb.fechaInicio, eb.fechaFinal,
                    b.institucionBeca,
                    CASE 
                        WHEN eb.fechaFinal >= CURDATE() THEN 'Activa'
                        ELSE 'Finalizada'
                    END as estado  
                FROM Estudiante_Beca eb
                JOIN Becario b ON eb.idBecario = b.idBecario
                WHERE eb.Codigo = :codigo
                ORDER BY eb.fechaInicio DESC
            ";

            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':codigo', $codigo);
            $resultado = $stmt->execute();
            return $resultado->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    // FUNCION PARA CONSULTAR CONSULTAR CALIFICACIONES
    public static function obtenerCalificaciones($codigo)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT 
                    ev.idEvaluacion, ev.tipo, ev.numero, ev.nota, ev.fecha, ev.Hora,
                    ex.Codigo as codigo_examen,
                    a.Nombre as asignatura,
                    p.nombre as profesor_nombre, p.apellidos as profesor_apellidos
                FROM Estudiante_Asignatura ea
                JOIN Asignatura a ON ea.idAsignatura = a.idAsignatura
                JOIN Examen ex ON a.idAsignatura = ex.idAsignatura
                JOIN Evaluacion ev ON ex.idExamen = ev.idExamen
                JOIN Profesor p ON ex.idProfesor = p.idProfesor
                WHERE ea.Codigo = :codigo
                ORDER BY ev.fecha DESC, ev.tipo
            ";

            $stmt = $instanciaConexion->prepare($sql);

            $stmt->bindParam(':codigo', $codigo);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    //FUNCION PARA VISUALIZAR HORARIOS DE EXÁMENES
    public static function obtenerHorariosExamenes($codigo)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT 
                    ev.fecha, ev.Hora, ev.tipo,
                    a.Nombre as asignatura,
                    au.nombre as aula
                FROM Estudiante_Asignatura ea
                JOIN Asignatura a ON ea.idAsignatura = a.idAsignatura
                JOIN Examen ex ON a.idAsignatura = ex.idAsignatura
                JOIN Evaluacion ev ON ex.idExamen = ev.idExamen
                LEFT JOIN Clase cl ON a.idAsignatura = cl.idAsignatura
                LEFT JOIN Aulas au ON cl.idAula = au.idAula
                WHERE ea.Codigo = :codigo 
                AND ev.fecha >= CURDATE()
                ORDER BY ev.fecha, ev.Hora
            ";

            $stmt = $instanciaConexion->prepare($sql);

            $stmt->bindParam(':codigo', $codigo);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    //FUNCION PARA CONSULTAR HORARIO DE CLASES
    public static function obtenerHorarioClases($codigo)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT 
                    cl.diaSemanal, cl.horaInicio, cl.HoraFinal,
                    a.Nombre as asignatura,
                    au.nombre as aula,
                    p.nombre as profesor_nombre, p.apellidos as profesor_apellidos,
                    h.nombre as horario
                FROM Estudiante_Asignatura ea
                JOIN Asignatura a ON ea.idAsignatura = a.idAsignatura
                JOIN Clase cl ON a.idAsignatura = cl.idAsignatura
                JOIN Aulas au ON cl.idAula = au.idAula
                JOIN Profesor p ON cl.idProfesor = p.idProfesor
                JOIN Horario h ON cl.idHorario = h.idHorario
                WHERE ea.Codigo = :codigo
                ORDER BY FIELD(cl.diaSemanal, 'L', 'M', 'X', 'J', 'V'),cl.horaInicio
            ";

            $stmt = $instanciaConexion->prepare($sql);

            $stmt->bindParam(':codigo', $codigo);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    // FUNCION PARA VER INFORMACIÓN DE PROFESORES
    public static function obtenerProfesoresAsignaturas($codigo)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT DISTINCT
                    p.idProfesor, p.nombre, p.apellidos, p.correo, 
                    p.departamento, p.genero,
                    a.Nombre as asignatura
                FROM Estudiante_Asignatura ea
                JOIN Asignatura a ON ea.idAsignatura = a.idAsignatura
                JOIN Clase cl ON a.idAsignatura = cl.idAsignatura
                JOIN Profesor p ON cl.idProfesor = p.idProfesor
                WHERE ea.Codigo = :codigo
                ORDER BY p.apellidos, p.nombre
            ";

            $stmt = $instanciaConexion->prepare($sql);

            $stmt->bindParam(':codigo', $codigo);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    //FUNCION PARA OBTENER CONSULTAS ENVIADAS A UN ESTUDIANTE(ACTUALIZAR)
    public static function obtenerConsultas($codigo)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT 
                    c.idConsulta, c.tipo, c.motivo, c.contenido,
                    cu.fecha_creacion,
                    u_emisor.login as emisor,
                    u_receptor.login as receptor
                FROM Consultas c
                JOIN Consultas_Usuario cu ON c.idConsulta = cu.idConsulta
                JOIN Usuario u_emisor ON cu.idEmisor = u_emisor.idUsuario
                AND  cu.idReceptor = u_receptor.idUsuario
                JOIN Estudiante e ON u_emisor.idUsuario = e.idUsuario
                WHERE e.codigo = :codigo
                ORDER BY cu.fecha_creacion DESC
            ";

            $stmt = $instanciaConexion->prepare($sql);

            $stmt->bindParam(':codigo', $codigo);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }


    //FUNCION PARA OBTENER LAS GUÍAS DIDÁCTICAS
    public static function obtenerGuiasDidacticas($codigo)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT 
                    gd.idGuia,
                    a.Nombre as asignatura,
                    d.url as documento_url,
                    d.fecha_subida
                FROM Estudiante_Asignatura ea
                JOIN Asignatura a ON ea.idAsignatura = a.idAsignatura
                JOIN Guiasdidacticas gd ON a.idAsignatura = gd.idAsignatura
                JOIN Documentos d ON gd.idGuia = d.idGuia
                WHERE ea.Codigo = :codigo
                ORDER BY a.Nombre, d.fecha_subida DESC
            ";

            $stmt = $instanciaConexion->prepare($sql);

            $stmt->bindParam(':codigo', $codigo);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }


    // FUNCION PARA OBTENER INFORMACION SOBRE LA CUENTA DE USUARIO QUE PERTENECE A UN ESTUDIANTE
    public static function obtenerInfoCuenta($codigo)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT 
                u.login, u.rol, u.estado,
                e.codigo, e.Nombre, e.Apellidos
                FROM Estudiante e
                JOIN Usuario u ON e.idUsuario = u.idUsuario
                WHERE e.codigo = :codigo
            ";

            $stmt = $instanciaConexion->prepare($sql);

            $stmt->bindParam(':codigo', $codigo);

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$resultado) {
                throw new Exception('No se pudo obtener la información de la cuenta');
            }

            return $resultado;
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }
}

?>