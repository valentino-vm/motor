<script type = "text/JavaScript"> 
    function change_option(filter, id){
        for (var i = 0; i < filter.length; i++) {
            var filter_select= document.getElementById(id);
            filter_select.disabled = false;

            var new_option= document.createElement('option');
            new_option.value=filter[i];
            new_option.text= filter[i];
            filter_select.add(new_option);
        }
        
    }
1
    function defaultv(id, default_op){
        var filter_select = document.getElementById(id);
        var new_option= document.createElement('option');
        new_option.value= default_op;
        new_option.text= default_op;
        filter_select.add(new_option);
        filter_select.value = default_op;
        filter_select.disabled = true;
    }

    function enable_rest_filters(id_filters){
        for (var i = 0; i < id_filters.length; i++){
            var filter_select1= document.getElementById(id_filters[i]);
            filter_select1.disabled = false;
        }
    }

    function add_username(username){
        var user = document.getElementById("username");
        user.textContent = username;
    }

</script>

<?php

    //___________________________________________________________________________________________________________

    // ---> Llamar a funciones esenciales para el funcionamiento adecuado de la página.

    //Inicio de sesión
    session_start(); 

    //Llamar siempre a la reload() con parámetro 0 para examinar si la página ha sido recargada o si el usuario 
    //ha presionado el botón de retroceso.
    reload(0); 
    
    //Llamar siempre a la función load_url() para almacenar en un array las URLs que ya han sido visitadas en
    //forms.php.
    load_url();
    
    //___________________________________________________________________________________________________________

    // ---> Se obtienen los datos que el formulario HTML ha recolectado.
    
    /*
    $api_format: contiene el formato necesario para construir la URL que llama a la API. Por ejemplo, si la URL para 
    llamar a la API que devuelve los modelos de una marca específica es https://motorleads-api-d3e1b9991ce6.herokuapp.com/api/v1/makes/<make_id>/models, 
    entonces el formato de la API sería "makes, marca, models". Cada elemento separado por comas que sea par en este formato 
    representa un filtro específico para el cual se necesita proporcionar un ID para obtener una salida deseada. 

    Por ejemplo, para obtener los modelos de una marca, necesitas proporcionar el ID de la marca específica. En el 
    formato "makes, marca, models", "marca" es el segundo elemento (contando desde 1), que es par. Esto se aplica a 
    todas las demás URLs de la API, donde el elemento par indica el filtro para el cual necesitas proporcionar un ID 
    para obtener la salida deseada. 

    Nota: los elementos pares de este mismo formato también se refieren a los identificadores (IDs) de los elementos de 
    selección (select) en el HTML. En otras palabras, cada ID de select en el HTML se alinea con un elemento en una 
    posición par en el $api_format.
    */
    $api_format = $_GET['api_format'] ?? null;

    /*
    $wanted_values: se utiliza para determinar qué información específica deseas obtener de un filtro en particular
    que ha sido seleccionado. Por ejemplo, cuando la API devuelve el nombre de todas las marcas de automóviles, 
    puedes querer obtener su ID, nombre o el ID del año del carro. Esta variable te permite hacer más flexible la 
    extracción de información, permitiéndote cambiar los valores específicos que deseas obtener de cada filtro en 
    cualquier momento.
    */
    $wanted_values = $_GET['wanted_values'] ?? null;

    /*
    $selectedFilter: representa la opción seleccionada del filtro utilizado. Esto significa que es el valor que el 
    usuario ha elegido de entre las opciones disponibles en el filtro. Por ejemplo, si el filtro es una lista desplegable 
    de marcas de automóviles, "$selectedFilter" contendría la marca de automóvil seleccionada por el usuario.
    */
    $selectedFilter = $_GET['selectedFilter'] ?? null;

    //___________________________________________________________________________________________________________

    // ---> Seleccionar qué acciones se van a realizar dependiendo de las variables recolectadas anteriormente.

    /*
    Si ninguna de las tres variables recolectadas anteriormente tiene un valor, significa que el archivo forms.php se
    ha llamado desde login.php, lo que indica que el proceso del formulario está comenzando. Por lo tanto, se llama a 
    la función get_marca() para obtener las marcas disponibles. Luego, se invoca al formulario para que se muestre y se 
    utiliza add_select() para agregar las marcas disponibles al elemento de selección (select) de marcas en HTML.
    */
    if ($selectedFilter==null && $wanted_values == null && $selectedFilter == null){
        get_marca();
        call_forms();
        add_select($_SESSION["marca"], "marca");
    }

    /*
    Cuando $wanted_values no tiene un valor especificado, significa que no hay necesidad de solicitar más información a la 
    API. Esto sucede cuando el usuario ya ha seleccionado los filtros que no requieren información adicional de la API, 
    como el color y el kilometraje. En este punto, estos filtros se guardan en la variable de sesión llamada "specified filts". 
    Además, en este momento, $api_format contiene solo los identificadores (IDs) de los elementos de selección (select) 
    correspondientes en el HTML.

    Finalmente, el flujo de ejecución se dirige hacia graph.php.
    */
    else if ($wanted_values == null) {
        $filts = explode(",", $selectedFilter);
        $keys = explode(",", $api_format);
        
        for ($i = 0; $i <(count($filts)); $i++){
            $_SESSION['specified_filts'][$keys[$i]] = $filts[$i];
        }

        header('Location:graph.php');
    }

    /*
    Cuando las tres variables recolectadas anteriormente tienen valor, se activa la función select_filter(). Esto ocurre 
    cuando aún faltan por seleccionar filtros que requieren información adicional de la API.
    */
    else{
        select_filter($selectedFilter,$api_format,$wanted_values);
    }

    //___________________________________________________________________________________________________________
    
    //----> Funciones

    /*
    load_url():

        Funcionalidad: Guarda en una variable de sesión las URLS que han sido visitadas a partir de que se comienza
        la visita en forms.php. Esto es necesario para determinar si un usuario ha hecho click al botón de retroceso.
    */

    function load_url(){
        $_SESSION['url_changes'][] =  $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    }

    /*reload($option):

        Funcionalidad: Verifica tres escenarios donde es necesario reiniciar las variables de sesión y comenzar el
        proceso de recolectar información nuevamente en el forms desde cero. Los tres escenarios son:

            - Escenario 1: Cuando la página ha sido refrescada.

            - Escenario 2: Cuando $option es igual a 1 (Opción utilizada cuando se seleccionan opciones del select como
            "null" u "otro" y la API no posee información para esas opciones).

            - Escenario 3: Cuando el usuario ha presionado el botón de retroceso. Esto es necesario ya que las variables
            de sesión no cambian a pesar de haber presionado el botón de retroceso lo que provoca que las variables 
            se sobrescriban.

            (in_array($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'], $_SESSION['url_changes'])) Verifica si la URL actual
            ya ha sido almacenada en el array "url_changes". Si ya ha sido almacenada es porque el usuario retrocedió la
            página, accediendo nuevamente a una URL que ya había visitado.
        
        Parámetros de entrada:
            - $option: 1 para el Escenario 2, 0 o cualquier otro valor para Escenario 1 o 3
    */

    function reload($option){
        $is_page_refreshed = (isset($_SERVER['HTTP_CACHE_CONTROL']) && $_SERVER['HTTP_CACHE_CONTROL'] == 'max-age=0');
    
        if($is_page_refreshed || $option == 1 || (in_array($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'], $_SESSION['url_changes']))) {

            //Obtener valores de inicio de sesión del usuario antes de destruir la sesión.
            $email = $_SESSION['email'];
            $contrasena = $_SESSION['contrasena'];

            //Se destruye la sesión para evitar que las variables de sesión se sobreescriban.
            session_destroy(); 

            //A continuación se llama a login.php ya que ahí se vuelve a iniciar la sesión y se reinician todas
            //las variables de sesión necesarias. Se mandan con el correo y la contraseña de la sesión anterior.
            echo"
            <script>
            url = 'http://localhost/motor/login.php?email=".$email."&contrasena=".$contrasena."'; 
            location.href=url;
            </script>";
        } 
    }

    /*
        get_marca():

            Funcionalidad: Llama a la API para recibir la información del nombre y los IDs de todos los carros disponibles.
            Posteriormente, guarda estos datos en las variables de sesión "marca" y "id_marca".
    */

    function get_marca(){
        $url = "https://motorleads-api-d3e1b9991ce6.herokuapp.com/api/v1/makes";
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($curl);

        if(curl_errno($curl)){
            $error_msg =curl_error($curl);
            echo"Error al conectarse a la API";
        }

        else{
            curl_close($curl);
            $marcas_data = json_decode($response, true);

            foreach ($marcas_data as $data) {
                //Guardar en las variables de sesión las marcas y sus IDs correspondientes.
                $_SESSION['marca'][] = $data["name"];
                $_SESSION['id_marca'][$data["name"]] = $data["id"];
            }
        }
    }

    /*
        enable($ids_form_nonapi):

            Funcionalidad: Llama a una función de JavaScript para que habilite los campos que no están afectarán 
            la búsqueda del carro. Para este caso son únicamente "color" y "kilometraje". Estos pueden aumentar o 
            cambiar modificando la variable de sesión "ids_form_nonapi". Esta variable representa un array de 
            IDs de elementos de selección HTML (select).
        
            Parámetros de entrada:
                -$ids_form_nonapi: representa un array que contiene los IDs de los elementos select que se desean habilitar.
    */
    function enable($ids_form_nonapi){

        $ids_form_nonapi = json_encode($ids_form_nonapi);
        echo "<script type='text/javascript'>enable_rest_filters(".$ids_form_nonapi.");</script>";
    }

    /*
        select_filter($filters, $api_format, $atribute):
            Funcionalidad:
    */

    function select_filter($filters, $api_format, $atribute){

        //__________________________________________________________________________________________________
        // ---> Añadir filtro recien elegido a la variable de sesión "specified_filts"

        $ids = array(); //array donde se guardarán los IDs necesarios para construir la URL de la API
        $api_format = explode(",", $api_format); //$api_format pasa a ser una lista

        //Se itera sobre $api_format pero únicamente se realiza una acción para los elementos que tengan
        //una posición par, comenzando por 1.
        for ($i = 0; $i <(count($api_format)); $i++){

            //Si el elemento de $api_format es par (contando desde 1) entonces se trata o de un filtro
            //que se acaba de seleccionar y que necesita guardarse en la variable "specified_filts o 
            //de un filtro que ya se seleccionó. Para ambos casos sus IDs se necesitan para construir 
            //la URL para la llamada a la API
            if ((($i+1) % 2) == 0){ 
                $id_filter = $api_format[$i];
                
                //Se verifica si el filtro ya ha sido agregado a la variable "specified_filts"
                //Si ya existe este paso se omite
                if (!array_key_exists($id_filter, $_SESSION['specified_filts'])) {
                    //añadir filtro recien elegido a la variable "specified_filts"
                    $_SESSION['specified_filts'][$id_filter] = $filters; 
                } 

                //Guardar en la variable $spec_filt el flltro elegido por el usuario que coincida con $id_filter.
                $spec_filt = $_SESSION['specified_filts'][$id_filter];

                //Error en caso de que el usuario haya seleccionado "null" como respuesta a un filtro.
                if($spec_filt == "null"){
                    echo "<script type='text/javascript'> alert('No se posee información del vehículo seleccionado. Intente de nuevo.');</script>";
                    reload(1);
                }


                //Agregar a una lista los IDs que son necesarios para llamar a la API.
                //Aquí se busca el ID en la variable donde se guardan las IDs de los filtros. 
                $ids[] =  $_SESSION["id_".$id_filter][$spec_filt]; 
            }
        }

        //__________________________________________________________________________________________________
        // ---> Construir la URL para llamar a la API

        //Construcción base de la URL
        $url = "https://motorleads-api-d3e1b9991ce6.herokuapp.com/api/v1";
        $j = 0;

        //Se itera sobre todo el formato específicado para la construcción de la URL de la API.
        for ($i = 0; $i < (count($api_format)); $i++){
            //Si se está iterando sobre un elemento de $api_format que es impar entonces al URL 
            //solo se le agrega el element impar.
            if ((($i+1) % 2) != 0){
                $url = $url."/".$api_format[$i];
            }

            //Si se está iterando sobre un elemento de $api_format que es par entonces al URL
            //se le agrega uno de los IDs necesarios de los filtros que ya han sido seleccionados.
            else{
                $url = $url."/".$ids[$j];
                $j = $j + 1;
            }
        }

        //__________________________________________________________________________________________________
        // ---> Se finaliza las llamadas a la API.
        
        // Si el número de veces que se ha llamado a forms.php es igual al número de filtros que dependen
        // de una llamada a la API, entonces la URL restante es la necesaria para generar la información
        // para la página de resultado y ya no es necesario seguir llamandola en forms.php
        if($_SESSION['iterate'] + 1 >= count($_SESSION['ids_form'])){

            $_SESSION['finalUrl'] = $url; //Se guarda en una variable de sesión la URL final para los resultados.
            call_forms(); //Se llama al formulario HTML
            //Se llama a update() para actualizar los datos mostrados en el formulario.
            update();
            //Se habilitan los select de los filtros que no dependen de la llamada a la API
            enable($_SESSION['ids_form_nonapi']); 
        }

        //__________________________________________________________________________________________________
        // ---> Se llama a la API.

        else{
            // La variable $next_filt representa el siguiente filtro que se habilitará. Las opciones posibles 
            // para este filtro serán obtenidas mediante la llamada a la API que se hará a continuación-
            $next_filt =  $_SESSION['ids_form'][$_SESSION['iterate'] + 1]; 
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($curl); //respuesta de la API

            //Si existe un error al conectarse a la API se informará al usuario.
            if(curl_errno($curl)){
                $error_msg =curl_error($curl);
                echo"Error al conectarse a la API";
                
            }

            //Si se lográ conectarse a la API
            else{
                curl_close($curl);
                $filter_data = json_decode($response, true);

                //Si la respuesta que la API devuelve está vacía o devuelve un error entonces se debe notificar al usuario
                //y recargar la página.
                if (empty($filter_data) || isset($filter_data['errors'])) {
                    echo "<script type='text/javascript'> alert('No se posee información del vehículo seleccionado. Intente de nuevo.');</script>";
                    reload(1);
                }

                //Si la respuesta que devuelve la API es válida entonces se almacena en las variables de sesión el nombre
                // y el ID de todas las posibles opciones del filtro correspondiente.
                else{
                    foreach ($filter_data as $data) {
                        //añadir nombre de marca/modelo/anio/version a la lista correspondiente
                        $_SESSION[$next_filt][] = $data[$atribute]; 
                        //añadir ids de marca/modelo/anio/version al dict correspondiente
                        $_SESSION['id_'.$next_filt][$data[$atribute]] = $data["id"]; 
                    }

                    // Incrementar en 1 la variable que registra el número de iteraciones de llamadas a forms.php
                    $_SESSION['iterate'] = $_SESSION['iterate'] + 1;

                    //Llama a call_forms() para desplegar el formulario en html.
                    call_forms();

                    //Se llama a update() para actualizar los datos mostrados en el formulario.
                    update();
                }
            }
        }
    }

    /*
        call_forms():

            Funcionalidad: Llama a forms.html para desplegar el formulario y llama a una función en JavaScript
            para añadir el nombre de usuario en el header de la página.
    */
    
    function call_forms(){
        include("forms.html");
        echo "<script type='text/javascript'>add_username('".$_SESSION['username']."');</script>";
    }

    /*
        add_select():

            Funcionalidad: Llama a una función en JavaScript para añadir opciones al select que se le indique.

            Parámetros de entrada:
                -$filter: variable de tipo array que representa cada uno de los valores que se desean agregar 
                como opciones al select correspondiente.
                -$id: el ID del select del cual se desean agregar opciones.
    */

    function add_select($filter, $id){
        $filter = json_encode($filter);
        echo "<script type='text/javascript'>change_option(".$filter.",'".$id."');</script>";

    }

    /*
        add_default_option():

            Funcionalidad: Esta funcionalidad en JavaScript permite establecer un valor predeterminado para un elemento 
            'select' de HTML. Lo hace llamando a una función que asigna el valor especificado al 'select'.

            Parámetros de entrada:
                -$option_id: el ID del select del cual se desea agregar un valor predeterminado.
                -$default_option: el valor predeterminado que se desea agregar al select.
    */

    function add_default_option($option_id, $default_option){
        echo "<script type='text/javascript'>defaultv('".$option_id."', '".$default_option."');</script>";
    }

    /*
        update():

            Funcionalidad: Se añade la opción seleccionada por el usuario como predeterminada al select 
            de HTML correspondiente para cada filtro elegido. Luego, se desactiva dicho select para evitar cambios. 
            Este proceso se repite para todos los filtros que ya han sido seleccionados. Posteriormente, las nuevas 
            opciones obtenidas de la API para el siguiente filtr se añaden como selecciones posibles en el select 
            correspondientes. Si el número de filtros seleccionados coincide con el número de filtros que requieren 
            datos de la API, se detiene el ciclo.
    */

    function update(){
        //variable que representa el número de filtros al momento que han sido seleccionados
        $filters = $_SESSION['specified_filts']; 

        for ($i = 0; $i <= ((count($filters))); $i++){  

            //Para cada filtro seleccionado que esté en $filters se llama add_default_option
            //para mostrar como predeterminada en el select correspondiente la opción elegida.
            if($i<count($filters)){
                $id = $_SESSION['ids_form'][$i];
                add_default_option($id, $filters[$id]);
            }

            //Cuando el usuario haya terminado de seleccionar todos los filtros que dependen de 
            //la API, ya no hace falta añadir nuevas opciones la siguente select por lo que el
            //ciclo se rompe.
            else if (count($filters) ==  count($_SESSION['ids_form'])){
                break;
            }

            //Cuando se hayan terminado de agregar los filtros elegidos por el usuario como
            //predeterminados a los selects se procede a habilitar el siguente select y a añadirle 
            //las opciones recolectadas al llamar a la API para que el usuario pueda elegir. 
            else{
                $id = $_SESSION['ids_form'][$i];
                add_select($_SESSION[$id],$id);
            }
        }
    }
?>
