<?php
class NoticiaModel
{
    public $idNoticia;
    public $asunto;
    public $descripcion;
    public $tipo;
    public $fechaPublicacion;
    public $fotos = []; // Para almacenar las fotos asociadas

    public function __construct($asunto = null, $descripcion = null, $tipo = null)
    {
        $this->asunto = $asunto;
        $this->descripcion = $descripcion;
        $this->tipo = $tipo;
    }

    // Hidratar modelo desde array de base de datos
    public function hidratarDesdeArray($data)
    {
        if (isset($data['idNoticia'])) $this->idNoticia = $data['idNoticia'];
        if (isset($data['asunto'])) $this->asunto = $data['asunto'];
        if (isset($data['descripcion'])) $this->descripcion = $data['descripcion'];
        if (isset($data['tipo'])) $this->tipo = $data['tipo'];
        if (isset($data['fechaPublicacion'])) $this->fechaPublicacion = $data['fechaPublicacion'];
        
        return $this;
    }

    // Convertir modelo a array
    public function convertirAArray($incluirFotos = true)
    {
        $data = [
            'idNoticia' => $this->idNoticia,
            'asunto' => $this->asunto,
            'descripcion' => $this->descripcion,
            'tipo' => $this->tipo,
            'fechaPublicacion' => $this->fechaPublicacion
        ];

        if ($incluirFotos && !empty($this->fotos)) {
            $data['fotos'] = $this->fotos;
        }

        return $data;
    }

    // Establecer fotos asociadas
    public function establecerFotos($fotos)
    {
        $this->fotos = $fotos;
        return $this;
    }

    // Validar si la noticia tiene fotos
    public function tieneFotos()
    {
        return !empty($this->fotos);
    }

    // Obtener la primera foto (para vista previa)
    public function obtenerPrimeraFoto()
    {
        return !empty($this->fotos) ? $this->fotos[0] : null;
    }

    // Obtener la URL de la primera foto
    public function obtenerUrlPrimeraFoto()
    {
        $primeraFoto = $this->obtenerPrimeraFoto();
        return $primeraFoto ? $primeraFoto['url'] : null;
    }
}
?>