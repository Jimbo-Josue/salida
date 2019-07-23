<?php

class grupo
{
	public static function post($peticion)
	{
		$grupobody = file_get_contents('php://input');
        $grupo = json_decode($grupobody);
        $nombre = $grupo->nombre;
        return self::crear($nombre);
	}
	
	public static function get($peticion)
	{
		return self::obtenerGrupos();
	}

	private function obtenerGrupos()
	{
		$idUsuario = usuarios::autorizar();
		$comando = "SELECT nombre, tipo FROM grupo WHERE idUsuario=?";
        $sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($comando);
        $sentencia->bindParam(1, $idUsuario);
        $sentencia->execute();

        http_response_code(200);

        return [
        	"estado" => 1,
        	"grupos" => $sentencia->fetchAll(PDO::FETCH_ASSOC) 
        ];
	}

	private function crear($nombre)
	{
		$idUsuario = usuarios::autorizar();
		
		if(self::comprobarRepeticion($idUsuario, $nombre))
		{
			http_response_code(400);
			return [
				"estado" => 2,
				"mensaje" => "Ya exite un grupo con el mismo nombre"
			];
		}

        try {

            $pdo = ConexionBD::obtenerInstancia()->obtenerBD();
            // Sentencia INSERT
            $comando = "INSERT INTO grupo (idUsuario, nombre, tipo) VALUES (?,?,1)";

            $sentencia = $pdo->prepare($comando);

            $sentencia->bindParam(1, $idUsuario);
            $sentencia->bindParam(2, $nombre);
               
            $resultado = $sentencia->execute();
            http_response_code(200);

            if ($resultado) {
                return [
                	"estado" => 1,
                	"mensaje" => "Grupo Creado"
                ];

            } else {
                http_response_code(400);
                return [
                	"estado" => 2,
                	"mensaje"  => "Error Desconocido"
                ];
            }
        } catch (PDOException $e) {
            throw new ExcepcionApi(self::ESTADO_ERROR_BD, $e->getMessage());
        }

	}

	private function comprobarRepeticion($idUsuario, $nombre)
	{
        $comando = "SELECT COUNT(*) FROM grupo WHERE idUsuario=? AND nombre=?";

        $sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($comando);

        $sentencia->bindParam(1, $idUsuario);
        $sentencia->bindParam(2, $nombre);

        $sentencia->execute();

        return $sentencia->fetchColumn(0) > 0;
	}
}
