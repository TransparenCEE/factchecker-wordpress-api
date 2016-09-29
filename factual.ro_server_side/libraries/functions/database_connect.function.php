<?php
function database_connect($db_user, $db_passwd, $db_host, $db_name){
	//aceasta functie face connectul la baza de date
	//////////////////////////////////////////////////////////////////////////////////////////////
	require_once('MDB2.php');
	PEAR::setErrorHandling(PEAR_ERROR_CALLBACK, 'mysql_errors_handler');

	$dsn = array(
	'phptype'  => 'mysql',
	'username' => $db_user,
	'password' => $db_passwd,
	'hostspec' => $db_host,
	'database' => $db_name
        ,'charset' => 'utf8'
	);
        
	$options = array(
	'debug'       => 0,
	'portability' => MDB2_PORTABILITY_ALL^MDB2_PORTABILITY_FIX_CASE^MDB2_PORTABILITY_EMPTY_TO_NULL,
	//^MDB2_PORTABILITY_FIX_CASE - numele fieldurilor din DB sunt case sensitive
	//^MDB2_PORTABILITY_EMPTY_TO_NULL - iar la autoExecute sa nu converteasca valorile care nu contin nimic cu null (pt ca majoritatea fieldurilor au not_null)
	'seqcol_name' => 'id'
	);

	$mdb2 =& MDB2::factory($dsn, $options);


	$mdb2->setFetchMode(MDB2_FETCHMODE_ASSOC);//setez asociativ modul de intoarcere a rezultatelor (adica indecsii sunt numele coloanelor si nu ordinea definirii lor in structura tabelului)

	//PEAR::setErrorHandling(PEAR_ERROR_CALLBACK, 'handle_pear_error');

	$mdb2->loadModule('Driver_Manager_mysql');
	$mdb2->driver_manager = $mdb2->driver_manager_mysql;
	$mdb2->loadModule('Extended', null, false);

	return($mdb2);

}//end de la function database_connect();

?>