






//////LOGIN FUNCTIONS

function loggin() {
    var usuario = document.getElementById('username');
    var password = document.getElementById('password');
    var xmlhttp;
    xmlhttp = new XMLHttpRequest();
    xmlhttp.open("POST", "./PHP/ExecuteAction.php?action=loggin&destino=token", false);
    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            var response = xmlhttp.responseText.trim();
            key = response.substr(0, 16);
            var token = key;
            iv = response.substr(16);
            var textBytes = aesjs.utils.utf8.toBytes(usuario.value);
            var res = textBytes.length % 16;
            if (res != 0) {
                for (res; res < 16; res++) {
                    var x = new Uint8Array(textBytes.length + 1);
                    x.set(textBytes, 0);
                    x[textBytes.length] = 32;
                    textBytes = x;
                }
            }
            key = aesjs.utils.utf8.toBytes(key);
            iv = aesjs.utils.utf8.toBytes(iv);
            var aesCbc = new aesjs.ModeOfOperation.cbc(key, iv);
            var encryptedBytes = aesCbc.encrypt(textBytes);
            var encryptedHex = aesjs.utils.hex.fromBytes(encryptedBytes);
            var encryptedBytes = aesjs.utils.hex.toBytes(encryptedHex);
            
            var xmlhttp2;
            xmlhttp2 = new XMLHttpRequest();
            xmlhttp2.open("POST", "./PHP/ExecuteAction.php?action=loggin&destino=logginProcess", false);
            var formData = new FormData();
            formData.append("data", encryptedHex);
            
            xmlhttp2.onreadystatechange = function() {
                if (xmlhttp2.readyState == 4 && xmlhttp2.status == 200 && xmlhttp2.responseText != "") {
                    var encryptedHex = xmlhttp2.responseText.trim();
                    var encryptedBytes = aesjs.utils.hex.toBytes(encryptedHex);
                    var aesCbc = new aesjs.ModeOfOperation.cbc(key, iv);
                    var decryptedBytes = aesCbc.decrypt(encryptedBytes);
                    var decryptedText = aesjs.utils.utf8.fromBytes(decryptedBytes);
                    var salt = decryptedText.substr(0, 29);
                    var response = decryptedText.substr(29, 32);

                    var bcrypt = dcodeIO.bcrypt;
                    var hash = bcrypt.hashSync(password.value, salt);

                    var encryptedBytes = aesjs.utils.hex.toBytes(response);
                    key = hash.substr(44);
                    iv = hash.substr(8, 16);
                    key = aesjs.utils.utf8.toBytes(key);
                    iv = aesjs.utils.utf8.toBytes(iv);
                    var aesCbc = new aesjs.ModeOfOperation.cbc(key, iv);
                    var decryptedBytes = aesCbc.decrypt(encryptedBytes);
                    var decryptedText = aesjs.utils.utf8.fromBytes(decryptedBytes);

                    if (decryptedText == token) {

                        var textBytes = aesjs.utils.utf8.toBytes(decryptedText);
                        var aesCbc = new aesjs.ModeOfOperation.cbc(key, iv);
                        var encryptedBytes = aesCbc.encrypt(textBytes);
                        var encryptedHex = aesjs.utils.hex.fromBytes(encryptedBytes);
                        
                        var xmlhttp3 = new XMLHttpRequest();
                        xmlhttp3.open("POST", "./PHP/ExecuteAction.php?action=loggin&destino=logginEnd", false);
                        
                        var formData3 = new FormData();
                        formData3.append("data", encryptedHex);
                        
                        xmlhttp3.onreadystatechange = function() {
                            if (xmlhttp3.readyState == 4 && xmlhttp3.status == 200 && xmlhttp3.responseText.trim() != "") {
                                if (xmlhttp3.responseText.trim() == token) {
                                    executeAction('prepararPanel', '&destino=panelLateral');
                                }
                            } else {
                                iqwerty.toast.Toast('Usuario o clave incorrecta', { settings: { duration: 2000 } });
                            }
                        }
                        xmlhttp3.send(formData3);
                    } else {
                        iqwerty.toast.Toast('Usuario o clave incorrecta', { settings: { duration: 2000 } });
                    }
                } else {
                    iqwerty.toast.Toast('Usuario o clave incorrecta', { settings: { duration: 2000 } });
                }
            }
            xmlhttp2.send(formData);
        }
    }
    xmlhttp.send();
}


///////FUNCTIONS
//////////////////
function animatePanelLateral(){
    var altoContenido = $("#panelLateralContenido").outerHeight();
    
    if ($("#panelLateral").css("display")=='none'){
        $("#panelLateral").css("top", -altoContenido + "px");
        $("#panelLateral").css("display", "block");
        $("#panelLateral").animate({top: '0px'});
        return;
    }

    if (parseInt($("#panelLateral").css("top")) === 0) {
        $("#panelLateral").animate({top: -altoContenido + 'px'});
    } else {
        $("#panelLateral").animate({top: '0px'});
    }
}

function animatePanelLateralLogginOff(){
    var altoContenido = $("#panelLateralContenido").outerHeight();
    $("#panelLateral").animate({top: -altoContenido + 'px'}, function() {
        $("#panelLateral").css("display", "none");
    });
}

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
function loadLayout(layout,destino,forzar=false,oculto=false,postExecution){
	if (typeof(destino)=="string") var div=document.getElementById(destino);
	else var div=destino;
	if (forzar==false && div.innerHTML!="") return;
	if (oculto) div.style="display:none";

	var xmlhttp;
	xmlhttp=new XMLHttpRequest();
	xmlhttp.open("GET","./PHP/LoadLayout.php?layout="+layout,true);
	xmlhttp.onreadystatechange=function()  {
		if (xmlhttp.readyState==4 && xmlhttp.status==200) mostrarLayout(xmlhttp.responseText,destino,postExecution);
	}
	xmlhttp.send();
}
function mostrarLayout(contenido,destino,postExecution){
  if (contenido=="") return;
	var div=document.getElementById(destino);
	div.innerHTML=contenido;
	if (postExecution)	eval(postExecution);
}

function sleep(ms) {
  return new Promise(resolve => setTimeout(resolve, ms));
}
function mostrarAction(contenido){
	eval(contenido);
}
function expande(object,oldSH=false,outer=false){
	if (typeof object === 'object' && !Array.isArray(object) && object !== null) {
    	var target=document.getElementById($(object).attr('target'));
	}
	else {
		var target=document.getElementById(object);
	}
	if (oldSH==$(target).get(0).scrollHeight) return;
	oldSH=$(target).get(0).scrollHeight;
	$(target).css('margin-bottom',100);
	$(target).animate({height:$(target).get(0).scrollHeight},500,function (){expande(object,oldSH,outer)});
	$(object).attr('onclick','');
	$(object).off('click');
	$(object).click(function(){collapse(object);});
	if (outer) {
		var outerTarget=$(target).parents().eq(2);
		console.log(outerTarget);
		expande(outerTarget.attr('id'));
	}
}
function collapse(object){
	var target=document.getElementById($(object).attr('target'));
	$(target).css('margin-bottom',0);
	$(target).animate({height:0},500);
	$(object).off('click');
	$(object).click(function(){expande(object);});
}
