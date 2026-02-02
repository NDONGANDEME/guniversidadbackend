<?php

class AdminDao
{

    // FUNCIONES PARA LA GESTION DE NOTICIAS NOTICIAS
    public function insertarNoticia($datosNoticia)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "INSERT INTO Noticias (asunto, descripcion, tipo) 
                    VALUES (:asunto, :descripcion, :tipo)";
            $stmt = $instanciaConexion->prepare($sql);

            $stmt->bindParam(':asunto', $datosNoticia['asunto'], PDO::PARAM_STR);
            $stmt->bindParam(':descripcion', $datosNoticia['descripcion'], PDO::PARAM_STR);
            $stmt->bindParam(':tipo', $datosNoticia['tipo'], PDO::PARAM_STR);

            $stmt->execute();
            return $instanciaConexion->lastInsertId();
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public function actualizarNoticia($idNoticia, $datosNoticia)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "UPDATE Noticias SET asunto=:asunto, descripcion=:descripcion, tipo=:tipo 
                    WHERE idNoticia=:idNoticia";
            $stmt = $instanciaConexion->prepare($sql);

            $stmt->bindParam(':asunto', $datosNoticia['asunto'], PDO::PARAM_STR);
            $stmt->bindParam(':descripcion', $datosNoticia['descripcion'], PDO::PARAM_STR);
            $stmt->bindParam(':tipo', $datosNoticia['tipo'], PDO::PARAM_STR);
            $stmt->bindParam(':idNoticia', $idNoticia, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public function eliminarNoticia($idNoticia)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "DELETE FROM Noticias WHERE idNoticia=:idNoticia";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idNoticia', $idNoticia, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public function obtenerNoticias($tipo = null)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT * FROM Noticias";
            if ($tipo) {
                $sql .= " WHERE tipo = :tipo";
            }
            $sql .= " ORDER BY idNoticia DESC";

            $stmt = $instanciaConexion->prepare($sql);
            if ($tipo) {
                $stmt->bindParam(':tipo', $tipo, PDO::PARAM_STR);
            }
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public function buscarNoticias($criterio)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT * FROM Noticias 
                    WHERE asunto LIKE :criterio OR descripcion LIKE :criterio OR tipo LIKE :criterio
                    ORDER BY idNoticia DESC";
            $stmt = $instanciaConexion->prepare($sql);
            $likeCriterio = "%" . $criterio . "%";
            $stmt->bindParam(':criterio', $likeCriterio, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public function cargarTiposNoticiaParaSelect()
    {
        $tipos = [
            ['value' => 'comunicado', 'label' => 'Comunicado (Público en general)'],
            ['value' => 'interna', 'label' => 'Interna (Miembros del departamento y estudiantes)'],
            ['value' => 'departamento', 'label' => 'Departamento (Miembros del departamento)']
        ];
        return $tipos;
    }

    public function insertarFotoNoticia($idNoticia, $url)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "INSERT INTO Foto (url, idNoticia) VALUES (:url, :idNoticia)";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':url', $url, PDO::PARAM_STR);
            $stmt->bindParam(':idNoticia', $idNoticia, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    // FUNCIONES PARA LA GESTIÓN DE LA INFORMACIÓN ESTÁTICA
    public function obtenerInformacionEstatica()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT * FROM Static LIMIT 1";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public function actualizarInformacionEstatica($datosStatic)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "UPDATE Static SET inicio=:inicio, sobreNosotros=:sobreNosotros, urlLogo=:urlLogo, 
                    quienesSomos=:quienesSomos WHERE idStatic=1";
            $stmt = $instanciaConexion->prepare($sql);

            $stmt->bindParam(':inicio', $datosStatic['inicio'], PDO::PARAM_STR);
            $stmt->bindParam(':sobreNosotros', $datosStatic['sobreNosotros'], PDO::PARAM_STR);
            $stmt->bindParam(':urlLogo', $datosStatic['urlLogo'], PDO::PARAM_STR);
            $stmt->bindParam(':quienesSomos', $datosStatic['quienesSomos'], PDO::PARAM_STR);

            return $stmt->execute();
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public function insertarContacto($datosContacto)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "INSERT INTO Contactos (contacto, tipo, idStatic) 
                    VALUES (:contacto, :tipo, :idStatic)";
            $stmt = $instanciaConexion->prepare($sql);

            $stmt->bindParam(':contacto', $datosContacto['contacto'], PDO::PARAM_STR);
            $stmt->bindParam(':tipo', $datosContacto['tipo'], PDO::PARAM_STR);
            $stmt->bindParam(':idStatic', $datosContacto['idStatic'], PDO::PARAM_INT);

            $stmt->execute();
            return $instanciaConexion->lastInsertId();
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public function obtenerContactos()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT * FROM Contactos ORDER BY tipo, contacto";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public function buscarContactos($criterio)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT * FROM Contactos 
                    WHERE contacto LIKE :criterio OR tipo LIKE :criterio
                    ORDER BY tipo, contacto";
            $stmt = $instanciaConexion->prepare($sql);
            $likeCriterio = "%" . $criterio . "%";
            $stmt->bindParam(':criterio', $likeCriterio, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    // FUNCIONES PARA LA GESTIÓN DE USUARIOS
    public static function insertarUsuario($datosUsuario)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "INSERT INTO Usuario (login, password, rol, estado) VALUES (:login, :password, :rol, :estado)";
            $stmt = $instanciaConexion->prepare($sql);

            $stmt->bindParam(':login', $datosUsuario['login'], PDO::PARAM_STR);
            $stmt->bindParam(':password', $datosUsuario['password'], PDO::PARAM_STR);
            $stmt->bindParam(':rol', $datosUsuario['rol'], PDO::PARAM_STR);
            $stmt->bindParam(':estado', $datosUsuario['estado'], PDO::PARAM_STR);

            $stmt->execute();
            return $instanciaConexion->lastInsertId();
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function actualizarUsuario($idUsuario, $datosUsuario)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "UPDATE Usuario SET login=:login, password=:password, rol=:rol, estado=:estado WHERE idUsuario=:idUsuario";
            $stmt = $instanciaConexion->prepare($sql);

            $stmt->bindParam(':login', $datosUsuario['login'], PDO::PARAM_STR);
            $stmt->bindParam(':password', $datosUsuario['password'], PDO::PARAM_STR);
            $stmt->bindParam(':rol', $datosUsuario['rol'], PDO::PARAM_STR);
            $stmt->bindParam(':estado', $datosUsuario['estado'], PDO::PARAM_STR);
            $stmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function eliminarUsuario($idUsuario)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "UPDATE Usuario SET estado='inactivo' WHERE idUsuario=:idUsuario";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function obtenerUsuarios()
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT * FROM Usuario WHERE estado='activo'";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function obtenerUsuarioPorId($idUsuario)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT * FROM Usuario WHERE idUsuario=:idUsuario";
            $stmt = $instanciaConexion->prepare($sql);
            $stmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }

    public static function buscarUsuarios($criterio)
    {
        try {
            $instanciaConexion = ConexionUtil::conectar();
            $sql = "SELECT * FROM Usuario 
                    WHERE login LIKE :criterio OR rol LIKE :criterio 
                    ORDER BY login";
            $stmt = $instanciaConexion->prepare($sql);
            $likeCriterio = "%" . $criterio . "%";
            $stmt->bindParam(':criterio', $likeCriterio, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            return $th->getMessage();
        }
    }
}
