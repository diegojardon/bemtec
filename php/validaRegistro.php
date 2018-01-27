<?php

/*
*		Validacion de registros de usuario para la plataforma bemtec.
*
* 	@author: Diego Jardón
* 	@creationDate: 27-Jan-2018
* 	@version: 1.0
*
*/

	require("conexion.php");
  require("utils.php");

  $llave = $_GET["id"];
  $llave = str_replace(' ', '+', $llave);

  //echo "LLAVE: " . $llave . "<br/>";

	$query = "SELECT * FROM usuarioKey WHERE keyUsuarioKey='".$llave."'";
	$result = mysql_query($query,$link);
	$totalFilas = mysql_num_rows($result);
  $info = mysql_fetch_assoc($result);

  //$usuarioUsuario = desencriptar($info["keyUsuarioKey"]);

  //echo "TOTAL DE FILAS: " . $totalFilas. "<br/>";
  //echo "NOMBRE DE USUARIO DESENCRIPTADO: " . $usuarioUsuario;

  if($totalFilas > 0){

    $usuarioUsuario = desencriptar($info["keyUsuarioKey"]);

    //Actualizamos el estatus del usuario

		$query = "UPDATE usuario SET estatusUsuario = 1 WHERE usuarioUsuario = '$usuarioUsuario'";

		$result = mysql_query($query);

		if($result === TRUE){
      //Enviamos a pantalla indicando el exito en la confirmacion del registro

		}else{
      //Enviamos a pantalla de error

		}

	}else{
		//Enviamos a pantalla de error

	}

?>
