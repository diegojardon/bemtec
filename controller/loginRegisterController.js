var app = angular.module("app", []);

app.controller("loginRegisterController", function($scope, $http){

	var app = this;

	$http.get("http://www.bemtec.mx/bemtec/php/validaSesion.php")
	.success(function(data){
		console.log(data);
		app.sesion = data.sesion;
		app.usuarioUsuario = data.usuarioUsuario;
		app.estatusUsuario = data.estatusUsuario;
		app.nombreNormaEnergetica = data.nombreNormaEnergetica;
		if(app.sesion == 0){
			/*alert("Es necesario que inicies sesión para visualizar el contenido completo");
			document.location.href = "home.html";*/
			console.log("SIN SESION");
		}else{
			console.log("CON SESION");
		}
	})
	.error(function(data){
		console.log(data);
	})

	$http.get("http://www.bemtec.mx/bemtec/php/contarTotales.php")
	.success(function(data){

		var hayGraficas = document.getElementById("doughnutChartVisits");

		if(hayGraficas != null){
			var totalVisitas = 0;
			var totalRegistros = 0;
			var totalCalculos = 0;

			for(i=0; i<5; i++){
				if(data[i].idTipoAccion == 1){
					//Total de visitas
					totalVisitas = parseInt(data[i].total);
				}
				if(data[i].idTipoAccion == 3 || data[i].idTipoAccion == 4){
					//Total de calculos
					totalCalculos += parseInt(data[i].total);
				}
				if(data[i].idTipoAccion == 5){
					//Total de registros
					totalRegistros = parseInt(data[i].total);
				}
			}

			console.log("VISITAS: " + totalVisitas);
			console.log("REGISTROS " + totalRegistros);
			console.log("CALCULOS: " + totalCalculos);

			var contraparteVisitas = totalVisitas / 6;
			var contraparteRegistros = totalRegistros / 6;
			var contraparteCalculos = totalCalculos / 6;

			if(contraparteVisitas == 0)
				contraparteVisitas = 1;
			if(contraparteRegistros == 0)
				contraparteRegistros = 1;
			if(contraparteRegistros == 0)
				contraparteRegistros = 1;

			$("#doughnutChartVisits").drawDoughnutChart([
				{ title: "Total de visitas",         value : totalVisitas,  color: "#9c3" },
				{ title: "",        value : (totalVisitas / 6),   color: "#FFF" }
			]);

			$("#doughnutChartRegister").drawDoughnutChart([
				{ title: "Total de registros",         value : totalRegistros,  color: "#9c3" },
				{ title: "",        value : (totalRegistros / 6),   color: "#FFF" }
			]);

			$("#doughnutChartCalculations").drawDoughnutChart([
				{ title: "Total de cálculos",         value : totalCalculos,  color: "#9c3" },
				{ title: "",        value :(totalCalculos / 6),   color: "#FFF" }
			]);
		}

	})
	.error(function(data){
		console.log(data);
	})

	$http.get("http://www.bemtec.mx/bemtec/php/contarTotalElementos.php")
	.success(function(data){
		console.log(data);
		app.TMN = data.TMN;
		app.TMS = data.TMS;
		app.TME = data.TME;
		app.TMO = data.TMO;
		app.TMT = data.TMT;
		app.TPN = data.TPN;
		app.TPS = data.TPS;
		app.TPE = data.TPE;
		app.TPO = data.TPO;
		app.TPT = data.TPT;
		app.TVN = data.TVN;
		app.TVS = data.TVS;
		app.TVE = data.TVE;
		app.TVO = data.TVO;
		app.TVT = data.TVT;

	})
	.error(function(data){
		console.log(data);
	})

	$scope.cierraSesion = function(){
		$http.post("http://www.bemtec.mx/bemtec/php/logout.php")
		.success(function(data){
			$scope.usuarioLogin = data;
			console.log(data);
			if(data.response != 0){
				location.reload(true);
				window.location.href = "../view/index.html";
			}
		})
		.error(function(data){
			//Enviar a HTML de error genérico (puede ser el de error 404)
		});
	}

	$scope.iniciaSesion = function(usuario){
		$http.post("http://www.bemtec.mx/bemtec/php/login.php", {'usuarioUsuario': usuario.usuario, 'passwordUsuario': usuario.password})
		.success(function(data){
			console.log("RESPONSE: " + data.response);
			if(data.response == 0){
					location.reload(true);
			}else{
				alert("Usuario y/o password incorrectos");
				$scope.mensaje = "Error! Usuario y/o password incorrecto.";
			}
		})
		.error(function(data){
		  document.location.href = "404.html";
		});
	}

	$scope.registraUsuario = function(usuario){
		$http.post("http://www.bemtec.mx/bemtec/php/altaUsuario.php", {'usuarioUsuario': usuario.usuario, 'passwordUsuario': usuario.password,
		'institucionUsuario': usuario.institucion})
		.success(function(data){
			console.log("RESPONSE: " + data.response);
			if(data.response == 0){
					//Redirigir a pantalla para confirmación del código recibido por correo
					$("#login-modal").modal('hide');
					document.location.href = "registroIncompleto.html";
			}else{
				alert("Error! Ya existe un usuario con ese nombre.");
				$scope.mensaje = "Error! Ya existe un usuario con ese nombre.";
			}
		})
		.error(function(data){
			console.log(data);
			document.location.href = "404.html";
		});
	}

	$scope.recuperaPassword = function(correoElectronico){
		$http.post("http://www.bemtec.mx/bemtec/php/recuperaPassword.php", {'correoRecuperarPassword': correoElectronico})
		.success(function(data){
			console.log("RESPONSE: " + data.response);
			if(data.response == 0){
					$("#login-modal").modal('hide');
					document.location.href = "exitoRecuperacion.html";
			}else{
					document.location.href = "404.html";
			}
		})
		.error(function(data){
			document.location.href = "404.html";
		});
	}

	$scope.registraAccion = function(idTipoAccion){
		$http.post("http://www.bemtec.mx/bemtec/php/altaAccion.php", {'idTipoAccion': idTipoAccion})
		.success(function(data){
			console.log("RESPONSE: " + data.response);
			if(data.response == 0){
					//Redirigir a pantalla para confirmación del código recibido por correo
					//document.location.href = "mensajeConfirmacion.html";
					console.log("VISITA REGISTRADA EXITOSAMENTE");
			}else{

			}
		});
	}

	$scope.consultaMunicipio = function(claveEntidad){
		$http.post("http://www.bemtec.mx/bemtec/php/consultaMunicipios.php", {'claveEntidad': claveEntidad})
		.success(function(data){
			console.log("RESPONSE: " + data.response);
			if(data.response == 0){
					app.municipios = data;
					console.log("Municipios obtenidos exitosamente!");
			}else{

			}
		});
	}

	$scope.agregaElemento = function(elemento){
		$http.post("http://www.bemtec.mx/bemtec/php/agregaElementoSesion.php", {'tipoElemento': elemento.tipo, 'direccionElemento': elemento.direccion,
		'totalElementos': elemento.total})
		.success(function(data){
			console.log("RESPONSE: " + data.response);
			if(data.response == 0){
					//Redirigir a pantalla para confirmación del código recibido por correo
					//document.location.href = "mensajeConfirmacion.html";
					alert("Elemento agregado exitosamente!");
			}else{

			}
		});
	}

	$scope.buscaElementoSiguiente = function(direccion, accion){
		$http.post("http://www.bemtec.mx/bemtec/php/contarTotalElementos.php", {'direccionElemento': direccion, 'accion': accion})
		.success(function(data){
			console.log(data);

			let elementoSiguiente = data.elementoSiguiente;

			console.log("ELEMENTO SIGUIENTE: " + elementoSiguiente);

			//Limpiamos los elementos que existan de los div
			switch(direccion){
				case "N":
					$("#elementoNorte").empty();
					$("#elementoNorte").append(armaComponente(elementoSiguiente));
					break;
				case "S":
					$("#elementoSur").empty();
					$("#elementoSur").append(armaComponente(elementoSiguiente));
					break;
				case "E":
					$("#elementoEste").empty();
					$("#elementoEste").append(armaComponente(elementoSiguiente));
					break;
				case "O":
					$("#elementoOeste").empty();
					$("#elementoOeste").append(armaComponente(elementoSiguiente));
					break;
				case "T":
					$("#elementoTecho").empty();
					$("#elementoTecho").append(armaComponente(elementoSiguiente));
					break;
			}

		})
		.error(function(data){
			console.log(data);
		})
	}

	function armaComponente(elementoSiguiente){
		if(elementoSiguiente.equals("XX0")){
			//No hay elementos disponibles para connfigurar en esta dirección
			alert("No hay elementos disponibles para connfigurar en esta dirección");
		}else{

			let numElemento = parseInt(elementoSiguiente.substring(2,3));
			let elemento = elementoSiguiente.substring(0,1);
			console.log("NUM ELEMENTO: " + numElemento);
			console.log("ELEMENTO: " + elemento);

			let componente = "";

			if(elemento.equals("M")){
		  	componente = '<h5 class="labelCalculadora">Muro ' + numElemento + '</h5>';
			}else{
				if(elemento.equals("P")){
					componente = '<h5 class="labelCalculadora">Puerta ' + numElemento + '</h5>';
				}else{
					if(elemento.equals("V")){
						componente = '<h5 class="labelCalculadora">Ventana ' + numElemento + '</h5>';
					}
				}
			}

			componente += '<h5 class="labelCalculadora">Número de componentes</h5>';
			componente += '<input type="number" id="numComponentes" class="form-control separacion-input" min="1" max="100" required/>';

			return componente;

		}
	}

});
