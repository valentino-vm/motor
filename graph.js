function handlePeriodoChange(){
    let periodoSeleccionado = document.getElementById("periodo").value;
    console.log(periodoSeleccionado);
    url = 'http://localhost/motor/graph.php?required_months='+periodoSeleccionado;
    location.href = url; 
}