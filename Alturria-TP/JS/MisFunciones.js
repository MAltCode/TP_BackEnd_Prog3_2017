var arrayCocheras=[101,102,103,104,105,106,201,202,203,204,205,206,301,302,303,304,305,306];
var servidor="php";

function completarUser(){
    $id('email').value="User";
    $id('clave').value="1234";
}
function completarAdmin(){
    $id('email').value="Admin";
    $id('clave').value="4321";
}


function login(){
    var dataPost = { email:$("#email").val(),clave: $('#clave').val()};

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


function $id(id){
    return document.getElementById(id);
}

function ingresarVehiculo(){
    var marca = $id('marca').value;
    var patente = $id('patente').value;
    var color = $id('color').value;
    var cochera = $id('cochera').value;
    
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
                patentesSalida();
            },function(error){
                alert("error al ingresar vehiculo: "+ error.responseText);
            });
    }
}

function sacarVehiculo(){
    var patente = $id('patenteSalida').value;
    console.log("sacando a "+patente);
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
                $('#tabla').html("<thead> <tr>   <th>Nยบ</th>   <th>Cochera</th>   <th>Patente</th>   <th>Marca</th>   <th>Color</th>   <th>Fecha ingreso</th> </tr></thead>");
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
