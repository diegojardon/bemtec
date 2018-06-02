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
		$("#areaOculta").val(data.areaElemento);
		console.log("AREA EN SESION: " + $("#areaOculta").val());
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

		if(data.totalElementosNorte != null){
			app.totalElementosNorte = data.totalElementosNorte;
		}else{
			app.totalElementosNorte = 0;
		}
		if(data.totalElementosSur != null){
			app.totalElementosSur = data.totalElementosSur;
		}else{
			app.totalElementosSur = 0;
		}
		if(data.totalElementosEste != null){
			app.totalElementosEste = data.totalElementosEste;
		}else{
			app.totalElementosEste = 0;
		}
		if(data.totalElementosOeste != null){
			app.totalElementosOeste = data.totalElementosOeste;
		}else{
			app.totalElementosOeste = 0;
		}
		if(data.totalElementosTecho != null){
			app.totalElementosTecho = data.totalElementosTecho;
		}else{
			app.totalElementosTecho = 0;
		}

	})
	.error(function(data){
		console.log(data);
	})

	$http.get("http://www.bemtec.mx/bemtec/php/consultaConfigCalculo.php")
	.success(function(data){
		console.log(data);
		app.calculo = data;
	})
	.error(function(data){
		console.log(data);
	})

	$http.get("http://www.bemtec.mx/bemtec/php/consultarSistemasConstructivos.php")
	.success(function(data){
		console.log(data);
		app.elementos = data;
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
					//location.reload(true);
					document.location.href = "calculadoraHome.html";
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

	$scope.actualizaCalculo = function(calculo, idEstado, ciudad){

		//console.log("DATOS CIUDAD: " + ciudad);

		var idCiudad = ciudad.substring(0, ciudad.indexOf("|"));
		var latitudEdificio = ciudad.substring(ciudad.indexOf("|") + 1);

		//console.log("idCiudad: " + idCiudad);
		//console.log("latitud: " + latitudEdificio);

		$http.post("http://www.bemtec.mx/bemtec/php/actualizaCalculo.php", {'nombrePropietario': calculo.nombrePropietario, 'direccionPropietario': calculo.direccionPropietario,
		'cpPropietario': calculo.cpPropietario, 'telefonoPropietario': calculo.telefonoPropietario, 'nombreEdificio': calculo.nombreEdificio, 'direccionEdificio': calculo.direccionEdificio,
	  'estadoEdificio': idEstado, 'ciudadEdificio': idCiudad, 'latitudEdificio': latitudEdificio})
		.success(function(data){
			console.log("RESPONSE: " + data.response);
			if(data.response == 0){
					//Redirigir a pantalla para confirmación del código recibido por correo
					alert("Datos guardados exitosamente");
			}else{
				alert("Error! No se pudo guardar la información del cálculo.");
				$scope.mensaje = "Error! No se pudo guardar la información del cálculo.";
			}
		})
		.error(function(data){
			console.log(data);
			document.location.href = "404.html";
		});
	}



	$scope.agregaElemento = function(elemento, direccion){

		$http.post("http://www.bemtec.mx/bemtec/php/agregaElemento.php", {'nombreElemento': elemento.nombre,'tipoElemento': elemento.tipo, 'direccionElemento': direccion,
		'esHomogeneoElemento': elemento.esHomogeneoElemento,'areaElemento': elemento.area})
		.success(function(data){
			console.log("RESPONSE: " + data.response);
			if(data.response == 0){
					//Redirigir a pantalla para configuración de elemento dependiendo si es homogeneo o no
					var esHomogeneo = parseInt(data.esHomogeneo);
					if(esHomogeneo == 1)
						document.location.href = "componenteHomogenea.html";
					else
						document.location.href = "componenteNoHomogenea.html";

			}else{

			}
		});
	}

	$scope.agregaSistemaConstructivo = function(idElemento, elemento){

		console.log("idElemento selccionado: " + idElemento);

		$http.post("http://www.bemtec.mx/bemtec/php/agregaSistemaConstructivo.php", {'idElemento': idElemento,'nombreElemento': elemento.nombre,'tipoElemento': elemento.tipo, 'areaElemento': elemento.area})
		.success(function(data){
			console.log("RESPONSE: " + data.response);
			if(data.response == 0){

			}else{

			}
		});
	}

	$scope.actualizaElemento = function(elemento){

		if(elemento.mParcial == null || elemento.mParcial == ""){
			elemento.mParcial = "0.0";
		}

		$http.post("http://www.bemtec.mx/bemtec/php/actualizaElemento.php", {'esMasivoElemento': elemento.tipo,'kTotal': elemento.kTotal,'mTotal': elemento.mTotal, 'mParcial': elemento.mParcial})
		.success(function(data){
			console.log("RESPONSE: " + data.response);
			if(data.response == 0){
					var direccionElemento = data.direccionElemento;

					console.log("DIRECCION ELEMENTO: " + direccionElemento);

					if(direccionElemento == "Norte"){
						document.location.href = "calculadora.html?dir=N";
					}
					if(direccionElemento == "Sur"){
						document.location.href = "calculadora.html?dir=S";
					}
					if(direccionElemento == "Este"){
						document.location.href = "calculadora.html?dir=E";
					}
					if(direccionElemento == "Oeste"){
						document.location.href = "calculadora.html?dir=O";
					}
					if(direccionElemento == "Techo"){
						document.location.href = "calculadora.html?dir=T";
					}
					if(direccionElemento == "SIN_SESION"){
						document.location.href = "calculadora.html";
					}
			}else{
				document.location.href = "404.html";
			}
		});
	}

	$scope.agregaComponente = function(componente){

		if(componente.areaNoHomogenea == null || componente.areaNoHomogenea == ""){
			componente.areaNoHomogenea = "0.0";
		}

		if(componente.fraccion == null || componente.fraccion == ""){
			componente.fraccion = "0.0";
		}

		console.log("AISLANTE: " + componente.aislante);

		$http.post("http://www.bemtec.mx/bemtec/php/agregaComponente.php", {'materialComponente': componente.material,'espesorComponente': componente.espesor, 'conductividadComponente': componente.conductividad,
		'aislanteComponente': componente.aislante,'areaNHComponente': componente.areaNoHomogenea,'fraccionNHComponente': componente.fraccion})
		.success(function(data){
			console.log("RESPONSE: " + data.response);
			if(data.response == 0){

			}else{

			}
		});
	}

	$scope.guardaComponentesHomogeneas = function(componente, componente1, componente2, componente3, componente4, componente5){
		if(componente1 != null && componente1.material != ""){
				//Se invoca el método para agregar componentes
				componente1.aislante = $("#aislante1").val();
				$scope.agregaComponente(componente1);
				console.log("COMPONENTE 1 AGREGADA");
		}else{
			console.log("COMPONENTE 1 ES NULO");
		}
		if(componente2 != null && componente2.material != ""){
				//Se invoca el método para agregar componentes
				componente2.aislante = $("#aislante2").val();
				$scope.agregaComponente(componente2);
				console.log("COMPONENTE 2 AGREGADA");
		}else{
			console.log("COMPONENTE 2 ES NULO");
		}
		if(componente3 != null && componente3.material != ""){
				//Se invoca el método para agregar componentes
				componente3.aislante = $("#aislante3").val();
				$scope.agregaComponente(componente3);
				console.log("COMPONENTE 3 AGREGADA");
		}else{
			console.log("COMPONENTE 3 ES NULO");
		}
		if(componente4 != null && componente4.material != ""){
				//Se invoca el método para agregar componentes
				componente4.aislante = $("#aislante4").val();
				$scope.agregaComponente(componente4);
				console.log("COMPONENTE 4 AGREGADA");
		}else{
			console.log("COMPONENTE 4 ES NULO");
		}
		if(componente5 != null && componente5.material != ""){
				//Se invoca el método para agregar componentes
				componente5.aislante = $("#aislante5").val();
				$scope.agregaComponente(componente5);
				console.log("COMPONENTE 5 AGREGADA");
		}else{
			console.log("COMPONENTE 5 ES NULO");
		}

		//Se actualizan los totales en el elemento
		componente.kTotal = $("#kComponentes").val();
		componente.mTotal = $("#mComponentes").val();
		$scope.actualizaElemento(componente);
		console.log("ACTUALIZACIÓN DE ELEMENTO");

	}

	$scope.guardaComponentesNoHomogeneas = function(componente, componente1, componente2, componente3, componente4, componente5, componenteNH1, componenteNH2){
		if(componente1 != null && componente1.material != ""){
				//Se invoca el método para agregar componentes
				componente1.aislante = $("#aislante1").val();
				$scope.agregaComponente(componente1);
				console.log("COMPONENTE 1 AGREGADA");
		}else{
			console.log("COMPONENTE 1 ES NULO");
		}
		if(componente2 != null && componente2.material != ""){
				//Se invoca el método para agregar componentes
				componente2.aislante = $("#aislante2").val();
				$scope.agregaComponente(componente2);
				console.log("COMPONENTE 2 AGREGADA");
		}else{
			console.log("COMPONENTE 2 ES NULO");
		}
		if(componente3 != null && componente3.material != ""){
				//Se invoca el método para agregar componentes
				componente3.aislante = $("#aislante3").val();
				$scope.agregaComponente(componente3);
				console.log("COMPONENTE 3 AGREGADA");
		}else{
			console.log("COMPONENTE 3 ES NULO");
		}
		if(componente4 != null && componente4.material != ""){
				//Se invoca el método para agregar componentes
				componente4.aislante = $("#aislante4").val();
				$scope.agregaComponente(componente4);
				console.log("COMPONENTE 4 AGREGADA");
		}else{
			console.log("COMPONENTE 4 ES NULO");
		}
		if(componente5 != null && componente5.material != ""){
				//Se invoca el método para agregar componentes
				componente5.aislante = $("#aislante5").val();
				$scope.agregaComponente(componente5);
				console.log("COMPONENTE 5 AGREGADA");
		}else{
			console.log("COMPONENTE 5 ES NULO");
		}
		if(componenteNH1 != null && componenteNH1.material != ""){
				//Se invoca el método para agregar componentes
				componenteNH1.aislante = $("#aislanteNH1").val();
				componenteNH1.fraccion = $("#fraccion1").val();
				$scope.agregaComponente(componenteNH1);
				console.log("COMPONENTE NH1 AGREGADA");
		}else{
			console.log("COMPONENTE NH1 ES NULO");
		}
		if(componenteNH2 != null && componenteNH2.material != ""){
				//Se invoca el método para agregar componentes
				componenteNH2.aislante = $("#aislanteNH2").val();
				componenteNH2.fraccion = $("#fraccion2").val();
				$scope.agregaComponente(componenteNH2);
				console.log("COMPONENTE NH2 AGREGADA");
		}else{
			console.log("COMPONENTE NH2 ES NULO");
		}

		//Se actualizan los totales en el elemento
		componente.kTotal = $("#kComponentes").val();
		componente.mTotal = $("#mComponentes").val();
		componente.mParcial = $("#mComponenteParcial").val();
		$scope.actualizaElemento(componente);
		console.log("ACTUALIZACIÓN DE ELEMENTO");

	}

});
