<?php
class SecretarioDao {
    
    // FUNCIONES PARA LA GESTIÓN DE PROFESORES
    public static function insertarProfesor($datosProfesor) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "INSERT INTO Profesor (idUsuario, nombre, apellidos, correo, telefono, nacionalidad, departamento, estado, genero, responsabilidad) 
                    VALUES (:idUsuario, :nombre, :apellidos, :correo, :telefono, :nacionalidad, :departamento, :estado, :genero, :responsabilidad)";
            $stmt = $instanciaConexion->prepare($sql);
            
            $stmt->bindParam(':idUsuario', $datosProfesor['idUsuario'], PDO::PARAM_INT);
            $stmt->bindParam(':nombre', $datosProfesor['nombre'], PDO::PARAM_STR);
            $stmt->bindParam(':apellidos', $datosProfesor['apellidos'], PDO::PARAM_STR);
            $stmt->bindParam(':correo', $datosProfesor['correo'], PDO::PARAM_STR);
            $stmt->bindParam(':telefono', $datosProfesor['telefono'], PDO::PARAM_STR);
            $stmt->bindParam(':nacionalidad', $datosProfesor['nacionalidad'], PDO::PARAM_STR);
            $stmt->bindParam(':departamento', $datosProfesor['departamento'], PDO::PARAM_STR);
            $stmt->bindParam(':estado', $datosProfesor['estado'], PDO::PARAM_STR);
            $stmt->bindParam(':genero', $datosProfesor['genero'], PDO::PARAM_STR);
            $stmt->bindParam(':responsabilidad', $datosProfesor['responsabilidad'], PDO::PARAM_STR);
            
            $stmt->execute();
            return $instanciaConexion->lastInsertId();
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function actualizarProfesor($idProfesor, $datosProfesor) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "UPDATE Profesor SET nombre=:nombre, apellidos=:apellidos, correo=:correo, telefono=:telefono, 
                    nacionalidad=:nacionalidad, departamento=:departamento, estado=:estado, genero=:genero, responsabilidad=:responsabilidad 
                    WHERE idProfesor=:idProfesor";
            $stmt = $instanciaConexion->prepare($sql);
            
            $stmt->bindParam(':nombre', $datosProfesor['nombre'], PDO::PARAM_STR);
            $stmt->bindParam(':apellidos', $datosProfesor['apellidos'], PDO::PARAM_STR);
            $stmt->bindParam(':correo', $datosProfesor['correo'], PDO::PARAM_STR);
            $stmt->bindParam(':telefono', $datosProfesor['telefono'], PDO::PARAM_STR);
            $stmt->bindParam(':nacionalidad', $datosProfesor['nacionalidad'], PDO::PARAM_STR);
            $stmt->bindParam(':departamento', $datosProfesor['departamento'], PDO::PARAM_STR);
            $stmt->bindParam(':estado', $datosProfesor['estado'], PDO::PARAM_STR);
            $stmt->bindParam(':genero', $datosProfesor['genero'], PDO::PARAM_STR);
            $stmt->bindParam(':responsabilidad', $datosProfesor['responsabilidad'], PDO::PARAM_STR);
            $stmt->bindParam(':idProfesor', $idProfesor, PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function eliminarProfesor($idProfesor) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "UPDATE Profesor SET estado='baja' WHERE idProfesor=:idProfesor";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idProfesor', $idProfesor, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public function obtenerProfesores()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT p.*, u.login, u.rol FROM Profesor p 
                    JOIN Usuario u ON p.idUsuario = u.idUsuario 
                    WHERE p.estado='alta'";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public function obtenerProfesorPorId($idProfesor) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT p.*, u.login, u.rol FROM Profesor p 
                    JOIN Usuario u ON p.idUsuario = u.idUsuario 
                    WHERE p.idProfesor=:idProfesor";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idProfesor', $idProfesor, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public function buscarProfesores($criterio) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT p.*, u.login FROM Profesor p 
                    JOIN Usuario u ON p.idUsuario = u.idUsuario 
                    WHERE p.nombre LIKE :criterio OR p.apellidos LIKE :criterio OR p.correo LIKE :criterio 
                    OR u.login LIKE :criterio
                    ORDER BY p.apellidos, p.nombre";
            $stmt = $instanciaConexion->prepare($sql);
            $likeCriterio = "%" . $criterio . "%";
            $stmt->bindParam(':criterio', $likeCriterio, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public function cargarNombresProfesores() {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT idProfesor as value, CONCAT(nombre, ' ', apellidos) as label 
                    FROM Profesor 
                    WHERE estado='alta' 
                    ORDER BY apellidos, nombre";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public function insertarFormacion($datosFormacion) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "INSERT INTO Formacion (institucion, tipo, titulo, nivel, idProfesor) 
                    VALUES (:institucion, :tipo, :titulo, :nivel, :idProfesor)";
            $stmt = $instanciaConexion->prepare($sql);
            
            $stmt->bindParam(':institucion', $datosFormacion['institucion'], PDO::PARAM_STR);
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

    public function obtenerFormacionesPorProfesor($idProfesor) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT * FROM Formacion WHERE idProfesor=:idProfesor ORDER BY nivel DESC";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idProfesor', $idProfesor, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }


    // FUNCIONES PARA LA GESTIÓN DE ESTUDIANTES
    public function insertarEstudiante($datosEstudiante) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "INSERT INTO Estudiante (codigo, idUsuario, Nombre, Apellidos, fechaNacimiento, Nacionalidad, centroProcedencia, genero, idFamiliar) 
                    VALUES (:codigo, :idUsuario, :Nombre, :Apellidos, :fechaNacimiento, :Nacionalidad, :centroProcedencia, :genero, :idFamiliar)";
            $stmt = $instanciaConexion->prepare($sql);
            
            $stmt->bindParam(':codigo', $datosEstudiante['codigo'], PDO::PARAM_STR);
            $stmt->bindParam(':idUsuario', $datosEstudiante['idUsuario'], PDO::PARAM_INT);
            $stmt->bindParam(':Nombre', $datosEstudiante['Nombre'], PDO::PARAM_STR);
            $stmt->bindParam(':Apellidos', $datosEstudiante['Apellidos'], PDO::PARAM_STR);
            $stmt->bindParam(':fechaNacimiento', $datosEstudiante['fechaNacimiento'], PDO::PARAM_STR);
            $stmt->bindParam(':Nacionalidad', $datosEstudiante['Nacionalidad'], PDO::PARAM_STR);
            $stmt->bindParam(':centroProcedencia', $datosEstudiante['centroProcedencia'], PDO::PARAM_STR);
            $stmt->bindParam(':genero', $datosEstudiante['genero'], PDO::PARAM_STR);
            $stmt->bindParam(':idFamiliar', $datosEstudiante['idFamiliar'], PDO::PARAM_INT);
            
            $stmt->execute();
            return $instanciaConexion->lastInsertId();
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public function actualizarEstudiante($codigo, $datosEstudiante) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "UPDATE Estudiante SET Nombre=:Nombre, Apellidos=:Apellidos, fechaNacimiento=:fechaNacimiento, 
                    Nacionalidad=:Nacionalidad, centroProcedencia=:centroProcedencia, genero=:genero, idFamiliar=:idFamiliar 
                    WHERE codigo=:codigo";
            $stmt = $instanciaConexion->prepare($sql);
            
            $stmt->bindParam(':Nombre', $datosEstudiante['Nombre'], PDO::PARAM_STR);
            $stmt->bindParam(':Apellidos', $datosEstudiante['Apellidos'], PDO::PARAM_STR);
            $stmt->bindParam(':fechaNacimiento', $datosEstudiante['fechaNacimiento'], PDO::PARAM_STR);
            $stmt->bindParam(':Nacionalidad', $datosEstudiante['Nacionalidad'], PDO::PARAM_STR);
            $stmt->bindParam(':centroProcedencia', $datosEstudiante['centroProcedencia'], PDO::PARAM_STR);
            $stmt->bindParam(':genero', $datosEstudiante['genero'], PDO::PARAM_STR);
            $stmt->bindParam(':idFamiliar', $datosEstudiante['idFamiliar'], PDO::PARAM_INT);
            $stmt->bindParam(':codigo', $codigo, PDO::PARAM_STR);
            
            return $stmt->execute();
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public function eliminarEstudiante($codigo) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "DELETE FROM Estudiante WHERE codigo=:codigo";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':codigo', $codigo, PDO::PARAM_STR);
            return $stmt->execute();
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public function obtenerEstudiantes() {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT e.*, u.login FROM Estudiante e 
                    JOIN Usuario u ON e.idUsuario = u.idUsuario 
                    ORDER BY e.Apellidos, e.Nombre";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public function obtenerEstudiantePorCodigo($codigo) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT e.*, u.login FROM Estudiante e 
                    JOIN Usuario u ON e.idUsuario = u.idUsuario 
                    WHERE e.codigo=:codigo";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':codigo', $codigo, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public function buscarEstudiantes($criterio) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT e.*, u.login FROM Estudiante e 
                    JOIN Usuario u ON e.idUsuario = u.idUsuario 
                    WHERE e.codigo LIKE :criterio OR e.Nombre LIKE :criterio OR e.Apellidos LIKE :criterio 
                    OR u.login LIKE :criterio OR e.centroProcedencia LIKE :criterio
                    ORDER BY e.Apellidos, e.Nombre";
            $stmt = $instanciaConexion->prepare($sql);
            $likeCriterio = "%" . $criterio . "%";
            $stmt->bindParam(':criterio', $likeCriterio, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function cargarNombresEstudiantes() {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT codigo as value, CONCAT(Nombre, ' ', Apellidos, ' (', codigo, ')') as label 
                    FROM Estudiante 
                    ORDER BY Apellidos, Nombre";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function cargarSoloNombresEstudiantes() {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT codigo as value, CONCAT(Nombre, ' ', Apellidos) as label 
                    FROM Estudiante 
                    ORDER BY Apellidos, Nombre";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function insertarFamiliar($datosFamiliar) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "INSERT INTO Familiares (nombre, apellidos, relacion, contacto, correo) 
                    VALUES (:nombre, :apellidos, :relacion, :contacto, :correo)";
            $stmt = $instanciaConexion->prepare($sql);
            
            $stmt->bindParam(':nombre', $datosFamiliar['nombre'], PDO::PARAM_STR);
            $stmt->bindParam(':apellidos', $datosFamiliar['apellidos'], PDO::PARAM_STR);
            $stmt->bindParam(':relacion', $datosFamiliar['relacion'], PDO::PARAM_STR);
            $stmt->bindParam(':contacto', $datosFamiliar['contacto'], PDO::PARAM_STR);
            $stmt->bindParam(':correo', $datosFamiliar['correo'], PDO::PARAM_STR);
            
            $stmt->execute();
            return $instanciaConexion->lastInsertId();
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function obtenerFamiliares() {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT * FROM Familiares ORDER BY apellidos, nombre";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function buscarFamiliares($criterio) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT * FROM Familiares 
                    WHERE nombre LIKE :criterio OR apellidos LIKE :criterio OR relacion LIKE :criterio
                    ORDER BY apellidos, nombre";
            $stmt = $instanciaConexion->prepare($sql);
            $likeCriterio = "%" . $criterio . "%";
            $stmt->bindParam(':criterio', $likeCriterio, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function obtenerFamiliaresPorEstudiante($codigoEstudiante) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT f.* FROM Familiares f
                    JOIN Estudiante e ON e.idFamiliar = f.idFamiliar
                    WHERE e.codigo = :codigoEstudiante
                    ORDER BY f.apellidos, f.nombre";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':codigoEstudiante', $codigoEstudiante, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }


    // FUNCIONES PARA LA INSCRIPCIÓN EN CARRERAS
    public static function insertarInscripcion($datosInscripcion) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "INSERT INTO Inscripcion (codigo, fecha, idCarrera, anoAcademico, periodo) 
                    VALUES (:codigo, :fecha, :idCarrera, :anoAcademico, :periodo)";
            $stmt = $instanciaConexion->prepare($sql);
            
            $stmt->bindParam(':codigo', $datosInscripcion['codigo'], PDO::PARAM_STR);
            $stmt->bindParam(':fecha', $datosInscripcion['fecha'], PDO::PARAM_STR);
            $stmt->bindParam(':idCarrera', $datosInscripcion['idCarrera'], PDO::PARAM_INT);
            $stmt->bindParam(':anoAcademico', $datosInscripcion['anoAcademico'], PDO::PARAM_INT);
            $stmt->bindParam(':periodo', $datosInscripcion['periodo'], PDO::PARAM_STR);
            
            $stmt->execute();
            return $instanciaConexion->lastInsertId();
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function obtenerInscripcionesPorEstudiante($codigo) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT i.*, c.Nombre as carrera_nombre, f.Nombre as facultad_nombre 
                    FROM Inscripcion i 
                    JOIN Carrera c ON i.idCarrera = c.idCarrera
                    JOIN Facultad f ON c.idFacultad = f.idFacultad
                    WHERE i.codigo=:codigo
                    ORDER BY i.anoAcademico DESC, i.periodo DESC";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':codigo', $codigo, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function buscarInscripciones($criterio) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT i.*, e.Nombre as estudiante_nombre, e.Apellidos as estudiante_apellidos, 
                    c.Nombre as carrera_nombre
                    FROM Inscripcion i 
                    JOIN Estudiante e ON i.codigo = e.codigo
                    JOIN Carrera c ON i.idCarrera = c.idCarrera
                    WHERE e.codigo LIKE :criterio OR e.Nombre LIKE :criterio OR e.Apellidos LIKE :criterio
                    OR c.Nombre LIKE :criterio
                    ORDER BY i.fecha DESC";
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
    public static function asignarBeca($datosBeca) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "INSERT INTO Estudiante_Beca (Codigo, idBecario, fechaInicio, fechaFinal, estado) 
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

    public static function actualizarBeca($idBeca, $datosBeca) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "UPDATE Estudiante_Beca SET fechaFinal=:fechaFinal, estado=:estado 
                    WHERE id=:id";
            $stmt = $instanciaConexion->prepare($sql);
            
            $stmt->bindParam(':fechaFinal', $datosBeca['fechaFinal'], PDO::PARAM_STR);
            $stmt->bindParam(':estado', $datosBeca['estado'], PDO::PARAM_STR);
            $stmt->bindParam(':id', $idBeca, PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function eliminarBeca($idBeca) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "DELETE FROM Estudiante_Beca WHERE id=:id";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':id', $idBeca, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function obtenerBecas() {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT eb.*, e.Nombre, e.Apellidos, b.institucionBeca 
                    FROM Estudiante_Beca eb
                    JOIN Estudiante e ON eb.Codigo = e.codigo
                    JOIN Becario b ON eb.idBecario = b.idBecario
                    ORDER BY eb.fechaInicio DESC";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function buscarBecas($criterio) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT eb.*, e.Nombre, e.Apellidos, b.institucionBeca 
                    FROM Estudiante_Beca eb
                    JOIN Estudiante e ON eb.Codigo = e.codigo
                    JOIN Becario b ON eb.idBecario = b.idBecario
                    WHERE e.codigo LIKE :criterio OR e.Nombre LIKE :criterio OR e.Apellidos LIKE :criterio
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

    public static function insertarBecario($datosBecario) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "INSERT INTO Becario (institucionBeca) VALUES (:institucionBeca)";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':institucionBeca', $datosBecario['institucionBeca'], PDO::PARAM_STR);
            $stmt->execute();
            return $instanciaConexion->lastInsertId();
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function obtenerBecarios() {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT * FROM Becario ORDER BY institucionBeca";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function cargarNombresBecarios() {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT idBecario as value, institucionBeca as label FROM Becario ORDER BY institucionBeca";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    //  FUNCIONES PARA LA MATRÍCULA EN ASIGNATURAS
    public static function matricularEstudianteAsignatura($codigo, $idAsignatura, $convocatoria) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "INSERT INTO Estudiante_Asignatura (Codigo, idAsignatura, convocatoria) 
                    VALUES (:Codigo, :idAsignatura, :convocatoria)";
            $stmt = $instanciaConexion->prepare($sql);
            
            $stmt->bindParam(':Codigo', $codigo, PDO::PARAM_STR);
            $stmt->bindParam(':idAsignatura', $idAsignatura, PDO::PARAM_INT);
            $stmt->bindParam(':convocatoria', $convocatoria, PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function obtenerAsignaturasPorEstudiante($codigo) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT ea.*, a.Nombre as asignatura_nombre, a.creditos, c.Nombre as curso_nombre
                    FROM Estudiante_Asignatura ea
                    JOIN Asignatura a ON ea.idAsignatura = a.idAsignatura
                    JOIN Curso c ON a.idCurso = c.idCurso
                    WHERE ea.Codigo=:codigo
                    ORDER BY a.Nombre";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':codigo', $codigo, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }


    // FUNCIONES PARA VER EL HISTORIAL ACADÉMICO
    public static function consultarHistorialAcademico($codigo) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT ea.*, a.Nombre as asignatura, a.creditos, e.tipo, e.nota, e.fecha 
                    FROM Estudiante_Asignatura ea
                    JOIN Asignatura a ON ea.idAsignatura = a.idAsignatura
                    LEFT JOIN Evaluacion e ON a.idAsignatura = e.idAsignatura
                    WHERE ea.Codigo = :codigo 
                    ORDER BY e.fecha DESC";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':codigo', $codigo, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }


    // FUNCIONES PARA LA GESTION DE PLANES DE ESTUDIO
    public static function insertarPlanEstudio($datosPlan) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "INSERT INTO PlanEstudio (periodoPlanEstudio, nombre, idCarrera, fechaElaboracion) 
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

    public static function actualizarPlanEstudio($idPlanEstudio, $datosPlan) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "UPDATE PlanEstudio SET periodoPlanEstudio=:periodoPlanEstudio, nombre=:nombre, 
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

    public static function eliminarPlanEstudio($idPlanEstudio) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "DELETE FROM PlanEstudio WHERE idPlanEstudio=:idPlanEstudio";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idPlanEstudio', $idPlanEstudio, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function obtenerPlanesEstudio() {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT pe.*, c.Nombre as carrera_nombre, f.Nombre as facultad_nombre
                    FROM PlanEstudio pe 
                    JOIN Carrera c ON pe.idCarrera = c.idCarrera
                    JOIN Facultad f ON c.idFacultad = f.idFacultad
                    ORDER BY pe.nombre";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function buscarPlanesEstudio($criterio) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT pe.*, c.Nombre as carrera_nombre
                    FROM PlanEstudio pe 
                    JOIN Carrera c ON pe.idCarrera = c.idCarrera
                    WHERE pe.nombre LIKE :criterio OR pe.periodoPlanEstudio LIKE :criterio 
                    OR c.Nombre LIKE :criterio
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

    public static function cargarPlanesEstudioParaSelect() {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT idPlanEstudio as value, CONCAT(nombre, ' (', periodoPlanEstudio, ')') as label 
                    FROM PlanEstudio 
                    ORDER BY nombre";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function asignarCursoPlanEstudio($idPlanEstudio, $idCurso, $idSemestre) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "INSERT INTO Curso_PlanEstudio (idPlanEstudio, idCurso, idSemestre) 
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

    public static function obtenerCursosPorPlanEstudio($idPlanEstudio) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT cpe.*, c.Nombre as curso_nombre, c.Creditos, s.Nombre as semestre_nombre
                    FROM Curso_PlanEstudio cpe
                    JOIN Curso c ON cpe.idCurso = c.idCurso
                    JOIN Semestre s ON cpe.idSemestre = s.idSemestre
                    WHERE cpe.idPlanEstudio = :idPlanEstudio
                    ORDER BY s.Nombre";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idPlanEstudio', $idPlanEstudio, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }


    // FUNCIONES PARA GESTIÓN DE ASIGNATURAS
    public function insertarAsignatura($datosAsignatura) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "INSERT INTO Asignatura (Nombre, creditos, idCurso, modalidad) 
                    VALUES (:Nombre, :creditos, :idCurso, :modalidad)";
            $stmt = $instanciaConexion->prepare($sql);
            
            $stmt->bindParam(':Nombre', $datosAsignatura['Nombre'], PDO::PARAM_STR);
            $stmt->bindParam(':creditos', $datosAsignatura['creditos'], PDO::PARAM_INT);
            $stmt->bindParam(':idCurso', $datosAsignatura['idCurso'], PDO::PARAM_INT);
            $stmt->bindParam(':modalidad', $datosAsignatura['modalidad'], PDO::PARAM_STR);
            
            $stmt->execute();
            return $instanciaConexion->lastInsertId();
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public function actualizarAsignatura($idAsignatura, $datosAsignatura) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "UPDATE Asignatura SET Nombre=:Nombre, creditos=:creditos, idCurso=:idCurso, modalidad=:modalidad 
                    WHERE idAsignatura=:idAsignatura";
            $stmt = $instanciaConexion->prepare($sql);
            
            $stmt->bindParam(':Nombre', $datosAsignatura['Nombre'], PDO::PARAM_STR);
            $stmt->bindParam(':creditos', $datosAsignatura['creditos'], PDO::PARAM_INT);
            $stmt->bindParam(':idCurso', $datosAsignatura['idCurso'], PDO::PARAM_INT);
            $stmt->bindParam(':modalidad', $datosAsignatura['modalidad'], PDO::PARAM_STR);
            $stmt->bindParam(':idAsignatura', $idAsignatura, PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public function eliminarAsignatura($idAsignatura) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "DELETE FROM Asignatura WHERE idAsignatura=:idAsignatura";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idAsignatura', $idAsignatura, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public function obtenerAsignaturas() {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT a.*, c.Nombre as curso_nombre FROM Asignatura a 
                    JOIN Curso c ON a.idCurso = c.idCurso
                    ORDER BY a.Nombre";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public function buscarAsignaturas($criterio) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT a.*, c.Nombre as curso_nombre 
                    FROM Asignatura a 
                    JOIN Curso c ON a.idCurso = c.idCurso
                    WHERE a.Nombre LIKE :criterio OR c.Nombre LIKE :criterio OR a.modalidad LIKE :criterio
                    ORDER BY a.Nombre";
            $stmt = $instanciaConexion->prepare($sql);
            $likeCriterio = "%" . $criterio . "%";
            $stmt->bindParam(':criterio', $likeCriterio, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public function cargarAsignaturasParaSelect() {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT idAsignatura as value, Nombre as label FROM Asignatura ORDER BY Nombre";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public function cargarNombresAsignaturas() {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT idAsignatura as value, CONCAT(Nombre, ' (', creditos, ' créditos)') as label 
                    FROM Asignatura ORDER BY Nombre";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    // FUNCIONES PARA LA OFERTA ACADÉMICA POR CARRERA

    public function consultarOfertaAcademica($idCarrera) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT a.*, c.Nombre as curso_nombre 
                    FROM Asignatura a
                    JOIN Curso c ON a.idCurso = c.idCurso
                    JOIN PlanEstudio pe ON c.idCurso IN (SELECT idCurso FROM Curso_PlanEstudio WHERE idPlanEstudio = pe.idPlanEstudio)
                    WHERE pe.idCarrera = :idCarrera
                    GROUP BY a.idAsignatura
                    ORDER BY a.Nombre";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idCarrera', $idCarrera, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    // FUNCIONES PARA LA GESTIÓN DE CLASES Y HORARIOS
    public function insertarClase($datosClase) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "INSERT INTO Clase (idProfesor, idAsignatura, diaSemanal, idAula, idHorario, horalnicio, HoraFinal) 
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

    public function actualizarClase($idClase, $datosClase) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "UPDATE Clase SET idProfesor=:idProfesor, idAsignatura=:idAsignatura, diaSemanal=:diaSemanal, 
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

    public function eliminarClase($idClase) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "DELETE FROM Clase WHERE idClase=:idClase";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idClase', $idClase, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public function obtenerClases() {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT c.*, p.nombre as profesor_nombre, p.apellidos as profesor_apellidos, 
                    a.Nombre as asignatura_nombre, au.nombre as aula_nombre, h.nombre as horario_nombre
                    FROM Clase c
                    JOIN Profesor p ON c.idProfesor = p.idProfesor
                    JOIN Asignatura a ON c.idAsignatura = a.idAsignatura
                    JOIN Aulas au ON c.idAula = au.idAula
                    LEFT JOIN Horario h ON c.idHorario = h.idHorario
                    ORDER BY c.diaSemanal, c.horalnicio";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public function buscarClases($criterio) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT c.*, p.nombre as profesor_nombre, p.apellidos as profesor_apellidos, 
                    a.Nombre as asignatura_nombre, au.nombre as aula_nombre
                    FROM Clase c
                    JOIN Profesor p ON c.idProfesor = p.idProfesor
                    JOIN Asignatura a ON c.idAsignatura = a.idAsignatura
                    JOIN Aulas au ON c.idAula = au.idAula
                    WHERE p.nombre LIKE :criterio OR p.apellidos LIKE :criterio 
                    OR a.Nombre LIKE :criterio OR au.nombre LIKE :criterio
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
    public function insertarPago($datosPago) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "INSERT INTO Pago (fechaPago, idInscripcion, monto) 
                    VALUES (:fechaPago, :idInscripcion, :monto)";
            $stmt = $instanciaConexion->prepare($sql);
            
            $stmt->bindParam(':fechaPago', $datosPago['fechaPago'], PDO::PARAM_STR);
            $stmt->bindParam(':idInscripcion', $datosPago['idInscripcion'], PDO::PARAM_INT);
            $stmt->bindParam(':monto', $datosPago['monto'], PDO::PARAM_STR);
            
            $stmt->execute();
            return $instanciaConexion->lastInsertId();
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public function obtenerPagos() {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT p.*, i.codigo as estudiante_codigo, e.Nombre as estudiante_nombre, 
                    e.Apellidos as estudiante_apellidos 
                    FROM Pago p
                    JOIN Inscripcion i ON p.idInscripcion = i.idInscripcion
                    JOIN Estudiante e ON i.codigo = e.codigo
                    ORDER BY p.fechaPago DESC";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public function buscarPagos($criterio) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT p.*, i.codigo as estudiante_codigo, e.Nombre as estudiante_nombre, 
                    e.Apellidos as estudiante_apellidos 
                    FROM Pago p
                    JOIN Inscripcion i ON p.idInscripcion = i.idInscripcion
                    JOIN Estudiante e ON i.codigo = e.codigo
                    WHERE i.codigo LIKE :criterio OR e.Nombre LIKE :criterio OR e.Apellidos LIKE :criterio
                    ORDER BY p.fechaPago DESC";
            $stmt = $instanciaConexion->prepare($sql);
            $likeCriterio = "%" . $criterio . "%";
            $stmt->bindParam(':criterio', $likeCriterio, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public function consultarEstadoPago($codigoEstudiante) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT p.*, i.idCarrera, c.Nombre as carrera_nombre
                    FROM Pago p
                    JOIN Inscripcion i ON p.idInscripcion = i.idInscripcion
                    JOIN Carrera c ON i.idCarrera = c.idCarrera
                    WHERE i.codigo = :codigo
                    ORDER BY p.fechaPago DESC";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':codigo', $codigoEstudiante, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public function generarReporteIngresos($fechaInicio, $fechaFin) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT SUM(monto) as total, DATE(fechaPago) as fecha, COUNT(*) as cantidad_pagos
                    FROM Pago 
                    WHERE fechaPago BETWEEN :fechaInicio AND :fechaFin 
                    GROUP BY DATE(fechaPago)
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
    public function insertarConsulta($datosConsulta) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "INSERT INTO Consultas (tipo, motivo, contenido, idEmisor) 
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

    public function obtenerConsultas() {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT c.*, u.login as emisor_login 
                    FROM Consultas c
                    JOIN Usuario u ON c.idEmisor = u.idUsuario
                    ORDER BY c.idConsulta DESC";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public function buscarConsultas($criterio) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT c.*, u.login as emisor_login 
                    FROM Consultas c
                    JOIN Usuario u ON c.idEmisor = u.idUsuario
                    WHERE c.tipo LIKE :criterio OR c.motivo LIKE :criterio OR u.login LIKE :criterio
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

    public function insertarDestinatarioConsulta($idConsulta, $idUsuarioDestinatario, $rolDestinado) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "INSERT INTO DestinatarioConsulta (idConsulta, idUsuarioDestinatario, rolDestinado) 
                    VALUES (:idConsulta, :idUsuarioDestinatario, :rolDestinado)";
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
    public function insertarGuiaDidactica($datosGuia) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "INSERT INTO Guiasdidacticas (idAsignatura) VALUES (:idAsignatura)";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idAsignatura', $datosGuia['idAsignatura'], PDO::PARAM_INT);
            $stmt->execute();
            $idGuia = $instanciaConexion->lastInsertId();
            
            if (isset($datosGuia['url'])) {
                $sqlDoc = "INSERT INTO Documentos (url, idGuia) VALUES (:url, :idGuia)";
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

    public function obtenerGuiasPorAsignatura($idAsignatura) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT g.*, d.url as documento_url, a.Nombre as asignatura_nombre
                    FROM Guiasdidacticas g
                    LEFT JOIN Documentos d ON g.idGuia = d.idGuia
                    JOIN Asignatura a ON g.idAsignatura = a.idAsignatura
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

    public function buscarGuias($criterio) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT g.*, d.url as documento_url, a.Nombre as asignatura_nombre
                    FROM Guiasdidacticas g
                    LEFT JOIN Documentos d ON g.idGuia = d.idGuia
                    JOIN Asignatura a ON g.idAsignatura = a.idAsignatura
                    WHERE a.Nombre LIKE :criterio
                    ORDER BY a.Nombre";
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
    public function insertarInforme($datosInforme) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "INSERT INTO Informes (asunto, contenido, idUsuario) 
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

    public function obtenerInformes() {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT i.*, u.login as usuario_login 
                    FROM Informes i
                    JOIN Usuario u ON i.idUsuario = u.idUsuario
                    ORDER BY i.idInforme DESC";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public function buscarInformes($criterio) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT i.*, u.login as usuario_login 
                    FROM Informes i
                    JOIN Usuario u ON i.idUsuario = u.idUsuario
                    WHERE i.asunto LIKE :criterio OR u.login LIKE :criterio
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
    public function insertarTitulo($datosTitulo) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "INSERT INTO Titulos (url, idFormacion) VALUES (:url, :idFormacion)";
            $stmt = $instanciaConexion->prepare($sql);
            
            $stmt->bindParam(':url', $datosTitulo['url'], PDO::PARAM_STR);
            $stmt->bindParam(':idFormacion', $datosTitulo['idFormacion'], PDO::PARAM_INT);
            
            $stmt->execute();
            return $instanciaConexion->lastInsertId();
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public function obtenerTitulosPorFormacion($idFormacion) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT * FROM Titulos WHERE idFormacion=:idFormacion ORDER BY idTitulo DESC";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idFormacion', $idFormacion, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    // FUNCIONES PARA GESTIONAR LAS ESTADÍSTICAS ACADÉMICAS
    public function consultarEstadisticasAcademicas($anoAcademico = null) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT 
                    (SELECT COUNT(DISTINCT codigo) FROM Estudiante) as total_estudiantes,
                    (SELECT COUNT(DISTINCT idProfesor) FROM Profesor WHERE estado='alta') as total_profesores,
                    (SELECT COUNT(DISTINCT idAsignatura) FROM Asignatura) as total_asignaturas,
                    (SELECT COUNT(DISTINCT idCarrera) FROM Carrera) as total_carreras,
                    (SELECT AVG(nota) FROM Evaluacion WHERE tipo='Final') as promedio_notas_finales,
                    (SELECT COUNT(DISTINCT codigo) FROM Inscripcion WHERE anoAcademico = :anoAcademico) as estudiantes_inscritos_ano";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':anoAcademico', $anoAcademico, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }


    
    // FACULTADES
    public function insertarFacultad($datosFacultad) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "INSERT INTO Facultad (Nombre) VALUES (:Nombre)";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':Nombre', $datosFacultad['Nombre'], PDO::PARAM_STR);
            $stmt->execute();
            return $instanciaConexion->lastInsertId();
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public function obtenerFacultades() {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT * FROM Facultad ORDER BY Nombre";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public function buscarFacultades($criterio) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT * FROM Facultad WHERE Nombre LIKE :criterio ORDER BY Nombre";
            $stmt = $instanciaConexion->prepare($sql);
            $likeCriterio = "%" . $criterio . "%";
            $stmt->bindParam(':criterio', $likeCriterio, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public function cargarFacultadesParaSelect() {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT idFacultad as value, Nombre as label FROM Facultad ORDER BY Nombre";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public function cargarNombresFacultades() {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT idFacultad as value, Nombre as label FROM Facultad ORDER BY Nombre";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    // CARRERAS
    public function insertarCarrera($datosCarrera) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "INSERT INTO Carrera (idFacultad) VALUES (:idFacultad)";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idFacultad', $datosCarrera['idFacultad'], PDO::PARAM_INT);
            $stmt->execute();
            return $instanciaConexion->lastInsertId();
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public function obtenerCarreras() {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT c.*, f.Nombre as facultad_nombre 
                    FROM Carrera c 
                    JOIN Facultad f ON c.idFacultad = f.idFacultad
                    ORDER BY f.Nombre";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public function buscarCarreras($criterio) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT c.*, f.Nombre as facultad_nombre 
                    FROM Carrera c 
                    JOIN Facultad f ON c.idFacultad = f.idFacultad
                    WHERE f.Nombre LIKE :criterio
                    ORDER BY f.Nombre";
            $stmt = $instanciaConexion->prepare($sql);
            $likeCriterio = "%" . $criterio . "%";
            $stmt->bindParam(':criterio', $likeCriterio, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public function cargarCarrerasParaSelect() {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT c.idCarrera as value, CONCAT(f.Nombre, ' - Carrera #', c.idCarrera) as label 
                    FROM Carrera c 
                    JOIN Facultad f ON c.idFacultad = f.idFacultad
                    ORDER BY f.Nombre";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public function cargarNombresCarreras() {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT c.idCarrera as value, f.Nombre as label 
                    FROM Carrera c 
                    JOIN Facultad f ON c.idFacultad = f.idFacultad
                    ORDER BY f.Nombre";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    // AULAS
    public function insertarAula($datosAula) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "INSERT INTO Aulas (nombre) VALUES (:nombre)";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':nombre', $datosAula['nombre'], PDO::PARAM_STR);
            $stmt->execute();
            return $instanciaConexion->lastInsertId();
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public function obtenerAulas() {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT * FROM Aulas ORDER BY nombre";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public function buscarAulas($criterio) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT * FROM Aulas WHERE nombre LIKE :criterio ORDER BY nombre";
            $stmt = $instanciaConexion->prepare($sql);
            $likeCriterio = "%" . $criterio . "%";
            $stmt->bindParam(':criterio', $likeCriterio, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public function cargarAulasParaSelect() {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT idAula as value, nombre as label FROM Aulas ORDER BY nombre";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public function cargarNombresAulas() {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT idAula as value, nombre as label FROM Aulas ORDER BY nombre";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    // HORARIOS
    public function insertarHorario($datosHorario) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "INSERT INTO Horario (nombre) VALUES (:nombre)";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':nombre', $datosHorario['nombre'], PDO::PARAM_STR);
            $stmt->execute();
            return $instanciaConexion->lastInsertId();
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public function obtenerHorarios() {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT * FROM Horario ORDER BY nombre";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public function buscarHorarios($criterio) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT * FROM Horario WHERE nombre LIKE :criterio ORDER BY nombre";
            $stmt = $instanciaConexion->prepare($sql);
            $likeCriterio = "%" . $criterio . "%";
            $stmt->bindParam(':criterio', $likeCriterio, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public function cargarHorariosParaSelect() {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT idHorario as value, nombre as label FROM Horario ORDER BY nombre";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public function cargarNombresHorarios() {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT idHorario as value, nombre as label FROM Horario ORDER BY nombre";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    // CURSOS
    public function insertarCurso($datosCurso) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "INSERT INTO Curso (Nombre, Creditos, Nivel) 
                    VALUES (:Nombre, :Creditos, :Nivel)";
            $stmt = $instanciaConexion->prepare($sql);
            
            $stmt->bindParam(':Nombre', $datosCurso['Nombre'], PDO::PARAM_STR);
            $stmt->bindParam(':Creditos', $datosCurso['Creditos'], PDO::PARAM_INT);
            $stmt->bindParam(':Nivel', $datosCurso['Nivel'], PDO::PARAM_INT);
            
            $stmt->execute();
            return $instanciaConexion->lastInsertId();
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public function obtenerCursos() {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT * FROM Curso ORDER BY Nombre";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public function buscarCursos($criterio) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT * FROM Curso 
                    WHERE Nombre LIKE :criterio 
                    ORDER BY Nombre";
            $stmt = $instanciaConexion->prepare($sql);
            $likeCriterio = "%" . $criterio . "%";
            $stmt->bindParam(':criterio', $likeCriterio, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public function cargarCursosParaSelect() {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT idCurso as value, CONCAT(Nombre, ' (', Creditos, ' créditos)') as label 
                    FROM Curso ORDER BY Nombre";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public function cargarNombresCursos() {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT idCurso as value, Nombre as label FROM Curso ORDER BY Nombre";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    // SEMESTRES
    public function insertarSemestre($datosSemestre) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "INSERT INTO Semestre (Nombre, Año, periodo) 
                    VALUES (:Nombre, :Año, :periodo)";
            $stmt = $instanciaConexion->prepare($sql);
            
            $stmt->bindParam(':Nombre', $datosSemestre['Nombre'], PDO::PARAM_STR);
            $stmt->bindParam(':Año', $datosSemestre['Año'], PDO::PARAM_INT);
            $stmt->bindParam(':periodo', $datosSemestre['periodo'], PDO::PARAM_STR);
            
            $stmt->execute();
            return $instanciaConexion->lastInsertId();
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public function obtenerSemestres() {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT * FROM Semestre ORDER BY Año DESC, periodo DESC";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public function buscarSemestres($criterio) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT * FROM Semestre 
                    WHERE Nombre LIKE :criterio OR periodo LIKE :criterio
                    ORDER BY Año DESC, periodo DESC";
            $stmt = $instanciaConexion->prepare($sql);
            $likeCriterio = "%" . $criterio . "%";
            $stmt->bindParam(':criterio', $likeCriterio, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public function cargarSemestresParaSelect() {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT idSemestre as value, CONCAT(Nombre, ' (Año ', Año, ', Periodo ', periodo, ')') as label 
                    FROM Semestre 
                    ORDER BY Año DESC, periodo DESC";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public function cargarNombresSemestres() {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT idSemestre as value, Nombre as label 
                    FROM Semestre 
                    ORDER BY Año DESC, periodo DESC";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    
    // FUNCIONES ADICIONALES PARA SELECTS
    
    public function cargarProfesoresParaSelect() {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT idProfesor as value, CONCAT(nombre, ' ', apellidos) as label 
                    FROM Profesor 
                    WHERE estado='alta' 
                    ORDER BY apellidos, nombre";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public function cargarEstudiantesParaSelect() {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT codigo as value, CONCAT(Nombre, ' ', Apellidos, ' (', codigo, ')') as label 
                    FROM Estudiante 
                    ORDER BY Apellidos, Nombre";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public function cargarBecariosParaSelect() {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT idBecario as value, institucionBeca as label FROM Becario ORDER BY institucionBeca";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }


    public function cargarModalidadesParaSelect() {
        $modalidades = [
            ['value' => 'presencial', 'label' => 'Presencial'],
            ['value' => 'virtual', 'label' => 'Virtual'],
            ['value' => 'hibrida', 'label' => 'Híbrida']
        ];
        return $modalidades;
    }

    public function cargarTiposEvaluacionParaSelect() {
        $tipos = [
            ['value' => 'parcial', 'label' => 'Parcial'],
            ['value' => 'trabajoPractico', 'label' => 'Trabajo Práctico'],
            ['value' => 'Final', 'label' => 'Final'],
            ['value' => 'Extraordinaria', 'label' => 'Extraordinaria']
        ];
        return $tipos;
    }

    public function cargarDiasSemanaParaSelect() {
        $dias = [
            ['value' => 'L', 'label' => 'Lunes'],
            ['value' => 'M', 'label' => 'Martes'],
            ['value' => 'X', 'label' => 'Miércoles'],
            ['value' => 'J', 'label' => 'Jueves'],
            ['value' => 'V', 'label' => 'Viernes'],
            ['value' => 'S', 'label' => 'Sábado'],
            ['value' => 'D', 'label' => 'Domingo']
        ];
        return $dias;
    }


    // ============================================
    // FUNCIONES DE CONSULTA RÁPIDA
    // ============================================
    
    public function obtenerResumenEstadistico() {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT 
                    (SELECT COUNT(*) FROM Estudiante) as total_estudiantes,
                    (SELECT COUNT(*) FROM Profesor WHERE estado='alta') as total_profesores,
                    (SELECT COUNT(*) FROM Asignatura) as total_asignaturas,
                    (SELECT COUNT(*) FROM Carrera) as total_carreras,
                    (SELECT COUNT(*) FROM Inscripcion WHERE YEAR(fecha) = YEAR(CURDATE())) as inscripciones_ano,
                    (SELECT SUM(monto) FROM Pago WHERE YEAR(fechaPago) = YEAR(CURDATE())) as ingresos_ano";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public function obtenerUltimasNoticias($limite = 5) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT * FROM Noticias ORDER BY idNoticia DESC LIMIT :limite";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }


    public function obtenerEstudiantesPorCarrera($idCarrera) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT e.* FROM Estudiante e
                    JOIN Inscripcion i ON e.codigo = i.codigo
                    WHERE i.idCarrera = :idCarrera
                    ORDER BY e.Apellidos, e.Nombre";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idCarrera', $idCarrera, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public function obtenerProfesoresPorDepartamento($departamento) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT * FROM Profesor 
                    WHERE departamento = :departamento AND estado='alta'
                    ORDER BY apellidos, nombre";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':departamento', $departamento, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }


    // FUNCIONES PARA LA GESTIÓN DE SEMESTRES Y PERIODOS ACADÉMICOS
    
    public function obtenerSemestreActivo() {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT * FROM Semestre 
                    WHERE YEAR(CURDATE()) = Año 
                    AND periodo = (
                        CASE 
                            WHEN MONTH(CURDATE()) BETWEEN 1 AND 6 THEN '1'
                            WHEN MONTH(CURDATE()) BETWEEN 7 AND 12 THEN '2'
                            ELSE periodo
                        END
                    )
                    LIMIT 1";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public function obtenerPeriodosAcademicos() {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT DISTINCT anoAcademico, periodo 
                    FROM Inscripcion 
                    ORDER BY anoAcademico DESC, periodo DESC";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    // GESTIÓN DE AULAS
    
    public function verificarDisponibilidadAula($idAula, $diaSemanal, $horaInicio, $horaFin, $excluirClaseId = null) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT COUNT(*) as ocupada FROM Clase 
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

    public function obtenerAulasDisponibles($diaSemanal, $horaInicio, $horaFin, $excluirClaseId = null) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT a.* FROM Aulas a
                    WHERE a.idAula NOT IN (
                        SELECT c.idAula FROM Clase c
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
    
    public function obtenerCursosPorNivel($nivel) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT * FROM Curso WHERE Nivel = :nivel ORDER BY Nombre";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':nivel', $nivel, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public function obtenerAsignaturasPorCurso($idCurso) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT * FROM Asignatura WHERE idCurso = :idCurso ORDER BY Nombre";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idCurso', $idCurso, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    // GESTIÓN DE CONVOCATORIAS DE ESTUDIANTES
    
    public function obtenerConvocatoriasEstudianteAsignatura($codigo, $idAsignatura) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT * FROM Estudiante_Asignatura 
                    WHERE Codigo = :codigo AND idAsignatura = :idAsignatura
                    ORDER BY convocatoria";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':codigo', $codigo, PDO::PARAM_STR);
            $stmt->bindParam(':idAsignatura', $idAsignatura, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    //GESTIÓN DE HORARIOS
    
    public function crearHorario($nombreHorario) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "INSERT INTO Horario (nombre) VALUES (:nombre)";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':nombre', $nombreHorario, PDO::PARAM_STR);
            return $stmt->execute();
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public function obtenerTodosHorarios() {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT * FROM Horario ORDER BY nombre";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public function obtenerHorarioPorId($idHorario) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT * FROM Horario WHERE idHorario = :idHorario";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idHorario', $idHorario, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public function actualizarHorario($idHorario, $nombreHorario) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "UPDATE Horario SET nombre = :nombre WHERE idHorario = :idHorario";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':nombre', $nombreHorario, PDO::PARAM_STR);
            $stmt->bindParam(':idHorario', $idHorario, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public function eliminarHorario($idHorario) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "DELETE FROM Horario WHERE idHorario = :idHorario";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idHorario', $idHorario, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public function buscarHorariosPorNombre($nombre) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT * FROM Horario WHERE nombre LIKE :nombre ORDER BY nombre";
            $stmt = $instanciaConexion->prepare($sql);
            $nombreBusqueda = "%" . $nombre . "%";
            $stmt->bindParam(':nombre', $nombreBusqueda, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public function horarioEstaEnUso($idHorario) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT COUNT(*) as total FROM Clase WHERE idHorario = :idHorario";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idHorario', $idHorario, PDO::PARAM_INT);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['total'] > 0;
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public function obtenerHorariosDisponibles() {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT h.* FROM Horario h 
                    LEFT JOIN Clase c ON h.idHorario = c.idHorario 
                    WHERE c.idHorario IS NULL 
                    ORDER BY h.nombre";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public function obtenerHorariosEnUso() {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT DISTINCT h.* FROM Horario h 
                    JOIN Clase c ON h.idHorario = c.idHorario 
                    ORDER BY h.nombre";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public function asignarHorarioAClase($idClase, $idHorario) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "UPDATE Clase SET idHorario = :idHorario WHERE idClase = :idClase";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idHorario', $idHorario, PDO::PARAM_INT);
            $stmt->bindParam(':idClase', $idClase, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public function quitarHorarioDeClase($idClase) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "UPDATE Clase SET idHorario = NULL WHERE idClase = :idClase";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idClase', $idClase, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public function obtenerClasesConHorarios() {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT c.*, h.nombre as nombre_horario, 
                    p.nombre as profesor_nombre, p.apellidos as profesor_apellidos,
                    a.Nombre as asignatura_nombre, au.nombre as aula_nombre
                    FROM Clase c
                    LEFT JOIN Horario h ON c.idHorario = h.idHorario
                    JOIN Profesor p ON c.idProfesor = p.idProfesor
                    JOIN Asignatura a ON c.idAsignatura = a.idAsignatura
                    JOIN Aulas au ON c.idAula = au.idAula
                    ORDER BY c.diaSemanal, c.horalnicio";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public function obtenerClasesSinHorario() {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT c.*, p.nombre as profesor_nombre, p.apellidos as profesor_apellidos,
                    a.Nombre as asignatura_nombre, au.nombre as aula_nombre
                    FROM Clase c
                    JOIN Profesor p ON c.idProfesor = p.idProfesor
                    JOIN Asignatura a ON c.idAsignatura = a.idAsignatura
                    JOIN Aulas au ON c.idAula = au.idAula
                    WHERE c.idHorario IS NULL
                    ORDER BY c.diaSemanal, c.horalnicio";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public function obtenerClasesPorHorario($idHorario) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT c.*, p.nombre as profesor_nombre, p.apellidos as profesor_apellidos,
                    a.Nombre as asignatura_nombre, au.nombre as aula_nombre
                    FROM Clase c
                    JOIN Profesor p ON c.idProfesor = p.idProfesor
                    JOIN Asignatura a ON c.idAsignatura = a.idAsignatura
                    JOIN Aulas au ON c.idAula = au.idAula
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
    public function verificarConflictoProfesor($idProfesor, $diaSemanal, $horaInicio, $horaFin, $excluirClaseId = null) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT COUNT(*) as conflicto FROM Clase 
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

    public function verificarConflictoAula($idAula, $diaSemanal, $horaInicio, $horaFin, $excluirClaseId = null) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT COUNT(*) as conflicto FROM Clase 
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


    // GENERACIÓN DE HORARIOS   
    public function generarHorarioProfesor($idProfesor) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT c.*, a.Nombre as asignatura_nombre, au.nombre as aula_nombre,
                    h.nombre as horario_nombre
                    FROM Clase c
                    JOIN Asignatura a ON c.idAsignatura = a.idAsignatura
                    JOIN Aulas au ON c.idAula = au.idAula
                    LEFT JOIN Horario h ON c.idHorario = h.idHorario
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

    public function generarHorarioAula($idAula) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT c.*, a.Nombre as asignatura_nombre, 
                    p.nombre as profesor_nombre, p.apellidos as profesor_apellidos,
                    h.nombre as horario_nombre
                    FROM Clase c
                    JOIN Asignatura a ON c.idAsignatura = a.idAsignatura
                    JOIN Profesor p ON c.idProfesor = p.idProfesor
                    LEFT JOIN Horario h ON c.idHorario = h.idHorario
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

    public function generarHorarioCompleto($dia = null) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT c.*, 
                    a.Nombre as asignatura_nombre,
                    p.nombre as profesor_nombre, p.apellidos as profesor_apellidos,
                    au.nombre as aula_nombre,
                    h.nombre as horario_nombre
                    FROM Clase c
                    JOIN Asignatura a ON c.idAsignatura = a.idAsignatura
                    JOIN Profesor p ON c.idProfesor = p.idProfesor
                    JOIN Aulas au ON c.idAula = au.idAula
                    LEFT JOIN Horario h ON c.idHorario = h.idHorario";
            
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


    // ESTADÍSTICAS DE HORARIOS    
    public function obtenerEstadisticasHorarios() {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT 
                    (SELECT COUNT(*) FROM Horario) as total_horarios,
                    (SELECT COUNT(DISTINCT idHorario) FROM Clase WHERE idHorario IS NOT NULL) as horarios_en_uso,
                    (SELECT COUNT(*) FROM Clase WHERE idHorario IS NULL) as clases_sin_horario,
                    (SELECT COUNT(DISTINCT idProfesor) FROM Clase) as profesores_con_clases,
                    (SELECT COUNT(DISTINCT idAula) FROM Clase) as aulas_en_uso";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public function obtenerHorariosMasUtilizados($limite = 5) {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT h.nombre, COUNT(c.idClase) as total_clases
                    FROM Horario h
                    LEFT JOIN Clase c ON h.idHorario = c.idHorario
                    GROUP BY h.idHorario, h.nombre
                    ORDER BY total_clases DESC, h.nombre
                    LIMIT :limite";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }
}
?>