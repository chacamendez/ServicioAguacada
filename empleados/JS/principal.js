var imagenes=new Array('./fotos/aguacadaimagen.bmp','./fotos/atencioncliente2.jpeg','./fotos/presupuestos2.jpeg','./fotos/cobrocuotas2.jpg','./fotos/revisionedificios.jpeg','./fotos/gestionadministrativa.jpeg','./fotos/custodiadocumentos.jpeg','./fotos/coberturaprofesional.jpeg');
var labeltitulos=new Array('AGUACADAGESFINCAS','ATENCI�N AL PROPIETARIO','ELABORACI�N DE PRESUPUESTO ORDINARIO','COBRO DE CUOTAS','REVISI�N DE EDIFICIOS','GESTIONES CON LAS ADMINISTRACIONES P�BLICAS','CUSTODIA DE DOCUMENTACI�N','COBERTURA PROFESIONAL');
var labelexplicaciones=new Array('AguacadaGesfincas es una empresa joven fundada en el 2006, dedicada principalmente a la administraci�n de fincas y a la gesti�n �ntegra de comunidades. Dirigida por Carmen Teresa M�ndez Alonso, Administradora de fincas colegiada y titulada en administraci�n de empresas, con una amplia experiencia en el sector.',
'Ponemos a disposici�n de nuestros clientes diversas maneras para cumplir eficientemente con nuestros servicios. Presencialmente o a trav�s del tel�fono en nuestro horario de oficina y las 24 horas a trav�s de la zona clientes de nuestra p�gina web.',
'Elaboramos todos los presupuestos necesarios para el mantenimiento de su comunidad, con la ventaja que supone trabajar con profesionales que rebajan precios por volumen de contrataci�n.',
'Elaboraci�n de remesas mensuales para el cobro domiciliado de las cuotas de la comunidad, as� como la posiblilidad de pagar en efectivo en nuestra oficina.',
'Revisi�n mensual de las instalaciones de la comunidad con el objetivo de detectar anomal��as. Tambi�n ponemos a disposici�n del propietario varias formas para comunicar incidencias, telef�nicamente durante el horario de apertura de nuestra oficina y las 24 horas desde la zona de clientes de nuestra p�gina web.',
'Realizamos todas las gestiones que la comunidad necesite con las administraciones p�blicas. Solicitud de vados, permisos de obra, etc.',
'Custodiamos toda la documentaci�n referente a la comunidad por un m�ximo de cinco a�os, periodo establecido por ley. Adem�s tendr� la posiblidad de acceder a toda esta documentaci�n a trav�s de la zona de clientes de nuestra p�gina web.','La empresa cuenta con un seguro de responsabilidad civil, para cualquier contigencia que pudiera surgir.');
var numero = 1;
var primera=true;
var cambio=true;

function cambiarImagen () {

	var imagen=document.getElementById('imagen');
	imagen.src=imagenes[numero];
}
function cambiarBackgroundImagen () {
	var backgroundImagen=document.getElementById('backgroundimagen');
	backgroundImagen.style.backgroundImage="url("+ imagenes[numero] +")";
}
function changeOpacity(id,msDuration,msStart,fromO,toO)
{
  var element=document.getElementById(id);
  var opacity = element.style.opacity * 100;
  var msNow = (new Date()).getTime();
  opacity = fromO + (toO - fromO) * (msNow - msStart) / msDuration;
  if (opacity<0) {
	  setOpacity(element,0);
  }

  else if (opacity>100)
    setOpacity(element,100)
  else
  {
    setOpacity(element,opacity);
    element.timer = window.setTimeout("changeOpacity('" + id + "'," + msDuration + "," + msStart + "," + fromO + "," + toO + ")",1);
  }
}
function fadeIn(id)
{
  var element=document.getElementById(id);
  if (element.timer) window.clearTimeout(element.timer);
  var startMS = (new Date()).getTime();
  element.timer = window.setTimeout("changeOpacity('" + id + "',1000," + startMS + ",0,100)",1);
}
function fadeOut(id)
{
  var element=document.getElementById(id);
  if (element.timer) window.clearTimeout(element.timer);
  var startMS = (new Date()).getTime();
  element.timer = window.setTimeout("changeOpacity('" + id + "',1000," + startMS + ",100,0)",1);
}
function presentacion(selector) {
	switch (selector) {
	case 1:
		if (!cambio){
			var labeltitulo=document.getElementById('labeltitulo');
			var labelexplicacion=document.getElementById('labelexplicacion');
			fadeOut(labeltitulo.id);
			fadeOut(labelexplicacion.id);
		}
		else cambio=false;
		if (numero<=(imagenes.length-1)){
			if (numero%2==0)	 window.setTimeout('presentacion(2)',1000);
			else window.setTimeout('presentacion(3)',1000);
		}
		else {
			if (numero%2==0) window.setTimeout('presentacion(2)',1000);
			else {
				window.setTimeout('presentacion(4)',1000);
				cambio=true;
			}
			numero=0;
		}
		break;
	case 2:
		cambiarImagen();
		var labeltitulo=document.getElementById('labeltitulo');
		var labelexplicacion=document.getElementById('labelexplicacion');
		labeltitulo.innerHTML=labeltitulos[numero];
		labelexplicacion.innerHTML=labelexplicaciones[numero];
		fadeIn('imagen');
		labeltitulo.innerHTML=labeltitulos[numero];
		labelexplicacion.innerHTML=labelexplicaciones[numero];
		fadeIn(labeltitulo.id);
		fadeIn(labelexplicacion.id);
		numero+=1;
		if (numero%2!=0) window.setTimeout('presentacion(1)',5000);
		else window.setTimeout('presentacion(4)',5000);
		break;
	case 3:
		if (!primera) cambiarBackgroundImagen();
		else primera=false;
		var labeltitulo=document.getElementById('labeltitulo');
		var labelexplicacion=document.getElementById('labelexplicacion');
		labeltitulo.innerHTML=labeltitulos[numero];
		labelexplicacion.innerHTML=labelexplicaciones[numero];
		fadeIn(labeltitulo.id);
		fadeIn(labelexplicacion.id);
		fadeOut('imagen');
		numero+=1;
		if (numero%2==0) window.setTimeout('presentacion(1)',5000);
		else window.setTimeout('presentacion(4)',5000);
		break;
	case 4:
		if (!cambio){
			var labeltitulo=document.getElementById('labeltitulo');
			var labelexplicacion=document.getElementById('labelexplicacion');
			fadeOut(labeltitulo.id);
			fadeOut(labelexplicacion.id);
		}
		else cambio=false;
		if (numero<=(imagenes.length-1)){
			if (numero%2!=0) window.setTimeout('presentacion(2)',1000);
			else window.setTimeout('presentacion(3)',1000);
		}
		else {
			if (numero%2==0) window.setTimeout('presentacion(4)',1000);
			else {
				window.setTimeout('presentacion(1)',1000);
				cambio=true;
			}
			numero=0;
		}
		break;
	}
}
function setOpacity(object,opacityPct)
{
  // IE.
  object.style.filter = 'alpha(opacity=' + opacityPct + ')';
  // Old mozilla and firefox
  object.style.MozOpacity = opacityPct/100;
  // Everything else.
  object.style.opacity = opacityPct/100;
}
