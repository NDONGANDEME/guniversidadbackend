<?php
require_once __DIR__ . "/../../utilidades/u_conexion.php";
require_once __DIR__ . "/../modelo/m_familiar.php";

class D_Familiar
{
    // OBTENER TODOS LOS FAMILIARES DE TODOS LOS ESTUDIANTES
    public static function obtenerFamiliares()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT f.*, e.nombre as nombreEstudiante, e.apellidos as apellidosEstudiante 
                    FROM familiares f
                    LEFT JOIN estudiantes e ON f.idEstudiante = e.idEstudiante
                    ORDER BY f.apellidos ASC, f.nombre ASC";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $familiares = [];
            
            foreach ($resultados as $fila) {
                $model = new FamiliarModel();
                $model->hidratarDesdeArray($fila);
                if (isset($fila['nombreEstudiante'])) {
                    $model->nombreEstudiante = $fila['nombreEstudiante'] . ' ' . ($fila['apellidosEstudiante'] ?? '');
                }
                $familiares[] = $model;
            }

            return $familiares;
        } catch (PDOException $e) {
            error_log("Error en obtenerFamiliares: " . $e->getMessage());
            return [];
        }
    }

    // OBTENER FAMILIARES POR ESTUDIANTE
    public static function obtenerFamiliaresPorEstudiante($idEstudiante)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT * FROM familiares WHERE idEstudiante = :idEstudiante ORDER BY esResponsablePago DESC, parentesco ASC";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idEstudiante', $idEstudiante, PDO::PARAM_INT);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $familiares = [];
            
            foreach ($resultados as $fila) {
                $model = new FamiliarModel();
                $model->hidratarDesdeArray($fila);
                $familiares[] = $model;
            }

            return $familiares;
        } catch (PDOException $e) {
            error_log("Error en obtenerFamiliaresPorEstudiante: " . $e->getMessage());
            return [];
        }
    }

    // OBTENER FAMILIAR RESPONSABLE POR ESTUDIANTE
    public static function obtenerFamiliarResponsablePorEstudiante($idEstudiante)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT * FROM familiares 
                    WHERE idEstudiante = :idEstudiante AND esResponsablePago = 1 
                    LIMIT 1";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idEstudiante', $idEstudiante, PDO::PARAM_INT);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($resultado) {
                $model = new FamiliarModel();
                return $model->hidratarDesdeArray($resultado);
            }
            
            return null;
        } catch (PDOException $e) {
            error_log("Error en obtenerFamiliarResponsablePorEstudiante: " . $e->getMessage());
            return null;
        }
    }

    // OBTENER FAMILIAR POR ID
    public static function obtenerFamiliarPorId($id)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT * FROM familiares WHERE idFamiliar = :id";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($resultado) {
                $model = new FamiliarModel();
                return $model->hidratarDesdeArray($resultado);
            }
            
            return null;
        } catch (PDOException $e) {
            error_log("Error en obtenerFamiliarPorId: " . $e->getMessage());
            return null;
        }
    }

    // INSERTAR FAMILIAR
    public static function insertarFamiliar($datos)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "INSERT INTO familiares (
                        nombre, apellidos, dipFamiliar, telefono, correoFamiliar, 
                        direccion, parentesco, esContactoIncidentes, esResponsablePago, idEstudiante
                    ) VALUES (
                        :nombre, :apellidos, :dipFamiliar, :telefono, :correoFamiliar,
                        :direccion, :parentesco, :esContactoIncidentes, :esResponsablePago, :idEstudiante
                    )";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':nombre', $datos['nombre']);
            $stmt->bindParam(':apellidos', $datos['apellidos']);
            $stmt->bindParam(':dipFamiliar', $datos['dipFamiliar']);
            $stmt->bindParam(':telefono', $datos['telefono']);
            $stmt->bindParam(':correoFamiliar', $datos['correoFamiliar']);
            $stmt->bindParam(':direccion', $datos['direccion']);
            $stmt->bindParam(':parentesco', $datos['parentesco']);
            $stmt->bindParam(':esContactoIncidentes', $datos['esContactoIncidentes'], PDO::PARAM_INT);
            $stmt->bindParam(':esResponsablePago', $datos['esResponsablePago'], PDO::PARAM_INT);
            $stmt->bindParam(':idEstudiante', $datos['idEstudiante'], PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                return $instanciaConexion->lastInsertId();
            }
            
            return null;
        } catch (PDOException $e) {
            error_log("Error en insertarFamiliar: " . $e->getMessage());
            return null;
        }
    }

    // ACTUALIZAR FAMILIAR
    public static function actualizarFamiliar($id, $datos)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "UPDATE familiares SET 
                        nombre = :nombre,
                        apellidos = :apellidos,
                        dipFamiliar = :dipFamiliar,
                        telefono = :telefono,
                        correoFamiliar = :correoFamiliar,
                        direccion = :direccion,
                        parentesco = :parentesco,
                        esContactoIncidentes = :esContactoIncidentes,
                        esResponsablePago = :esResponsablePago,
                        idEstudiante = :idEstudiante
                    WHERE idFamiliar = :id";
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':nombre', $datos['nombre']);
            $stmt->bindParam(':apellidos', $datos['apellidos']);
            $stmt->bindParam(':dipFamiliar', $datos['dipFamiliar']);
            $stmt->bindParam(':telefono', $datos['telefono']);
            $stmt->bindParam(':correoFamiliar', $datos['correoFamiliar']);
            $stmt->bindParam(':direccion', $datos['direccion']);
            $stmt->bindParam(':parentesco', $datos['parentesco']);
            $stmt->bindParam(':esContactoIncidentes', $datos['esContactoIncidentes'], PDO::PARAM_INT);
            $stmt->bindParam(':esResponsablePago', $datos['esResponsablePago'], PDO::PARAM_INT);
            $stmt->bindParam(':idEstudiante', $datos['idEstudiante'], PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en actualizarFamiliar: " . $e->getMessage());
            return false;
        }
    }

    // ELIMINAR FAMILIAR
    public static function eliminarFamiliar($id)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "DELETE FROM familiares WHERE idFamiliar = :id";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en eliminarFamiliar: " . $e->getMessage());
            return false;
        }
    }

    // VERIFICAR SI EXISTE FAMILIAR POR DIP
    public static function existeFamiliarPorDip($dipFamiliar, $excluirId = null)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();

            $sql = "SELECT COUNT(*) as total FROM familiares WHERE dipFamiliar = :dipFamiliar";
            
            if ($excluirId !== null) {
                $sql .= " AND idFamiliar != :excluirId";
            }
            
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':dipFamiliar', $dipFamiliar);
            
            if ($excluirId !== null) {
                $stmt->bindParam(':excluirId', $excluirId, PDO::PARAM_INT);
            }
            
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['total'] > 0;
        } catch (PDOException $e) {
            error_log("Error en existeFamiliarPorDip: " . $e->getMessage());
            return false;
        }
    }
}
?>