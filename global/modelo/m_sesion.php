<?php
class SesionModel
{
    public $correo;
    public $contrasena;


    public function __construct($correo, $contrasena)
    {
        $this->correo = $correo;
        $this->contrasena = $contrasena;
    }
}
