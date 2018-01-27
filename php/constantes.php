<?php

/*
*		Clase en donde se manejan las constantes para las diferentes
* 	respuestas de los servicios REST consumidos en bemtec.
*
* 	@author: Diego Jardón
* 	@creationDate: 27-Jan-2018
* 	@version: 1.0
*
*/

	class Constantes{
		const EXITO = 0;
		const ERROR = -1;

		const ERROR_USUARIO_Y_O_PASSWORD_INCORRECTOS = -100;
		const ERROR_SELECCION_NO_VALIDA = -101;
		const ERROR_INSERCION_USUARIO_NO_VALIDA = -102;
		const ERROR_INSERCION_RESULTADO_NO_VALIDO = -105;
		const ERROR_USUARIO_EXISTENTE = -106;
		const ERROR_USUARIO_INEXISTENTE = -107;

	}
?>
