<?php
class SecretarioDao
{

    // FUNCIONES PARA LA GESTIÓN DE PROFESORES
    public static function insertarProfesor($datosProfesor)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "INSERT INTO profesor (NombreProfesor, ApellidosProfesor, dipProfesor, Especialidad, GradoEstudio, Telefono, CorreoProfesor, idFacultad, idDepartamento, Foto) 
                    VALUES (:NombreProfesor, :ApellidosProfesor, :dipProfesor, :Especialidad, :GradoEstudio, :Telefono, :CorreoProfesor, :idFacultad, :idDepartamento, :Foto)";
            $stmt = $instanciaConexion->prepare($sql);

            $stmt->bindParam(':NombreProfesor', $datosProfesor['NombreProfesor'], PDO::PARAM_STR);
            $stmt->bindParam(':ApellidosProfesor', $datosProfesor['ApellidosProfesor'], PDO::PARAM_STR);
            $stmt->bindParam(':dipProfesor', $datosProfesor['dipProfesor'], PDO::PARAM_INT);
            $stmt->bindParam(':Especialidad', $datosProfesor['Especialidad'], PDO::PARAM_STR);
            $stmt->bindParam(':GradoEstudio', $datosProfesor['GradoEstudio'], PDO::PARAM_STR);
            $stmt->bindParam(':Telefono', $datosProfesor['Telefono'], PDO::PARAM_STR);
            $stmt->bindParam(':CorreoProfesor', $datosProfesor['CorreoProfesor'], PDO::PARAM_STR);
            $stmt->bindParam(':idFacultad', $datosProfesor['idFacultad'], PDO::PARAM_INT);
            $stmt->bindParam(':idDepartamento', $datosProfesor['idDepartamento'], PDO::PARAM_INT);
            $stmt->bindParam(':Foto', $datosProfesor['Foto'], PDO::PARAM_STR);

            $stmt->execute();
            return $instanciaConexion->lastInsertId();
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function actualizarProfesor($datosProfesor)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "UPDATE profesor SET NombreProfesor=:NombreProfesor, ApellidosProfesor=:ApellidosProfesor, 
                    dipProfesor=:dipProfesor, Especialidad=:Especialidad, GradoEstudio=:GradoEstudio, 
                    Telefono=:Telefono, CorreoProfesor=:CorreoProfesor, idFacultad=:idFacultad, 
                    idDepartamento=:idDepartamento, Foto=:Foto 
                    WHERE idProfesor=:idProfesor";
            $stmt = $instanciaConexion->prepare($sql);

            $stmt->bindParam(':NombreProfesor', $datosProfesor['NombreProfesor'], PDO::PARAM_STR);
            $stmt->bindParam(':ApellidosProfesor', $datosProfesor['ApellidosProfesor'], PDO::PARAM_STR);
            $stmt->bindParam(':dipProfesor', $datosProfesor['dipProfesor'], PDO::PARAM_INT);
            $stmt->bindParam(':Especialidad', $datosProfesor['Especialidad'], PDO::PARAM_STR);
            $stmt->bindParam(':GradoEstudio', $datosProfesor['GradoEstudio'], PDO::PARAM_STR);
            $stmt->bindParam(':Telefono', $datosProfesor['Telefono'], PDO::PARAM_STR);
            $stmt->bindParam(':CorreoProfesor', $datosProfesor['CorreoProfesor'], PDO::PARAM_STR);
            $stmt->bindParam(':idFacultad', $datosProfesor['idFacultad'], PDO::PARAM_INT);
            $stmt->bindParam(':idDepartamento', $datosProfesor['idDepartamento'], PDO::PARAM_INT);
            $stmt->bindParam(':Foto', $datosProfesor['Foto'], PDO::PARAM_STR);
            $stmt->bindParam(':idProfesor', $datosProfesor['idProfesor'], PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function eliminarProfesor($idProfesor)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "DELETE FROM profesor WHERE idProfesor=:idProfesor";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idProfesor', $idProfesor, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function obtenerProfesores()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT 
                        p.*,
                        d.NombreDepartamento,
                        f.NombreFacultad,
                        GROUP_CONCAT(DISTINCT fo.titulo SEPARATOR '; ') AS formaciones,
                        GROUP_CONCAT(DISTINCT a.NombreAsignatura SEPARATOR '; ') AS asignaturas,
                        GROUP_CONCAT(DISTINCT c.diaSemanal SEPARATOR '; ') AS dias_clase
                    FROM profesor p
                    LEFT JOIN departamento d ON p.idDepartamento = d.idDepartamento
                    LEFT JOIN facultad f ON p.idFacultad = f.idfacultad
                    LEFT JOIN formacion fo ON p.idProfesor = fo.idProfesor
                    LEFT JOIN clase c ON p.idProfesor = c.idProfesor
                    LEFT JOIN asignatura a ON c.idAsignatura = a.idAsignatura
                    GROUP BY p.idProfesor
                    ORDER BY p.ApellidosProfesor, p.NombreProfesor";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function obtenerProfesorPorId($idProfesor)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT 
                        p.*,
                        d.NombreDepartamento,
                        f.NombreFacultad,
                        GROUP_CONCAT(DISTINCT fo.titulo SEPARATOR '; ') AS formaciones,
                        GROUP_CONCAT(DISTINCT a.NombreAsignatura SEPARATOR '; ') AS asignaturas,
                        GROUP_CONCAT(DISTINCT c.diaSemanal SEPARATOR '; ') AS dias_clase
                    FROM profesor p
                    LEFT JOIN departamento d ON p.idDepartamento = d.idDepartamento
                    LEFT JOIN facultad f ON p.idFacultad = f.idfacultad
                    LEFT JOIN formacion fo ON p.idProfesor = fo.idProfesor
                    LEFT JOIN clase c ON p.idProfesor = c.idProfesor
                    LEFT JOIN asignatura a ON c.idAsignatura = a.idAsignatura
                    WHERE p.idProfesor = :idProfesor
                    GROUP BY p.idProfesor";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idProfesor', $idProfesor, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function buscarProfesores($criterio)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT 
                        p.*,
                        d.NombreDepartamento,
                        f.NombreFacultad,
                        GROUP_CONCAT(DISTINCT fo.titulo SEPARATOR '; ') AS formaciones,
                        GROUP_CONCAT(DISTINCT a.NombreAsignatura SEPARATOR '; ') AS asignaturas,
                        GROUP_CONCAT(DISTINCT c.diaSemanal SEPARATOR '; ') AS dias_clase
                    FROM profesor p
                    LEFT JOIN departamento d ON p.idDepartamento = d.idDepartamento
                    LEFT JOIN facultad f ON p.idFacultad = f.idfacultad
                    LEFT JOIN formacion fo ON p.idProfesor = fo.idProfesor
                    LEFT JOIN clase c ON p.idProfesor = c.idProfesor
                    LEFT JOIN asignatura a ON c.idAsignatura = a.idAsignatura
                    WHERE p.NombreProfesor LIKE :criterio 
                    OR p.ApellidosProfesor LIKE :criterio
                    OR p.CorreoProfesor LIKE :criterio
                    OR d.NombreDepartamento LIKE :criterio
                    OR f.NombreFacultad LIKE :criterio
                    OR a.NombreAsignatura LIKE :criterio
                    GROUP BY p.idProfesor
                    ORDER BY p.ApellidosProfesor, p.NombreProfesor";
            $stmt = $instanciaConexion->prepare($sql);
            $likeCriterio = "%" . $criterio . "%";
            $stmt->bindParam(':criterio', $likeCriterio, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }


    public static function insertarFormacion($datosFormacion)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "INSERT INTO formación (institution, tipo, titulo, nivel, idProfesor) 
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

    public static function actualizarFormacion($datosFormacion)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "UPDATE formación SET institution=:institution , tipo=:tipo, titulo=:titulo, nivel=:nivel 
                    WHERE idFormacion=:idFormacion";

            $stmt = $instanciaConexion->prepare($sql);

            $stmt->bindParam(':institution', $datosFormacion['institution'], PDO::PARAM_STR);
            $stmt->bindParam(':tipo', $datosFormacion['tipo'], PDO::PARAM_STR);
            $stmt->bindParam(':titulo', $datosFormacion['titulo'], PDO::PARAM_STR);
            $stmt->bindParam(':nivel', $datosFormacion['nivel'], PDO::PARAM_INT);
            $stmt->bindParam(':idFormacion', $datosFormacion['idFormacion'], PDO::PARAM_INT);

            $stmt->execute();
            return $instanciaConexion->lastInsertId();
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function eliminarFormacion($idFormacion)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "DELETE FROM formacion WHERE idFormacion=:idFormacion";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idFormacion', $idFormacion, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function obtenerFormacionesPorProfesor($idProfesor)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT * FROM formación WHERE idProfesor=:idProfesor ORDER BY nivel DESC";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idProfesor', $idProfesor, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }


    // FUNCIONES PARA LA GESTIÓN DE ESTUDIANTES
    public static function insertarEstudiante($datosEstudiante)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "INSERT INTO estudiante (CodigoEstudiante, NombreEstudiante, ApellidosEstudiante, dipEstudiante, 
                    CorreoEstudiante, idCarrera, idCurso, FechadeNacimiento, Sexo, Nacionalidad, Foto) 
                    VALUES (:CodigoEstudiante, :NombreEstudiante, :ApellidosEstudiante, :dipEstudiante, 
                    :CorreoEstudiante, :idCarrera, :idCurso, :FechadeNacimiento, :Sexo, :Nacionalidad, :Foto)";
            $stmt = $instanciaConexion->prepare($sql);

            $stmt->bindParam(':CodigoEstudiante', $datosEstudiante['CodigoEstudiante'], PDO::PARAM_STR);
            $stmt->bindParam(':NombreEstudiante', $datosEstudiante['NombreEstudiante'], PDO::PARAM_STR);
            $stmt->bindParam(':ApellidosEstudiante', $datosEstudiante['ApellidosEstudiante'], PDO::PARAM_STR);
            $stmt->bindParam(':dipEstudiante', $datosEstudiante['dipEstudiante'], PDO::PARAM_INT);
            $stmt->bindParam(':CorreoEstudiante', $datosEstudiante['CorreoEstudiante'], PDO::PARAM_STR);
            $stmt->bindParam(':idCarrera', $datosEstudiante['idCarrera'], PDO::PARAM_INT);
            $stmt->bindParam(':idCurso', $datosEstudiante['idCurso'], PDO::PARAM_INT);
            $stmt->bindParam(':FechadeNacimiento', $datosEstudiante['FechadeNacimiento'], PDO::PARAM_STR);
            $stmt->bindParam(':Sexo', $datosEstudiante['Sexo'], PDO::PARAM_STR);
            $stmt->bindParam(':Nacionalidad', $datosEstudiante['Nacionalidad'], PDO::PARAM_STR);
            $stmt->bindParam(':Foto', $datosEstudiante['Foto'], PDO::PARAM_STR);

            $stmt->execute();
            return $instanciaConexion->lastInsertId();
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function actualizarEstudiante($datosEstudiante)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "UPDATE estudiante SET CodigoEstudiante=:CodigoEstudiante, NombreEstudiante=:NombreEstudiante, 
                    ApellidosEstudiante=:ApellidosEstudiante, dipEstudiante=:dipEstudiante, 
                    CorreoEstudiante=:CorreoEstudiante, idCarrera=:idCarrera, idCurso=:idCurso, 
                    FechadeNacimiento=:FechadeNacimiento, Sexo=:Sexo, Nacionalidad=:Nacionalidad, Foto=:Foto 
                    WHERE idEstudiante=:idEstudiante";
            $stmt = $instanciaConexion->prepare($sql);

            $stmt->bindParam(':CodigoEstudiante', $datosEstudiante['CodigoEstudiante'], PDO::PARAM_STR);
            $stmt->bindParam(':NombreEstudiante', $datosEstudiante['NombreEstudiante'], PDO::PARAM_STR);
            $stmt->bindParam(':ApellidosEstudiante', $datosEstudiante['ApellidosEstudiante'], PDO::PARAM_STR);
            $stmt->bindParam(':dipEstudiante', $datosEstudiante['dipEstudiante'], PDO::PARAM_INT);
            $stmt->bindParam(':CorreoEstudiante', $datosEstudiante['CorreoEstudiante'], PDO::PARAM_STR);
            $stmt->bindParam(':idCarrera', $datosEstudiante['idCarrera'], PDO::PARAM_INT);
            $stmt->bindParam(':idCurso', $datosEstudiante['idCurso'], PDO::PARAM_INT);
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

    public static function eliminarEstudiante($idEstudiante)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "DELETE FROM estudiante WHERE idEstudiante=:idEstudiante";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idEstudiante', $idEstudiante, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }
    public static function obtenerEstudiantes()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT 
                        e.*,
                        c.NombreCarrera,
                        d.NombreDepartamento,
                        f.NombreFacultad,
                        cu.NombreCurso,
                        GROUP_CONCAT(DISTINCT a.NombreAsignatura SEPARATOR '; ') AS asignaturas,
                        GROUP_CONCAT(DISTINCT CONCAT(fam.NombreTutor, ' (', fam.ResponsabledePago, ')') SEPARATOR '; ') AS familiares,
                        GROUP_CONCAT(DISTINCT CONCAT(eb.fechalnicio, ' - ', eb.fechaFinal, ': ', eb.estado) SEPARATOR '; ') AS becas,
                        m.EstadoMatricula,
                        m.AñoAcademico
                    FROM estudiante e
                    LEFT JOIN carrera c ON e.idCarrera = c.idCarrera
                    LEFT JOIN departamento d ON c.idDepartamento = d.idDepartamento
                    LEFT JOIN facultad f ON d.idFacultad = f.idfacultad
                    LEFT JOIN curso cu ON e.idCurso = cu.idCurso
                    LEFT JOIN estudiante_asignatura ea ON e.idEstudiante = ea.idEstudiante
                    LEFT JOIN asignatura a ON ea.idAsignatura = a.idAsignatura
                    LEFT JOIN familiar fam ON e.idEstudiante = fam.idEstudiante
                    LEFT JOIN estudiante_beca eb ON e.idEstudiante = eb.idEstudiante
                    LEFT JOIN matricula m ON e.idEstudiante = m.idEstudiante
                    GROUP BY e.idEstudiante
                    ORDER BY e.ApellidosEstudiante, e.NombreEstudiante";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function obtenerEstudiantePorCodigo($CodigoEstudiante)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT 
                        e.*,
                        c.NombreCarrera,
                        d.NombreDepartamento,
                        f.NombreFacultad,
                        cu.NombreCurso,
                        GROUP_CONCAT(DISTINCT a.NombreAsignatura SEPARATOR '; ') AS asignaturas,
                        GROUP_CONCAT(DISTINCT CONCAT(fam.NombreTutor, ' (', fam.ResponsabledePago, ')') SEPARATOR '; ') AS familiares,
                        GROUP_CONCAT(DISTINCT CONCAT(eb.fechalnicio, ' - ', eb.fechaFinal, ': ', eb.estado) SEPARATOR '; ') AS becas,
                        m.EstadoMatricula,
                        m.AñoAcademico
                    FROM estudiante e
                    LEFT JOIN carrera c ON e.idCarrera = c.idCarrera
                    LEFT JOIN departamento d ON c.idDepartamento = d.idDepartamento
                    LEFT JOIN facultad f ON d.idFacultad = f.idfacultad
                    LEFT JOIN curso cu ON e.idCurso = cu.idCurso
                    LEFT JOIN estudiante_asignatura ea ON e.idEstudiante = ea.idEstudiante
                    LEFT JOIN asignatura a ON ea.idAsignatura = a.idAsignatura
                    LEFT JOIN familiar fam ON e.idEstudiante = fam.idEstudiante
                    LEFT JOIN estudiante_beca eb ON e.idEstudiante = eb.idEstudiante
                    LEFT JOIN matricula m ON e.idEstudiante = m.idEstudiante
                    WHERE e.CodigoEstudiante = :CodigoEstudiante
                    GROUP BY e.idEstudiante";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':CodigoEstudiante', $CodigoEstudiante, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function buscarEstudiantes($criterio)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT 
                        e.*,
                        c.NombreCarrera,
                        d.NombreDepartamento,
                        f.NombreFacultad,
                        cu.NombreCurso,
                        GROUP_CONCAT(DISTINCT a.NombreAsignatura SEPARATOR '; ') AS asignaturas,
                        GROUP_CONCAT(DISTINCT CONCAT(fam.NombreTutor, ' (', fam.ResponsabledePago, ')') SEPARATOR '; ') AS familiares,
                        GROUP_CONCAT(DISTINCT CONCAT(eb.fechalnicio, ' - ', eb.fechaFinal, ': ', eb.estado) SEPARATOR '; ') AS becas,
                        m.EstadoMatricula,
                        m.AñoAcademico
                    FROM estudiante e
                    LEFT JOIN carrera c ON e.idCarrera = c.idCarrera
                    LEFT JOIN departamento d ON c.idDepartamento = d.idDepartamento
                    LEFT JOIN facultad f ON d.idFacultad = f.idfacultad
                    LEFT JOIN curso cu ON e.idCurso = cu.idCurso
                    LEFT JOIN estudiante_asignatura ea ON e.idEstudiante = ea.idEstudiante
                    LEFT JOIN asignatura a ON ea.idAsignatura = a.idAsignatura
                    LEFT JOIN familiar fam ON e.idEstudiante = fam.idEstudiante
                    LEFT JOIN estudiante_beca eb ON e.idEstudiante = eb.idEstudiante
                    LEFT JOIN matricula m ON e.idEstudiante = m.idEstudiante
                    WHERE e.CodigoEstudiante LIKE :criterio 
                    OR e.NombreEstudiante LIKE :criterio 
                    OR e.ApellidosEstudiante LIKE :criterio 
                    OR e.CorreoEstudiante LIKE :criterio
                    OR c.NombreCarrera LIKE :criterio
                    OR f.NombreFacultad LIKE :criterio
                    OR cu.NombreCurso LIKE :criterio
                    OR a.NombreAsignatura LIKE :criterio
                    OR fam.NombreTutor LIKE :criterio
                    OR eb.estado LIKE :criterio
                    OR m.EstadoMatricula LIKE :criterio
                    GROUP BY e.idEstudiante
                    ORDER BY e.ApellidosEstudiante, e.NombreEstudiante";
            $stmt = $instanciaConexion->prepare($sql);
            $likeCriterio = "%" . $criterio . "%";
            $stmt->bindParam(':criterio', $likeCriterio, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }


    public static function obtenerEstudiantesPorFacultad($idFacultad)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT 
                        e.*,
                        c.NombreCarrera,
                        d.NombreDepartamento,
                        f.NombreFacultad,
                        cu.NombreCurso
                    FROM estudiante e
                    LEFT JOIN carrera c ON e.idCarrera = c.idCarrera
                    LEFT JOIN departamento d ON c.idDepartamento = d.idDepartamento
                    LEFT JOIN facultad f ON d.idFacultad = f.idfacultad
                    LEFT JOIN curso cu ON e.idCurso = cu.idCurso
                    WHERE f.idfacultad = :idFacultad
                    GROUP BY e.idEstudiante
                    ORDER BY e.ApellidosEstudiante, e.NombreEstudiante";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idFacultad', $idFacultad, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function obtenerEstudiantesPorCarrera($idCarrera)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT 
                        e.*,
                        c.NombreCarrera,
                        d.NombreDepartamento,
                        f.NombreFacultad,
                        cu.NombreCurso
                    FROM estudiante e
                    LEFT JOIN carrera c ON e.idCarrera = c.idCarrera
                    LEFT JOIN departamento d ON c.idDepartamento = d.idDepartamento
                    LEFT JOIN facultad f ON d.idFacultad = f.idfacultad
                    LEFT JOIN curso cu ON e.idCurso = cu.idCurso
                    WHERE e.idCarrera = :idCarrera
                    GROUP BY e.idEstudiante
                    ORDER BY e.ApellidosEstudiante, e.NombreEstudiante";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idCarrera', $idCarrera, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function obtenerEstudiantesPorCurso($idCurso)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT 
                        e.*,
                        c.NombreCarrera,
                        d.NombreDepartamento,
                        f.NombreFacultad,
                        cu.NombreCurso
                    FROM estudiante e
                    LEFT JOIN carrera c ON e.idCarrera = c.idCarrera
                    LEFT JOIN departamento d ON c.idDepartamento = d.idDepartamento
                    LEFT JOIN facultad f ON d.idFacultad = f.idfacultad
                    LEFT JOIN curso cu ON e.idCurso = cu.idCurso
                    WHERE e.idCurso = :idCurso
                    GROUP BY e.idEstudiante
                    ORDER BY e.ApellidosEstudiante, e.NombreEstudiante";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idCurso', $idCurso, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function obtenerEstudiantesPorAsignatura($idAsignatura)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT 
                        e.*,
                        c.NombreCarrera,
                        d.NombreDepartamento,
                        f.NombreFacultad,
                        cu.NombreCurso,
                        a.NombreAsignatura,
                        ea.convocatoria
                    FROM estudiante e
                    LEFT JOIN carrera c ON e.idCarrera = c.idCarrera
                    LEFT JOIN departamento d ON c.idDepartamento = d.idDepartamento
                    LEFT JOIN facultad f ON d.idFacultad = f.idfacultad
                    LEFT JOIN curso cu ON e.idCurso = cu.idCurso
                    INNER JOIN estudiante_asignatura ea ON e.idEstudiante = ea.idEstudiante
                    INNER JOIN asignatura a ON ea.idAsignatura = a.idAsignatura
                    WHERE ea.idAsignatura = :idAsignatura
                    GROUP BY e.idEstudiante
                    ORDER BY e.ApellidosEstudiante, e.NombreEstudiante";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idAsignatura', $idAsignatura, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }


    public static function insertarFamiliar($datosFamiliar)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "INSERT INTO familiar (idEstudiante, NombreTutor, ResponsabledePago, Telefono, Correo) 
                    VALUES (:idEstudiante, :NombreTutor, :ResponsabledePago, :Telefono, :Correo)";
            $stmt = $instanciaConexion->prepare($sql);

            $stmt->bindParam(':idEstudiante', $datosFamiliar['idEstudiante'], PDO::PARAM_INT);
            $stmt->bindParam(':NombreTutor', $datosFamiliar['NombreTutor'], PDO::PARAM_STR);
            $stmt->bindParam(':ResponsabledePago', $datosFamiliar['ResponsabledePago'], PDO::PARAM_STR);
            $stmt->bindParam(':Telefono', $datosFamiliar['Telefono'], PDO::PARAM_INT);
            $stmt->bindParam(':Correo', $datosFamiliar['Correo'], PDO::PARAM_STR);

            $stmt->execute();
            return $instanciaConexion->lastInsertId();
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function actualizarFamiliar($datos)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "UPDATE familiar SET 
                        idEstudiante = :idEstudiante,
                        NombreTutor = :NombreTutor,
                        ResponsabledePago = :ResponsabledePago,
                        Telefono = :Telefono,
                        Correo = :Correo
                    WHERE idFamiliar = :idFamiliar";

            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idFamiliar', $datos['idFamiliar'], PDO::PARAM_INT);
            $stmt->bindParam(':idEstudiante', $datos['idEstudiante'], PDO::PARAM_INT);
            $stmt->bindParam(':NombreTutor', $datos['NombreTutor'], PDO::PARAM_STR);
            $stmt->bindParam(':ResponsabledePago', $datos['ResponsabledePago'], PDO::PARAM_STR);
            $stmt->bindParam(':Telefono', $datos['Telefono'], PDO::PARAM_STR);
            $stmt->bindParam(':Correo', $datos['Correo'], PDO::PARAM_STR);

            return $stmt->execute();
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function eliminarFamiliar($idFamiliar)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "DELETE FROM familiar WHERE idFamiliar = :idFamiliar";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idFamiliar', $idFamiliar, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function obtenerFamiliaresPorEstudiante($idEstudiante)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT f.* FROM familiar f
                    WHERE f.idEstudiante = :idEstudiante
                    ORDER BY f.NombreTutor";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idEstudiante', $idEstudiante, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }


    // FUNCIONES PARA LA INSCRIPCIÓN EN CARRERAS
    public static function insertarMatricula($datosMatricula)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "INSERT INTO matricula (idEstudiante, idCurso, FechaMatricula, AñoAcademico, EstadoMatricula) 
                    VALUES (:idEstudiante, :idCurso, :FechaMatricula, :AñoAcademico, :EstadoMatricula)";
            $stmt = $instanciaConexion->prepare($sql);

            $stmt->bindParam(':idEstudiante', $datosMatricula['idEstudiante'], PDO::PARAM_INT);
            $stmt->bindParam(':idCurso', $datosMatricula['idCurso'], PDO::PARAM_INT);
            $stmt->bindParam(':FechaMatricula', $datosMatricula['FechaMatricula'], PDO::PARAM_INT);
            $stmt->bindParam(':AñoAcademico', $datosMatricula['AñoAcademico'], PDO::PARAM_STR);
            $stmt->bindParam(':EstadoMatricula', $datosMatricula['EstadoMatricula'], PDO::PARAM_STR);

            $stmt->execute();
            return $instanciaConexion->lastInsertId();
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function obtenerMatriculasPorEstudiante($idEstudiante)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT m.*, e.NombreEstudiante, e.ApellidosEstudiante, c.NombreCurso 
                    FROM matricula m 
                    JOIN estudiante e ON m.idEstudiante = e.idEstudiante
                    JOIN curso c ON m.idCurso = c.idCurso
                    WHERE m.idEstudiante=:idEstudiante
                    ORDER BY m.AñoAcademico DESC";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idEstudiante', $idEstudiante, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function buscarMatriculas($criterio)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT m.*, e.NombreEstudiante, e.ApellidosEstudiante, e.CodigoEstudiante,
                    c.NombreCurso
                    FROM matricula m 
                    JOIN estudiante e ON m.idEstudiante = e.idEstudiante
                    JOIN curso c ON m.idCurso = c.idCurso
                    WHERE e.CodigoEstudiante LIKE :criterio OR e.NombreEstudiante LIKE :criterio OR e.ApellidosEstudiante LIKE :criterio
                    OR c.NombreCurso LIKE :criterio
                    ORDER BY m.FechaMatricula DESC";
            $stmt = $instanciaConexion->prepare($sql);
            $likeCriterio = "%" . $criterio . "%";
            $stmt->bindParam(':criterio', $likeCriterio, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    // FUNCIONES PARA LA GESTIÓN DE BECAS
    public static function asignarBeca($datosBeca)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "INSERT INTO estudiante_beca (Codigo, idBecario, fechaInicio, fechaFinal, estado) 
                    VALUES (:Codigo, :idBecario, :fechaInicio, :fechaFinal, :estado)";
            $stmt = $instanciaConexion->prepare($sql);

            $stmt->bindParam(':Codigo', $datosBeca['Codigo'], PDO::PARAM_STR);
            $stmt->bindParam(':idBecario', $datosBeca['idBecario'], PDO::PARAM_INT);
            $stmt->bindParam(':fechaInicio', $datosBeca['fechaInicio'], PDO::PARAM_STR);
            $stmt->bindParam(':fechaFinal', $datosBeca['fechaFinal'], PDO::PARAM_STR);
            $stmt->bindParam(':estado', $datosBeca['estado'], PDO::PARAM_STR);

            $stmt->execute();
            return $instanciaConexion->lastInsertId();
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function actualizarBeca($idEstudianteBecario, $datosBeca)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "UPDATE estudiante_beca SET fechaFinal=:fechaFinal, estado=:estado 
                    WHERE idEstudianteBecario=:idEstudianteBecario";
            $stmt = $instanciaConexion->prepare($sql);

            $stmt->bindParam(':fechaFinal', $datosBeca['fechaFinal'], PDO::PARAM_STR);
            $stmt->bindParam(':estado', $datosBeca['estado'], PDO::PARAM_STR);
            $stmt->bindParam(':idEstudianteBecario', $idEstudianteBecario, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function eliminarBeca($idEstudianteBecario)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "DELETE FROM estudiante_beca WHERE idEstudianteBecario=:idEstudianteBecario";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idEstudianteBecario', $idEstudianteBecario, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function obtenerBecas()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT eb.*, e.NombreEstudiante, e.ApellidosEstudiante, b.institucionBeca 
                    FROM estudiante_beca eb
                    JOIN estudiante e ON eb.Codigo = e.idEstudiante
                    JOIN becario b ON eb.idBecario = b.idBecario
                    ORDER BY eb.fechaInicio DESC";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function buscarBecas($criterio)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT eb.*, e.NombreEstudiante, e.ApellidosEstudiante, b.institucionBeca 
                    FROM estudiante_beca eb
                    JOIN estudiante e ON eb.Codigo = e.idEstudiante
                    JOIN becario b ON eb.idBecario = b.idBecario
                    WHERE e.CodigoEstudiante LIKE :criterio OR e.NombreEstudiante LIKE :criterio OR e.ApellidosEstudiante LIKE :criterio
                    OR b.institucionBeca LIKE :criterio
                    ORDER BY eb.fechaInicio DESC";
            $stmt = $instanciaConexion->prepare($sql);
            $likeCriterio = "%" . $criterio . "%";
            $stmt->bindParam(':criterio', $likeCriterio, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function insertarBecario($datosBecario)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "INSERT INTO becario (institucionBeca) VALUES (:institucionBeca)";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':institucionBeca', $datosBecario['institucionBeca'], PDO::PARAM_STR);
            $stmt->execute();
            return $instanciaConexion->lastInsertId();
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function obtenerBecarios()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT * FROM becario ORDER BY institucionBeca";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function cargarNombresBecarios()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT idBecario as value, institucionBeca as label FROM becario ORDER BY institucionBeca";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    //  FUNCIONES PARA LA MATRÍCULA EN ASIGNATURAS
    public static function matricularEstudianteAsignatura($idEstudianteConvocatoria, $idAsignatura, $convocatoria)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "INSERT INTO estudiante_asignatura (idEstudianteConvocatoria, idAsignatura, convocatoria) 
                    VALUES (:idEstudianteConvocatoria, :idAsignatura, :convocatoria)";
            $stmt = $instanciaConexion->prepare($sql);

            $stmt->bindParam(':idEstudianteConvocatoria', $idEstudianteConvocatoria, PDO::PARAM_INT);
            $stmt->bindParam(':idAsignatura', $idAsignatura, PDO::PARAM_INT);
            $stmt->bindParam(':convocatoria', $convocatoria, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function obtenerAsignaturasPorEstudiante($idEstudiante)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT ea.*, a.NombreAsignatura, a.Creditos, c.NombreCurso
                    FROM estudiante_asignatura ea
                    JOIN asignatura a ON ea.idAsignatura = a.idAsignatura
                    JOIN curso c ON a.idCurso = c.idCurso
                    JOIN estudiante e ON ea.idEstudianteConvocatoria = e.idEstudiante
                    WHERE e.idEstudiante=:idEstudiante
                    ORDER BY a.NombreAsignatura";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idEstudiante', $idEstudiante, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }


    // FUNCIONES PARA VER EL HISTORIAL ACADÉMICO
    public static function consultarHistorialAcademico($idEstudiante)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT ea.*, a.NombreAsignatura, a.Creditos, e.tipo, e.nota, e.fecha 
                    FROM estudiante_asignatura ea
                    JOIN asignatura a ON ea.idAsignatura = a.idAsignatura
                    LEFT JOIN evaluación e ON a.idAsignatura = e.idExamen
                    JOIN estudiante est ON ea.idEstudianteConvocatoria = est.idEstudiante
                    WHERE est.idEstudiante = :idEstudiante 
                    ORDER BY e.fecha DESC";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idEstudiante', $idEstudiante, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }


    // FUNCIONES PARA LA GESTION DE PLANES DE ESTUDIO
    public static function insertarPlanEstudio($datosPlan)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "INSERT INTO planestudio (periodoPlanEstudio, nombre, idCarrera, fechaElaboracion) 
                    VALUES (:periodoPlanEstudio, :nombre, :idCarrera, :fechaElaboracion)";
            $stmt = $instanciaConexion->prepare($sql);

            $stmt->bindParam(':periodoPlanEstudio', $datosPlan['periodoPlanEstudio'], PDO::PARAM_STR);
            $stmt->bindParam(':nombre', $datosPlan['nombre'], PDO::PARAM_STR);
            $stmt->bindParam(':idCarrera', $datosPlan['idCarrera'], PDO::PARAM_INT);
            $stmt->bindParam(':fechaElaboracion', $datosPlan['fechaElaboracion'], PDO::PARAM_STR);

            $stmt->execute();
            return $instanciaConexion->lastInsertId();
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function actualizarPlanEstudio($idPlanEstudio, $datosPlan)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "UPDATE planestudio SET periodoPlanEstudio=:periodoPlanEstudio, nombre=:nombre, 
                    fechaElaboracion=:fechaElaboracion WHERE idPlanEstudio=:idPlanEstudio";
            $stmt = $instanciaConexion->prepare($sql);

            $stmt->bindParam(':periodoPlanEstudio', $datosPlan['periodoPlanEstudio'], PDO::PARAM_STR);
            $stmt->bindParam(':nombre', $datosPlan['nombre'], PDO::PARAM_STR);
            $stmt->bindParam(':fechaElaboracion', $datosPlan['fechaElaboracion'], PDO::PARAM_STR);
            $stmt->bindParam(':idPlanEstudio', $idPlanEstudio, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function eliminarPlanEstudio($idPlanEstudio)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "DELETE FROM planestudio WHERE idPlanEstudio=:idPlanEstudio";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idPlanEstudio', $idPlanEstudio, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function obtenerPlanesEstudio()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT pe.*, c.NombreCarrera 
                    FROM planestudio pe 
                    JOIN carrera c ON pe.idCarrera = c.idCarrera
                    ORDER BY pe.nombre";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function buscarPlanesEstudio($criterio)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT pe.*, c.NombreCarrera
                    FROM planestudio pe 
                    JOIN carrera c ON pe.idCarrera = c.idCarrera
                    WHERE pe.nombre LIKE :criterio OR pe.periodoPlanEstudio LIKE :criterio 
                    OR c.NombreCarrera LIKE :criterio
                    ORDER BY pe.nombre";
            $stmt = $instanciaConexion->prepare($sql);
            $likeCriterio = "%" . $criterio . "%";
            $stmt->bindParam(':criterio', $likeCriterio, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function cargarPlanesEstudioParaSelect()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT idPlanEstudio as value, CONCAT(nombre, ' (', periodoPlanEstudio, ')') as label 
                    FROM planestudio 
                    ORDER BY nombre";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function asignarCursoPlanEstudio($idPlanEstudio, $idCurso, $idSemestre)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "INSERT INTO curso_planestudio (idPlanEstudio, idCurso, idSemestre) 
                    VALUES (:idPlanEstudio, :idCurso, :idSemestre)";
            $stmt = $instanciaConexion->prepare($sql);

            $stmt->bindParam(':idPlanEstudio', $idPlanEstudio, PDO::PARAM_INT);
            $stmt->bindParam(':idCurso', $idCurso, PDO::PARAM_INT);
            $stmt->bindParam(':idSemestre', $idSemestre, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function obtenerCursosPorPlanEstudio($idPlanEstudio)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT cpe.*, c.NombreCurso, c.CreditosCurso, s.NombreSemestre
                    FROM curso_planestudio cpe
                    JOIN curso c ON cpe.idCurso = c.idCurso
                    JOIN semestre s ON cpe.idSemestre = s.idSemestre
                    WHERE cpe.idPlanEstudio = :idPlanEstudio
                    ORDER BY s.NombreSemestre";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idPlanEstudio', $idPlanEstudio, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }


    // FUNCIONES PARA GESTIÓN DE ASIGNATURAS
    public static function insertarAsignatura($datosAsignatura)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "INSERT INTO asignatura (NombreAsignatura, Creditos, idProfesor, idCurso, idCarrera, idSemestre) 
                    VALUES (:NombreAsignatura, :Creditos, :idProfesor, :idCurso, :idCarrera, :idSemestre)";
            $stmt = $instanciaConexion->prepare($sql);

            $stmt->bindParam(':NombreAsignatura', $datosAsignatura['NombreAsignatura'], PDO::PARAM_STR);
            $stmt->bindParam(':Creditos', $datosAsignatura['Creditos'], PDO::PARAM_INT);
            $stmt->bindParam(':idProfesor', $datosAsignatura['idProfesor'], PDO::PARAM_INT);
            $stmt->bindParam(':idCurso', $datosAsignatura['idCurso'], PDO::PARAM_INT);
            $stmt->bindParam(':idCarrera', $datosAsignatura['idCarrera'], PDO::PARAM_INT);
            $stmt->bindParam(':idSemestre', $datosAsignatura['idSemestre'], PDO::PARAM_INT);

            $stmt->execute();
            return $instanciaConexion->lastInsertId();
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function actualizarAsignatura($idAsignatura, $datosAsignatura)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "UPDATE asignatura SET NombreAsignatura=:NombreAsignatura, Creditos=:Creditos, 
                    idProfesor=:idProfesor, idCurso=:idCurso, idCarrera=:idCarrera, idSemestre=:idSemestre 
                    WHERE idAsignatura=:idAsignatura";
            $stmt = $instanciaConexion->prepare($sql);

            $stmt->bindParam(':NombreAsignatura', $datosAsignatura['NombreAsignatura'], PDO::PARAM_STR);
            $stmt->bindParam(':Creditos', $datosAsignatura['Creditos'], PDO::PARAM_INT);
            $stmt->bindParam(':idProfesor', $datosAsignatura['idProfesor'], PDO::PARAM_INT);
            $stmt->bindParam(':idCurso', $datosAsignatura['idCurso'], PDO::PARAM_INT);
            $stmt->bindParam(':idCarrera', $datosAsignatura['idCarrera'], PDO::PARAM_INT);
            $stmt->bindParam(':idSemestre', $datosAsignatura['idSemestre'], PDO::PARAM_INT);
            $stmt->bindParam(':idAsignatura', $idAsignatura, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function eliminarAsignatura($idAsignatura)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "DELETE FROM asignatura WHERE idAsignatura=:idAsignatura";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idAsignatura', $idAsignatura, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function obtenerAsignaturas()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT a.*, p.NombreProfesor, c.NombreCurso, car.NombreCarrera, s.NombreSemestre 
                    FROM asignatura a 
                    JOIN profesor p ON a.idProfesor = p.idProfesor
                    JOIN curso c ON a.idCurso = c.idCurso
                    JOIN carrera car ON a.idCarrera = car.idCarrera
                    JOIN semestre s ON a.idSemestre = s.idSemestre
                    ORDER BY a.NombreAsignatura";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function buscarAsignaturas($criterio)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT a.*, p.NombreProfesor, c.NombreCurso 
                    FROM asignatura a 
                    JOIN profesor p ON a.idProfesor = p.idProfesor
                    JOIN curso c ON a.idCurso = c.idCurso
                    WHERE a.NombreAsignatura LIKE :criterio OR c.NombreCurso LIKE :criterio OR p.NombreProfesor LIKE :criterio
                    ORDER BY a.NombreAsignatura";
            $stmt = $instanciaConexion->prepare($sql);
            $likeCriterio = "%" . $criterio . "%";
            $stmt->bindParam(':criterio', $likeCriterio, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function cargarAsignaturasParaSelect()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT idAsignatura as value, NombreAsignatura as label FROM asignatura ORDER BY NombreAsignatura";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function cargarNombresAsignaturas()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT idAsignatura as value, CONCAT(NombreAsignatura, ' (', Creditos, ' créditos)') as label 
                    FROM asignatura ORDER BY NombreAsignatura";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    // FUNCIONES PARA LA OFERTA ACADÉMICA POR CARRERA

    public static function consultarOfertaAcademica($idCarrera)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT a.*, c.NombreCurso, p.NombreProfesor
                    FROM asignatura a
                    JOIN curso c ON a.idCurso = c.idCurso
                    JOIN profesor p ON a.idProfesor = p.idProfesor
                    WHERE a.idCarrera = :idCarrera
                    ORDER BY a.NombreAsignatura";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idCarrera', $idCarrera, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    // FUNCIONES PARA LA GESTIÓN DE CLASES Y HORARIOS
    public static function insertarClase($datosClase)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "INSERT INTO clase (idProfesor, idAsignatura, diaSemanal, idAula, idHorario, horalnicio, HoraFinal) 
                    VALUES (:idProfesor, :idAsignatura, :diaSemanal, :idAula, :idHorario, :horalnicio, :HoraFinal)";
            $stmt = $instanciaConexion->prepare($sql);

            $stmt->bindParam(':idProfesor', $datosClase['idProfesor'], PDO::PARAM_INT);
            $stmt->bindParam(':idAsignatura', $datosClase['idAsignatura'], PDO::PARAM_INT);
            $stmt->bindParam(':diaSemanal', $datosClase['diaSemanal'], PDO::PARAM_STR);
            $stmt->bindParam(':idAula', $datosClase['idAula'], PDO::PARAM_INT);
            $stmt->bindParam(':idHorario', $datosClase['idHorario'], PDO::PARAM_INT);
            $stmt->bindParam(':horalnicio', $datosClase['horalnicio'], PDO::PARAM_STR);
            $stmt->bindParam(':HoraFinal', $datosClase['HoraFinal'], PDO::PARAM_STR);

            $stmt->execute();
            return $instanciaConexion->lastInsertId();
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function actualizarClase($idClase, $datosClase)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "UPDATE clase SET idProfesor=:idProfesor, idAsignatura=:idAsignatura, diaSemanal=:diaSemanal, 
                    idAula=:idAula, idHorario=:idHorario, horalnicio=:horalnicio, HoraFinal=:HoraFinal 
                    WHERE idClase=:idClase";
            $stmt = $instanciaConexion->prepare($sql);

            $stmt->bindParam(':idProfesor', $datosClase['idProfesor'], PDO::PARAM_INT);
            $stmt->bindParam(':idAsignatura', $datosClase['idAsignatura'], PDO::PARAM_INT);
            $stmt->bindParam(':diaSemanal', $datosClase['diaSemanal'], PDO::PARAM_STR);
            $stmt->bindParam(':idAula', $datosClase['idAula'], PDO::PARAM_INT);
            $stmt->bindParam(':idHorario', $datosClase['idHorario'], PDO::PARAM_INT);
            $stmt->bindParam(':horalnicio', $datosClase['horalnicio'], PDO::PARAM_STR);
            $stmt->bindParam(':HoraFinal', $datosClase['HoraFinal'], PDO::PARAM_STR);
            $stmt->bindParam(':idClase', $idClase, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function eliminarClase($idClase)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "DELETE FROM clase WHERE idClase=:idClase";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idClase', $idClase, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function obtenerClases()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT c.*, p.NombreProfesor, p.ApellidosProfesor, 
                    a.NombreAsignatura, au.nombre as aula_nombre
                    FROM clase c
                    JOIN profesor p ON c.idProfesor = p.idProfesor
                    JOIN asignatura a ON c.idAsignatura = a.idAsignatura
                    JOIN aulas au ON c.idAula = au.idAula
                    ORDER BY c.diaSemanal, c.horalnicio";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function buscarClases($criterio)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT c.*, p.NombreProfesor, p.ApellidosProfesor, 
                    a.NombreAsignatura, au.nombre as aula_nombre
                    FROM clase c
                    JOIN profesor p ON c.idProfesor = p.idProfesor
                    JOIN asignatura a ON c.idAsignatura = a.idAsignatura
                    JOIN aulas au ON c.idAula = au.idAula
                    WHERE p.NombreProfesor LIKE :criterio OR p.ApellidosProfesor LIKE :criterio 
                    OR a.NombreAsignatura LIKE :criterio OR au.nombre LIKE :criterio
                    OR c.diaSemanal LIKE :criterio
                    ORDER BY c.diaSemanal, c.horalnicio";
            $stmt = $instanciaConexion->prepare($sql);
            $likeCriterio = "%" . $criterio . "%";
            $stmt->bindParam(':criterio', $likeCriterio, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    // FUNCIONES PARA LA GESTIÓN DE PAGOS 
    public static function insertarPago($datosPago)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "INSERT INTO pago (cuotas, monto, idMatricula, fecha) 
                    VALUES (:cuotas, :monto, :idMatricula, :fecha)";
            $stmt = $instanciaConexion->prepare($sql);

            $stmt->bindParam(':cuotas', $datosPago['cuotas'], PDO::PARAM_INT);
            $stmt->bindParam(':monto', $datosPago['monto'], PDO::PARAM_INT);
            $stmt->bindParam(':idMatricula', $datosPago['idMatricula'], PDO::PARAM_INT);
            $stmt->bindParam(':fecha', $datosPago['fecha'], PDO::PARAM_STR);

            $stmt->execute();
            return $instanciaConexion->lastInsertId();
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function obtenerPagos()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT p.*, m.idEstudiante, e.NombreEstudiante, e.ApellidosEstudiante, e.CodigoEstudiante
                    FROM pago p
                    JOIN matricula m ON p.idMatricula = m.idMatricula
                    JOIN estudiante e ON m.idEstudiante = e.idEstudiante
                    ORDER BY p.fecha DESC";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function buscarPagos($criterio)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT p.*, m.idEstudiante, e.NombreEstudiante, e.ApellidosEstudiante, e.CodigoEstudiante
                    FROM pago p
                    JOIN matricula m ON p.idMatricula = m.idMatricula
                    JOIN estudiante e ON m.idEstudiante = e.idEstudiante
                    WHERE e.CodigoEstudiante LIKE :criterio OR e.NombreEstudiante LIKE :criterio OR e.ApellidosEstudiante LIKE :criterio
                    ORDER BY p.fecha DESC";
            $stmt = $instanciaConexion->prepare($sql);
            $likeCriterio = "%" . $criterio . "%";
            $stmt->bindParam(':criterio', $likeCriterio, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function consultarEstadoPago($idEstudiante)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT p.*, m.idCurso, c.NombreCurso
                    FROM pago p
                    JOIN matricula m ON p.idMatricula = m.idMatricula
                    JOIN curso c ON m.idCurso = c.idCurso
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

    public static function generarReporteIngresos($fechaInicio, $fechaFin)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT SUM(monto) as total, DATE(fecha) as fecha, COUNT(*) as cantidad_pagos
                    FROM pago 
                    WHERE fecha BETWEEN :fechaInicio AND :fechaFin 
                    GROUP BY DATE(fecha)
                    ORDER BY fecha";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':fechaInicio', $fechaInicio, PDO::PARAM_STR);
            $stmt->bindParam(':fechaFin', $fechaFin, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }


    // FUNCIONES PARA LA GESTIÓN  DE CONSULTAS DE USUARIOS
    public static function insertarConsulta($datosConsulta)
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
            return $instanciaConexion->lastInsertId();
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function obtenerConsultas()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT c.* FROM consultas c
                    ORDER BY c.idConsulta DESC";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function buscarConsultas($criterio)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT c.* FROM consultas c
                    WHERE c.tipo LIKE :criterio OR c.motivo LIKE :criterio
                    ORDER BY c.idConsulta DESC";
            $stmt = $instanciaConexion->prepare($sql);
            $likeCriterio = "%" . $criterio . "%";
            $stmt->bindParam(':criterio', $likeCriterio, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function insertarDestinatarioConsulta($idConsulta, $idUsuarioDestinatario, $rolDestinado)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "INSERT INTO destinatarioconsulta (idConsulta, idUsuarioDestinatario, rolDestinado, estado) 
                    VALUES (:idConsulta, :idUsuarioDestinatario, :rolDestinado, 'pendiente')";
            $stmt = $instanciaConexion->prepare($sql);

            $stmt->bindParam(':idConsulta', $idConsulta, PDO::PARAM_INT);
            $stmt->bindParam(':idUsuarioDestinatario', $idUsuarioDestinatario, PDO::PARAM_INT);
            $stmt->bindParam(':rolDestinado', $rolDestinado, PDO::PARAM_STR);

            return $stmt->execute();
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }


    // FUNCIONES PARA LA GESTIÓN DE GUÍAS DIDÁCTICAS
    public static function insertarGuiaDidactica($datosGuia)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "INSERT INTO guiasdidacticas (idAsignatura) VALUES (:idAsignatura)";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idAsignatura', $datosGuia['idAsignatura'], PDO::PARAM_INT);
            $stmt->execute();
            $idGuia = $instanciaConexion->lastInsertId();

            if (isset($datosGuia['url'])) {
                $sqlDoc = "INSERT INTO documentos (url, idGuia) VALUES (:url, :idGuia)";
                $stmtDoc = $instanciaConexion->prepare($sqlDoc);
                $stmtDoc->bindParam(':url', $datosGuia['url'], PDO::PARAM_STR);
                $stmtDoc->bindParam(':idGuia', $idGuia, PDO::PARAM_INT);
                $stmtDoc->execute();
            }

            return $idGuia;
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function obtenerGuiasPorAsignatura($idAsignatura)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT g.*, d.url as documento_url, a.NombreAsignatura
                    FROM guiasdidacticas g
                    LEFT JOIN documentos d ON g.idGuia = d.idGuia
                    JOIN asignatura a ON g.idAsignatura = a.idAsignatura
                    WHERE g.idAsignatura = :idAsignatura
                    ORDER BY g.idGuia DESC";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idAsignatura', $idAsignatura, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function buscarGuias($criterio)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT g.*, d.url as documento_url, a.NombreAsignatura
                    FROM guiasdidacticas g
                    LEFT JOIN documentos d ON g.idGuia = d.idGuia
                    JOIN asignatura a ON g.idAsignatura = a.idAsignatura
                    WHERE a.NombreAsignatura LIKE :criterio
                    ORDER BY a.NombreAsignatura";
            $stmt = $instanciaConexion->prepare($sql);
            $likeCriterio = "%" . $criterio . "%";
            $stmt->bindParam(':criterio', $likeCriterio, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    // FUNCIONES PARA LA GESTIÓN DE INFORMES ACADÉMICOS
    public static function insertarInforme($datosInforme)
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

    public static function obtenerInformes()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT i.* FROM informes i
                    ORDER BY i.idInforme DESC";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function buscarInformes($criterio)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT i.* FROM informes i
                    WHERE i.asunto LIKE :criterio
                    ORDER BY i.idInforme DESC";
            $stmt = $instanciaConexion->prepare($sql);
            $likeCriterio = "%" . $criterio . "%";
            $stmt->bindParam(':criterio', $likeCriterio, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    // FUNCIONES PARA LA GESTION DE TÍTULOS Y CERTIFICACIONES
    public static function insertarDocumento($datosDocumento)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "INSERT INTO documentos (url, idGuia) VALUES (:url, :idGuia)";
            $stmt = $instanciaConexion->prepare($sql);

            $stmt->bindParam(':url', $datosDocumento['url'], PDO::PARAM_STR);
            $stmt->bindParam(':idGuia', $datosDocumento['idGuia'], PDO::PARAM_INT);

            $stmt->execute();
            return $instanciaConexion->lastInsertId();
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function obtenerDocumentosPorGuia($idGuia)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT * FROM documentos WHERE idGuia=:idGuia ORDER BY idDocumento DESC";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idGuia', $idGuia, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    // FUNCIONES PARA GESTIONAR LAS ESTADÍSTICAS ACADÉMICAS
    public static function consultarEstadisticasAcademicas($anoAcademico = null)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT 
                    (SELECT COUNT(DISTINCT idEstudiante) FROM estudiante) as total_estudiantes,
                    (SELECT COUNT(DISTINCT idProfesor) FROM profesor) as total_profesores,
                    (SELECT COUNT(DISTINCT idAsignatura) FROM asignatura) as total_asignaturas,
                    (SELECT COUNT(DISTINCT idCarrera) FROM carrera) as total_carreras,
                    (SELECT AVG(nota) FROM evaluación WHERE tipo='Final') as promedio_notas_finales,
                    (SELECT COUNT(DISTINCT idEstudiante) FROM matricula WHERE AñoAcademico = :anoAcademico) as estudiantes_matriculados_ano";

            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':anoAcademico', $anoAcademico, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }



    // FACULTADES
    public static function insertarFacultad($datosFacultad)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "INSERT INTO facultad (NombreFacultad, TelefonoFacultad, DireccionFacultad) 
                    VALUES (:NombreFacultad, :TelefonoFacultad, :DireccionFacultad)";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':NombreFacultad', $datosFacultad['NombreFacultad'], PDO::PARAM_STR);
            $stmt->bindParam(':TelefonoFacultad', $datosFacultad['TelefonoFacultad'], PDO::PARAM_INT);
            $stmt->bindParam(':DireccionFacultad', $datosFacultad['DireccionFacultad'], PDO::PARAM_STR);
            $stmt->execute();
            return $instanciaConexion->lastInsertId();
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function obtenerFacultades()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT * FROM facultad ORDER BY NombreFacultad";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function buscarFacultades($criterio)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT * FROM facultad WHERE NombreFacultad LIKE :criterio ORDER BY NombreFacultad";
            $stmt = $instanciaConexion->prepare($sql);
            $likeCriterio = "%" . $criterio . "%";
            $stmt->bindParam(':criterio', $likeCriterio, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function cargarFacultadesParaSelect()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT idfacultad as value, NombreFacultad as label FROM facultad ORDER BY NombreFacultad";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function cargarNombresFacultades()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT idfacultad as value, NombreFacultad as label FROM facultad ORDER BY NombreFacultad";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    // CARRERAS
    public static function insertarCarrera($datosCarrera)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "INSERT INTO carrera (NombreCarrera, idDepartamento) 
                    VALUES (:NombreCarrera, :idDepartamento)";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':NombreCarrera', $datosCarrera['NombreCarrera'], PDO::PARAM_STR);
            $stmt->bindParam(':idDepartamento', $datosCarrera['idDepartamento'], PDO::PARAM_INT);
            $stmt->execute();
            return $instanciaConexion->lastInsertId();
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function obtenerCarreras()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT c.*, d.NombreDepartamento, f.NombreFacultad
                    FROM carrera c 
                    JOIN departamento d ON c.idDepartamento = d.idDepartamento
                    JOIN facultad f ON d.idFacultad = f.idfacultad
                    ORDER BY c.NombreCarrera";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function buscarCarreras($criterio)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT c.*, d.NombreDepartamento, f.NombreFacultad
                    FROM carrera c 
                    JOIN departamento d ON c.idDepartamento = d.idDepartamento
                    JOIN facultad f ON d.idFacultad = f.idfacultad
                    WHERE c.NombreCarrera LIKE :criterio OR d.NombreDepartamento LIKE :criterio OR f.NombreFacultad LIKE :criterio
                    ORDER BY c.NombreCarrera";
            $stmt = $instanciaConexion->prepare($sql);
            $likeCriterio = "%" . $criterio . "%";
            $stmt->bindParam(':criterio', $likeCriterio, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function cargarCarrerasParaSelect()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT c.idCarrera as value, CONCAT(c.NombreCarrera, ' (', d.NombreDepartamento, ')') as label 
                    FROM carrera c 
                    JOIN departamento d ON c.idDepartamento = d.idDepartamento
                    ORDER BY c.NombreCarrera";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function cargarNombresCarreras()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT c.idCarrera as value, c.NombreCarrera as label 
                    FROM carrera c 
                    ORDER BY c.NombreCarrera";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    // AULAS
    public static function insertarAula($datosAula)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "INSERT INTO aulas (nombre) VALUES (:nombre)";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':nombre', $datosAula['nombre'], PDO::PARAM_STR);
            $stmt->execute();
            return $instanciaConexion->lastInsertId();
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function obtenerAulas()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT * FROM aulas ORDER BY nombre";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function buscarAulas($criterio)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT * FROM aulas WHERE nombre LIKE :criterio ORDER BY nombre";
            $stmt = $instanciaConexion->prepare($sql);
            $likeCriterio = "%" . $criterio . "%";
            $stmt->bindParam(':criterio', $likeCriterio, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function cargarAulasParaSelect()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT idAula as value, nombre as label FROM aulas ORDER BY nombre";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function cargarNombresAulas()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT idAula as value, nombre as label FROM aulas ORDER BY nombre";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    // HORARIOS
    public static function insertarHorario($datosHorario)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "INSERT INTO horario (NumeroAula, idCarrera, idCurso, dia, idAsignatura, Horalnicio, HoraFinal, idProfesor, idSemestre) 
                    VALUES (:NumeroAula, :idCarrera, :idCurso, :dia, :idAsignatura, :Horalnicio, :HoraFinal, :idProfesor, :idSemestre)";
            $stmt = $instanciaConexion->prepare($sql);

            $stmt->bindParam(':NumeroAula', $datosHorario['NumeroAula'], PDO::PARAM_INT);
            $stmt->bindParam(':idCarrera', $datosHorario['idCarrera'], PDO::PARAM_INT);
            $stmt->bindParam(':idCurso', $datosHorario['idCurso'], PDO::PARAM_INT);
            $stmt->bindParam(':dia', $datosHorario['dia'], PDO::PARAM_STR);
            $stmt->bindParam(':idAsignatura', $datosHorario['idAsignatura'], PDO::PARAM_INT);
            $stmt->bindParam(':Horalnicio', $datosHorario['Horalnicio'], PDO::PARAM_STR);
            $stmt->bindParam(':HoraFinal', $datosHorario['HoraFinal'], PDO::PARAM_STR);
            $stmt->bindParam(':idProfesor', $datosHorario['idProfesor'], PDO::PARAM_INT);
            $stmt->bindParam(':idSemestre', $datosHorario['idSemestre'], PDO::PARAM_INT);

            $stmt->execute();
            return $instanciaConexion->lastInsertId();
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function obtenerHorarios()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT h.*, c.NombreCarrera, cur.NombreCurso, a.NombreAsignatura, p.NombreProfesor, s.NombreSemestre
                    FROM horario h
                    JOIN carrera c ON h.idCarrera = c.idCarrera
                    JOIN curso cur ON h.idCurso = cur.idCurso
                    JOIN asignatura a ON h.idAsignatura = a.idAsignatura
                    JOIN profesor p ON h.idProfesor = p.idProfesor
                    JOIN semestre s ON h.idSemestre = s.idSemestre
                    ORDER BY h.dia, h.Horalnicio";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function buscarHorarios($criterio)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT h.*, c.NombreCarrera, a.NombreAsignatura, p.NombreProfesor
                    FROM horario h
                    JOIN carrera c ON h.idCarrera = c.idCarrera
                    JOIN asignatura a ON h.idAsignatura = a.idAsignatura
                    JOIN profesor p ON h.idProfesor = p.idProfesor
                    WHERE c.NombreCarrera LIKE :criterio OR a.NombreAsignatura LIKE :criterio OR p.NombreProfesor LIKE :criterio
                    OR h.dia LIKE :criterio
                    ORDER BY h.dia, h.Horalnicio";
            $stmt = $instanciaConexion->prepare($sql);
            $likeCriterio = "%" . $criterio . "%";
            $stmt->bindParam(':criterio', $likeCriterio, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function cargarHorariosParaSelect()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT idHorario as value, CONCAT(dia, ' ', Horalnicio, '-', HoraFinal, ' - ', a.NombreAsignatura) as label 
                    FROM horario h
                    JOIN asignatura a ON h.idAsignatura = a.idAsignatura
                    ORDER BY h.dia, h.Horalnicio";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function cargarNombresHorarios()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT idHorario as value, CONCAT(dia, ' ', Horalnicio, '-', HoraFinal) as label 
                    FROM horario 
                    ORDER BY dia, Horalnicio";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    // CURSOS
    public static function insertarCurso($datosCurso)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "INSERT INTO curso (NombreCurso, CreditosCurso, idCarrera, idSemestre) 
                    VALUES (:NombreCurso, :CreditosCurso, :idCarrera, :idSemestre)";
            $stmt = $instanciaConexion->prepare($sql);

            $stmt->bindParam(':NombreCurso', $datosCurso['NombreCurso'], PDO::PARAM_STR);
            $stmt->bindParam(':CreditosCurso', $datosCurso['CreditosCurso'], PDO::PARAM_INT);
            $stmt->bindParam(':idCarrera', $datosCurso['idCarrera'], PDO::PARAM_INT);
            $stmt->bindParam(':idSemestre', $datosCurso['idSemestre'], PDO::PARAM_INT);

            $stmt->execute();
            return $instanciaConexion->lastInsertId();
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function obtenerCursos()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT c.*, car.NombreCarrera, s.NombreSemestre 
                    FROM curso c
                    JOIN carrera car ON c.idCarrera = car.idCarrera
                    JOIN semestre s ON c.idSemestre = s.idSemestre
                    ORDER BY c.NombreCurso";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function buscarCursos($criterio)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT c.*, car.NombreCarrera, s.NombreSemestre 
                    FROM curso c
                    JOIN carrera car ON c.idCarrera = car.idCarrera
                    JOIN semestre s ON c.idSemestre = s.idSemestre
                    WHERE c.NombreCurso LIKE :criterio OR car.NombreCarrera LIKE :criterio
                    ORDER BY c.NombreCurso";
            $stmt = $instanciaConexion->prepare($sql);
            $likeCriterio = "%" . $criterio . "%";
            $stmt->bindParam(':criterio', $likeCriterio, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function cargarCursosParaSelect()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT idCurso as value, CONCAT(NombreCurso, ' (', CreditosCurso, ' créditos)') as label 
                    FROM curso ORDER BY NombreCurso";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function cargarNombresCursos()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT idCurso as value, NombreCurso as label FROM curso ORDER BY NombreCurso";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    // SEMESTRES
    public static function insertarSemestre($datosSemestre)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "INSERT INTO semestre (NombreSemestre, idAsignatura, idCarrera, idCurso, idEstudiante) 
                    VALUES (:NombreSemestre, :idAsignatura, :idCarrera, :idCurso, :idEstudiante)";
            $stmt = $instanciaConexion->prepare($sql);

            $stmt->bindParam(':NombreSemestre', $datosSemestre['NombreSemestre'], PDO::PARAM_STR);
            $stmt->bindParam(':idAsignatura', $datosSemestre['idAsignatura'], PDO::PARAM_INT);
            $stmt->bindParam(':idCarrera', $datosSemestre['idCarrera'], PDO::PARAM_INT);
            $stmt->bindParam(':idCurso', $datosSemestre['idCurso'], PDO::PARAM_INT);
            $stmt->bindParam(':idEstudiante', $datosSemestre['idEstudiante'], PDO::PARAM_INT);

            $stmt->execute();
            return $instanciaConexion->lastInsertId();
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function obtenerSemestres()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT s.*, a.NombreAsignatura, c.NombreCarrera, cur.NombreCurso, e.NombreEstudiante
                    FROM semestre s
                    LEFT JOIN asignatura a ON s.idAsignatura = a.idAsignatura
                    LEFT JOIN carrera c ON s.idCarrera = c.idCarrera
                    LEFT JOIN curso cur ON s.idCurso = cur.idCurso
                    LEFT JOIN estudiante e ON s.idEstudiante = e.idEstudiante
                    ORDER BY s.NombreSemestre";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function buscarSemestres($criterio)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT s.*, a.NombreAsignatura, c.NombreCarrera, e.NombreEstudiante
                    FROM semestre s
                    LEFT JOIN asignatura a ON s.idAsignatura = a.idAsignatura
                    LEFT JOIN carrera c ON s.idCarrera = c.idCarrera
                    LEFT JOIN estudiante e ON s.idEstudiante = e.idEstudiante
                    WHERE s.NombreSemestre LIKE :criterio OR a.NombreAsignatura LIKE :criterio 
                    OR c.NombreCarrera LIKE :criterio OR e.NombreEstudiante LIKE :criterio
                    ORDER BY s.NombreSemestre";
            $stmt = $instanciaConexion->prepare($sql);
            $likeCriterio = "%" . $criterio . "%";
            $stmt->bindParam(':criterio', $likeCriterio, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function cargarSemestresParaSelect()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT idSemestre as value, NombreSemestre as label 
                    FROM semestre 
                    ORDER BY NombreSemestre";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function cargarNombresSemestres()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT idSemestre as value, NombreSemestre as label 
                    FROM semestre 
                    ORDER BY NombreSemestre";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }


    // FUNCIONES ADICIONALES PARA SELECTS   
    public static function cargarProfesoresParaSelect()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT idProfesor as value, CONCAT(NombreProfesor, ' ', ApellidosProfesor) as label 
                    FROM profesor 
                    ORDER BY ApellidosProfesor, NombreProfesor";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function cargarEstudiantesParaSelect()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT idEstudiante as value, CONCAT(NombreEstudiante, ' ', ApellidosEstudiante, ' (', CodigoEstudiante, ')') as label 
                    FROM estudiante 
                    ORDER BY ApellidosEstudiante, NombreEstudiante";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function cargarBecariosParaSelect()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT idBecario as value, institucionBeca as label FROM becario ORDER BY institucionBeca";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    // FUNCIONES DE CONSULTA RÁPIDA    
    public static function obtenerResumenEstadistico()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT 
                    (SELECT COUNT(*) FROM estudiante) as total_estudiantes,
                    (SELECT COUNT(*) FROM profesor) as total_profesores,
                    (SELECT COUNT(*) FROM asignatura) as total_asignaturas,
                    (SELECT COUNT(*) FROM carrera) as total_carreras,
                    (SELECT COUNT(*) FROM matricula WHERE YEAR(FechaMatricula) = YEAR(CURDATE())) as matriculas_ano,
                    (SELECT SUM(monto) FROM pago WHERE YEAR(fecha) = YEAR(CURDATE())) as ingresos_ano";

            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function obtenerUltimasNoticias($limite = 5)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT * FROM noticia ORDER BY idNoticia DESC LIMIT :limite";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function obtenerProfesoresPorDepartamento($idDepartamento)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT * FROM profesor 
                    WHERE idDepartamento = :idDepartamento
                    ORDER BY ApellidosProfesor, NombreProfesor";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idDepartamento', $idDepartamento, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }


    // FUNCIONES PARA LA GESTIÓN DE SEMESTRES Y PERIODOS ACADÉMICOS   
    public static function obtenerSemestreActivo()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT * FROM semestre 
                    LIMIT 1";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function obtenerPeriodosAcademicos()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT DISTINCT AñoAcademico, EstadoMatricula 
                    FROM matricula 
                    ORDER BY AñoAcademico DESC";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    // GESTIÓN DE AULAS   
    public static function verificarDisponibilidadAula($idAula, $diaSemanal, $horaInicio, $horaFin, $excluirClaseId = null)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT COUNT(*) as ocupada FROM clase 
                    WHERE idAula = :idAula 
                    AND diaSemanal = :diaSemanal
                    AND (
                        (:horaInicio BETWEEN horalnicio AND HoraFinal) OR
                        (:horaFin BETWEEN horalnicio AND HoraFinal) OR
                        (horalnicio BETWEEN :horaInicio AND :horaFin)
                    )";

            if ($excluirClaseId) {
                $sql .= " AND idClase != :excluirClaseId";
            }

            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idAula', $idAula, PDO::PARAM_INT);
            $stmt->bindParam(':diaSemanal', $diaSemanal, PDO::PARAM_STR);
            $stmt->bindParam(':horaInicio', $horaInicio, PDO::PARAM_STR);
            $stmt->bindParam(':horaFin', $horaFin, PDO::PARAM_STR);

            if ($excluirClaseId) {
                $stmt->bindParam(':excluirClaseId', $excluirClaseId, PDO::PARAM_INT);
            }

            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['ocupada'] == 0; // Devuelve true si está disponible
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function obtenerAulasDisponibles($diaSemanal, $horaInicio, $horaFin, $excluirClaseId = null)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT a.* FROM aulas a
                    WHERE a.idAula NOT IN (
                        SELECT c.idAula FROM clase c
                        WHERE c.diaSemanal = :diaSemanal
                        AND (
                            (:horaInicio BETWEEN c.horalnicio AND c.HoraFinal) OR
                            (:horaFin BETWEEN c.horalnicio AND c.HoraFinal) OR
                            (c.horalnicio BETWEEN :horaInicio AND :horaFin)
                        )";

            if ($excluirClaseId) {
                $sql .= " AND c.idClase != :excluirClaseId";
            }

            $sql .= ") ORDER BY a.nombre";

            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':diaSemanal', $diaSemanal, PDO::PARAM_STR);
            $stmt->bindParam(':horaInicio', $horaInicio, PDO::PARAM_STR);
            $stmt->bindParam(':horaFin', $horaFin, PDO::PARAM_STR);

            if ($excluirClaseId) {
                $stmt->bindParam(':excluirClaseId', $excluirClaseId, PDO::PARAM_INT);
            }

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    // GESTIÓN DE CURSOS Y NIVELES
    public static function obtenerCursosPorCarrera($idCarrera)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT * FROM curso WHERE idCarrera = :idCarrera ORDER BY NombreCurso";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idCarrera', $idCarrera, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function obtenerAsignaturasPorCurso($idCurso)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT * FROM asignatura WHERE idCurso = :idCurso ORDER BY NombreAsignatura";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idCurso', $idCurso, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    // GESTIÓN DE CONVOCATORIAS DE ESTUDIANTES   
    public static function obtenerConvocatoriasEstudianteAsignatura($idEstudiante, $idAsignatura)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT * FROM estudiante_asignatura 
                    WHERE idEstudianteConvocatoria = :idEstudiante AND idAsignatura = :idAsignatura
                    ORDER BY convocatoria";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idEstudiante', $idEstudiante, PDO::PARAM_INT);
            $stmt->bindParam(':idAsignatura', $idAsignatura, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    //GESTIÓN DE HORARIOS

    public static function crearHorario($datosHorario)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "INSERT INTO horario (NumeroAula, idCarrera, idCurso, dia, idAsignatura, Horalnicio, HoraFinal, idProfesor, idSemestre) 
                    VALUES (:NumeroAula, :idCarrera, :idCurso, :dia, :idAsignatura, :Horalnicio, :HoraFinal, :idProfesor, :idSemestre)";
            $stmt = $instanciaConexion->prepare($sql);

            $stmt->bindParam(':NumeroAula', $datosHorario['NumeroAula'], PDO::PARAM_INT);
            $stmt->bindParam(':idCarrera', $datosHorario['idCarrera'], PDO::PARAM_INT);
            $stmt->bindParam(':idCurso', $datosHorario['idCurso'], PDO::PARAM_INT);
            $stmt->bindParam(':dia', $datosHorario['dia'], PDO::PARAM_STR);
            $stmt->bindParam(':idAsignatura', $datosHorario['idAsignatura'], PDO::PARAM_INT);
            $stmt->bindParam(':Horalnicio', $datosHorario['Horalnicio'], PDO::PARAM_STR);
            $stmt->bindParam(':HoraFinal', $datosHorario['HoraFinal'], PDO::PARAM_STR);
            $stmt->bindParam(':idProfesor', $datosHorario['idProfesor'], PDO::PARAM_INT);
            $stmt->bindParam(':idSemestre', $datosHorario['idSemestre'], PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function obtenerTodosHorarios()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT h.*, c.NombreCarrera, cur.NombreCurso, a.NombreAsignatura, p.NombreProfesor, s.NombreSemestre
                    FROM horario h
                    JOIN carrera c ON h.idCarrera = c.idCarrera
                    JOIN curso cur ON h.idCurso = cur.idCurso
                    JOIN asignatura a ON h.idAsignatura = a.idAsignatura
                    JOIN profesor p ON h.idProfesor = p.idProfesor
                    JOIN semestre s ON h.idSemestre = s.idSemestre
                    ORDER BY h.dia, h.Horalnicio";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function obtenerHorarioPorId($idHorario)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT h.*, c.NombreCarrera, cur.NombreCurso, a.NombreAsignatura, p.NombreProfesor, s.NombreSemestre
                    FROM horario h
                    JOIN carrera c ON h.idCarrera = c.idCarrera
                    JOIN curso cur ON h.idCurso = cur.idCurso
                    JOIN asignatura a ON h.idAsignatura = a.idAsignatura
                    JOIN profesor p ON h.idProfesor = p.idProfesor
                    JOIN semestre s ON h.idSemestre = s.idSemestre
                    WHERE h.idHorario = :idHorario";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idHorario', $idHorario, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function actualizarHorario($idHorario, $datosHorario)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "UPDATE horario SET NumeroAula=:NumeroAula, idCarrera=:idCarrera, idCurso=:idCurso, 
                    dia=:dia, idAsignatura=:idAsignatura, Horalnicio=:Horalnicio, HoraFinal=:HoraFinal, 
                    idProfesor=:idProfesor, idSemestre=:idSemestre 
                    WHERE idHorario=:idHorario";
            $stmt = $instanciaConexion->prepare($sql);

            $stmt->bindParam(':NumeroAula', $datosHorario['NumeroAula'], PDO::PARAM_INT);
            $stmt->bindParam(':idCarrera', $datosHorario['idCarrera'], PDO::PARAM_INT);
            $stmt->bindParam(':idCurso', $datosHorario['idCurso'], PDO::PARAM_INT);
            $stmt->bindParam(':dia', $datosHorario['dia'], PDO::PARAM_STR);
            $stmt->bindParam(':idAsignatura', $datosHorario['idAsignatura'], PDO::PARAM_INT);
            $stmt->bindParam(':Horalnicio', $datosHorario['Horalnicio'], PDO::PARAM_STR);
            $stmt->bindParam(':HoraFinal', $datosHorario['HoraFinal'], PDO::PARAM_STR);
            $stmt->bindParam(':idProfesor', $datosHorario['idProfesor'], PDO::PARAM_INT);
            $stmt->bindParam(':idSemestre', $datosHorario['idSemestre'], PDO::PARAM_INT);
            $stmt->bindParam(':idHorario', $idHorario, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function eliminarHorario($idHorario)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "DELETE FROM horario WHERE idHorario = :idHorario";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idHorario', $idHorario, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function buscarHorariosPorNombre($nombre)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT h.*, c.NombreCarrera, a.NombreAsignatura, p.NombreProfesor
                    FROM horario h
                    JOIN carrera c ON h.idCarrera = c.idCarrera
                    JOIN asignatura a ON h.idAsignatura = a.idAsignatura
                    JOIN profesor p ON h.idProfesor = p.idProfesor
                    WHERE c.NombreCarrera LIKE :nombre OR a.NombreAsignatura LIKE :nombre OR p.NombreProfesor LIKE :nombre
                    ORDER BY h.dia, h.Horalnicio";
            $stmt = $instanciaConexion->prepare($sql);
            $nombreBusqueda = "%" . $nombre . "%";
            $stmt->bindParam(':nombre', $nombreBusqueda, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function horarioEstaEnUso($idHorario)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT COUNT(*) as total FROM clase WHERE idHorario = :idHorario";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idHorario', $idHorario, PDO::PARAM_INT);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['total'] > 0;
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function obtenerHorariosDisponibles()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT h.* FROM horario h 
                    LEFT JOIN clase c ON h.idHorario = c.idHorario 
                    WHERE c.idHorario IS NULL 
                    ORDER BY h.dia, h.Horalnicio";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function obtenerHorariosEnUso()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT DISTINCT h.* FROM horario h 
                    JOIN clase c ON h.idHorario = c.idHorario 
                    ORDER BY h.dia, h.Horalnicio";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function asignarHorarioAClase($idClase, $idHorario)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "UPDATE clase SET idHorario = :idHorario WHERE idClase = :idClase";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idHorario', $idHorario, PDO::PARAM_INT);
            $stmt->bindParam(':idClase', $idClase, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function quitarHorarioDeClase($idClase)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "UPDATE clase SET idHorario = NULL WHERE idClase = :idClase";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idClase', $idClase, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function obtenerClasesConHorarios()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT c.*, h.dia as horario_dia, h.Horalnicio as horario_inicio, h.HoraFinal as horario_fin, 
                    p.NombreProfesor, p.ApellidosProfesor,
                    a.NombreAsignatura, au.nombre as aula_nombre
                    FROM clase c
                    LEFT JOIN horario h ON c.idHorario = h.idHorario
                    JOIN profesor p ON c.idProfesor = p.idProfesor
                    JOIN asignatura a ON c.idAsignatura = a.idAsignatura
                    JOIN aulas au ON c.idAula = au.idAula
                    ORDER BY c.diaSemanal, c.horalnicio";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function obtenerClasesSinHorario()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT c.*, p.NombreProfesor, p.ApellidosProfesor,
                    a.NombreAsignatura, au.nombre as aula_nombre
                    FROM clase c
                    JOIN profesor p ON c.idProfesor = p.idProfesor
                    JOIN asignatura a ON c.idAsignatura = a.idAsignatura
                    JOIN aulas au ON c.idAula = au.idAula
                    WHERE c.idHorario IS NULL
                    ORDER BY c.diaSemanal, c.horalnicio";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function obtenerClasesPorHorario($idHorario)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT c.*, p.NombreProfesor, p.ApellidosProfesor,
                    a.NombreAsignatura, au.nombre as aula_nombre
                    FROM clase c
                    JOIN profesor p ON c.idProfesor = p.idProfesor
                    JOIN asignatura a ON c.idAsignatura = a.idAsignatura
                    JOIN aulas au ON c.idAula = au.idAula
                    WHERE c.idHorario = :idHorario
                    ORDER BY c.diaSemanal, c.horalnicio";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idHorario', $idHorario, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }



    // GESTIÓN DE CONFLICTOS DE HORARIOS
    public static function verificarConflictoProfesor($idProfesor, $diaSemanal, $horaInicio, $horaFin, $excluirClaseId = null)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT COUNT(*) as conflicto FROM clase 
                    WHERE idProfesor = :idProfesor 
                    AND diaSemanal = :diaSemanal
                    AND (
                        (:horaInicio BETWEEN horalnicio AND HoraFinal) OR
                        (:horaFin BETWEEN horalnicio AND HoraFinal) OR
                        (horalnicio BETWEEN :horaInicio AND :horaFin)
                    )";

            if ($excluirClaseId) {
                $sql .= " AND idClase != :excluirClaseId";
            }

            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idProfesor', $idProfesor, PDO::PARAM_INT);
            $stmt->bindParam(':diaSemanal', $diaSemanal, PDO::PARAM_STR);
            $stmt->bindParam(':horaInicio', $horaInicio, PDO::PARAM_STR);
            $stmt->bindParam(':horaFin', $horaFin, PDO::PARAM_STR);

            if ($excluirClaseId) {
                $stmt->bindParam(':excluirClaseId', $excluirClaseId, PDO::PARAM_INT);
            }

            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['conflicto'] > 0;
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function verificarConflictoAula($idAula, $diaSemanal, $horaInicio, $horaFin, $excluirClaseId = null)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT COUNT(*) as conflicto FROM clase 
                    WHERE idAula = :idAula 
                    AND diaSemanal = :diaSemanal
                    AND (
                        (:horaInicio BETWEEN horalnicio AND HoraFinal) OR
                        (:horaFin BETWEEN horalnicio AND HoraFinal) OR
                        (horalnicio BETWEEN :horaInicio AND :horaFin)
                    )";

            if ($excluirClaseId) {
                $sql .= " AND idClase != :excluirClaseId";
            }

            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idAula', $idAula, PDO::PARAM_INT);
            $stmt->bindParam(':diaSemanal', $diaSemanal, PDO::PARAM_STR);
            $stmt->bindParam(':horaInicio', $horaInicio, PDO::PARAM_STR);
            $stmt->bindParam(':horaFin', $horaFin, PDO::PARAM_STR);

            if ($excluirClaseId) {
                $stmt->bindParam(':excluirClaseId', $excluirClaseId, PDO::PARAM_INT);
            }

            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['conflicto'] > 0;
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }


    public static function generarHorarioProfesor($idProfesor)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT c.*, a.NombreAsignatura, au.nombre as aula_nombre,
                    h.dia as horario_dia, h.Horalnicio as horario_inicio, h.HoraFinal as horario_fin
                    FROM clase c
                    JOIN asignatura a ON c.idAsignatura = a.idAsignatura
                    JOIN aulas au ON c.idAula = au.idAula
                    LEFT JOIN horario h ON c.idHorario = h.idHorario
                    WHERE c.idProfesor = :idProfesor
                    ORDER BY 
                        CASE c.diaSemanal 
                            WHEN 'L' THEN 1
                            WHEN 'M' THEN 2
                            WHEN 'X' THEN 3
                            WHEN 'J' THEN 4
                            WHEN 'V' THEN 5
                            WHEN 'S' THEN 6
                            WHEN 'D' THEN 7
                            ELSE 8
                        END,
                        c.horalnicio";

            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idProfesor', $idProfesor, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function generarHorarioAula($idAula)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT c.*, a.NombreAsignatura, 
                    p.NombreProfesor, p.ApellidosProfesor,
                    h.dia as horario_dia, h.Horalnicio as horario_inicio, h.HoraFinal as horario_fin
                    FROM clase c
                    JOIN asignatura a ON c.idAsignatura = a.idAsignatura
                    JOIN profesor p ON c.idProfesor = p.idProfesor
                    LEFT JOIN horario h ON c.idHorario = h.idHorario
                    WHERE c.idAula = :idAula
                    ORDER BY 
                        CASE c.diaSemanal 
                            WHEN 'L' THEN 1
                            WHEN 'M' THEN 2
                            WHEN 'X' THEN 3
                            WHEN 'J' THEN 4
                            WHEN 'V' THEN 5
                            WHEN 'S' THEN 6
                            WHEN 'D' THEN 7
                            ELSE 8
                        END,
                        c.horalnicio";

            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idAula', $idAula, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function generarHorarioCompleto($dia = null)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT c.*, 
                    a.NombreAsignatura,
                    p.NombreProfesor, p.ApellidosProfesor,
                    au.nombre as aula_nombre,
                    h.dia as horario_dia, h.Horalnicio as horario_inicio, h.HoraFinal as horario_fin
                    FROM clase c
                    JOIN asignatura a ON c.idAsignatura = a.idAsignatura
                    JOIN profesor p ON c.idProfesor = p.idProfesor
                    JOIN aulas au ON c.idAula = au.idAula
                    LEFT JOIN horario h ON c.idHorario = h.idHorario";

            if ($dia) {
                $sql .= " WHERE c.diaSemanal = :dia";
            }

            $sql .= " ORDER BY 
                        CASE c.diaSemanal 
                            WHEN 'L' THEN 1
                            WHEN 'M' THEN 2
                            WHEN 'X' THEN 3
                            WHEN 'J' THEN 4
                            WHEN 'V' THEN 5
                            WHEN 'S' THEN 6
                            WHEN 'D' THEN 7
                            ELSE 8
                        END,
                        c.horalnicio,
                        au.nombre";

            $stmt = $instanciaConexion->prepare($sql);

            if ($dia) {
                $stmt->bindParam(':dia', $dia, PDO::PARAM_STR);
            }

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function obtenerEstadisticasHorarios()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT 
                    (SELECT COUNT(*) FROM horario) as total_horarios,
                    (SELECT COUNT(DISTINCT idHorario) FROM clase WHERE idHorario IS NOT NULL) as horarios_en_uso,
                    (SELECT COUNT(*) FROM clase WHERE idHorario IS NULL) as clases_sin_horario,
                    (SELECT COUNT(DISTINCT idProfesor) FROM clase) as profesores_con_clases,
                    (SELECT COUNT(DISTINCT idAula) FROM clase) as aulas_en_uso";

            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }
}
