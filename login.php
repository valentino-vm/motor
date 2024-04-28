<?php 

session_start();

//________________________________________________________________________________
// ---> Variable para inicio de sesión

$_SESSION['email'] = null;
$_SESSION['contrasena'] = null;
$_SESSION['username'] = null;

//________________________________________________________________________________
// ---> Variables para forms.php

//$_SESSION['url_changes']: Variable de tipo array donde se almacenan las URLs que han sido visitadas desde
//que se ingresa a forms.php.
$_SESSION['url_changes'] = array();

//$_SESSION['ids_form_nonapi']: Variable de tipo array donde se almacenan los filtros que no dependen de una
//llamada a la API.
$_SESSION['ids_form_nonapi'] = array('kilometraje', 'color');

//$_SESSION['id_marca']: Variable de tipo array donde se almacenan todos los IDs de las marcas disponibles.
$_SESSION['id_marca'] = array();

//$_SESSION['marca']: Variable de tipo array donde se almacenan todas las marcas disponibles.
$_SESSION['marca'] = array();

//$_SESSION['id_modelo']: Variable de tipo array donde se almacenan todos los IDs de los modelos disponibles de la 
//marca seleccionada.
$_SESSION['id_modelo'] = array();

//$_SESSION['modelo']: Variable de tipo array donde se almacenan todos los modelos disponibles de la marca seleccionada.
$_SESSION['modelo'] = array();

//$_SESSION['id_anio']: Variable de tipo array donde se almacenan todos los IDs de los años disponibles del modelo 
//seleccionado.
$_SESSION['id_anio'] = array();

//$_SESSION['anio']: Variable de tipo array donde se almacenan todos los años disponibles del modelo seleccionado.
$_SESSION['anio'] = array();

//$_SESSION['id_version']: Variable de tipo array donde se almacenan todos los IDs de las versiones disponibles
//del modelo seleccionado del año seleccionado.
$_SESSION['id_version'] = array();

//$_SESSION['version']: Variable de tipo array  donde se almacenan todas las versiones disponibles del modelo 
//seleccionado del año seleccionado.
$_SESSION['version'] = array();

//$_SESSION['ids_form']: Variable de tipo array que representa los filtros que dependen de la llamada a la API.
$_SESSION['ids_form'] = array('marca', 'modelo', 'anio', 'version');

//$_SESSION['iterate']: Variable de control que representa el número de veces que se ha llamado a forms.php.
//Nota: Esta variable sirve para reconocer cuándo el usuario ha terminado de seleccionar los filtos que dependen 
//de la API.
$_SESSION['iterate'] = 0;

//$_SESSION['specified_filts']: Variable de tipo array donde se guardan los filtros que el usuario ha seleccionado. 
$_SESSION['specified_filts'] = array();

//________________________________________________________________________________
// ---> Variables para graph.php

$_SESSION['finalUrl'] = null;
$_SESSION['graph_info'] = array(array());
$_SESSION['sales_info'] = array();

//________________________________________________________________________________
// ---> Recibir valores de inicio de sesión

$_SESSION['email'] = $_GET['email'] ?? null;
$_SESSION['contrasena'] = $_GET['contrasena'] ?? null;

//________________________________________________________________________________
// ---> Construir nombre de usuario a partir del correo electrónico.

$user = explode("@", $_SESSION['email']);
$_SESSION['username'] = $user[0];

//________________________________________________________________________________
// ---> Redirigir a forms.php para comenzar con el formulario.

echo "
<script>
var deletingAll = browser.history.deleteAll();
deletingAll.then(onDeleteAll);
</script>";

header('Location:forms.php');





?>