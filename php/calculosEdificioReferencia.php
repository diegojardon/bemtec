<?php

/*
*		Cálculo para Edificio de Referencia (calor y radiación).
*
* 	@author: Diego Jardón
* 	@creationDate: 2-Junio-2018
* 	@version: 1.0
*
*/

  session_start();

  require("conexion.php");
  require("constantes.php");
  require("cors.php");
  
  $data = json_decode(file_get_contents("php://input"));

  //Se valida que exista y se obtiene el idCalculo almacenado en la sesión

  if(isset($_SESSION['idCalculo'])){

    $idCalculo = $_SESSION['idCalculo'];
 
    //Se obtiene el número de niveles, la latitud y la norma

    $resultCalculo = mysql_query("SELECT numNivelesEdificio, latitud, normaCalculo FROM calculo WHERE idCalculo = '".$idCalculo."'",$link);
    if($resultCalculo === FALSE){
			$resultado["response"] = Constantes::ERROR;
    }else{

      $totalUsu = mysql_num_rows($resultCalculo);
      if($totalUsu > 0){
        $i=0;
        while($info = mysql_fetch_assoc($resultCalculo)){
            $numNivelesEdificio = $info["numNivelesEdificio"];
            $latitud = $info["latitud"];
            $norma = $info["normaCalculo"];
            $i++;
        }

        //Obtenemos la suma de áreas para los elementos por orientación

        $resultAreaTotal = mysql_query("SELECT direccionElemento, sum(areaElemento) AS areaTotal FROM elemento WHERE idCalculo = '".$idCalculo."' GROUP BY direccionElemento",$link);
        if($resultAreaTotal === FALSE){
          $resultado["response"] = Constantes::ERROR;
        }else{
    
          $totalUsu = mysql_num_rows($resultAreaTotal);
          if($totalUsu > 0){
            $i=0;
            while($info = mysql_fetch_assoc($resultAreaTotal)){
                $resultado[$i]["direccionElemento"] = $info["direccionElemento"];
                $resultado[$i]["areaTotal"] = $info["areaTotal"];
                $i++;
            }

            $resultadosCalculos = array(0.0, 0.0, 0.0, 0.0);

            //Se realizan los cálculos correspondientes
            for($j=0; $j<6; $j++){
              if($resultado[$j]["direccionElemento"] == "Norte"){
                $resultadosCalculos = calculoCalorYRadiacion("Norte", $norma, $numNivelesEdificio, $latitud, $resultado[$j]["areaTotal"], $resultadosCalculos);
                continue;
              }
              if($resultado[$j]["direccionElemento"] == "Sur"){
                $resultadosCalculos = calculoCalorYRadiacion("Sur", $norma, $numNivelesEdificio, $latitud, $resultado[$j]["areaTotal"], $resultadosCalculos);
                continue;
              }
              if(($resultado[$j]["direccionElemento"] == "Este"){
                $resultadosCalculos = calculoCalorYRadiacion("Este", $norma, $numNivelesEdificio, $latitud, $resultado[$j]["areaTotal"], $resultadosCalculos);
                continue;
              }
              if(($resultado[$j]["direccionElemento"] == "Oeste"){
                $resultadosCalculos = calculoCalorYRadiacion("Oeste", $norma, $numNivelesEdificio, $latitud, $resultado[$j]["areaTotal"], $resultadosCalculos);
                continue;
              }
              if(($resultado[$j]["direccionElemento"] == "Techo"){
                $resultadosCalculos = calculoCalorYRadiacion("Techo", $norma, $numNivelesEdificio, $latitud, $resultado[$j]["areaTotal"], $resultadosCalculos);
                continue;
              }
              if(($resultado[$j]["direccionElemento"] == "TechoI"){
                $resultadosCalculos = calculoCalorYRadiacion("TechoI", $norma, $numNivelesEdificio, $latitud, $resultado[$j]["areaTotal"], $resultadosCalculos);
                continue;
              }
            }

            //Se codifica la respuesta
            $resultado["response"] = Constantes::EXITO;
            $resultado["gananciaCalorRef"] = $resultadosCalculos[0];
            $resultado["gananciaCalorProy"] = $resultadosCalculos[1];
            $resultado["gananciaRadiacionRef"] = $resultadosCalculos[2];
            $resultado["gananciaRadiacionProy"] = $resultadosCalculos[3];

          }else{
            $resultado["response"] = Constantes::ERROR;
          }
        }    

      }else{
        $resultado["response"] = Constantes::ERROR;
      }
    }

    echo json_encode($resultado);

  }


  /**
   * @param orientacion -- Indica el tipo de direccion. Puede tomar los valores 
   * Norte, Sur, Este, Oeste, Techo y TechoI
   * @param norma -- Indica la norma, puede tomar los valores 2001 o 2011
   * @param numNivelesEdificio -- Indica el numero de niveles del edificio de referencia
   * @param latitud -- Indica latitud del edificio
   * @param areaTotalOrientacion -- Indica la suma del área total por la orientación definida en 
   * el parámetro orientación
   */
  function calculoCalorYRadiacion($orientacion, $norma, $numNivelesEdificio, $latitud, $areaTotalOrientacion, $resultadosCalculos){

    $result = mysql_query("SELECT tipoElemento, kTotal, esMasivoElemento, tipoSombra, coeficienteSombra, LVoladoMas, 
                           HVoladoMas, AVoladoMas, LVoladoLimite, HVoladoLimite, WVoladoLimite, AVoladoLimite, ERemetida,
                           PRemetida, WRemetida, LParteluces, WParteluces FROM elemento WHERE idCalculo = '".$idCalculo."' AND direccionElemento = '".$orientacion."'",$link);

    $totalUsu = mysql_num_rows($result);
      if($totalUsu > 0){
        $i=0;
        while($info = mysql_fetch_assoc($result)){
            $tipoElemento = $info["tipoElemento"];
            $kTotal = $info["kTotal"];
            $masivoLigero = $info["esMasivoElemento"];
            $tipoSombra = $info["tipoSombra"];
            $cs = $info["coeficienteSombra"];
            $lVoladoMas = $info["LVoladoMas"];
            $hVoladoMas = $info["HVoladoMas"];
            $aVoladoMas = $info["AVoladoMas"];
            $lVoladoLimite = $info["LVoladoLimite"];
            $hVoladoLimite = $info["HVoladoLimite"];
            $wVoladoLimite = $info["WVoladoLimite"];
            $aVoladoLimite = $info["AVoladoLimite"];
            $eRemetida = $info["ERemetida"];
            $pRemetida = $info["PRemetida"];
            $wRemetida = $info["WRemetida"];
            $lParteluces = $info["LParteluces"];
            $wParteluces = $info["WParteluces"];

            if($tipoElemento == "Ventana"){
              $fraccionComponente = 0.9;
            }else{
              if($tipoElemento == "Tragaluz"){
                $fraccionComponente = 0;
              }else{
                $fraccionComponente = 1.0;
              }
            }

            //De acuerdo al tipo de sombra, sacamos los datos del factor1 y factor2
            if($tipoElemento == "Ventana"){
              $se = 0;
              $query = "";

              if($norma == "2011"){

                if($tipoSombreado == "1"){
                  $se = 1;
                }
                if($tipoSombreado == "2"){
                  //Ocupar dirección, latitud y factor1 (L/H)
                  $columna = "";
                  $factor1 = (float)$lVoladoMas / (float)$hVoladoMas;

                  if($orientacion == "Norte")
                    $columna = "norte";
                  if($orientacion == "Este" || $orientacion == "Oeste")
                    $columna = "esteOeste";
                  if($orientacion == "Sur")
                    $columna = "sur";

                  $latitudNum = (float)$latitud;  
                  if($latitudNum <= 33.0 && $latitudNum >= 23.0)
                    $columna .= "23a33";
                  if($latitudNum < 23.0 && $latitudNum >= 14.0)
                    $columna .= "14a23";  

                  $query = "SELECT " . $columna . " FROM tabla_2_2011 WHERE lH >= " . $factor1 . " ORDER BY lH ASC";

                  $resultSombreado = mysql_query($query,$link);
                  if($resultSombreado === FALSE){
                    $resultado["response"] = Constantes::ERROR;
                  }else{
                    $totalUsu = mysql_num_rows($resultSombreado);
                    if($totalUsu > 0){
                      while($info = mysql_fetch_assoc($resultSombreado)){
                          $se = $info[$columna];
                          break;
                      }
                    }else{
                      $resultado["response"] = Constantes::ERROR;
                    }
                  }    
                }else{
                  if($tipoSombreado == "3"){
                    //Ocupar dirección, latitud, factor1 (L/H) y factor2 (W/H)
                    $tabla = "tabla_3_2011_";
                    $columna = "wH_";
                    $factor1 = (float)$lVoladoLimite / (float)$hVoladoLimite;
                    $factor2 = (float)$wVoladoLimite / (float)$hVoladoLimite;

                    if($orientacion == "Norte")
                      $tabla .= "Norte_";
                    if($orientacion == "Este" || $orientacion == "Oeste")
                      $tabla .= "EsteOeste_";
                    if($orientacion == "Sur")
                      $tabla .= "Sur_";

                    $latitudNum = (float)$latitud; 
                    if($latitudNum >= 14.0 && $latitudNum <= 19.0)
                      $tabla .= "1";
                    if($latitudNum > 19.0 & $latitudNum <= 23.0)
                      $tabla .= "2";
                    if($latitudNum > 23.0 && $latitudNum <= 28.0)
                      $tabla .= "3";
                    if($latitudNum > 28.0 && $latitudNum <= 32.0)
                      $tabla .= "4";

                    $factor2 = round($factor2);
                    
                    if($factor2 <= 0.5)
                      $columna .= "0.5";
                    if($factor2 > 0.5 && $factor2 <= 1.0)
                      $columna .= "1";
                    if($factor2 > 1.0 && $factor2 <= 2.0)
                      $columna .= "2";
                    if($factor2 > 2.0 && $factor2 <= 4.0)
                      $columna .= "4";  
                    if($factor2 > 4.0 && $factor2 <= 6.0)
                      $columna .= "6";
                    if($factor2 > 6.0)
                      $columna .= "8";

                    $query = "SELECT " . $columna . " FROM " . $tabla . " WHERE lH >= ". $factor1 . " ORDER BY lH ASC";

                    $resultSombreado = mysql_query($query,$link);
                    if($resultSombreado === FALSE){
                      $resultado["response"] = Constantes::ERROR;
                    }else{
                      $totalUsu = mysql_num_rows($resultSombreado);
                      if($totalUsu > 0){
                        while($info = mysql_fetch_assoc($resultSombreado)){
                            $se = $info[$columna];
                            break;
                        }
                      }else{
                        $resultado["response"] = Constantes::ERROR;
                      }
                    }   
                  }else{
                    if($tipoSombreado == "4"){
                      //Ocupar dirección, latitud, factor1 (P/E) y factor2 (W/E)
                      $tabla = "tabla_4_2011_";
                      $columna = "wE_";
                      $factor1 = (float)$pRemetida / (float)$eRemetida;
                      $factor2 = (float)$wRemetida / (float)$eRemetida;

                      if($orientacion == "Norte")
                        $tabla .= "Norte_";
                      if($orientacion == "Este" || $orientacion == "Oeste")
                        $tabla .= "EsteOeste_";
                      if($orientacion == "Sur")
                        $tabla .= "Sur_";

                      $latitudNum = (float)$latitud; 
                      if($latitudNum >= 14.0 && $latitudNum <= 19.0)
                        $tabla .= "1";
                      if($latitudNum > 19.0 & $latitudNum <= 23.0)
                        $tabla .= "2";
                      if($latitudNum > 23.0 && $latitudNum <= 28.0)
                        $tabla .= "3";
                      if($latitudNum > 28.0 && $latitudNum <= 32.0)
                        $tabla .= "4";

                      $factor2 = round($factor2);
                      
                      if($factor2 <= 0.5)
                        $columna .= "0.5";
                      if($factor2 > 0.5 && $factor2 <= 1.0)
                        $columna .= "1";
                      if($factor2 > 1.0 && $factor2 <= 2.0)
                        $columna .= "2";
                      if($factor2 > 2.0 && $factor2 <= 4.0)
                        $columna .= "4";  
                      if($factor2 > 4.0 && $factor2 <= 6.0)
                        $columna .= "6";
                      if($factor2 > 6.0)
                        $columna .= "8";

                      $query = "SELECT " . $columna . " FROM " . $tabla . " WHERE pE >= ". $factor1 . " ORDER BY pE ASC";
                      
                      $resultSombreado = mysql_query($query,$link);
                      if($resultSombreado === FALSE){
                        $resultado["response"] = Constantes::ERROR;
                      }else{
                        $totalUsu = mysql_num_rows($resultSombreado);
                        if($totalUsu > 0){
                          while($info = mysql_fetch_assoc($resultSombreado)){
                              $se = $info[$columna];
                              break;
                          }
                        }else{
                          $resultado["response"] = Constantes::ERROR;
                        }
                      }   
                    }else{
                      if($tipoSombreado == "5"){
                        //Ocupar dirección, latitud, factor1 (L/W)
                        $columna = "";
                        $factor1 = (float)$lParteluces / (float)$wParteluces;

                        if($orientacion == "Norte")
                          $columna = "norte";
                        if($orientacion == "Este" || $orientacion == "Oeste")
                          $columna = "esteOeste";
                        if($orientacion == "Sur")
                          $columna = "sur";

                        $latitudNum = (float)$latitud;  
                        if($latitudNum >= 14.0 && $latitudNum <= 19.0)
                          $columna .= "14a19";
                        if($latitudNum > 19.0 && $latitudNum <= 23.0)
                          $columna .= "19a23";
                        if($latitudNum > 23.0 && $latitudNum <= 28.0)
                          $columna .= "23a28";    
                        if($latitudNum > 28.0 && $latitudNum <=32)
                          $columna .= "28a32";

                        $query = "SELECT " . $columna . " FROM tabla_5_2011 WHERE lW >= " . $factor1 . " ORDER BY lW ASC";

                        //Del resultado tomar solo el primer elemento
                        $resultSombreado = mysql_query($query,$link);
                        if($resultSombreado === FALSE){
                          $resultado["response"] = Constantes::ERROR;
                        }else{
                          $totalUsu = mysql_num_rows($resultSombreado);
                          if($totalUsu > 0){
                            while($info = mysql_fetch_assoc($resultSombreado)){
                                $se = $info[$columna];
                                break;
                            }
                          }else{
                            $resultado["response"] = Constantes::ERROR;
                          }
                        }   
                      }else{
                        $se = 1;
                      }
                    }
                  }
                }  
              }else{
                //Es la norma 008 (PENDIENTE COLOCAR LOS VALORES)
              }
            }

            //Armamos la consulta para obtener la temperatura equivalente, la temperatura interior, 
            //el factor de ganancia y la K de referencia

            $query = "SELECT tInterior, ";

            if($orientacion == "Techo"){
              $query .= "teTecho, ";
              $columnaTE = "teTecho";
            }else{
              if($orientacion == "TechoI"){
                $query .= "sInterior, ";
                $columnaTE = "sInterior";
              }else{
                if$tipoElemento == "Tragaluz"){
                  $query .= "teTragaluz, ";
                  $columnaTE = "teTragaluz";
                }else{
                  if($tipoElemento == "Muro"){
                    if($masivoLigero == "Masivo"){
                      $query .= "teMasivo";
                      $columnaTE = "teMasivo";
                    }else{
                      $query .= "teLigero":
                      $columnaTE = "teLigero";
                    }
                  }else{
                    $query .= "teVentana";
                    $columnaTE = "teVentana";
                  }
                  $query .= $orientacion.", ";
                  $columnaTE .= $orientacion;
                }
              }
            }

            $nivelesEdificio = (int)$numNivelesEdificio;
            if(nivelesEdificio <= 3){
              $query .="kMenor3, "
              $columnaK = "kMenor3";
            }else{
              if($orientacion == "Techo" || $orientacion == "TechoI"){
                $query .= "kMayor3Techo";
                $columnaK = "kMayor3Techo";
              }else{
                $query .= "kMayor3Muro";
                $columnaK = "kMayor3Muro";
              }
            }

            if($tipoElemento == "Tragaluz"){
              $query .= ", fgTragaluz ";
              $columnaFG = "fgTragaluz";
            }else{
              if($tipoElemento == "Ventana"){
                $query .= ", fgTragaluz".$orientacion." ";
                $columnaFG = "fgTragaluz".$orientacion;
              }
            }

            $query .= "FROM tabla_1_".$norma." WHERE latitud = '".$latitud."'";

            $resultTabla1 = mysql_query($query,$link); 
            if($resultTabla1 === FALSE){
              $resultado["response"] = Constantes::ERROR;
            }else{
              $totalUsu = mysql_num_rows($resultTabla1);
              if($totalUsu > 0){
                $i=0;
                while($info = mysql_fetch_assoc($resultTabla1)){
                  $te = info[$columnaTE];
                  $ti = info["tInterior"];
                  $fg = info[$columnaFG];
                  $kProy = info[$columnaK];
                }
              }
            }

            //Se aplican los cálculos para la ganancia de calor (referencia y proyectado)

            //Ganancia de calor referencia
            $resultadosCalculos[0] += ((float)$kTotal * (float)$areaTotalOrientacion * (float)$fraccionComponente * ((float)$te - (float)$ti)); 
            //Ganancia de calor proyectado
            $resultadosCalculos[1] += ((float)$kProy * (float)$areaTotalOrientacion * (float)$fraccionComponente * ((float)$te - (float)$ti));

            //Se aplican los cálculos para la ganancia por radiación

            if($tipoElemento == "Tragaluz" || $tipoElemento == "Ventana"){
              //Ganancia de calor radiación referencia
              $resultadosCalculos[2] += (float)$areaTotalOrientacion * (float)$cs * (float)$fraccionComponente * (float)$fg;
              //Ganancia de calor radiación proyectado
              $resultadosCalculos[3] += (float)$areaTotalOrientacion * (float)$cs * (float)$fg * (float)$se;
            }

            $i++;
        }

        return $resultadosCalculos;

  }

?>
