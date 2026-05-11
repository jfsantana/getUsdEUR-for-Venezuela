<?php
/************************************************************************************/
//PRODUCCION NO TOCAR 
/* programador Jesus Santana */

function controladorINN_PRD3030(){ //GH SUITES
	$serverName = "10.0.0.23\ICGINN"; //serverName\instanceName	
	$connectionInfo = array( "Database"=>"GESTION_RECREO", "UID"=>"sa", "PWD"=>"masterkey");
	$conn = sqlsrv_connect( $serverName, $connectionInfo);
	ini_set('mssql.charset', 'UTF-8');
	return $conn;	
}

function controladorSUITE_PRD2020(){ //GH SUITES
	$serverName = "10.0.4.8\SUITES"; //serverName\instanceName	
	$connectionInfo = array( "Database"=>"FRHSUITES", "UID"=>"sa", "PWD"=>"masterkey");
	$conn = sqlsrv_connect( $serverName, $connectionInfo);
	ini_set('mssql.charset', 'UTF-8');
	return $conn;		
}

function controladorBuenaventura(){//GH Buenaventura
	$serverName = "10.0.2.8\bv"; //serverName\instanceName	
	$connectionInfo = array( "Database"=>"FRHBV", "UID"=>"sa", "PWD"=>"masterkey");
	$conn = sqlsrv_connect( $serverName, $connectionInfo);
	ini_set('mssql.charset', 'UTF-8');
	return $conn;	
}

function controladorHysica(){//GH Hysica  SERVER-HYSYCA\HYSYCA
	$serverName = "10.0.0.36\HYSYCA"; //serverName\instanceName	
	$connectionInfo = array( "Database"=>"GESTION_HYSYCA", "UID"=>"sa", "PWD"=>"masterkey");
	$conn = sqlsrv_connect( $serverName, $connectionInfo);
	ini_set('mssql.charset', 'UTF-8');
	return $conn;	
}

function controladorEvento(){//GH Evento  SERVER-HYSYCA\HYSYCA
	$serverName = "10.0.0.23\ICGINN"; //serverName\instanceName	
	$connectionInfo = array( "Database"=>"EVENTOS", "UID"=>"sa", "PWD"=>"masterkey");
	$conn = sqlsrv_connect( $serverName, $connectionInfo);
	ini_set('mssql.charset', 'UTF-8');
	return $conn;	
}

function controladorHoteleraOLD(){ //GH INN
	$serverName = "10.0.0.11\ICG"; //serverName\instanceName	
   $connectionInfo = array( "Database"=>"GESTION_RECREO", "UID"=>"sa", "PWD"=>"masterkey");
   $conn = sqlsrv_connect( $serverName, $connectionInfo);
   ini_set('mssql.charset', 'UTF-8');
   return $conn;	
}

function controladorHotelera(){ //GH INN
	$serverName = "10.0.0.36\HYSYCA"; //serverName\instanceName	
   $connectionInfo = array( "Database"=>"HOTELERA", "UID"=>"sa", "PWD"=>"masterkey");
   $conn = sqlsrv_connect( $serverName, $connectionInfo);
   ini_set('mssql.charset', 'UTF-8');
   return $conn;	
}
/************************************************************************************/




//SERVIDORES DE DESARROLLO 

function controlador1(){
	//LABORATORIO
	$serverName = "10.0.0.219\INN"; //serverName\instanceName
	$connectionInfo = array( "Database"=>"GESTION_RECREO", "UID"=>"sa", "PWD"=>"masterkey");
	$conn = sqlsrv_connect( $serverName, $connectionInfo);
	ini_set('mssql.charset', 'UTF-8');
	return $conn;	
}



function controladorINN_DEV2020(){ //GH SUITES
	$serverName = "10.0.0.23\ICGINN"; //serverName\instanceName	
	$connectionInfo = array( "Database"=>"GESTION_RECREO", "UID"=>"sa", "PWD"=>"masterkey");
	$conn = sqlsrv_connect( $serverName, $connectionInfo);
	ini_set('mssql.charset', 'UTF-8');
	return $conn;	
}

function controladorPRDSUITES(){ //GH SUITES
	$serverName = "10.0.4.8\SUITES"; //serverName\instanceName	
	$connectionInfo = array( "Database"=>"FRHSUITES", "UID"=>"sa", "PWD"=>"masterkey");
	$conn = sqlsrv_connect( $serverName, $connectionInfo);
	ini_set('mssql.charset', 'UTF-8');
	return $conn;	
}


function controladorGHBuenaventura(){
	$serverName = "10.0.0.36\ICGBV"; //serverName\instanceName	
	$connectionInfo = array( "Database"=>"FRHBV", "UID"=>"sa", "PWD"=>"masterkey");
	$conn = sqlsrv_connect( $serverName, $connectionInfo);
	ini_set('mssql.charset', 'UTF-8');
	return $conn;	
	
}