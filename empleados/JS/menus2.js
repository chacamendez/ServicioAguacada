var s,
Menu = {
  settings: {
    status: "init",
    collapse: true,
    numItems:0,
    items:[],
    orientation:"none",
    position:"none",
    size:0
  },
  init: function() {
    s = this.settings;
  },
  addItem: function(item){
    this.settings.items.push(item);
    this.settings.numItems+=1;
  },
  deploy: function(){
    if (this.numItems<2) {console.log("numero insuficiente de items para un menu");return;}
    $('body').append('<div id=menu style="position:fixed;z-index:1;background-color: rgba(255, 0, 0, 0);opacity:1"></div>');
    this.settings.height= this.settings.orientation=="horizontal" ? ((window.innerHeight/100)*this.settings.size) : (window.innerHeight);
    this.settings.width= this.settings.orientation=="vertical" ? ((window.innerWidth/100)*this.size) : (window.innerWidth);
    this.settings.length= this.settings.height>this.settings.width ? this.settings.width : this.settings.height;
    this.settings.margin=this.settings.length/4;
    $('#menu').css({"height":this.settings.height,"width":this.settings.width,"bottom":this.settings.margin+"px","left":this.settings.margin+"px"});
    $('#menu').append('<div id="menuItem0" style="position:absolute;top:0;left:0"></div>');
    var item=this.settings.items[0];
    $('#menuItem0').append('<img id=menuImg0 class=botonMenuPrincipalAnimado style="float:right;clear:right;position:relative;left:'+this.settings.length/4+'px" src="'+item.image+'" height="'+this.settings.length+'" width="'+this.settings.length+'">');
    $('#menuItem0').css("z-index",this.settings.numItems);
    $('#menuImg0').click(item.onclick);
    $('#menuItem0').css(item.css);
    var position=$('#menuImg0').position();
    for (var i=1;i<this.settings.numItems;i++){
      $('#menu').append('<div id="menuItem'+i+'" style="position:absolute;top:0;left:'+(position.left+this.settings.length/2)+'px"></div>');
      var item=this.settings.items[i];
      $('#menuItem'+i).append('<img id=menuImg'+i+' style="float:right;clear:right;position:relative;left:'+this.settings.length/2+'px" src="'+item.image+'" height="'+this.settings.length+'" width="'+this.settings.length+'">');
      var zIndex=this.settings.numItems-(i+2);
      $('#menuItem'+i).css("z-index",zIndex);
      $('#menuImg'+i).click(item.onclick);
      $('#menuItem'+i).css(item.css);
    }

  },
  expand: function(){
      console.log(this);
    this.settings.status="expanded";
    this.settings.collapse=false;
    var step=(this.settings.width-(this.settings.margin * 4)-this.settings.length)/(this.settings.numItems-1);
    $('#menuImg0').removeClass("botonMenuPrincipalAnimado");
    $('#menuImg0').addClass("botonMenuPrincipal");
    for (var i=1;i<this.settings.numItems;i++){
      var value=step*i;

      $('#menuItem'+i).css("display","block");
      $('#menuItem'+i).animate({ width: value }, 500);
    }
    $('#menuImg0').off("click");
    $('#menuImg0').click(function (){Menu.collapse();});
  },
  collapse: function(){
    this.settings.status="collapsed";
    this.settings.collapse=true;
    for (var i=1;i<this.settings.numItems;i++){
      $('#menuItem'+i).animate({ width: 0 }, 500,"linear", function() {$(this).css("display","none")});
      $('#menuImg0').off("click");
      $('#menuImg0').click(function (){Menu.expand();});
    }
    $('#menuImg0').addClass("botonMenuPrincipalAnimado");
    $('#menuImg0').removeClass("botonMenuPrincipal");
  }
};

class item {
  constructor (name,image,css,onclick){
    $.get(image).done(function() {
      console.log("imagen cargada");

    }).fail(function() {
      console.log("imagen no existe");
    })
    this.image=image;
    this.name=name;
    this.css=css;
    this.onclick=onclick;
  }
}
