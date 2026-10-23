////GLOBALS
var postExecution = false;

///FUNCTIONS
function cargarFechaHora() {
    var fecha = new Date();
    var segundos = fecha.getSeconds();
    var minutos = fecha.getMinutes();
    var horas = fecha.getHours();
    var diaMes = fecha.getDate();
    if (0 <= minutos && minutos <= 9) minutos = "0" + minutos;
    if (0 <= segundos && segundos <= 9) segundos = "0" + segundos;
    var dia = fecha.getDay();
    switch (dia) {
        case 0: dia = 'Domingo'; break;
        case 1: dia = 'Lunes'; break;
        case 2: dia = 'Martes'; break;
        case 3: dia = 'Miércoles'; break;
        case 4: dia = 'Jueves'; break;
        case 5: dia = 'Viernes'; break;
        case 6: dia = 'Sábado'; break;
    }

    var mes = fecha.getMonth();
    switch (mes) {
        case 0: mes = 'Enero'; break;
        case 1: mes = 'Febrero'; break;
        case 2: mes = 'Marzo'; break;
        case 3: mes = 'Abril'; break;
        case 4: mes = 'Mayo'; break;
        case 5: mes = 'Junio'; break;
        case 6: mes = 'Julio'; break;
        case 7: mes = 'Agosto'; break;
        case 8: mes = 'Septiembre'; break;
        case 9: mes = 'Octubre'; break;
        case 10: mes = 'Noviembre'; break;
        case 11: mes = 'Diciembre'; break;
    }
    var año = fecha.getFullYear();
    var cadenaFecha = dia + ", " + diaMes + " de " + mes + " de " + año + " " + horas + ":" + minutos + ":" + segundos;
    var labelFecha = document.getElementById('labelDivTPVfechaHora');
    if (labelFecha) labelFecha.innerHTML = cadenaFecha;
    setTimeout(cargarFechaHora, 1000);
}

function limpiarObjeto(objeto) {
    switch (Object.prototype.toString.call(objeto)) {
        case '[object String]':
            var objElem = document.getElementById(objeto);
            if (objElem) objElem.innerHTML = '';
            break;
        default:
            if (objeto) objeto.innerHTML = '';
    }
}
function enviarConsulta() {
    var inputNombre = document.getElementById('contactoNombre');
    var inputEmail = document.getElementById('contactoCorreo');
    var inputConsulta = document.getElementById('contactoConsulta');

    var nombre = inputNombre ? inputNombre.value.trim() : '';
    var email = inputEmail ? inputEmail.value.trim() : '';
    var consulta = inputConsulta ? inputConsulta.value.trim() : '';

    if (nombre === '' || email === '' || consulta === '') {
        iqwerty.toast.Toast('Por favor, completa todos los campos antes de enviar.', { settings: { duration: 2000 } });

        if (nombre === '' && inputNombre) inputNombre.focus();
        else if (email === '' && inputEmail) inputEmail.focus();
        else if (consulta === '' && inputConsulta) inputConsulta.focus();
        return false;
    }

    var regexEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!regexEmail.test(email)) {
        iqwerty.toast.Toast('Por favor, introduce una dirección de correo electrónico válida.', { settings: { duration: 2000 } });

        if (inputEmail) {
            inputEmail.focus();
            inputEmail.select();
        }
        return false;
    }

    iqwerty.toast.Toast('Consulta validada correctamente. Enviando...', { settings: { duration: 2000 } });
	executeAction('enviarCorreo', null, ['contactoNombre', 'contactoCorreo', 'contactoConsulta']);
}


function mostrarListado(nombreObjeto) {
    var selector = document.getElementById(nombreObjeto);
    if (!selector) return;
    switch (selector.value) {
        case "BalanceCierres":
            var fechaInicio = document.getElementById('inputFechaInicioEstadisticas').value;
            var fechaFin = document.getElementById('inputFechaFinEstadisticas').value;
            fechaInicio = fechaInicio.replace(/\//g, "-");
            fechaFin = fechaFin.replace(/\//g, "-");
            window.open('./PHP/listados/BalanceCierres.php?fechaInicio=' + fechaInicio + '&fechaFin=' + fechaFin);
            break;
        case "BalancePorCuartos":
            var fechaInicio = document.getElementById('inputFechaInicioEstadisticas').value;
            fechaInicio = fechaInicio.replace(/\//g, "-");
            window.open('./PHP/listados/BalancePorCuartos.php?fechaInicio=' + fechaInicio);
            break;
        default:
            window.open('./PHP/listados/' + selector.value + '.php');
    }
}

function executeAction(action, form, variable, delay) {
    if (delay) {
        if (postExecution) postExecution += `executeAction('${action}','${form}','${variable}');`;
        else postExecution = `executeAction('${action}','${form}','${variable}');`;
        return;
    }
    var stringForm = "";
    var stringValor = "";
    if (form) {
        var formElement = document.getElementById(form);
        if (!formElement) {
            stringForm = form;
        } else {
            var formElements = formElement.getElementsByTagName('INPUT');
            for (var i = 0; i < formElements.length; i++) {
                var element = formElements[i];
                var key = element.id;
                var value = (element.type === "radio" || element.type === "checkbox") ? element.checked : element.value;
                stringForm += "&" + key + "=" + value;
            }
            formElements = formElement.getElementsByTagName('SELECT');
            for (var i = 0; i < formElements.length; i++) {
                var element = formElements[i];
                stringForm += "&" + element.id + "=" + element.value;
            }
            formElements = formElement.getElementsByTagName('TEXTAREA');
            for (var i = 0; i < formElements.length; i++) {
                var element = formElements[i];
                stringForm += "&" + element.id + "=" + element.value;
            }
        }
    }
    if (variable) {
        if (Array.isArray(variable)) {
            variable.forEach(function (value) {
                var valor = document.getElementById(value).value;
                stringValor += `&${value}=${valor}`;
            });
        } else {
            var element = document.getElementById(variable);
            if (element) {
                if (variable.substr(0, 12) == "tablaListado") {
                    if (element.rows.length != 0) return;
                } else {
                    var valor = element.innerHTML;
                    var tipo = element.type;
                    if (tipo == 'checkbox') valor = element.checked;
                    else if (valor == "") valor = element.value;
                    stringValor = "&" + variable + "=" + valor;
                }
            } else {
                stringForm += variable;
            }
        }
    }

    var xmlhttp = new XMLHttpRequest();
    xmlhttp.open("GET", "./PHP/ExecuteAction.php?action=" + action + stringForm + stringValor, true);
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) mostrarAction(xmlhttp.responseText);
    };
    xmlhttp.send();
}

function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

function mostrarAction(contenido) {
    eval(contenido);
}



function cambiarClaveProceso() {
    var clave = document.getElementById('cambioClave').value;
    var verificacion = document.getElementById('cambioClaveV').value;

    if (clave.length < 5) { iqwerty.toast.Toast('La clave debe tener al menos 5 caracteres', { settings: { duration: 1000 } }); return; }
    if (clave != verificacion) { iqwerty.toast.Toast('Las claves no coinciden', { settings: { duration: 1000 } }); return; }

    var textBytes = aesjs.utils.utf8.toBytes(clave);
    var res = textBytes.length % 16;
    if (res != 0) {
        for (var r = res; r < 16; r++) {
            var x = new Uint8Array(textBytes.length + 1);
            x.set(textBytes, 0);
            x[textBytes.length] = 32;
            textBytes = x;
        }
    }
    var aesCbc = new aesjs.ModeOfOperation.cbc(key, iv);
    var encryptedBytes = aesCbc.encrypt(textBytes);
    var encryptedPassword = aesjs.utils.hex.fromBytes(encryptedBytes);
    
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.open("GET", "./PHP/ExecuteAction.php?action=loggin&destino=cambiarClave&data=" + encryptedPassword, false);
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            document.location.reload();
        }
    };
    xmlhttp.send();
}

function imprimirDocumento(objeto) {
    var documento = document.getElementById(objeto).innerHTML;
    documento = escape(documento);
    documento = documento.replace(/[+]/g, "%2B");
    window.open('http://restauranteelabogado.es/Clientes/PHP/core/ext/imprimirDocumento.php?documento=' + documento, '_blank');
}


function unloadLayout(destino) {
    var dest = document.getElementById(destino);
    if (dest) dest.innerHTML = "";
}

function slide(elem) {
    var $btn, $panelTarget;

    if (typeof elem === 'number') {
        // Si pasamos un número, buscamos directamente el panel por su atributo ind
        $panelTarget = $("div.paneles[ind='" + elem + "']");
        // Intentamos buscar si existe un botón asociado en la barra (opcional)
        $btn = $("#tablabarra td[ind='" + elem + "']");
    } else {
        $btn = $(elem);
        var index = parseInt($btn.attr('ind'), 10);
        var target = $btn.attr('target') || 'contenido';
        var $contenedor = $("#" + target);
        var $paneles = $contenedor.children("div.paneles");
        $panelTarget = $paneles.eq(index);
    }

    var $panelActivo = $("div.paneles[status='active']");
    var indexActive = $panelActivo.length ? $panelActivo.index() : -1;
    var ind = $panelTarget.index();

    if (indexActive === ind) return;

    var id = $panelTarget.attr('id');
    if (id === "panelInicio") {
        if (typeof iniciarAnimacionInicio === 'function') iniciarAnimacionInicio();
    } else {
        if (typeof detenerAnimacionInicio === 'function') detenerAnimacionInicio();
    }

    var layout = $panelTarget.attr('layout');
    if (typeof loadLayout === 'function' && layout) {
        loadLayout(layout, id);
    }

    // Solo actualizamos las clases de la barra si el botón existe
    if ($btn.length) {
        $("#tablabarra td").removeClass('botonBarraSeleccionada').addClass('botonBarra');
        $btn.removeClass('botonBarra').addClass('botonBarraSeleccionada');
    }

    if ($panelActivo.length) {
        $panelActivo.attr("status", "");
    }
    $panelTarget.attr("status", "active");

    var callback = function () {
        if ($panelActivo.length) {
            $panelActivo.hide();
        }
        if (typeof afterAnimate === 'function') {
            afterAnimate();
        }
    };

    if (indexActive > ind) {
        $panelTarget.css({ "left": "-100%", "display": "block", "z-index": 1 });
        if ($panelActivo.length) {
            $panelActivo.animate({ left: '100%' }, 400);
        }
        $panelTarget.animate({ left: '0%' }, 400, callback);
    } else {
        $panelTarget.css({ "left": "100%", "display": "block", "z-index": 1 });
        if ($panelActivo.length) {
            $panelActivo.animate({ left: '-100%' }, 400);
        }
        $panelTarget.animate({ left: '0%' }, 400, callback);
    }

    if (id === "panelInicio") {
        setTimeout(function () {
            $(".fincas-animar").addClass("aparecer");
        }, 100);
    }
}

function afterAnimate() {
    var val = $('.botonSubPanelOpciones.editarArticulo').first().hasClass('selected');
    if (val) $('.botonSubPanelOpciones.editarArticulo').last().addClass('selected');
    if (postExecution) eval(postExecution);
    postExecution = false;
}

function initSlider() {
    $(".slidercontrol").off("click", slide);
    $(".slidercontrol").click(slide);
    $(".slider").children("div").addClass("sliderPanel");
    var elements = $(".slider").children("div [status='active']");
    for (var index in elements) {
        var element = elements[index];
        if (element && element.innerHTML == "") {
            $(element).css("z-index", 1);
            var target = $(element).parent().attr("id");
            var el = $(".slidercontrol.active[target='" + target + "']");
            $(el).trigger("click");
        }
    }
}

var s, Menu = {
    settings: {
        status: "init",
        collapse: true,
        numItems: 0,
        items: [],
        orientation: "none",
        position: "none",
        size: 0
    },
    init: function () {
        s = this.settings;
    },
    addItem: function (itemObj) {
        this.settings.items.push(itemObj);
        this.settings.numItems += 1;
    },
    deploy: function () {
        if (this.settings.numItems < 2) return;
        $('body').append('<div id="menu" style="position:fixed;z-index:1;background-color: rgba(255, 0, 0, 0);opacity:1"></div>');
        this.settings.height = this.settings.orientation == "horizontal" ? ((window.innerHeight / 100) * this.settings.size) : (window.innerHeight);
        this.settings.width = this.settings.orientation == "vertical" ? ((window.innerWidth / 100) * this.settings.size) : (window.innerWidth);
        this.settings.length = this.settings.height > this.settings.width ? this.settings.width : this.settings.height;
        this.settings.margin = this.settings.length / 4;

        $('#menu').css({ "height": this.settings.height, "width": this.settings.width, "bottom": this.settings.margin + "px", "left": this.settings.margin + "px" });
        $('#menu').append('<div id="menuItem0" style="position:absolute;top:0;left:0"></div>');

        var itemObj = this.settings.items[0];
        $('#menuItem0').append('<img id="menuImg0" class="botonMenuPrincipalAnimado" style="float:right;clear:right;position:relative;left:' + (this.settings.length / 4) + 'px" src="' + itemObj.image + '" height="' + this.settings.length + '" width="' + this.settings.length + '">');
        $('#menuItem0').css("z-index", this.settings.numItems);
        $('#menuImg0').click(itemObj.onclick);
        $('#menuItem0').css(itemObj.css);

        var position = $('#menuImg0').position();
        for (var i = 1; i < this.settings.numItems; i++) {
            $('#menu').append('<div id="menuItem' + i + '" style="position:absolute;top:0;left:' + (position.left + this.settings.length / 2) + 'px"></div>');
            var itemActual = this.settings.items[i];
            $('#menuItem' + i).append('<img id="menuImg' + i + '" style="float:right;clear:right;position:relative;left:' + (this.settings.length / 2) + 'px" src="' + itemActual.image + '" height="' + this.settings.length + '" width="' + this.settings.length + '">');
            var zIndex = this.settings.numItems - (i + 2);
            $('#menuItem' + i).css("z-index", zIndex);
            $('#menuImg' + i).click(itemActual.onclick);
            $('#menuItem' + i).css(itemActual.css);
        }
    },
   expand: function () {
        this.settings.status = "expanded";
        this.settings.collapse = false;
        var step = (this.settings.width - (this.settings.margin * 4) - this.settings.length) / (this.settings.numItems - 1);
        $('#menuImg0').removeClass("botonMenuPrincipalAnimado").addClass("botonMenuPrincipal");
        for (var i = 1; i < this.settings.numItems; i++) {
            var value = step * i;
            $('#menuItem' + i).css("display", "block").animate({ width: value }, 500);
        }
        $('#pielegal').css("z-index", "1");
        $('#menuImg0').off("click").on("click", function () { Menu.collapse(); });
    },
    collapse: function () {
        this.settings.status = "collapsed";
        this.settings.collapse = true;
        for (var i = 1; i < this.settings.numItems; i++) {
            $('#menuItem' + i).animate({ width: 0 }, 500, "linear", function () {
                $(this).css("display", "none");
            });
        }
        $('#pielegal').css("z-index", "10");
        $('#menuImg0').off("click").on("click", function () { Menu.expand(); });
        $('#menuImg0').addClass("botonMenuPrincipalAnimado").removeClass("botonMenuPrincipal");
    }
};

class item {
    constructor(name, image, css, onclick) {
        $.get(image).done(function () { }).fail(function () { });
        this.image = image;
        this.name = name;
        this.css = css;
        this.onclick = onclick;
    }
}

function alCargar() {
    Menu.init();
    Menu.settings.orientation = "horizontal";
    Menu.settings.size = 6;

    var unItem1 = new item("aguacada", "./RESOURCES/imagentitulo.png", { "background": "rgba(0,0,0,0)" }, function () { Menu.expand(); });
    Menu.addItem(unItem1);

    var unItem2 = new item("phone", "./RESOURCES/phone.png", { "background": "#003366", "display": "none" }, function () { window.location.href = 'tel:629077064'; });
    Menu.addItem(unItem2);

    var unItem3 = new item("mail", "./RESOURCES/mail.png", { "background": "#a72300", "display": "none" }, function () { window.open('mailto:info@aguacadagesfincas.es', 'mail'); });
    Menu.addItem(unItem3);

    var unItem4 = new item("whatsapp", "./RESOURCES/whatsapp.png", { "background": "#00db0b", "display": "none" }, function () { 
        var esMovil = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
        var url = esMovil ? 'https://wa.me/34629077064' : 'https://web.whatsapp.com/send?phone=34629077064&app_absent=0';
        window.open(url, 'whatsApp'); 
    });
    Menu.addItem(unItem4);

    Menu.deploy();

    setTimeout(function () { Menu.expand(); }, 5000);
    setTimeout(function () { Menu.collapse(); }, 10000);
}

function activarAnimacionesInicio() {
    setTimeout(function () {
        $("#panelInicio .fincas-animar").addClass("aparecer");
    }, 150);
}

/// ==========================================
// CONTROL DE BLOQUES ANIMADOS: DESPLAZAMIENTO VERTICAL (ELEVADO AL 35%)
// ==========================================
let intervaloAnimacionInicio = null;
let indiceBloqueActual = 0;

function animarInicio() {
    const $bloques = $('#panelInicioInt .bloque-servicio');
    if (!$bloques.length) return;

    var $bloqueActual = $bloques.eq(indiceBloqueActual);
    
    var siguienteIndice = (indiceBloqueActual + 1) % $bloques.length;
    var $siguienteBloque = $bloques.eq(siguienteIndice);

    $siguienteBloque.css({
        top: '120%',
        opacity: 0,
        display: 'block'
    });

    $bloqueActual.removeClass('activo').animate({
        top: '-20%',
        opacity: 0
    }, 800, function() {
        $(this).css({ display: 'none', top: '35%', opacity: 1 });
    });

    $siguienteBloque.addClass('activo').animate({
        top: '35%',
        opacity: 1
    }, 800, function() {
        indiceBloqueActual = siguienteIndice;
    });
}

function iniciarAnimacionInicio() {
    const $bloques = $('#panelInicioInt .bloque-servicio');
    if (!$bloques.length) return;

    $bloques.hide().removeClass('activo').css({ top: '35%', opacity: 1 });
    $bloques.eq(indiceBloqueActual).show().addClass('activo').css({ top: '35%', opacity: 1 });

    detenerAnimacionInicio();
    intervaloAnimacionInicio = setInterval(animarInicio, 8000);
}

function detenerAnimacionInicio() {
    if (intervaloAnimacionInicio) {
        clearInterval(intervaloAnimacionInicio);
        intervaloAnimacionInicio = null;
    }
}

function aceptarCookies() {
    localStorage.setItem('cookiesAceptadas', 'true');
    const banner = document.getElementById('avisoCookies');
    if (banner) {
        banner.style.display = 'none';
        alCargar();
    }
}

/////// jQuery document ready
$(document).ready(function () {
    $(".slider").children("div").css("display", "none");
    $(".slider").children("div").addClass("sliderPanel");
    $(".slider div:first").css("display", "block");
    $(".slider div:first").attr("status", "active");
    $(".slidercontrol").click(slide);

    var $btnInicial = $("#tablabarra td[ind='0']");
    if ($btnInicial.length) {
        $("#tablabarra td").removeClass('botonBarraSeleccionada').addClass('botonBarra');
        $btnInicial.removeClass('botonBarra').addClass('botonBarraSeleccionada');
    }

    var $panelInicio = $("#panelInicio");
    if ($panelInicio.length) {
        var id = $panelInicio.attr('id');
        var layout = $panelInicio.attr('layout');
        if (typeof loadLayout === 'function' && layout) {
            loadLayout(layout, id);
        }
    }

    activarAnimacionesInicio();
    iniciarAnimacionInicio();

    if (localStorage.getItem('cookiesAceptadas') === 'true') {
        const banner = document.getElementById('avisoCookies');
        if (banner) {
            banner.style.display = 'none';
        }
        alCargar();
    } 

    const btnAvisoLegal = document.getElementById('pielegal');
    if (btnAvisoLegal) {
        btnAvisoLegal.addEventListener('click', function () {
            if (typeof loadLayout === 'function') {
                loadLayout('panelAvisoLegal', 'panelAuxiliar');
            }
            const panelAux = document.getElementById('panelAuxiliar');
            if (panelAux) {
                panelAux.style.display = 'flex';
            }
        });
    }
    const btnWhatsapp = document.getElementById('btnWhatsapp');
    if (btnWhatsapp) {
        btnWhatsapp.addEventListener('click', function () {
            var esMovil = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
            if (esMovil) {
                window.location.href = 'https://wa.me/34629077064';
            } else {
                window.open('https://web.whatsapp.com/send?phone=34629077064&app_absent=0', '_blank');
            }
        });
    }
});