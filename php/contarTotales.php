<?php

/*
*		Consulta de totales visitas, registros y calculos realizados
*   para la plataforma bemtec.
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

  $result = mysql_query("SELECT idTipoAccion, count(*) AS total FROM accion GROUP BY idTipoAccion", $link);

  $totalReg = mysql_num_rows($result);

  if($totalReg > 1){

    $i=0;
     while($info = mysql_fetch_assoc($result)){
       $resultado[$i]["idTipoAccion"] = $info["idTipoAccion"];
       $resultado[$i]["total"] = $info["total"];
       $i++;
     }
     $resultado["response"] = Constantes::EXITO;
  }else{
     $resultado["total"] = $totalReg;
     $resultado["response"] = Constantes::ERROR;
  }

	echo json_encode($resultado);
?>