<?php 


function euro_bcv (){
    $arrContextOptions=array(
        "ssl"=>array(
            "verify_peer"=>false,
            "verify_peer_name"=>false,
        ),
    );  
    
    $html = file_get_contents("https://www.bcv.org.ve/", false, stream_context_create($arrContextOptions));

$cadena_de_texto = $html;//'Esta es la frase donde haremos la búsqueda';
$cadena_buscada   = '<span> EUR </span>'; // valor que busca
$posicion_coincidencia = strpos($cadena_de_texto, $cadena_buscada);  //ubica la posicion de la busqueda

//echo $posicion_coincidencia; die;
$posicion_coincidencia=$posicion_coincidencia-7; // le resta 7 para colcoar las etiquetas completa

//echo $posicion_coincidencia;die;


$resultado = substr($html, $posicion_coincidencia, 150);
//echo $resultado; die;
$valor = explode (" ", $resultado);


//var_dump($valor); die;
$resultado = str_replace(",", ".", $valor[48]);
$resultado = round($resultado, 4);
//echo $resultado; die;
	return($resultado);
}

//echo euro_bcv();

?>