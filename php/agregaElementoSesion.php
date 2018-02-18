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

	$data = json_decode(file_get_contents("php://input"));
	$tipoElemento = mysql_real_escape_string($data->tipoElemento);
	$direccionElemento = mysql_real_escape_string($data->direccionElemento);
	$totalElementos = mysql_real_escape_string($data->totalElementos);

	//Se agrega el elemento con su configuración al array

	if(!isset($_SESSION['calculo'])){
		$calculo = array();
		$calculo[] = array();
	}else{
		$calculo = $_SESSION['calculo'];
	}

	$llave = $tipoElemento . $direccionElemento;
	$totalInicial = count($calculo[$llave]);

	for($i = $totalInicial;$i< ($totalInicial + intval($totalElementos)); $i++){
		$calculo[$llave][$i] = array();
	}

	$_SESSION['calculo']=$calculo;

	$resultado["response"] = count($calculo[$llave]);

	echo json_encode($resultado);
?>
