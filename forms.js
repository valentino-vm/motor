function send_marcas(){
    marca = document.form1.marca.value;
    api_format = "makes,marca,models";
    wanted_values= "name";
    url = "http://localhost/motor/forms.php?api_format="+api_format+"&wanted_values="+wanted_values;
    url = url+"&selectedFilter="+marca;
    location.href=url;
    

}

function send_modelos(){
    modelo = document.form1.modelo.value;
    api_format = "models,modelo,years";
    wanted_values= "name";
    url = "http://localhost/motor/forms.php?api_format="+api_format+"&wanted_values="+wanted_values;
    url = url+"&selectedFilter="+modelo;
    location.href=url;
}


function send_anio(){
    anio = document.form1.anio.value;
    api_format = "models,modelo,years,anio,vehicles";
    wanted_values= "version";
    url = "http://localhost/motor/forms.php?api_format="+api_format+"&wanted_values="+wanted_values;
    url = url+"&selectedFilter="+anio;
    location.href=url;
}

function send_version(){
    version = document.form1.version.value;
    months = "3";
    api_format = "vehicles,version,pricings?filter[since]="+months;
    wanted_values= "sale_price_variation,sale_price_percentage_variation,purchase_price_variation,purchase_price_percentage_variation,medium_price_variation,medium_price_percentage_variation";
    url = "http://localhost/motor/forms.php?api_format="+api_format+"&wanted_values="+wanted_values;
    url = url+"&selectedFilter="+version;
    location.href=url;
}

function send_rest_values(){
    api_format = "kilometraje,color";
    kilometraje = document.form1.kilometraje.value;
    version = document.form1.version.value;
    anio = document.form1.anio.value;
    modelo = document.form1.modelo.value;
    color = document.form1.color.value;
    marca = document.form1.marca.value;
    
    if(kilometraje =='' || color == ''){
        alert("Por favor, seleccione todos los filtros necesarios antes de proceder.");
    }
    
    else{
        datos = kilometraje + "," + color;
        url = "http://localhost/motor/forms.php?selectedFilter="+datos+"&api_format="+api_format;
        location.href=url;
    }
}



