<?php

/*
*		Consulta de totales de elementos (muros, puertas y ventanas) por dirección
*   (norte, sur, este, oeste, techo) para la plataforma bemtec.
*
* 	@author: Diego Jardón
* 	@creationDate: 17-Feb-2018
* 	@version: 1.0
*
*/

  session_start();

	require("conexion.php");
	require("constantes.php");

  $tipoElemento = array('M','P','V');
  $direcciones = array('N','S','E','O','T');
  $calculo = $_SESSION['calculo'];
  
  for($i=0;$i<3;$i++){
    for($j=0;$j<5;$j++){
      $llave = $tipoElemento[$i].$direcciones[$j];
      if(!isset($_SESSION['calculo'])){
        $resultado["T".$llave] = 0;
      }else{
        $resultado["T".$llave] = count($calculo[$llave]);
      }
    }
  }

  $resultado["response"] = Constantes::EXITO;

	echo json_encode($resultado);
?>
