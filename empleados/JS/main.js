////GLOBALS
var panelActual = 1; // Inicialmente el panel 1 está visible
var token;
function cambiarPanel(nuevoPanel) {
    // Verifica que el nuevo panel no sea el mismo que el actual
    if (nuevoPanel !== panelActual) {
        $("#panel" + panelActual).fadeOut(400, function () {
            // Una vez oculto el actual, mostramos el nuevo panel
            $("#panel" + nuevoPanel).fadeIn(400);
        });
        // Actualizamos el panel actual
        panelActual = nuevoPanel;
    }
	console.log('Cambiando al nuevo panel:', nuevoPanel);
}





//////////////////

function executeAction(action,form,variable,delay){
	if (delay){
		if (postExecution) postExecution+=`executeAction('${action}','${form}','${variable}');`;
		else postExecution=`executeAction('${action}','${form}','${variable}');`;
		return;
	}
	var stringForm="";
	var stringValor="";
	if (form) {
			formElement=document.getElementById(form);
		if (!formElement) {
			stringForm=form;
		}
		else {
			formElements=formElement.getElementsByTagName('INPUT');
			for (var i in formElements){
				var element=formElements[i];
				var tipo=element.type;
				switch (tipo) {
					case "radio":
						var key=element.id;
						var value=element.checked;
						break;
					case "checkbox":
						var key=element.id;
						var value=element.checked;
						break;
					default:
					var key=element.id;
					var value=element.value;
				}

				stringForm+="&"+key+"="+value;
			}
			formElements=formElement.getElementsByTagName('SELECT');
			for (var i in formElements){
				var element=formElements[i];
				var key=element.id;
				var value=element.value;
				stringForm+="&"+key+"="+value;
			}
			formElements=formElement.getElementsByTagName('TEXTAREA');
			for (var i in formElements){
				var element=formElements[i];
				var key=element.id;
				var value=element.value;
				stringForm+="&"+key+"="+value;
			}

		}
	}
	if (variable){
		if (Array.isArray(variable)){
				variable.forEach(function(value, index, array) {
					valor=document.getElementById(value).value;
					stringValor+=`&${value}=${valor}`;
				});
		}
		else {
			var element=document.getElementById(variable);
			if (element){
				if (variable.substr(0,12)=="tablaListado") {if (element.rows.length!=0) return;}
				else{
					var valor=document.getElementById(variable).innerHTML;
					var tipo=element.type;
					if (tipo=='checkbox') valor=document.getElementById(variable).checked;
					else if (valor=="") valor=document.getElementById(variable).value;
					var stringValor="&"+variable+"="+valor;
				}
			}
			else stringForm+=variable;
		}
	}

	var xmlhttp;
	xmlhttp=new XMLHttpRequest();
	xmlhttp.open("GET","./PHP/ExecuteAction.php?action="+action+stringForm+stringValor,true);
	xmlhttp.onreadystatechange=function()  {
		if (xmlhttp.readyState==4 && xmlhttp.status==200) mostrarAction(xmlhttp.responseText);
	}
	xmlhttp.send();
}

function sleep(ms) {
  return new Promise(resolve => setTimeout(resolve, ms));
}
function mostrarAction(contenido){
	eval(contenido);
}

/////////////////
function login(){
	var usuario=document.getElementById('username');
  	var password=document.getElementById('password');
	fetch("./PHP/ExecuteAction.php?action=login&destino=token",{
        method: "GET",
        headers: {
            "Content-Type": "text/plain"
        }
    }) 
	  .then(response => response.text())  
	  .then(data => {
		console.log('Datos del primer fetch:', data);
		key=data.slice(0,16);
		token=key;
		iv=data.slice(16);
  		var textBytes = aesjs.utils.utf8.toBytes(usuario.value);
		var res=textBytes.length % 16;
		if (res != 0){
		for (res;res<16;res++) {
			var x = new Uint8Array(textBytes.length+1);
			x.set(textBytes, 0);
			x[textBytes.length]=32;
			textBytes=x;
			}
		}
		key=aesjs.utils.utf8.toBytes(key);
		iv=aesjs.utils.utf8.toBytes(iv);
		var aesCbc = new aesjs.ModeOfOperation.cbc(key, iv);
		var encryptedBytes = aesCbc.encrypt(textBytes);
		var encryptedHex = aesjs.utils.hex.fromBytes(encryptedBytes);
		var codeBytes=btoa(encryptedHex);
		return fetch("./PHP/ExecuteAction.php?action=login&destino=logginProcess&data="+codeBytes);  // Aquí hacemos el segundo fetch
	  })
	  .then(response => response.text())  
	  .then(data => {
		console.log('Datos del segundo fetch:', data);
		var encryptedBytes=aesjs.utils.hex.toBytes(data);
		var aesCbc = new aesjs.ModeOfOperation.cbc(key,iv);
		var decryptedBytes = aesCbc.decrypt(encryptedBytes);
		var decryptedText = aesjs.utils.utf8.fromBytes(decryptedBytes);
		var salt = decryptedText.substr(0, 29);
		var response=decryptedText.substr(29,32);

		var bcrypt = dcodeIO.bcrypt;
		var hash = bcrypt.hashSync(password.value, salt);

		var encryptedBytes=aesjs.utils.hex.toBytes(response);
		key=hash.substr(44);
		iv=hash.substr(8,16);
		key = aesjs.utils.utf8.toBytes(key);
		iv = aesjs.utils.utf8.toBytes(iv);
		var aesCbc = new aesjs.ModeOfOperation.cbc(key,iv);
		var decryptedBytes = aesCbc.decrypt(encryptedBytes);
		var decryptedText = aesjs.utils.utf8.fromBytes(decryptedBytes);

		if (decryptedText==token){
			var textBytes = aesjs.utils.utf8.toBytes(decryptedText);
			var aesCbc = new aesjs.ModeOfOperation.cbc(key, iv);
			var encryptedBytes = aesCbc.encrypt(textBytes);
			var encryptedHex = aesjs.utils.hex.fromBytes(encryptedBytes);
			var codeBytes=btoa(encryptedHex);
			return fetch("./PHP/ExecuteAction.php?action=login&destino=loginEnd&data="+codeBytes);  // Aquí hacemos el tercer fetch
		}
		else iqwerty.toast.Toast('Clave o usuario incorrecto',{settings:{duration:1000}});$('#panelAuxiliar').fadeOut('slow');
	  })
	  .then(response => response.text())  
	  .then(data => {
		console.log('Datos del tercero fetch:', data );
		console.log('Datos de token:', token );
		
		if (data==token){
			//login aceptado
			return fetch("./PHP/ExecuteAction.php?action=login&destino=sessionStart");  // Aquí hacemos el cuarto fetch

		}
		
		else iqwerty.toast.Toast('Clave o usuario incorrecto',{settings:{duration:1000}});$('#panelAuxiliar').fadeOut('slow');


	  })
	  .then(response => response.json())  
	  .then(data => {
			console.log('Datos del cuarto fetch:', data);
			if (data.reset=="1") cambiarPanel(2);
			else {
				return fetch("./PHP/ExecuteAction.php?action=rellenarPanel&destino=panelEntradasSalidas&idEmpleado="+data.idEmpleado);  // Aquí hacemos el quinto fetch
			}

		})
	  .then(response => response.text())  
	  .then(data => {
			console.log('Datos del quinto fetch:', data);
			mostrarAction(data);
			cambiarPanel(3);
	    })
		.catch(error => {
		console.log('Ocurrió un error:', error);
	  });
	

}

function cambiarClave(){
	var clave=document.getElementById('password1').value;
	var verificacion=document.getElementById('password2').value;

	if (clave.length < 5)   { iqwerty.toast.Toast('La clave deve tener al menos 5 caracteres',{settings:{duration:1000}});return;}
	if (clave!=verificacion) {iqwerty.toast.Toast('Las claves no coinciden',{settings:{duration:1000}});return;}

	var textBytes = aesjs.utils.utf8.toBytes(clave);
	var res=textBytes.length % 16;
	if (res != 0){
	    for (res;res<16;res++) {
		    var x = new Uint8Array(textBytes.length+1);
		    x.set(textBytes, 0);
		    x[textBytes.length]=32;
		    textBytes=x;
	    }
	}
	var aesCbc = new aesjs.ModeOfOperation.cbc(key, iv);
	var encryptedBytes = aesCbc.encrypt(textBytes);
	var encryptedPassword = aesjs.utils.hex.fromBytes(encryptedBytes);
	var codeBytes=btoa(encryptedPassword);
	fetch("./PHP/ExecuteAction.php?action=login&destino=cambiarClave&data="+codeBytes) 
	  .then(response => response.text())  
	  .then(data => {
		console.log('Datos del segundo fetch:', data);
		mostrarAction(data);
		cambiarPanel(3);
	  })
	  .catch(error => {
		console.log('Ocurrió un error:', error);
	  });
	
}


function enviarEntradaSalida(tipoEntrada){
	var latitud=28.5684483;
	var longitud=-16.3291105;
	var idEmpleado=document.getElementById('idEmpleado').value;
	
	if ("geolocation" in navigator) {
		navigator.geolocation.getCurrentPosition(
		  function (position) {
			// Extrae la latitud y longitud
			latitud = position.coords.latitude;
			longitud = position.coords.longitude;
			
			console.log("Latitud:", latitud);
			console.log("Longitud:", longitud);
			fetch("./PHP/ExecuteAction.php?action=insertar&destino=entradasSalidas&tipoEntrada="+tipoEntrada+"&idEmpleado="+idEmpleado+"&latitud="+latitud+"&longitud="+longitud) 
				.then(response => response.text())  
				.then(data => {
					console.log('Datos del primer fetch:', data);
					mostrarAction(data);
			})
				.catch(error => {
					console.log('Ocurrió un error:', error);
			});
		},
		  function (error) {
    		let mensajeError = "";

    			if (error.code === error.PERMISSION_DENIED) {
        		// El usuario ha denegado el permiso en el navegador
        			mensajeError = "⛔ Permiso denegado para esta página. Por favor, revoca y vuelve a dar permiso en los ajustes de tu navegador.";
    			} else {
        		// Fallo del sistema operativo o del GPS
        			mensajeError = "⚠️ Ubicación no disponible. Por favor, verifica dos cosas: 1) Que el interruptor de Ubicación principal de tu móvil esté ACTIVO y 2) Que tengas buena señal GPS o Wi-Fi.";
    			}

    			alert(mensajeError + " No se puede registrar la entrada/salida.");
    			return; // Detiene la ejecución
		}
		);
	} else {
		alert ("La geolocalización no está soportada en este navegador.");
		console.error("La geolocalización no está soportada en este navegador.");
	}
	
	

}
 
  