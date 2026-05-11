<?php 
function usd_bcv (){
$html = file_get_contents('http://www.bcv.org.ve/'); //Convierte la información de la URL en cadena

$cadena_de_texto = $html;//'Esta es la frase donde haremos la búsqueda';
$cadena_buscada   = 'Bs/EUR'; // valor que busca
$posicion_coincidencia = strpos($cadena_de_texto, $cadena_buscada);  //ubica la posicion de la busqueda
$posicion_coincidencia=$posicion_coincidencia-7; // le resta 7 para colcoar las etiquetas completa

//echo $posicion_coincidencia;


$resultado = substr($html, $posicion_coincidencia, 130);
//echo $resultado;
$valor = explode (" ", $resultado);

//var_dump($valor); // imprime "uebacadenas"

/*echo('<pre>');
var_dump($valor);
echo('</pre>');*/

/*echo $valor[31];
echo '</br>';
echo date('d-m-yy');*/

	return($valor[30]);
}

echo usd_bcv();

?>