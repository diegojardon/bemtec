<?php

/*
*		Alta de acciones para la plataforma bemtec.
*
* 	@author: Diego Jardón
* 	@creationDate: 02-Feb-2018
* 	@version: 1.0
*
*/

  session_start();

	require("conexion.php");
	require("constantes.php");
	require("cors.php");

	$data = json_decode(file_get_contents("php://input"));
	$idTipoAccion = mysql_real_escape_string($data->idTipoAccion);

  //Leemos el idUsuario de la sesión
  if(!isset($_SESSION['idUsuario'])){
		$idUsuario = $_SESSION['idUsuario'];
	}

  $idUsuario = "0"; //Usuario visitante

  $query = "INSERT INTO accion (`idAccion`,`idUsuario`,`idTipoAccion`,`fechaAccion`)
            VALUES (NULL, '$idUsuario', '$idTipoAccion' , NOW())";

  $result = mysql_query($query);

  if($result === TRUE){
     $resultado["response"] = Constantes::EXITO;
  }else{
    $resultado["response"] = Constantes::ERROR;
  }

	echo json_encode($resultado);
?>
