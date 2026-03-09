<?php
require_once __DIR__ . "/../../utilidades/u_conexion.php";
require_once __DIR__ . "/../modelo/m_estudiante.php";

class D_Estudiante
{
    // OBTENER TODOS LOS ESTUDIANTES
    public static function obtenerEstudiantes()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT e.*, u.nombreUsuario, u.correo, u.foto, u.estado as estadoUsuario
                    FROM estudiantes e
                    LEFT JOIN usuarios u ON e.idUsuario = u.idUsuario
                    ORDER BY e.apellidos ASC, e.nombre ASC";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $estudiantes = [];
            
            foreach ($resultados as $fila) {
                $model = new EstudianteModel();
                $model->hidratarDesdeArray($fila);
                if (isset($fila['nombreUsuario'])) {
                    $model->nombreUsuario = $fila['nombreUsuario'];
                }
                $estudiantes[] = $model;
            }

            return $estudiantes;
        } catch (PDOException $e) {
            error_log("Error en obtenerEstudiantes: " . $e->getMessage());
            return [];
        }
    }

    // OBTENER ESTUDIANTES POR ASIGNATURA
    public static function obtenerEstudiantesPorAsignatura($idAsignatura)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT DISTINCT e.* 
                    FROM estudiantes e
                    INNER JOIN matriculas m ON e.idEstudiante = m.idEstudiante
                    INNER JOIN matricula_asignatura ma ON m.idMatricula = ma.idMatricula
                    INNER JOIN plan_semestre_asignatura psa ON ma.idPlanCursoAsignatura = psa.idPlanCursoAsignatura
                    WHERE psa.idAsignatura = :idAsignatura
                    ORDER BY e.apellidos ASC, e.nombre ASC";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idAsignatura', $idAsignatura, PDO::PARAM_INT);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $estudiantes = [];
            
            foreach ($resultados as $fila) {
                $model = new EstudianteModel();
                $model->hidratarDesdeArray($fila);
                $estudiantes[] = $model;
            }

            return $estudiantes;
        } catch (PDOException $e) {
            error_log("Error en obtenerEstudiantesPorAsignatura: " . $e->getMessage());
            return [];
        }
    }

    // OBTENER ESTUDIANTES POR FACULTAD
    public static function obtenerEstudiantesPorFacultad($idFacultad)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT DISTINCT e.* 
                    FROM estudiantes e
                    INNER JOIN matriculas m ON e.idEstudiante = m.idEstudiante
                    INNER JOIN planestudio pe ON m.idPlanEstudio = pe.idPlanEstudio
                    INNER JOIN carrera c ON pe.idCarrera = c.idCarrera
                    INNER JOIN departamento d ON c.idDepartamento = d.idDepartamento
                    WHERE d.idFacultad = :idFacultad
                    ORDER BY e.apellidos ASC, e.nombre ASC";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idFacultad', $idFacultad, PDO::PARAM_INT);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $estudiantes = [];
            
            foreach ($resultados as $fila) {
                $model = new EstudianteModel();
                $model->hidratarDesdeArray($fila);
                $estudiantes[] = $model;
            }

            return $estudiantes;
        } catch (PDOException $e) {
            error_log("Error en obtenerEstudiantesPorFacultad: " . $e->getMessage());
            return [];
        }
    }

    // OBTENER DATOS ESPECÍFICOS DE ESTUDIANTES MATRICULADOS EN AÑO ACADÉMICO CORRIENTE
    public static function obtenerDatosEspecificosEstudiantes($anioAcademico = null)
    {
        try {
            if ($anioAcademico === null) {
                $anioAcademico = date('Y');
            }

            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT e.idEstudiante, e.codigoEstudiante, e.nombre, e.apellidos, e.dipEstudiante,
                           e.correoEstudiante, e.telefono,
                           m.idMatricula, m.cursoAcademico, m.modalidadMatricula, m.totalCreditos,
                           pe.nombre as planEstudio,
                           c.nombreCarrera,
                           d.nombreDepartamento,
                           f.nombreFacultad
                    FROM estudiantes e
                    INNER JOIN matriculas m ON e.idEstudiante = m.idEstudiante
                    INNER JOIN planestudio pe ON m.idPlanEstudio = pe.idPlanEstudio
                    INNER JOIN carrera c ON pe.idCarrera = c.idCarrera
                    INNER JOIN departamento d ON c.idDepartamento = d.idDepartamento
                    INNER JOIN facultad f ON d.idFacultad = f.idFacultad
                    WHERE m.cursoAcademico = :anioAcademico AND m.estado = 'activo'
                    ORDER BY f.nombreFacultad, d.nombreDepartamento, c.nombreCarrera, e.apellidos";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':anioAcademico', $anioAcademico);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerDatosEspecificosEstudiantes: " . $e->getMessage());
            return [];
        }
    }

    // OBTENER ESTUDIANTE POR ID
    public static function obtenerEstudiantePorId($id)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT e.*, u.nombreUsuario, u.correo, u.foto, u.estado as estadoUsuario
                    FROM estudiantes e
                    LEFT JOIN usuarios u ON e.idUsuario = u.idUsuario
                    WHERE e.idEstudiante = :id";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($resultado) {
                $model = new EstudianteModel();
                return $model->hidratarDesdeArray($resultado);
            }
            
            return null;
        } catch (PDOException $e) {
            error_log("Error en obtenerEstudiantePorId: " . $e->getMessage());
            return null;
        }
    }

    // OBTENER ESTUDIANTE POR CÓDIGO
    public static function obtenerEstudiantePorCodigo($codigoEstudiante)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT * FROM estudiantes WHERE codigoEstudiante = :codigoEstudiante";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':codigoEstudiante', $codigoEstudiante);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($resultado) {
                $model = new EstudianteModel();
                return $model->hidratarDesdeArray($resultado);
            }
            
            return null;
        } catch (PDOException $e) {
            error_log("Error en obtenerEstudiantePorCodigo: " . $e->getMessage());
            return null;
        }
    }

    // OBTENER ESTUDIANTE POR ID USUARIO
    public static function obtenerEstudiantePorIdUsuario($idUsuario)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT * FROM estudiantes WHERE idUsuario = :idUsuario";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($resultado) {
                $model = new EstudianteModel();
                return $model->hidratarDesdeArray($resultado);
            }
            
            return null;
        } catch (PDOException $e) {
            error_log("Error en obtenerEstudiantePorIdUsuario: " . $e->getMessage());
            return null;
        }
    }

    // INSERTAR ESTUDIANTE
    public static function insertarEstudiante($datos)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "INSERT INTO estudiantes (
                        idUsuario, codigoEstudiante, nombre, apellidos, dipEstudiante,
                        fechaNacimiento, sexo, nacionalidad, direccion, localidad,
                        provincia, pais, telefono, correoEstudiante, centroProcedencia,
                        universidadProcedencia, esBecado
                    ) VALUES (
                        :idUsuario, :codigoEstudiante, :nombre, :apellidos, :dipEstudiante,
                        :fechaNacimiento, :sexo, :nacionalidad, :direccion, :localidad,
                        :provincia, :pais, :telefono, :correoEstudiante, :centroProcedencia,
                        :universidadProcedencia, :esBecado
                    )";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idUsuario', $datos['idUsuario'], PDO::PARAM_INT);
            $stmt->bindParam(':codigoEstudiante', $datos['codigoEstudiante']);
            $stmt->bindParam(':nombre', $datos['nombre']);
            $stmt->bindParam(':apellidos', $datos['apellidos']);
            $stmt->bindParam(':dipEstudiante', $datos['dipEstudiante']);
            $stmt->bindParam(':fechaNacimiento', $datos['fechaNacimiento']);
            $stmt->bindParam(':sexo', $datos['sexo']);
            $stmt->bindParam(':nacionalidad', $datos['nacionalidad']);
            $stmt->bindParam(':direccion', $datos['direccion']);
            $stmt->bindParam(':localidad', $datos['localidad']);
            $stmt->bindParam(':provincia', $datos['provincia']);
            $stmt->bindParam(':pais', $datos['pais']);
            $stmt->bindParam(':telefono', $datos['telefono']);
            $stmt->bindParam(':correoEstudiante', $datos['correoEstudiante']);
            $stmt->bindParam(':centroProcedencia', $datos['centroProcedencia']);
            $stmt->bindParam(':universidadProcedencia', $datos['universidadProcedencia']);
            $stmt->bindParam(':esBecado', $datos['esBecado']);
            
            if ($stmt->execute()) {
                return $instanciaConexion->lastInsertId();
            }
            
            return null;
        } catch (PDOException $e) {
            error_log("Error en insertarEstudiante: " . $e->getMessage());
            return null;
        }
    }

    // ACTUALIZAR ESTUDIANTE
    public static function actualizarEstudiante($id, $datos)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "UPDATE estudiantes SET 
                        codigoEstudiante = :codigoEstudiante,
                        nombre = :nombre,
                        apellidos = :apellidos,
                        dipEstudiante = :dipEstudiante,
                        fechaNacimiento = :fechaNacimiento,
                        sexo = :sexo,
                        nacionalidad = :nacionalidad,
                        direccion = :direccion,
                        localidad = :localidad,
                        provincia = :provincia,
                        pais = :pais,
                        telefono = :telefono,
                        correoEstudiante = :correoEstudiante,
                        centroProcedencia = :centroProcedencia,
                        universidadProcedencia = :universidadProcedencia,
                        esBecado = :esBecado
                    WHERE idEstudiante = :id";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':codigoEstudiante', $datos['codigoEstudiante']);
            $stmt->bindParam(':nombre', $datos['nombre']);
            $stmt->bindParam(':apellidos', $datos['apellidos']);
            $stmt->bindParam(':dipEstudiante', $datos['dipEstudiante']);
            $stmt->bindParam(':fechaNacimiento', $datos['fechaNacimiento']);
            $stmt->bindParam(':sexo', $datos['sexo']);
            $stmt->bindParam(':nacionalidad', $datos['nacionalidad']);
            $stmt->bindParam(':direccion', $datos['direccion']);
            $stmt->bindParam(':localidad', $datos['localidad']);
            $stmt->bindParam(':provincia', $datos['provincia']);
            $stmt->bindParam(':pais', $datos['pais']);
            $stmt->bindParam(':telefono', $datos['telefono']);
            $stmt->bindParam(':correoEstudiante', $datos['correoEstudiante']);
            $stmt->bindParam(':centroProcedencia', $datos['centroProcedencia']);
            $stmt->bindParam(':universidadProcedencia', $datos['universidadProcedencia']);
            $stmt->bindParam(':esBecado', $datos['esBecado'], PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en actualizarEstudiante: " . $e->getMessage());
            return false;
        }
    }

    // CAMBIAR ESTADO (habilitar/deshabilitar) - soft delete a través de usuario asociado
    public static function cambiarEstadoEstudiante($id, $estado)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            // Primero obtener el idUsuario del estudiante
            $estudiante = self::obtenerEstudiantePorId($id);
            if (!$estudiante || !$estudiante->idUsuario) {
                return false;
            }

            // Cambiar estado en tabla usuarios
            $sql = "UPDATE usuarios SET estado = :estado WHERE idUsuario = :idUsuario";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idUsuario', $estudiante->idUsuario, PDO::PARAM_INT);
            $stmt->bindParam(':estado', $estado);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en cambiarEstadoEstudiante: " . $e->getMessage());
            return false;
        }
    }

    // VERIFICAR SI EXISTE CÓDIGO DE ESTUDIANTE
    public static function existeCodigoEstudiante($codigoEstudiante, $excluirId = null)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT COUNT(*) as total FROM estudiantes WHERE codigoEstudiante = :codigoEstudiante";
            
            if ($excluirId !== null) {
                $sql .= " AND idEstudiante != :excluirId";
            }
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':codigoEstudiante', $codigoEstudiante);
            
            if ($excluirId !== null) {
                $stmt->bindParam(':excluirId', $excluirId, PDO::PARAM_INT);
            }
            
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['total'] > 0;
        } catch (PDOException $e) {
            error_log("Error en existeCodigoEstudiante: " . $e->getMessage());
            return false;
        }
    }

    // VERIFICAR SI EXISTE DIP DE ESTUDIANTE
    public static function existeDipEstudiante($dipEstudiante, $excluirId = null)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT COUNT(*) as total FROM estudiantes WHERE dipEstudiante = :dipEstudiante";
            
            if ($excluirId !== null) {
                $sql .= " AND idEstudiante != :excluirId";
            }
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':dipEstudiante', $dipEstudiante);
            
            if ($excluirId !== null) {
                $stmt->bindParam(':excluirId', $excluirId, PDO::PARAM_INT);
            }
            
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['total'] > 0;
        } catch (PDOException $e) {
            error_log("Error en existeDipEstudiante: " . $e->getMessage());
            return false;
        }
    }


    // OBTENER FACULTAD DEL ESTUDIANTE A TRAVÉS DE MATRÍCULA ACTIVA
    public static function obtenerFacultadEstudiante($idEstudiante)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            
            $sql = "SELECT f.idFacultad, f.nombreFacultad
                    FROM facultad f
                    INNER JOIN departamento d ON f.idFacultad = d.idFacultad
                    INNER JOIN carrera c ON d.idDepartamento = c.idDepartamento
                    INNER JOIN planestudio pe ON c.idCarrera = pe.idCarrera
                    INNER JOIN matriculas m ON pe.idPlanEstudio = m.idPlanEstudio
                    WHERE m.idEstudiante = :idEstudiante AND m.estado = 'activa'
                    LIMIT 1";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idEstudiante', $idEstudiante, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerFacultadEstudiante: " . $e->getMessage());
            return null;
        }
    }
}
?>