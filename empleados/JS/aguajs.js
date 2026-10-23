
///////
var menuPrincipal;
function alCargar(){
	//alert($(window).width() + " " + $(window).height());
	Menu.init();
	Menu.settings.orientation="horizontal";
	Menu.settings.size=6;
	var unItem=new item("aguacada","./RESOURCES/imagentitulo.png",{"background":"rgba(0,0,0,0)"},function () {Menu.expand();});
	Menu.addItem(unItem);

	var unItem=new item("phone","./RESOURCES/phone.png",{"background":"#003366","display":"none"},function () {window.location.href='tel:629077064';});
	Menu.addItem(unItem);
	var unItem=new item("mail","./RESOURCES/mail.png",{"background":"#a72300","display":"none"},function () {window.open('mailto:info@aguacadagesfincas.es','mail');});
	Menu.addItem(unItem);
	var unItem=new item("whatsapp","./RESOURCES/whatsapp.png",{"background":"#00db0b","display":"none"},function () {window.open('https://web.whatsapp.com/send?phone=34629077064&app_absent=0','whatsApp')});
	Menu.addItem(unItem);
	Menu.deploy();
	var keys = Object.keys(Menu);
	setTimeout(function (){Menu.expand();},5000);
	//alert(JSON.stringify(Menu));
}

var indexActive=false;
function slide(origen){
			if (typeof(origen)=='object') var index=$(origen).attr("ind");
			else {
				index=origen;
				if (index<4){
					origen=$("#tablabarra").find('td[ind='+index+']');
				}
				else origen=$("#panelLateralContenido").children('div[ind='+index+']');
			}
			console.log("paso");
			console.log(origen);
      var target=$("#contenido").children('div[ind='+index+']');
			var old=$("#contenido").children('div:visible');

			if (!indexActive) indexActive=index;
			else if (indexActive==index) return;


			$("#tablabarra").find('td').removeClass();
			$("#panelLateralContenido").children('div').removeClass();
			$("#tablabarra").find('td').addClass('botonBarra');
			$("#panelLateralContenido").children('div').addClass('panelLateralElement');
			if (index<4){
				$(origen).addClass('botonBarraSeleccionada');
				$(origen).removeClass('botonBarra');
			}
			else{
				$(origen).addClass('panelLateralElementSelected');
				$(origen).removeClass('panelLateralElement');
			}

			var valor=Math.max(0, (($(window).width() - $(target).outerWidth()) / 2) + $(window).scrollLeft()) + "px";
			var outer=$(target).outerWidth();
			var win=$(window).width();
			var left= $(window).scrollLeft();

			//console.log(valor +" " + win + " "+ outer+" "+ left);

			if (indexActive>index) {
				$(target).css("left","-2000px");
				$(target).css("display","block");
				$(old).animate({left: '2000px'},function(){$(old).css("display","none");console.log(old);});
				$(target).animate({left: valor});
				$(target).css("z-index",1);
			}
			else {
				//alert(indexActive+ "Activo es menor target" + index);
				$(target).css("left","2000px");
				$(target).css("display","block");
				$(old).animate({left: '-2000px'},function(){$(old).css("display","none");console.log(old);});
				$(target).animate({left: valor});
				$(target).css("z-index",1);

			}
			indexActive=index;


}

function slideCopia(origen){
			var index=$(origen).attr("ind");
			var seleccionado=$(".botonBarraSeleccionada");
			var indexActive=$(seleccionado).index();
			var target=$("#contenido").children("div").eq(index);
			old=$("#contenido").children("div").eq(indexActive);
			//console.log(target);

			if (indexActive==index)return;




			var valor=Math.max(0, (($(window).width() - $(target).outerWidth()) / 2) + $(window).scrollLeft()) + "px";
			var outer=$(target).outerWidth();
			var win=$(window).width();
			var left= $(window).scrollLeft();

			//console.log(valor +" " + win + " "+ outer+" "+ left);

			if (indexActive>index) {
				$(target).css("left","-2000px");
				$(target).css("display","block");
				$(old).animate({left: '2000px'},function(){$(old).css("display","none");console.log(old);});
				$(target).animate({left: valor});
				$(target).css("z-index",1);
			}
			else {
				//alert(indexActive+ "Activo es menor target" + index);
				$(target).css("left","2000px");
				$(target).css("display","block");
				$(old).animate({left: '-2000px'},function(){$(old).css("display","none");console.log(old);});
				$(target).animate({left: valor});
				$(target).css("z-index",1);

			}
			$(origen).addClass('botonBarraSeleccionada');
			$(origen).removeClass('botonBarra');
			$(seleccionado).addClass('botonBarra');
			$(seleccionado).removeClass('botonBarraSeleccionada');

}
