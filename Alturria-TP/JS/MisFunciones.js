var arrayCocheras=[101,102,103,104,105,106,201,202,203,204,205,206,301,302,303,304,305,306];
var servidor="php";

function completarUser(){
    $("#usuario").val("User");
    $("#clave").val("1234");
}
function completarAdmin(){
    $("#usuario").val("Admin");
    $("#clave").val("4321");
}


function login(){
    var dataPost = { usuario:$("#usuario").val(),clave: $('#clave').val()};

    var funcionAjax=$.ajax({
        type:"POST",
        url:servidor+"/login/",
        data: dataPost,
        dataType: 'json'
        }).then(function(retorno){
            if (typeof(Storage) !== "undefined") {
                localStorage.setItem('tokenUTNFRA', retorno);
                window.location.replace("./main.html");
            } else {
               console.log("Sorry! No Web Storage support..");
            }
        },function(error){
            console.log(error);
            alert((error.responseJSON).error);
        });
}

function ingresarVehiculo(){
    var marca = $("#marca").val();
    var patente = $("#patente").val();
    var color = $("#color").val();
    var cochera = $("#cochera").val();
    
    var token = localStorage.getItem('tokenUTNFRA');

    if(marca != '' && patente != '' && color != '' && cochera != '' ){
        var dataPost = { marca:marca,patente:patente,color:color,cochera:cochera,token:token};
        var funcionAjax=$.ajax({
            type:"POST",
            data: dataPost,
            dataType: 'json',
            url:servidor+"/administracion/ingresarVehiculo"
            }).then(function(retorno){
                console.log("volvio de  ingresar Vehiculo" + retorno.responseText);
                cocherasLibres();
                cargarGrilla();
            },function(error){
                alert("error al ingresar vehiculo: "+ error.responseText);
            });
    }
}

function sacarVehiculo(){
    var patente = $("#patenteSalidaOculto").val();
    console.log("sacando a patenteSalidaOculto "+patente);
    var token = localStorage.getItem('tokenUTNFRA');

    if(patente != ''){
        var dataPost = { patente:patente,token:token};
        var funcionAjax=$.ajax({
            type:"POST",
            data: dataPost,
            dataType: 'json',
            url:servidor+"/administracion/sacarVehiculo"
            }).then(function(retorno){
                console.log("volvio de  sacar Vehiculo" + retorno);
                cocherasLibres();
                cargarGrilla();
            },function(error){
                alert("error al ingresar vehiculo: "+ error.responseText);
            });
    }
}

function cocherasLibres(){
    $(document).ready(function () {
        var funcionAjax=$.ajax({
            type:"GET",
            url:servidor+"/administracion/cocherasLibres",
            }).then(function(retorno){
                console.log("volvio ok con las cocheras libres para la LOV");
                $('#cochera').html('');
                for (let i = 0; i < retorno.length; i++) {
                    $('#cochera').append("<option>"+retorno[i]['nroCochera']+"</option>");
                }
            },function(error){
                alert(error);
            });
    });
}

function cargarGrilla(){
    $(document).ready(function () {
        var funcionAjax=$.ajax({
            type:"POST",
            url:servidor+"/administracion/cocherasOcupadas",
            }).then(function(retorno){
                var resto=arrayCocheras;
                var ultimo=retorno.length;
                console.log("volvio ok con las cocheras ocupadas");
                //nroCochera,operaciones.patente,marca,color,entrada
                $('#tabla').html("<thead> <tr>   <th>N°</th>   <th>Cochera</th>   <th>Patente</th>   <th>Marca</th>   <th>Color</th>   <th>Fecha ingreso</th> </tr></thead>");
                for (let i = 0; i < (retorno.length); i++) {
                    $('#tabla').append("<tbody> <tr>" +
                    "<td>" + (i+1) + "</td>" +
                    "<td>" + retorno[i]['nroCochera'] + "</td>" +
                    "<td>" + retorno[i]['patente'] + "</td>" +
                    "<td>" + retorno[i]['marca'] + "</td>" +
                    "<td>" + retorno[i]['color'] + "</td>" +
                    "<td>" + retorno[i]['entrada'] + "</td></tr> </tbody>");
                    resto =  arrayCocheras.filter(function (item) {
                        if (item != retorno[i]['nroCochera'])
                            return item;
                    });
                }
                for (let i = 0; i < (resto.length); i++) {
                    $('#tabla').append("<tbody> <tr>" +
                    "<td>" + (ultimo+1) + "</td>" +
                    "<td>" + resto[i] + "</td>" +
                    "<td>" + ''+ "</td>" +
                    "<td>" + '' + "</td>" +
                    "<td>" + ''+ "</td>" +
                    "<td>" + '' + "</td></tr> </tbody>");
                    ultimo=ultimo+1
                 }
            },function(error){
                alert(error.responseText);
            });
    });
}

function patentesSalida(){
    console.log("1- va a buscar las patentes para salir");
    $(document).ready(function () {
        var funcionAjax=$.ajax({
            type:"POST",
            url:servidor+"/administracion/patentesSalida",
            }).then(function(retorno){
                console.log("2- volvio ok con las patentes para salir");
                $('#patenteSalida').html('');
                for (let i = 0; i < retorno.length; i++) {
                    $('#patenteSalida').append("<option>"+retorno[i]['patente']+"</option>");
                }
            },function(error){
                console.log("3- va a buscar las patentes para salir");
            });
    });
    console.log("4- salio");
}

function confirmarSalidaVehiculo(){
    var patente = $("#patenteSalida").val();
    console.log("obteniendo datos de "+patente);
    var token = localStorage.getItem('tokenUTNFRA');

    if(patente != ''){
        var dataPost = { patente:patente,token:token};
        var funcionAjax=$.ajax({
            type:"POST",
            data: dataPost,
            dataType: 'json',
            url:servidor+"/administracion/confirmarSalidaVehiculo"
            }).then(function(retorno){
                console.log("volvio de confirmarSalidaVehiculo Vehiculo");
                console.log(retorno);
                $("#sacar").show();
                $("#confirmarSacar").hide();
                $('#patenteSalidaOculto').val(patente);
                $('#form-group').html("<h5 id='patenteSalida' value='"+patente+"'><br>Patente: " +patente+"</h5>");
                $('#form-group').append("<br>Marca : "+retorno[0]['marca']);
                $('#form-group').append("<br>Color : "+retorno[0]['color']);
                $('#form-group').append("<br>ingreso : "+retorno[0]['entrada']);
                $('#form-group').append("<br>Horas : "+retorno[1]);
                $('#form-group').append("<br>Costo : "+retorno[2]);

            },function(error){
                alert("error al confirmar salida de auto. Contacte al administrador.");
                console.log(error.responseText);
            });
    }
}
$(document).on('show.bs.modal','#modalSalida', function () {
    $("#form-group").html("<br>Patente<select class='form-control' id='patenteSalida' value=''></select>");
    patentesSalida();
    $("#confirmarSacar").show();
    $("#sacar").hide();
  })

function exportar(){
    var extension=$('#extension').val();
    alert(extension);
}


function altaEmpleado(){
        var nombreAlta = $("#nombreAlta").val();
        var apellidoAlta = $("#apellidoAlta").val();
        var usuarioAlta = $("#usuarioAlta").val();
        var dniAlta = $("#dniAlta").val();
        var adminNuevo = false;
        if($('#esAdminAlta').is(':checked')){
            adminNuevo = true;
        }
        console.log("adminNuevo: "+adminNuevo);
        var clave = $("#claveAlta").val();

        var file_data = $("#idFoto").prop("files")[0];   
        var form_data = new FormData();                  
        form_data.append('file', file_data);
        form_data.append('apellido', apellidoAlta);
        form_data.append('nombre', nombreAlta);
        form_data.append('dni', dniAlta);
        form_data.append('usuario', usuarioAlta);
        form_data.append('clave', clave);
        form_data.append('admin', adminNuevo);
        form_data.append('token', localStorage.getItem('tokenUTNFRA'));
        
        $.ajax({
          type: "POST",
          url: servidor+"/administracion/altaEmpleado",
          data: form_data,
          contentType: false,
          processData: false,
          cache: false
        }).then(function(retorno){
            console.log("volvio de altaEmpleado");
            console.log(retorno);

        },function(error){
            alert("error en altaEmpleado. Contacte al administrador.");
            console.log(error.responseText);
            console.log(error);
        });
}

function cargarDatos(){
    var token = localStorage.getItem('tokenUTNFRA');
    $.ajax({
        type: "POST",
        url: servidor+"/administracion/datosToken",
        data: {token:token}
      }).then(function(retorno){
          console.log("volvio de cargarDatos");
          console.log(retorno);
          $("#headerHola").text("Bienvenido " + retorno.nombre + " "+retorno.apellido);

          $("#imagen").attr('src', "./fotos/"+retorno.foto);
      },function(error){
          alert("error en cargarDatos. Contacte al administrador.");
          console.log(error.responseText);
          console.log(error);
      });
}



function bajaEmpleado(){
    var usuario = $("#bajaEmpleado").val();
    var token = localStorage.getItem('tokenUTNFRA');   
    if(usuario=="Admin"){
        alert("No pueda dar de baja al Admin");
    }else{
        $.ajax({
            type: "POST",
            url: servidor+"/administracion/bajaEmpleado",
            data: {token:token,usuario:usuario}
          }).then(function(retorno){
              console.log("volvio de cargarDatos");
              console.log(retorno);
              $("#headerHola").text("Bienvenido " + retorno.nombre + " "+retorno.apellido);
    
              $("#imagen").attr('src', "./fotos/"+retorno.foto);
          },function(error){
              alert("error en cargarDatos. Contacte al administrador.");
              console.log(error.responseText);
              console.log(error);
          });
    }

}