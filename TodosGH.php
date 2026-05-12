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

function get_oficial_rate_from_api($url, $monedaEsperada)
{
	$context = stream_context_create(array(
		'http' => array(
			'timeout' => 15,
		),
	));

	$json = @file_get_contents($url, false, $context);
	if ($json === false) {
		return array(null, null);
	}

	$data = json_decode($json, true);
	if (!is_array($data)) {
		return array(null, null);
	}

	foreach ($data as $item) {
		if (
			isset($item['fuente'], $item['moneda'], $item['promedio']) &&
			strtolower($item['fuente']) === 'oficial' &&
			strtoupper($item['moneda']) === strtoupper($monedaEsperada)
		) {
			$promedio = (float)$item['promedio'];
			$fecha = date('Y-m-d');

			if (!empty($item['fechaActualizacion'])) {
				$timestamp = strtotime($item['fechaActualizacion']);
				if ($timestamp !== false) {
					$fecha = date('Y-m-d', $timestamp);
				}
			}

			return array($promedio, $fecha);
		}
	}

	return array(null, null);
}

function usd_bcv_api_oficial()
{
	return get_oficial_rate_from_api('https://ve.dolarapi.com/v1/dolares', 'USD');
}

function euro_bcv_api_oficial()
{
	return get_oficial_rate_from_api('https://ve.dolarapi.com/v1/euros', 'EUR');
}

function ejecutar_query($conn, $sql, $params = array())
{
	$stmt = sqlsrv_query($conn, $sql, $params);
	if ($stmt === false) {
		throw new Exception(print_r(sqlsrv_errors(), true));
	}

	return $stmt;
}

function iniciar_transaccion($conn)
{
	if (!sqlsrv_begin_transaction($conn)) {
		throw new Exception(print_r(sqlsrv_errors(), true));
	}
}

function confirmar_transaccion($conn)
{
	if (!sqlsrv_commit($conn)) {
		throw new Exception(print_r(sqlsrv_errors(), true));
	}
}

function revertir_transaccion($conn)
{
	if (!sqlsrv_rollback($conn)) {
		throw new Exception(print_r(sqlsrv_errors(), true));
	}
}

//llama a la conexion del ambiente de PRD

$connINN = controladorINN_PRD3030(); //GH INN3030
$connSUITES = controladorSUITE_PRD2020(); //GH suite2020
$connHYSYCA = controladorHysica(); //GH hysyca
$connEVENTO = controladorEvento(); //GH controlador Evento
$connBUENAVENTURA = controladorBuenaventura(); //GH BUENAVENTURA
$connHoteleraOLD = controladorHoteleraOLD(); // hotelereaoLD
$controladorHotelera = controladorHotelera(); // hotelerea


/*******************CALCULA EL VALOR DEL USD, EUR Y LA FECHA  ********************/
// list($USD_BCV,$diabcv)=usd_bcv();  // Calculo del USD (scraping anterior)
list($USD_BCV, $FechaDesdeUsdApi) = usd_bcv_api_oficial();

if ($USD_BCV === null) {
	list($USD_BCV, $FechaDesdeUsdApi) = usd_bcv(); // Fallback al scraping
}

// $EUR_BCV=euro_bcv(); // Extraccion del valor de EUR (scraping anterior)
list($EUR_BCV, $FechaDesdeEurApi) = euro_bcv_api_oficial();

if ($EUR_BCV === null) {
	$EUR_BCV = euro_bcv(); // Fallback al scraping
}

if ($USD_BCV === null || $EUR_BCV === null) {
	die('No fue posible obtener tasas USD/EUR.');
}

$USD_BCV = (float)str_replace(',', '.', $USD_BCV);
$EUR_BCV = (float)str_replace(',', '.', $EUR_BCV);

if ($USD_BCV <= 0 || $EUR_BCV <= 0) {
	die('Las tasas obtenidas no son validas.');
}

// El dia oficial se toma del API de USD. Si no viene, usa fecha local.
$FechaHoy = !empty($FechaDesdeUsdApi) ? $FechaDesdeUsdApi : date('Y-m-d');

$empresas = array(
	array('bandera' => 'INN', 'conn' => $connINN, 'usd' => 2, 'eur' => 3),
	array('bandera' => 'SUITE', 'conn' => $connSUITES, 'usd' => 2, 'eur' => 5),
	array('bandera' => 'EVENTO', 'conn' => $connEVENTO, 'usd' => 2, 'eur' => 3),
	array('bandera' => 'HYSYCA', 'conn' => $connHYSYCA, 'usd' => 2, 'eur' => 3),
	array('bandera' => 'HOTELERA', 'conn' => $controladorHotelera, 'usd' => 2, 'eur' => 3),
	array('bandera' => 'BUENAVENTURA', 'conn' => $connBUENAVENTURA, 'usd' => 2, 'eur' => 5),
	array('bandera' => 'HOTELERAOLD', 'conn' => $connHoteleraOLD, 'usd' => 2, 'eur' => 5),
);

foreach ($empresas as $empresa) {
	$bandera = $empresa['bandera'];
	$conn = $empresa['conn'];
	$CodMonedaUsd = $empresa['usd'];
	$CodMonedaEur = $empresa['eur'];

	echo "<br> $bandera INICIO DEL PROCESO ---------> ";

	if ($conn === false || $conn === null) {
		echo "<br> ERROR DE CONEXION EN $bandera ---------> ";
		continue;
	}

	try {
		iniciar_transaccion($conn);

		// Reemplazo del consolidado del dia en IT_VALOR_USD_BCV.
		ejecutar_query(
			$conn,
			"DELETE FROM IT_VALOR_USD_BCV WHERE fecha_dia = CONVERT(DATETIME, ?, 102)",
			array($FechaHoy)
		);

		ejecutar_query(
			$conn,
			"INSERT INTO IT_VALOR_USD_BCV (FECHA_DIA, VALORUSD_BCV, VALOREUR_BCV) VALUES (CONVERT(DATETIME, ?, 102), ?, ?)",
			array($FechaHoy, $USD_BCV, $EUR_BCV)
		);

		// Reemplazo puntual por dia y moneda para no afectar otras monedas del mismo dia.
		ejecutar_query(
			$conn,
			"DELETE FROM COTIZACIONES WHERE FECHA = CONVERT(DATETIME, ?, 102) AND CODMONEDA = ?",
			array($FechaHoy, $CodMonedaUsd)
		);

		ejecutar_query(
			$conn,
			"DELETE FROM COTIZACIONES WHERE FECHA = CONVERT(DATETIME, ?, 102) AND CODMONEDA = ?",
			array($FechaHoy, $CodMonedaEur)
		);

		ejecutar_query(
			$conn,
			"INSERT INTO COTIZACIONES (FECHA, CODMONEDA, COTIZACION) VALUES (CONVERT(DATETIME, ?, 102), ?, ?)",
			array($FechaHoy, $CodMonedaUsd, $USD_BCV)
		);

		ejecutar_query(
			$conn,
			"INSERT INTO COTIZACIONES (FECHA, CODMONEDA, COTIZACION) VALUES (CONVERT(DATETIME, ?, 102), ?, ?)",
			array($FechaHoy, $CodMonedaEur, $EUR_BCV)
		);

		confirmar_transaccion($conn);

		echo "<br> reemplazo OK dia $FechaHoy USD:$USD_BCV EUR:$EUR_BCV ---------> ";
		echo " CULMINO CON EXITO</br></br>";
	} catch (Exception $e) {
		revertir_transaccion($conn);
		echo "<br> ERROR EN $bandera: " . $e->getMessage() . " ---------> ";
	}

	sqlsrv_close($conn);
}


 ?>