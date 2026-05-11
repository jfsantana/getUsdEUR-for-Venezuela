<?php
/****************************
CREADO POR: JESUS SANTANA
FECHA 04/MAY/2022
DESCRIPCION: TOMA EL VALOR DEL USD Y EL EURO DE LA PAGINA EL BANCO CENTRAL DE VENEZUELA
Y LO INCERTA EN LAS TABLAS COTIZACIONES Y IT_VALOR_USD_BCV EN CADA UNA DE LAS EMPRESAS DEL GRUPO
****************************/

include_once('function/conn.php');
include_once('function/usd_bcv.php');
include_once('function/euro_bcv.php');	

//llama a la conexion del ambiente de PRD
$connINN = controladorINN_PRD3030(); //GH INN3030
$connSUITES = controladorSUITE_PRD2020(); //GH suite2020
$connHYSYCA = controladorHysica(); //GH hysyca
$connEVENTO = controladorEvento(); //GH controlador Evento
$connBUENAVENTURA = controladorBuenaventura(); //GH BUENAVENTURA
$connHoteleraOLD = controladorHoteleraOLD(); // hotelereaoLD
$controladorHotelera = controladorHotelera(); // hotelerea






/******************CALCULA LA FECHA CALENDARIO DEL DIA DE HOY *********************/
$FechaHoy=date("Y").'-'.date("m").'-'.date("d");
$FechaHoy=date($FechaHoy);
$FechaAyer= date('Y-m-d',strtotime("-1 days"));



/*******************CALCULA EL VALOR DEL USD, EUR Y LA FECHA  ********************/
list($USD_BCV,$diabcv)=usd_bcv();


$FechaHoy = $diabcv;  
$EUR_BCV=euro_bcv();

$num_BBD=6; //NIMERO DE VECES QUE SE EJECUTA EL PROCESO DEBERIA SE4R EL NUMERO DE EMPRESAS QUE TIENEQUE ACTUALIZAR

for ($i = 0; $i <= $num_BBD; $i++) {

	switch ($i) {
		case 0:
			$bandera = 'INN';
			$conn = $connINN;
			$CodMonedaUsd=2;
			$CodMonedaEur=3;
			break;
		case 1:
			$bandera = 'SUITE';
			$conn = $connSUITES;
			$CodMonedaUsd=2;
			$CodMonedaEur=5;
			break;
		case 2:
			$bandera = 'EVENTO';
			$conn = $connEVENTO;
			$CodMonedaUsd=2;
			$CodMonedaEur=3;
			break;
		case 3:
			$bandera = 'HYSYCA';
			$conn = $connHYSYCA;
			$CodMonedaUsd=2;
			$CodMonedaEur=3;
			break;
		case 4:
			$bandera = 'HOTELERA';
			$conn = $controladorHotelera;
			$CodMonedaUsd=2;
			$CodMonedaEur=3;
			break;
		case 5:
			$bandera = 'BUENAVENTURA';
			$conn = $connBUENAVENTURA;
			$CodMonedaUsd=2;
			$CodMonedaEur=5;
			break;	
			
		case 6:
			$bandera = 'HOTELERAOLD';
			$conn = $connHoteleraOLD;
			$CodMonedaUsd=2;
			$CodMonedaEur=5;
			break;
			
		
				
				

		default:
		   echo "NO ETSA DEFINIDA LA CONEXION A LA BASE DE DATOS CORRESPONDIENTE"; 
		   die;
		   break;
	}

	echo "<br> $bandera INICIO DEL PROCESO ---------> ";

	/*******************SE ELIMINA LOS VALORES DEL DIA DE HOY IT_VALOR_USD_BCV ********************/
	$QueryDelUSBCotizacionesToday="delete from IT_VALOR_USD_BCV 
	WHERE fecha_dia=CONVERT(DATETIME,'".$FechaHoy."', 102)";
	$DelcotizacionToday = sqlsrv_query( $conn, $QueryDelUSBCotizacionesToday);
	if( $DelcotizacionToday === false ) {
		die( print_r( sqlsrv_errors(), true));
	}
	echo "<br> borrado de la tabal IT_VALOR_USD_BCV del dia $FechaHoy---------> ";

	/*******************SE ELIMINA LOS VALORES DEL DIA DE HOY COTIZACIONES ********************/
	$QueryDelUSBCotizacionesToday="delete from COTIZACIONES 
	WHERE fecha =CONVERT(DATETIME,'".$FechaHoy."', 102)";
	//echo $QueryDelUSBCotizacionesToday; die;
	$DelcotizacionToday = sqlsrv_query( $conn, $QueryDelUSBCotizacionesToday);
	if( $DelcotizacionToday === false ) {
		die( print_r( sqlsrv_errors(), true));
	}
	echo "<br> borrado de la tabal COTIZACIONES del dia  $FechaHoy---------> ";

	/******************INSERTAR EN LA TABLA IT_VALOR_USD_BCV ************************************/
	$USD_BCV=str_replace ( ',' , '.' , $USD_BCV);
	$EUR_BCV=str_replace ( ',' , '.' , $EUR_BCV );

	$insertITBCV="INSERT INTO IT_VALOR_USD_BCV (FECHA_DIA, VALORUSD_BCV, VALOREUR_BCV) 
					VALUES (CONVERT(DATETIME,'".$FechaHoy."', 102), ".$USD_BCV.",".$EUR_BCV.")";
					//echo $insertITBCV; die;
	$insertcotizacion = sqlsrv_query( $conn, $insertITBCV);
	if( $insertcotizacion === false ) {
		die( print_r( sqlsrv_errors(), true));
	}

	echo "<br> creacion del registro en la tabla IT_VALOR_USD_BCV del dia  $FechaHoy USD:$USD_BCV EUR:$EUR_BCV---------> ";

	/****************INSERTAR EN LA TABLA COTIZACIONES $ *************************************************/
	$insertcotizacion="INSERT INTO COTIZACIONES (FECHA, CODMONEDA, COTIZACION) VALUES (CONVERT(DATETIME,'".$FechaHoy."', 102), ".$CodMonedaUsd.",".$USD_BCV.")";
	
	$insertcotizacion = sqlsrv_query( $conn, $insertcotizacion);
	if( $insertcotizacion === false ) {
		die( print_r( sqlsrv_errors(), true));
	}
	echo "<br> creacion del registro en la tabla COTIZACIONES del dia  $FechaHoy CodMonedaUsd:$CodMonedaUsd USD:$USD_BCV---------> ";

	/****************INSERTAR EN LA TABLA COTIZACIONES EUR *************************************************/
	$insertcotizacion="INSERT INTO COTIZACIONES (FECHA, CODMONEDA, COTIZACION) VALUES (CONVERT(DATETIME,'".$FechaHoy."', 102),  ".$CodMonedaEur.",".$EUR_BCV.")";
	$insertcotizacion = sqlsrv_query( $conn, $insertcotizacion);
	if( $insertcotizacion === false ) {
		die( print_r( sqlsrv_errors(), true));
	}
	echo "<br> creacion del registro en la tabla COTIZACIONES del dia  $FechaHoy CodMonedaUsd:$CodMonedaUsd EUR:$USD_BCV---------> ";

	echo "            CULMINO </br></br>";
	sqlsrv_close( $conn)   ;
}


 ?>