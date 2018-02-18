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
  require("cors.php");

  $data = json_decode(file_get_contents("php://input"));
  $direccionElemento = mysql_real_escape_string($data->direccionElemento);
  $accion = mysql_real_escape_string($data->accion);

  $tipoElemento = array('M','P','V');
  $direcciones = array('N','S','E','O','T');

  if(!isset($_SESSION['elementos'])){
    $resultado["response"] = Constantes::ERROR_NO_HAY_ELEMENTOS;
  }else{
    $totalElementos = $_SESSION['elementos'];
    if(!isset($_SESSION['controlElementos'])){
      $controlElementos = array();

      //Se inicializa el arreglo con los contadores en 1
      for($i=0;$i<3;$i++){
        for($j=0;$j<5;$j++){
          $llave = $tipoElemento[$i].$direcciones[$j];
          $controlElementos[$llave] = 1;
        }
      }

    }else{
      $controlElementos = $_SESSION['controlElementos'];
    }

    for($i=0;$i<3;$i++){
      $llave = $tipoElemento[$i].$direccionElemento;
      if($controlElementos[$llave] <= $totalElementos[$llave]){
        $resultado["elementoSiguiente"] = $llave.$controlElementos[$llave];
        if(strcmp($accion, "GUARDAR") == 0)
          $controlElementos[$llave] += 1;
        break;
      }
    }

    if($i == 3){
      //Todos los elementos han sido configurados, ya que ninguno entro al ´break´
      $resultado["elementoSiguiente"] = 'XX0';
    }

    $_SESSION['controlElementos'] = $controlElementos;
  }

  $resultado["response"] = Constantes::EXITO;

	echo json_encode($resultado);
?>
