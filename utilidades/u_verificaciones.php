<?php

class VerificacionesUtil
{

    //FUNCION PARA VALIDAR ACCION
    public static function validarAccion($accion)
    {
        $accion = trim($accion);
        if (empty($accion)) {
            return false;
        }

        $valoresNoPermitidos = ['true', 'false', 'null', 'undefined'];
        if (in_array(strtolower($accion), $valoresNoPermitidos, true)) {
            return false;
        }

        $palabrasSwitch = ['case', 'default', 'break', 'switch'];
        if (in_array(strtolower($accion), $palabrasSwitch, true)) {
            return false;
        }

        $controlPHP = ['if', 'else', 'elseif', 'for', 'while', 'foreach', 'return', 'exit', 'die'];
        if (in_array(strtolower($accion), $controlPHP, true)) {
            return false;
        }

        return preg_match('/^[a-zA-Z][a-zA-Z0-9]*$/', $accion) === 1;
    }


    //FUNCION PARA VALIDAR TODOS LOS VALORES ALMACENADOS EN PARAMETROS
    public static function validarParametros(array $parametros)
    {
        foreach ($parametros as $valor) {
            if (!self::esValorSeguro($valor)) {
                return false;
            }
        }
        return true;
    }


    //FUNCION PARA VALIDAR SI CADA VALOR ALMACENADO EN PARAMETROS SON SEGUROS
    private static function esValorSeguro($valor)
    {
        if (is_array($valor)) {
            foreach ($valor as $item) {
                if (!self::esValorSeguro($item)) {
                    return false;
                }
            }
            return true;
        }

        $texto = (string) $valor;

        $patronesNoPermitidos = [
            '/\b(DROP|DELETE|INSERT|UPDATE|TRUNCATE|ALTER|CREATE|EXEC)\b/i',
            '/\b(SELECT\s+\*|UNION\s+SELECT)\b/i',
            '/;/',
            '/--/',
            '/\/\*.*\*\//s',
            '/\bOR\s+1=1\b/i',
            '/\bAND\s+1=1\b/i',
            "/'.*OR.*'/i",
            '/".*OR.*"/i'
        ];

        foreach ($patronesNoPermitidos as $patron) {
            if (preg_match($patron, $texto)) {
                return false;
            }
        }

        return true;
    }


    //FUNCION PARA VALIDAR CORREO
    public static function validarCorreo($correo)
    {
        return filter_var(trim($correo), FILTER_VALIDATE_EMAIL) !== false;
    }


    //FUNCION PARA VALIDAR CONTRASEÑA
    public static function verificarContrasenas($contrasena, $hash)
    {
        return password_verify($contrasena, $hash);
    }



    //FUNCION GENERAL PARA VALIDAR SESION
    public static function validarSesion($accion,$correo)
    {
        return self::validarAccion($accion) && self::validarCorreo($correo) && self::esValorSeguro($correo) ;
    }

    //FUNCION GENERAL PARA VALIDAR DISPATCH
    public static function validarDispatch($accion, $parametros)
    {
        return self::validarAccion($accion) && self::validarParametros($parametros);
    }
}
