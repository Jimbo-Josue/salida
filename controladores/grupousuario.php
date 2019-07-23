<?php

class grupousuario
{
	public static function post($peticion)
	{
		$grupousuario = file_get_contents('php://input');
        $datos = json_decode($grupousuario);
        
        $correo = $datos->correo;
        $nombre = $datos->nombre;

        return self::agregar($correo, $nombre);
	}
	public static function get($peticion)
	{
		$grupousuario = file_get_contents('php://input');
        $datos = json_decode($grupousuario);

        $nombre = $datos->nombre;

        return self::obtenerUsuariosGrupo($nombre);		
	}
	private function agregar($correo, $nombre)
	{

	}
	private function obtenerUsuariosGrupo($nombre)
	{
		
	}
}