<?php

  require("conexion.php");
	require("constantes.php");
	require("cors.php");

  $data = json_decode(file_get_contents("php://input"));
  $correoElectronico = mysql_real_escape_string($data->correoRecuperarPassword);

	$query = "SELECT usuarioUsuario, passwordUsuario FROM usuario WHERE usuarioUsuario = '".$correoElectronico."'";

	$result = mysql_query($query,$link);

	if($result === FALSE){
		$resultado["response"] = Constantes::ERROR_SELECCION_NO_VALIDA;
	}else{
		$totalFilas = mysql_num_rows($result);
		if($totalFilas > 0){
        $info = mysql_fetch_assoc($result);
		    $para = $correoElectronico;
        $titulo = 'Recuperación de Password de Sr. Seducción';
        $mensaje = '<html>'.
        '<head></head>'.
        '<body><h3>Recuperación de password en Sr. Seducción</h3>'.
        '<b>Usuario: </b>'.
        $info["usuarioUsuario"].
        '<br/>'.
        '<b>Password: </b>'.
        $info["passwordUsuario"].
        '<br/><br/>'.
        'Muchas Gracias'.
        '<br/><br/>'.
        '<h4>ZGR MOBILE 2018.</h4>'.
        '</body>'.
        '</html>';
        $cabeceras = 'MIME-Version: 1.0' . "\r\n";
        $cabeceras .= 'Content-type: text/html; charset=utf-8' . "\r\n";
        $cabeceras .= 'From: Sr. Seducción<srseduccion@zgrmobile.com>';
        $enviado = mail($para, $titulo, $mensaje, $cabeceras);

      $resultado["response"] = Constantes::EXITO;

		}else{
      $resultado["response"] = Constantes::ERROR;
    }

	}

echo json_encode($resultado);

?>
