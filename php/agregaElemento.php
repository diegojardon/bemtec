<?php

/*
*		Servicio REST para agregar elementos al cálculo en la sesión actual.
*
* 	@author: Diego Jardón
* 	@creationDate: 17-Feb-2018
* 	@version: 1.0
*
*/

	session_start();

  require("conexion.php");
	require("constantes.php");
	require("cors.php");

	if(!isset($_SESSION['idCalculo']))
		$idCalculo = 0;
	else
		$idCalculo = $_SESSION['idCalculo'];

	$data = json_decode(file_get_contents("php://input"));
	$nombreElemento = mysql_real_escape_string($data->nombreElemento);
	$tipoElemento = mysql_real_escape_string($data->tipoElemento);
	$direccionElemento = mysql_real_escape_string($data->direccionElemento);
	$esHomogeneoElemento = mysql_real_escape_string($data->esHomogeneoElemento);
	$areaElemento = mysql_real_escape_string($data->areaElemento);

	//Se inserta el elemento en la BD

	$query = "INSERT INTO elemento (`idElemento`,`nombreElemento`,`tipoElemento`,`direccionElemento`,`idCalculo`, `esHomogeneoElemento`,`areaElemento`)
            VALUES (NULL, '$nombreElemento', '$tipoElemento' , '$direccionElemento', '$idCalculo', '$esHomogeneoElemento', '$areaElemento')";

	$result = mysql_query($query);

	$_SESSION['idElemento'] = mysql_insert_id();

  if($result === TRUE){
 		$resultado["response"] = Constantes::EXITO;
		$resultado["esHomogeneo"] = $esHomogeneoElemento;

		$_SESSION["esHomogeneo"] = $esHomogeneoElemento;
		$_SESSION["nombreElemento"] = $nombreElemento;
		$_SESSION["direccionElemento"] = $direccionElemento;
		$_SESSION["areaElemento"] = $areaElemento;
	}else{
		 $resultado["response"] = Constantes::ERROR;
	}

	echo json_encode($resultado);
?>
