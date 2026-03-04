<?php
require_once __DIR__ . "/../../utilidades/u_conexion.php";
require_once __DIR__ . "/../modelo/m_profesor.php";

class D_Profesor
{
    // OBTENER TODOS LOS PROFESORES
    public static function obtenerProfesores()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT p.*, d.nombreDepartamento, u.nombreUsuario, u.correo, u.foto, u.estado
                    FROM profesor p
                    LEFT JOIN departamento d ON p.idDepartamento = d.idDepartamento
                    LEFT JOIN usuarios u ON p.idUsuario = u.idUsuario
                    ORDER BY p.apellidosProfesor ASC, p.nombreProfesor ASC";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $profesores = [];
            
            foreach ($resultados as $fila) {
                $model = new ProfesorModel();
                $model->hidratarDesdeArray($fila);
                if (isset($fila['nombreDepartamento'])) {
                    $model->nombreDepartamento = $fila['nombreDepartamento'];
                }
                $profesores[] = $model;
            }

            return $profesores;
        } catch (PDOException $e) {
            error_log("Error en obtenerProfesores: " . $e->getMessage());
            return [];
        }
    }

    // OBTENER PROFESORES POR FACULTAD
    public static function obtenerProfesoresPorFacultad($idFacultad)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT p.*, d.nombreDepartamento
                    FROM profesor p
                    INNER JOIN departamento d ON p.idDepartamento = d.idDepartamento
                    WHERE d.idFacultad = :idFacultad
                    ORDER BY p.apellidosProfesor ASC, p.nombreProfesor ASC";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idFacultad', $idFacultad, PDO::PARAM_INT);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $profesores = [];
            
            foreach ($resultados as $fila) {
                $model = new ProfesorModel();
                $model->hidratarDesdeArray($fila);
                if (isset($fila['nombreDepartamento'])) {
                    $model->nombreDepartamento = $fila['nombreDepartamento'];
                }
                $profesores[] = $model;
            }

            return $profesores;
        } catch (PDOException $e) {
            error_log("Error en obtenerProfesoresPorFacultad: " . $e->getMessage());
            return [];
        }
    }

    // OBTENER PROFESORES POR DEPARTAMENTO
    public static function obtenerProfesoresPorDepartamento($idDepartamento)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT * FROM profesor 
                    WHERE idDepartamento = :idDepartamento
                    ORDER BY apellidosProfesor ASC, nombreProfesor ASC";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idDepartamento', $idDepartamento, PDO::PARAM_INT);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $profesores = [];
            
            foreach ($resultados as $fila) {
                $model = new ProfesorModel();
                $model->hidratarDesdeArray($fila);
                $profesores[] = $model;
            }

            return $profesores;
        } catch (PDOException $e) {
            error_log("Error en obtenerProfesoresPorDepartamento: " . $e->getMessage());
            return [];
        }
    }

    // OBTENER PROFESOR POR ID
    public static function obtenerProfesorPorId($id)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT p.*, d.nombreDepartamento, u.nombreUsuario, u.correo, u.foto
                    FROM profesor p
                    LEFT JOIN departamento d ON p.idDepartamento = d.idDepartamento
                    LEFT JOIN usuarios u ON p.idUsuario = u.idUsuario
                    WHERE p.idProfesor = :id";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($resultado) {
                $model = new ProfesorModel();
                return $model->hidratarDesdeArray($resultado);
            }
            
            return null;
        } catch (PDOException $e) {
            error_log("Error en obtenerProfesorPorId: " . $e->getMessage());
            return null;
        }
    }

    // INSERTAR PROFESOR
    public static function insertarProfesor($datos)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "INSERT INTO profesor (
                        nombreProfesor, apellidosProfesor, dipProfesor, especialidad,
                        gradoEstudio, idDepartamento, idUsuario, genero, nacionalidad,
                        responsabilidad, correoProfesor, contactoProfesor
                    ) VALUES (
                        :nombreProfesor, :apellidosProfesor, :dipProfesor, :especialidad,
                        :gradoEstudio, :idDepartamento, :idUsuario, :genero, :nacionalidad,
                        :responsabilidad, :correoProfesor, :contactoProfesor
                    )";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':nombreProfesor', $datos['nombreProfesor']);
            $stmt->bindParam(':apellidosProfesor', $datos['apellidosProfesor']);
            $stmt->bindParam(':dipProfesor', $datos['dipProfesor']);
            $stmt->bindParam(':especialidad', $datos['especialidad']);
            $stmt->bindParam(':gradoEstudio', $datos['gradoEstudio']);
            $stmt->bindParam(':idDepartamento', $datos['idDepartamento'], PDO::PARAM_INT);
            $stmt->bindParam(':idUsuario', $datos['idUsuario'], PDO::PARAM_INT);
            $stmt->bindParam(':genero', $datos['genero']);
            $stmt->bindParam(':nacionalidad', $datos['nacionalidad']);
            $stmt->bindParam(':responsabilidad', $datos['responsabilidad']);
            $stmt->bindParam(':correoProfesor', $datos['correoProfesor']);
            $stmt->bindParam(':contactoProfesor', $datos['contactoProfesor']);
            
            if ($stmt->execute()) {
                return $instanciaConexion->lastInsertId();
            }
            
            return null;
        } catch (PDOException $e) {
            error_log("Error en insertarProfesor: " . $e->getMessage());
            return null;
        }
    }

    // ACTUALIZAR PROFESOR
    public static function actualizarProfesor($id, $datos)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "UPDATE profesor SET 
                        nombreProfesor = :nombreProfesor,
                        apellidosProfesor = :apellidosProfesor,
                        dipProfesor = :dipProfesor,
                        especialidad = :especialidad,
                        gradoEstudio = :gradoEstudio,
                        idDepartamento = :idDepartamento,
                        genero = :genero,
                        nacionalidad = :nacionalidad,
                        responsabilidad = :responsabilidad,
                        correoProfesor = :correoProfesor,
                        contactoProfesor = :contactoProfesor
                    WHERE idProfesor = :id";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':nombreProfesor', $datos['nombreProfesor']);
            $stmt->bindParam(':apellidosProfesor', $datos['apellidosProfesor']);
            $stmt->bindParam(':dipProfesor', $datos['dipProfesor']);
            $stmt->bindParam(':especialidad', $datos['especialidad']);
            $stmt->bindParam(':gradoEstudio', $datos['gradoEstudio']);
            $stmt->bindParam(':idDepartamento', $datos['idDepartamento'], PDO::PARAM_INT);
            $stmt->bindParam(':genero', $datos['genero']);
            $stmt->bindParam(':nacionalidad', $datos['nacionalidad']);
            $stmt->bindParam(':responsabilidad', $datos['responsabilidad']);
            $stmt->bindParam(':correoProfesor', $datos['correoProfesor']);
            $stmt->bindParam(':contactoProfesor', $datos['contactoProfesor']);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en actualizarProfesor: " . $e->getMessage());
            return false;
        }
    }

    // VERIFICAR SI EXISTE PROFESOR POR DIP
    public static function existeProfesorPorDip($dipProfesor, $excluirId = null)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT COUNT(*) as total FROM profesor WHERE dipProfesor = :dipProfesor";
            
            if ($excluirId !== null) {
                $sql .= " AND idProfesor != :excluirId";
            }
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':dipProfesor', $dipProfesor);
            
            if ($excluirId !== null) {
                $stmt->bindParam(':excluirId', $excluirId, PDO::PARAM_INT);
            }
            
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['total'] > 0;
        } catch (PDOException $e) {
            error_log("Error en existeProfesorPorDip: " . $e->getMessage());
            return false;
        }
    }

    // VERIFICAR SI EXISTE PROFESOR POR CORREO
    public static function existeProfesorPorCorreo($correoProfesor, $excluirId = null)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT COUNT(*) as total FROM profesor WHERE correoProfesor = :correoProfesor";
            
            if ($excluirId !== null) {
                $sql .= " AND idProfesor != :excluirId";
            }
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':correoProfesor', $correoProfesor);
            
            if ($excluirId !== null) {
                $stmt->bindParam(':excluirId', $excluirId, PDO::PARAM_INT);
            }
            
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['total'] > 0;
        } catch (PDOException $e) {
            error_log("Error en existeProfesorPorCorreo: " . $e->getMessage());
            return false;
        }
    }
    
    // Añadir este método al final de la clase D_Profesor en d_profesor.php

    // OBTENER PROFESOR POR ID DE USUARIO
    public static function obtenerProfesorPorIdUsuario($idUsuario)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT p.*, d.nombreDepartamento 
                    FROM profesor p
                    LEFT JOIN departamento d ON p.idDepartamento = d.idDepartamento
                    WHERE p.idUsuario = :idUsuario";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($resultado) {
                $model = new ProfesorModel();
                return $model->hidratarDesdeArray($resultado);
            }
            
            return null;
        } catch (PDOException $e) {
            error_log("Error en obtenerProfesorPorIdUsuario: " . $e->getMessage());
            return null;
        }
    }

}
?>