<?php

function usb_bcv2 (){
   $arrContextOptions=array(
        "ssl"=>array(
            "verify_peer"=>false,
            "verify_peer_name"=>false,
        ),
    );   
    $response = file_get_contents('http://www.bcv.org.ve/', false, stream_context_create($arrContextOptions));
    // Si falla, file_get_contents() devolverá false
    echo $response;
}

function dia_feriado($valor){
    $feriados=array(

        //ACTUALIZADO PARA EL 2025 )JSANTANA=

                //ENERO
                '01-01', 
                '01-12', 
                '01-19', 
        
                //FEBRERO
                '02-16',
                '02-17',
        
                //MARZO
                //'03-03', 
               // '03-04', 
                '03-19', 
        
                //ABRIL
                '04-02', 
                '04-03', 
                '04-19',
        
                //MAYO
                '05-01',
                '05-18',
                
                //JUNIO        
                '06-08',
                '06-13',
                //'06-23',
                '06-24',
                '06-29',
        
                //JULIO
                '07-05',
                '07-24',
        
                //AGOSTO
                '08-15',
        
                //SEPTIEMBRE
                '09-14',
        
                //OCTUBRE
                '10-12',
                '10-26',
        
                //NOVIEMBRE
                '11-01',
                '11-23',
        
                //DICIEMBRE
                '12-14',
                '12-24',
                '12-25',
                '12-31',
        
    );

    $hoyCalculado=date("m").'-'.date("d");
    $esferiado = array_search($hoyCalculado, $feriados);
    /* SI $NOesferiado trae valor siginifia que es un dia feriado*/


    if ($esferiado==''){
       
        $day = date("l");
       
        switch ($day) {
            case "Sunday":
                //para el sabado (NO FERIADO) calcula la fecha calendario pro que en la pagina del BCV aparece la fecha del lunes
                $diaString=date("Y").'-'.date("m").'-'.date("d");  
            break;
            case "Saturday":
                //para el sabado (NO FERIADO) calcula la fecha calendario pro que en la pagina del BCV aparece la fecha del lunes
                $diaString=date("Y").'-'.date("m").'-'.date("d");
            break;
            default:
                // en caso que no sea feriado ni fin de semana toma la fecha de la pagian de BCV
                $diaString= substr($valor[5], 9,10);
            break;
        }
         
    }else{
        //$diaString= substr($valor[5], 9,10);
        //FECHA CALENDARIO
        $diaString=date("Y").'-'.date("m").'-'.date("d");
             
    }

    return($diaString);
}

function usd_bcv (){
	
    //$html = file_get_contents('https://www.bcv.org.ve/'); //Convierte la información de la URL en cadena

    $arrContextOptions=array(
        "ssl"=>array(
            "verify_peer"=>false,
            "verify_peer_name"=>false,
        ),
    );  
    
    $html = file_get_contents("https://www.bcv.org.ve/", false, stream_context_create($arrContextOptions));
    
    //Si falla, file_get_contents() devolverá false.
    if(!$html){
        //echo 'vacio';
        return(0);
    }

    /***** VALOR USD ACTUAL BCV****/

    $cadena_de_texto = $html;//'Esta es la frase donde haremos la búsqueda';

 
    $cadena_buscada   = '<span> USD</span>'; // valor que busca
    $posicion_coincidencia = strpos($cadena_de_texto, $cadena_buscada);  //ubica la posicion de la busqueda
    $posicion_coincidencia=$posicion_coincidencia-7; // le resta 7 para colcoar las etiquetas completa

    //echo $posicion_coincidencia;die;
    $resultado = substr($html, $posicion_coincidencia, 250);
    //echo $resultado;die;

    $valor = explode (" ", $resultado);

    $resultado = str_replace(",", ".", $valor[64]);
    $resultadoUsd = round($resultado, 4);
    //echo $resultadoUsd; die;

    /***** FECHA ACTUAL BCV****/

    $cadena_de_texto = $html;//'Esta es la frase donde haremos la búsqueda';
    $cadena_buscada   = 'class="date-display-single"'; // valor que busca
    $posicion_coincidencia = strpos($cadena_de_texto, $cadena_buscada);  //ubica la posicion de la busqueda
    $posicion_coincidencia=$posicion_coincidencia-7; // le resta 7 para colcoar las etiquetas completa

    $resultado = substr($html, $posicion_coincidencia, 120);
    
    //var_dump($resultado);// die;
    $largo=strlen($resultado);
    //echo $largo; echo '<br>'; //die;
    $pos = strpos($resultado, '>');
    $pos=$pos+1;
    //echo $pos; echo '<br>'; //die;

    $Arraydia= substr($resultado, 0,$pos);
    //echo $Arraydia; 
 
    $valor = explode (" ", $Arraydia);

    /******* verifia que no es fin de semana ni dia feriado */
    $esFeriado=dia_feriado($valor);

    $dia=date($esFeriado);

    //echo $dia; die;
    /** */
    /*   Se reemplazo porla funcion que calcula si es fin de semana o fia feriado
    
    $diaString= substr($valor[5], 9,10);
    $dia=date($diaString);
    
    */
    /*****  VALORES QUE RETORNA LA FUNCION ****/  
   	return array ($resultadoUsd,$dia);
}



//$exe = usd_bcv();
?>